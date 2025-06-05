<?php

use App\Models\AccountCode;
use App\Models\Comment;
use App\Models\Device;
use App\Models\Lecturer;
use App\Models\Rfid;
use App\Models\User;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $totalUser;
    public ?int $totalComment;
    public ?int $totalRealization;

    public function mount(): void
    {
        $this->totalUser = User::count() ?? 0;
        $this->totalComment = Comment::count() ?? 0;
        $fiscalYear = \App\Models\FiscalYear::where('status', true)->first();
        $this->totalRealization = \App\Models\BudgetPlan::where('fiscal_year_id', $fiscalYear->id ?? 0)->where('realization', 'already')->count() ?? 0;
    }
}; ?>

<div>
    <x-budget.breadcrumb active="Halaman Utama"/>
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl mt-2">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="flex items-center justify-center aspect-video overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="w-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="5em" height="5em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-16 lucide lucide-users-icon lucide-users"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="w-1/2 flex flex-col gap-4">
                    <flux:heading size="xl" level="1">Total Pengguna</flux:heading>
                    <flux:heading size="xl" level="1">{{ $this->totalUser }} Akun</flux:heading>
                </div>
            </div>
            <div
                class="flex items-center justify-center aspect-video overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="w-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="5em" height="5em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-16 lucide lucide-inbox-icon lucide-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/></svg>
                </div>
                <div class="w-1/2 flex flex-col gap-4 p-2">
                    <flux:heading size="xl" level="1">Total Kotak Saran</flux:heading>
                    <flux:heading size="xl" level="1">{{ $this->totalComment }} Komentar</flux:heading>
                </div>
            </div>
            <div
                class="flex items-center justify-center aspect-video overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 ">
                <div class="w-1/2">
                    <svg  width="5em" height="5em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-16 lucide lucide-hand-coins-icon lucide-hand-coins"><path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17"/><path d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 16 6 6"/><circle cx="16" cy="9" r="2.9"/><circle cx="6" cy="5" r="3"/></svg>
                </div>
                <div class="w-1/2 flex flex-col gap-4">
                    <flux:heading size="xl" level="1">Total Anggaran Terealisasi</flux:heading>
                    <flux:heading size="xl" level="1">{{ $this->totalRealization }} Terealisasi</flux:heading>
                </div>
            </div>
        </div>
    </div>
</div>
