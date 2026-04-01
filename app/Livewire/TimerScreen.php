<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Session;
use App\Services\SessionService;
use App\Services\SettingsService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]

class TimerScreen extends Component
{
    public string $type = 'focus';
    public int $elapsedSeconds = 0;
    public bool $isRunning = false;
    public bool $isPaused = false;
    public ?int $categoryId = null;
    public int $cycleCount = 0;
    public ?int $currentSessionId = null;
    public bool $showSummary = false;
    public array $summary = [];

    // Settings
    public int $focusDuration = 25;
    public int $shortBreakDuration = 5;
    public int $longBreakDuration = 15;
    public int $sessionsBeforeLong = 4;

    public function mount(SettingsService $settings): void
    {
        $all = $settings->all();
        $this->focusDuration       = (int) $all['focus_duration'];
        $this->shortBreakDuration  = (int) $all['short_break_duration'];
        $this->longBreakDuration   = (int) $all['long_break_duration'];
        $this->sessionsBeforeLong  = (int) $all['sessions_before_long'];
    }

    public function getDurationSeconds(): int
    {
        return match ($this->type) {
            'focus'       => $this->focusDuration * 60,
            'short_break' => $this->shortBreakDuration * 60,
            'long_break'  => $this->longBreakDuration * 60,
        };
    }

    public function start(SessionService $service): void
    {
        $session = $service->start(
            type: $this->type,
            durationMinutes: (int) ($this->getDurationSeconds() / 60),
            categoryId: $this->categoryId,
        );

        $this->currentSessionId = $session->id;
        $this->elapsedSeconds   = 0;
        $this->isRunning        = true;
        $this->isPaused         = false;
        $this->showSummary      = false;
    }

    public function pause(): void
    {
        $this->isRunning = false;
        $this->isPaused  = true;
    }

    public function resume(): void
    {
        $this->isRunning = true;
        $this->isPaused  = false;
    }

    public function tick(): void
    {
        if (! $this->isRunning) {
            return;
        }

        $this->elapsedSeconds++;

        if ($this->elapsedSeconds >= $this->getDurationSeconds()) {
            $this->complete();
        }
    }

    public function complete(SessionService $service = null): void
    {
        $service ??= app(SessionService::class);

        $this->isRunning = false;
        $this->isPaused  = false;

        if ($this->currentSessionId) {
            $session = Session::find($this->currentSessionId);
            if ($session) {
                $service->complete($session);
            }
        }

        if ($this->type === 'focus') {
            $this->cycleCount++;
        }

        $this->summary = [
            'type'     => $this->type,
            'duration' => $this->elapsedSeconds,
            'category' => $this->categoryId
                ? Category::find($this->categoryId)?->name
                : null,
        ];

        $this->showSummary      = true;
        $this->currentSessionId = null;
        $this->elapsedSeconds   = 0;
        $this->advanceType();
    }

    public function stop(SessionService $service): void
    {
        $this->isRunning = false;
        $this->isPaused  = false;

        if ($this->currentSessionId) {
            $session = Session::find($this->currentSessionId);
            if ($session) {
                $service->interrupt($session, $this->elapsedSeconds);
            }
        }

        $this->summary = [
            'type'     => $this->type,
            'duration' => $this->elapsedSeconds,
            'category' => $this->categoryId
                ? Category::find($this->categoryId)?->name
                : null,
        ];

        $this->showSummary      = true;
        $this->currentSessionId = null;
        $this->elapsedSeconds   = 0;
    }

    public function dismissSummary(): void
    {
        $this->showSummary = false;
    }

    public function setType(string $type): void
    {
        if ($this->isRunning) {
            return;
        }

        $this->type           = $type;
        $this->elapsedSeconds = 0;
    }

    private function advanceType(): void
    {
        if ($this->type !== 'focus') {
            $this->type = 'focus';

            return;
        }

        $this->type = ($this->cycleCount % $this->sessionsBeforeLong === 0)
            ? 'long_break'
            : 'short_break';
    }

    public function render()
    {
        $durationSeconds = $this->getDurationSeconds();

        return view('livewire.timer-screen', [
            'categories'       => Category::orderBy('name')->get(),
            'durationSeconds'  => $durationSeconds,
            'remainingSeconds' => max(0, $durationSeconds - $this->elapsedSeconds),
        ]);
    }
}
