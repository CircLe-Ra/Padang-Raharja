<?php

use App\Models\BudgetRealization;
use Livewire\Volt\Component;
use App\Models\FiscalYear;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Realisasi Anggaran')]
class extends Component {
    public $realization;

    public function mount(): void
    {
        $fiscalYear = FiscalYear::where('status', true)->first();
        $this->realization = BudgetRealization::where('fiscal_year_id', $fiscalYear->id)->first()->budget_realization ?? null;
    }

}; ?>

<section class=" bg-white dark:bg-zinc-800 w-full">
    <flux:text class=" mt-4 -mb-4 mx-auto max-w-screen-xl  font-bold text-2xl dark:text-white text-zinc-900">
        Realisasi Anggaran
    </flux:text>
    <div class="flex gap-8 max-w-screen-xl mx-auto py-10 lg:flex-row flex-col">
        @if($this->realization ?? false)
            <iframe
                src="{{ asset('storage/' . $this->realization) }}"
                width="100%"
                height="800px"
                style="border: none;">
                Browser Anda tidak mendukung tampilan PDF.
                <a href="{{ asset('storage/' . $this->realization) }}">Download PDF</a>
            </iframe>
        @else
            <p class="text-center text-zinc-400">Belum ada realisasi anggaran yang diunggah.</p>
        @endif
    </div>
</section>
