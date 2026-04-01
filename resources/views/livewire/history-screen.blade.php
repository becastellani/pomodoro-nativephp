<div class="px-6 pt-8 pb-6">
    <h1 class="text-2xl font-bold mb-6">History</h1>

    {{-- Category filter --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-6 no-scrollbar">
        <button
            wire:click="$set('filterCategoryId', null)"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-colors
                {{ $filterCategoryId === null ? 'bg-red-500 text-white' : 'bg-gray-900 text-gray-400' }}"
        >
            All
        </button>
        @foreach($categories as $category)
            <button
                wire:click="$set('filterCategoryId', {{ $category->id }})"
                class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2
                    {{ $filterCategoryId === $category->id ? 'bg-gray-700 text-white' : 'bg-gray-900 text-gray-400' }}"
            >
                <span class="w-2 h-2 rounded-full" style="background-color: {{ $category->color }}"></span>
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    {{-- Grouped by date --}}
    @forelse($grouped as $date => $sessions)
        @php
            $focusMinutes = $sessions->where('type', 'focus')->sum('elapsed_seconds') / 60;
            $sessionCount = $sessions->count();
        @endphp
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-gray-400 text-sm font-medium">
                    {{ \Illuminate\Support\Carbon::parse($date)->isToday() ? 'Today' : \Illuminate\Support\Carbon::parse($date)->format('M j, Y') }}
                </p>
                <p class="text-gray-600 text-xs">
                    {{ $sessionCount }} {{ Str::plural('session', $sessionCount) }} · {{ round($focusMinutes) }}m focus
                </p>
            </div>
            <div class="flex flex-col gap-2">
                @foreach($sessions as $session)
                    <div class="bg-gray-900 rounded-xl px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($session->category)
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $session->category->color }}"></span>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-gray-700"></span>
                            @endif
                            <div>
                                <p class="text-sm">{{ ucfirst(str_replace('_', ' ', $session->type)) }}</p>
                                <p class="text-gray-600 text-xs">{{ $session->started_at->format('H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-mono text-gray-300">
                                {{ sprintf('%02d:%02d', intdiv($session->elapsed_seconds, 60), $session->elapsed_seconds % 60) }}
                            </p>
                            <span class="text-xs {{ $session->status === 'completed' ? 'text-green-500' : 'text-yellow-500' }}">
                                {{ $session->status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p class="text-gray-600 text-center mt-20">No sessions recorded yet.</p>
    @endforelse
</div>
