<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]

class HistoryScreen extends Component
{
    public ?int $filterCategoryId = null;

    public function render()
    {
        $query = Session::with('category')
            ->when($this->filterCategoryId, fn ($q) => $q->where('category_id', $this->filterCategoryId))
            ->orderByDesc('started_at')
            ->get();

        $grouped = $query->groupBy(fn ($s) => $s->started_at->toDateString());

        return view('livewire.history-screen', [
            'grouped'    => $grouped,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
