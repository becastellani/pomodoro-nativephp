<div class="px-6 pt-8 pb-6">
    <h1 class="text-2xl font-bold mb-6">Settings</h1>

    {{-- Timer durations --}}
    <div class="bg-gray-900 rounded-2xl p-5 mb-6">
        <p class="text-gray-400 text-xs uppercase tracking-widest mb-4">Timer</p>

        @foreach([
            ['focusDuration', 'Focus', 'min', 1, 120],
            ['shortBreakDuration', 'Short Break', 'min', 1, 60],
            ['longBreakDuration', 'Long Break', 'min', 1, 120],
            ['sessionsBeforeLong', 'Sessions before long break', '', 1, 10],
        ] as [$field, $label, $unit, $min, $max])
            <div class="flex items-center justify-between py-3 border-b border-gray-800 last:border-0">
                <label class="text-sm text-gray-300">{{ $label }}</label>
                <div class="flex items-center gap-2">
                    <input
                        type="number"
                        wire:model="{{ $field }}"
                        min="{{ $min }}"
                        max="{{ $max }}"
                        class="w-16 bg-gray-800 text-white text-center rounded-lg py-1.5 text-sm border border-gray-700 focus:outline-none focus:border-red-500"
                    />
                    @if($unit)
                        <span class="text-gray-500 text-sm">{{ $unit }}</span>
                    @endif
                </div>
            </div>
        @endforeach

        @if($saved)
            <p class="text-green-500 text-xs text-center mt-3">Saved!</p>
        @endif

        <button
            wire:click="saveSettings"
            class="w-full mt-4 bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-xl"
        >
            Save
        </button>
    </div>

    {{-- Categories --}}
    <div class="bg-gray-900 rounded-2xl p-5">
        <p class="text-gray-400 text-xs uppercase tracking-widest mb-4">Categories</p>

        <div class="flex flex-col gap-3 mb-4">
            @foreach($categories as $category)
                @if($editingCategoryId === $category->id)
                    <div class="flex items-center gap-2">
                        <input type="color" wire:model="editingCategoryColor" class="w-8 h-8 rounded cursor-pointer bg-transparent border-0">
                        <input
                            type="text"
                            wire:model="editingCategoryName"
                            class="flex-1 bg-gray-800 text-white rounded-lg px-3 py-2 text-sm border border-gray-700 focus:outline-none focus:border-red-500"
                        />
                        <button wire:click="saveEdit" class="text-green-500 text-sm font-medium">Save</button>
                        <button wire:click="cancelEdit" class="text-gray-500 text-sm">Cancel</button>
                    </div>
                @else
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $category->color }}"></span>
                            <span class="text-sm">{{ $category->name }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button wire:click="startEdit({{ $category->id }})" class="text-gray-500 text-xs">Edit</button>
                            @if($category->sessions_count === 0)
                                <button wire:click="deleteCategory({{ $category->id }})" class="text-red-500 text-xs">Delete</button>
                            @else
                                <span class="text-gray-700 text-xs">{{ $category->sessions_count }} sessions</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Add category --}}
        <div class="flex items-center gap-2 pt-3 border-t border-gray-800">
            <input type="color" wire:model="newCategoryColor" class="w-8 h-8 rounded cursor-pointer bg-transparent border-0">
            <input
                type="text"
                wire:model="newCategoryName"
                placeholder="New category"
                class="flex-1 bg-gray-800 text-white rounded-lg px-3 py-2 text-sm border border-gray-700 focus:outline-none focus:border-red-500 placeholder-gray-600"
            />
            <button wire:click="addCategory" class="bg-gray-700 hover:bg-gray-600 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Add
            </button>
        </div>
        @error('newCategoryName')
            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>
