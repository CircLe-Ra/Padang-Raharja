<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('master-data/account-code', 'master-data.account-code')->name('master-data.account-code');
    Volt::route('master-data/funding-source', 'master-data.funding-source')->name('master-data.funding-source');
    Volt::route('master-data/history', 'master-data.history')->name('master-data.history');
    Volt::route('master-data/geography-demographics', 'master-data.geography-demographics')->name('master-data.geography-demographics');
    Volt::route('master-data/vision-mission-goals', 'master-data.vision-mission-goal')->name('master-data.vision-mission-goals');
    Volt::route('master-data/structure', 'master-data.structure')->name('master-data.structure');
    Volt::route('master-data/users', 'master-data.user')->name('master-data.users');

    Volt::route('activity', 'activity')->name('activity');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
