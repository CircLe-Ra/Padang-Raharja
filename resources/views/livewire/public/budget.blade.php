<?php

use App\Models\BudgetPlanFile;
use Livewire\Volt\Component;
use App\Models\FiscalYear;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Penyusunan Anggaran')]
class extends Component {
    public $budgetPlan;

    public function mount(): void
    {
        $fiscalYear = FiscalYear::where('status', true)->first();
        $this->budgetPlan = BudgetPlanFile::where('fiscal_year_id', $fiscalYear->id)->first()->budget_plan ?? null;
    }

}; ?>

<section class=" bg-white dark:bg-zinc-800 w-full px-4">
    <flux:text class=" px-8 mt-4 -mb-4 mx-auto max-w-screen-xl  font-bold text-2xl dark:text-white text-zinc-900">
        Penyusunan Anggaran
    </flux:text>
    <div class="flex gap-8 max-w-screen-xl mx-auto px-8 py-10 lg:flex-row flex-col">
        @if($this->budgetPlan ?? false)
            <iframe
                src="{{ asset('storage/' . $this->budgetPlan) }}"
                width="100%"
                height="800px"
                style="border: none;">
                Browser Anda tidak mendukung tampilan PDF.
                <a href="{{ asset('storage/' . $this->budgetPlan) }}">Download PDF</a>
            </iframe>
        @else
            <p class="text-center text-zinc-400">Belum ada penyusunan anggaran yang diunggah.</p>
        @endif
    </div>
</section>
