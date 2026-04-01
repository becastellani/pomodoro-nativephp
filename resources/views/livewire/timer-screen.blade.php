<div
    x-data="{
        running: @entangle('isRunning'),
        init() {
            setInterval(() => {
                if (this.running) {
                    $wire.tick();
                }
            }, 1000);
        }
    }"
    class="flex flex-col items-center px-6 pt-10 pb-6 min-h-screen"
>
    {{-- Summary modal --}}
    @if($showSummary)
        <div class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-6">
            <div class="bg-gray-900 rounded-2xl p-6 w-full max-w-sm text-center">
                <div class="text-4xl mb-3">{{ $summary['type'] === 'focus' ? '🍅' : '☕' }}</div>
                <h2 class="text-xl font-semibold mb-1">
                    {{ $summary['type'] === 'focus' ? 'Focus session done' : 'Break over' }}
                </h2>
                @if($summary['category'])
                    <p class="text-gray-400 text-sm mb-1">{{ $summary['category'] }}</p>
                @endif
                <p class="text-gray-500 text-sm mb-5">
                    {{ gmdate('G\h i\m', $summary['duration']) }}
                </p>
                <button wire:click="dismissSummary" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 rounded-xl">
                    Continue
                </button>
            </div>
        </div>
    @endif

    {{-- Type selector --}}
    <div class="flex gap-2 mb-10 bg-gray-900 rounded-xl p-1 w-full">
        @foreach([['focus', 'Focus'], ['short_break', 'Short'], ['long_break', 'Long']] as [$value, $label])
            <button
                wire:click="setType('{{ $value }}')"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors
                    {{ $type === $value ? 'bg-red-500 text-white' : 'text-gray-400' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Cycle counter --}}
    <p class="text-gray-500 text-sm mb-4">
        Session {{ $cycleCount + 1 }} of {{ $sessionsBeforeLong }}
    </p>

    {{-- Countdown ring --}}
    <div class="relative flex items-center justify-center mb-10">
        <svg class="w-64 h-64 -rotate-90" viewBox="0 0 200 200">
            <circle cx="100" cy="100" r="90" fill="none" stroke="#1f2937" stroke-width="10"/>
            <circle
                cx="100" cy="100" r="90"
                fill="none"
                stroke="#ef4444"
                stroke-width="10"
                stroke-linecap="round"
                stroke-dasharray="{{ 2 * M_PI * 90 }}"
                stroke-dashoffset="{{ 2 * M_PI * 90 * (1 - ($elapsedSeconds / max(1, $durationSeconds))) }}"
                style="transition: stroke-dashoffset 0.8s linear;"
            />
        </svg>
        <div class="absolute text-center">
            <span class="text-6xl font-mono font-bold tracking-tight">
                {{ sprintf('%02d:%02d', intdiv($remainingSeconds, 60), $remainingSeconds % 60) }}
            </span>
        </div>
    </div>

    {{-- Controls --}}
    <div class="flex gap-4 mb-8">
        @if(! $isRunning && ! $isPaused)
            <button wire:click="start" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-10 py-4 rounded-2xl text-lg">
                Start
            </button>
        @elseif($isRunning)
            <button wire:click="pause" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold px-8 py-4 rounded-2xl text-lg">
                Pause
            </button>
            <button wire:click="stop" class="bg-gray-800 hover:bg-gray-700 text-gray-400 font-semibold px-8 py-4 rounded-2xl text-lg">
                Stop
            </button>
        @elseif($isPaused)
            <button wire:click="resume" class="bg-red-500 hover:bg-red-600 text-white font-semibold px-8 py-4 rounded-2xl text-lg">
                Resume
            </button>
            <button wire:click="stop" class="bg-gray-800 hover:bg-gray-700 text-gray-400 font-semibold px-8 py-4 rounded-2xl text-lg">
                Stop
            </button>
        @endif
    </div>

    {{-- Category selector --}}
    <div class="w-full">
        <p class="text-gray-500 text-xs uppercase tracking-widest mb-3">Category</p>
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="$set('categoryId', null)"
                class="px-4 py-2 rounded-xl text-sm font-medium transition-colors
                    {{ $categoryId === null ? 'bg-gray-700 text-white' : 'bg-gray-900 text-gray-400' }}"
            >
                None
            </button>
            @foreach($categories as $category)
                <button
                    wire:click="$set('categoryId', {{ $category->id }})"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2
                        {{ $categoryId === $category->id ? 'bg-gray-700 text-white' : 'bg-gray-900 text-gray-400' }}"
                >
                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $category->color }}"></span>
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>
