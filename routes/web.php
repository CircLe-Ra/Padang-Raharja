<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('/', function () {
    if(auth()->user()){
        if(auth()->user()->roles->first()->name == 'masyarakat') {
            return redirect()->route('personal-dashboard');
        }else if(auth()->user()->roles->first()->name == 'staff') {
            return redirect()->route('dashboard');
        }
    }else{
        return redirect()->route('home');
    }

})->name('toView');

Volt::route('/home', 'public.home')->name('home');
Volt::route('portal/activity-read/{id}', 'public.activity-read')->name('activity-read');
Volt::route('portal/activity', 'public.activity')->name('portal.activity');
Volt::route('portal/budget', 'public.budget')->name('portal.budget');
Volt::route('portal/realization', 'public.realization')->name('portal.realization');
Volt::route('portal/aspiration', 'public.aspiration')->name('portal.aspiration');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('aspiration-detail/ticket/{ticket}', 'personal-aspiration-detail')->name('portal.aspiration-detail');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('master-data/account-code', 'master-data.account-code')->name('master-data.account-code');
    Volt::route('master-data/funding-source', 'master-data.funding-source')->name('master-data.funding-source');
    Volt::route('master-data/history', 'master-data.history')->name('master-data.history');
    Volt::route('master-data/geography-demographics', 'master-data.geography-demographics')->name('master-data.geography-demographics');
    Volt::route('master-data/vision', 'master-data.vision')->name('master-data.vision');
    Volt::route('master-data/mission', 'master-data.mission')->name('master-data.mission');
    Volt::route('master-data/structure', 'master-data.structure')->name('master-data.structure');
    Volt::route('master-data/users', 'master-data.user')->name('master-data.users');

    Volt::route('activity', 'activity')->name('activity');
    Volt::route('fiscal-years', 'budget.fiscal-year')->name('budget.plan.fiscal-years');
    Volt::route('budget-plan/{fiscalYearId}', 'budget.budget-plan')->name('budget.plan.budget-plan');
//    Volt::route('budget/plan/fiscal-years', 'budget.fiscal-year')->name('budget.plan.fiscal-years');
//    Volt::route('budget/plan/{fiscalYearId}', 'budget.budget-plan-file')->name('budget.plan.budget-plan');

    Volt::route('budget/realization/fiscal-years', 'budget.fiscal-year-realiztion')->name('budget.realization.fiscal-years');
    Volt::route('budget/realization/data/{fiscalYearId}', 'budget.budget-realization')->name('budget.realization.data');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');


    Volt::route('personal-dashboard', 'personal-dashboard')->name('personal-dashboard');
    Volt::route('personal-data', 'personal-data')->name('personal-data');
    Volt::route('personal-aspiration', 'personal-aspiration')->name('personal-aspiration');
});


require __DIR__.'/auth.php';
