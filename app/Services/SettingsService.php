<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SettingsService
{
    private array $defaults = [
        'focus_duration'        => 25,
        'short_break_duration'  => 5,
        'long_break_duration'   => 15,
        'sessions_before_long'  => 4,
    ];

    public function get(string $key): mixed
    {
        $setting = DB::table('settings')->where('key', $key)->first();

        return $setting ? $setting->value : $this->defaults[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        DB::table('settings')->upsert(
            ['key' => $key, 'value' => $value, 'created_at' => now(), 'updated_at' => now()],
            ['key'],
            ['value', 'updated_at']
        );
    }

    public function all(): array
    {
        $stored = DB::table('settings')->pluck('value', 'key')->toArray();

        return array_merge($this->defaults, $stored);
    }
}