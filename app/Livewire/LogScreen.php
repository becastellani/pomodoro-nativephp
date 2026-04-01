<?php

namespace App\Livewire;

use App\Models\Session;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]

class LogScreen extends Component
{
    public function render()
    {
        $sessions = Session::with('category')
            ->whereDate('started_at', Carbon::today())
            ->orderByDesc('started_at')
            ->get();

        $grouped = $sessions->groupBy('type');

        $totalFocusSeconds = $sessions
            ->where('type', 'focus')
            ->sum('elapsed_seconds');

        return view('livewire.log-screen', [
            'grouped'           => $grouped,
            'totalFocusSeconds' => $totalFocusSeconds,
        ]);
    }
}
