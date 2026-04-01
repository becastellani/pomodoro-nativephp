<div class="px-6 pt-8 pb-6">
    <h1 class="text-2xl font-bold mb-1">Today</h1>
    <p class="text-gray-400 text-sm mb-6">{{ now()->format('l, F j') }}</p>

    {{-- Total focus time --}}
    <div class="bg-gray-900 rounded-2xl p-4 mb-6 flex items-center justify-between">
        <span class="text-gray-400 text-sm">Total focus time</span>
        <span class="text-red-400 font-semibold text-lg">
            {{ sprintf('%dh %02dm', intdiv($totalFocusSeconds, 3600), intdiv($totalFocusSeconds % 3600, 60)) }}
        </span>
    </div>

    @forelse([['focus', 'Focus'], ['short_break', 'Short Break'], ['long_break', 'Long Break']] as [$type, $label])
        @if($grouped->has($type))
            <div class="mb-6">
                <p class="text-gray-500 text-xs uppercase tracking-widest mb-3">{{ $label }}</p>
                <div class="flex flex-col gap-3">
                    @foreach($grouped[$type] as $session)
                        <div class="bg-gray-900 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($session->category)
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $session->category->color }}"></span>
                                @else
                                    <span class="w-3 h-3 rounded-full shrink-0 bg-gray-700"></span>
                                @endif
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ $session->category?->name ?? 'No category' }}
                                    </p>
                                    <p class="text-gray-500 text-xs">
                                        {{ $session->started_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-mono">
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
        @endif
    @empty
        <p class="text-gray-600 text-center mt-20">No sessions today yet.</p>
    @endforelse
</div>
