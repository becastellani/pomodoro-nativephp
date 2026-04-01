<?php

namespace App\Services;

use App\Models\Session;
use Illuminate\Support\Carbon;

class SessionService
{
    public function start(string $type, int $durationMinutes, ?int $categoryId = null): Session
    {
        return Session::create([
            'type'             => $type,
            'status'           => 'interrupted',
            'duration_minutes' => $durationMinutes,
            'elapsed_seconds'  => 0,
            'category_id'      => $categoryId,
            'started_at'       => Carbon::now(),
        ]);
    }

    public function complete(Session $session): Session
    {
        $session->update([
            'status'          => 'completed',
            'elapsed_seconds' => $session->duration_minutes * 60,
            'ended_at'        => Carbon::now(),
        ]);

        return $session;
    }

    public function interrupt(Session $session, int $elapsedSeconds): Session
    {
        $session->update([
            'status'          => 'interrupted',
            'elapsed_seconds' => $elapsedSeconds,
            'ended_at'        => Carbon::now(),
        ]);

        return $session;
    }
}