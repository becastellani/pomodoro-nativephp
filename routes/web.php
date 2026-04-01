<?php

use App\Livewire\HistoryScreen;
use App\Livewire\LogScreen;
use App\Livewire\SettingsScreen;
use App\Livewire\TimerScreen;
use Illuminate\Support\Facades\Route;

Route::get('/', TimerScreen::class)->name('timer');
Route::get('/log', LogScreen::class)->name('log');
Route::get('/history', HistoryScreen::class)->name('history');
Route::get('/settings', SettingsScreen::class)->name('settings');