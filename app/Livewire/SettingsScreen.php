<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\SettingsService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]

class SettingsScreen extends Component
{
    public int $focusDuration = 25;
    public int $shortBreakDuration = 5;
    public int $longBreakDuration = 15;
    public int $sessionsBeforeLong = 4;

    public string $newCategoryName = '';
    public string $newCategoryColor = '#FF6B6B';
    public ?int $editingCategoryId = null;
    public string $editingCategoryName = '';
    public string $editingCategoryColor = '#FF6B6B';

    public bool $saved = false;

    public function mount(SettingsService $settings): void
    {
        $all = $settings->all();
        $this->focusDuration      = (int) $all['focus_duration'];
        $this->shortBreakDuration = (int) $all['short_break_duration'];
        $this->longBreakDuration  = (int) $all['long_break_duration'];
        $this->sessionsBeforeLong = (int) $all['sessions_before_long'];
    }

    public function saveSettings(SettingsService $settings): void
    {
        $this->validate([
            'focusDuration'      => 'required|integer|min:1|max:120',
            'shortBreakDuration' => 'required|integer|min:1|max:60',
            'longBreakDuration'  => 'required|integer|min:1|max:120',
            'sessionsBeforeLong' => 'required|integer|min:1|max:10',
        ]);

        $settings->set('focus_duration', $this->focusDuration);
        $settings->set('short_break_duration', $this->shortBreakDuration);
        $settings->set('long_break_duration', $this->longBreakDuration);
        $settings->set('sessions_before_long', $this->sessionsBeforeLong);

        $this->saved = true;
    }

    public function addCategory(): void
    {
        $this->validate(['newCategoryName' => 'required|string|max:50']);

        Category::create([
            'name'  => trim($this->newCategoryName),
            'color' => $this->newCategoryColor,
        ]);

        $this->newCategoryName  = '';
        $this->newCategoryColor = '#FF6B6B';
    }

    public function startEdit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId    = $id;
        $this->editingCategoryName  = $category->name;
        $this->editingCategoryColor = $category->color;
    }

    public function saveEdit(): void
    {
        $this->validate(['editingCategoryName' => 'required|string|max:50']);

        Category::findOrFail($this->editingCategoryId)->update([
            'name'  => trim($this->editingCategoryName),
            'color' => $this->editingCategoryColor,
        ]);

        $this->editingCategoryId = null;
    }

    public function cancelEdit(): void
    {
        $this->editingCategoryId = null;
    }

    public function deleteCategory(int $id): void
    {
        $category = Category::withCount('sessions')->findOrFail($id);

        if ($category->sessions_count > 0) {
            return;
        }

        $category->delete();
    }

    public function render()
    {
        return view('livewire.settings-screen', [
            'categories' => Category::withCount('sessions')->orderBy('name')->get(),
        ]);
    }
}
