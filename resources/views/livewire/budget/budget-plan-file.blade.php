<?php

use App\Models\BudgetPlanFile;
use Livewire\Volt\Component;
use App\Models\FiscalYear;

new
#[\Livewire\Attributes\Title('Penyusunan Anggaran')]
class extends Component {
    use \Livewire\WithFileUploads;

    #[\Livewire\Attributes\Locked]
    public ?int $fiscalYearId;
    public string $year = '';

    public $budget_plan = null;
    public $currentImage = null;

    public function mount($fiscalYearId): void
    {
        $this->fiscalYearId = $fiscalYearId;
        $this->year = FiscalYear::find($fiscalYearId)->year;
        $plan = BudgetPlanFile::where('fiscal_year_id', $fiscalYearId)->first()->budget_plan ?? null;
        $this->currentImage = $plan;
    }

    public function save(): void
    {
        $validation = $this->validate([
            'budget_plan' => ['required', 'file', 'max:2048']
        ]);
        try {
            if ($this->budget_plan) {
                $budget_plan_file = BudgetPlanFile::where('fiscal_year_id', $this->fiscalYearId)->first();
                if($budget_plan_file) {
                    if ($this->currentImage) {
                        Storage::delete($this->currentImage);
                    }
                }
                $budget_plan = $this->budget_plan->store('budget_plan');
                $this->budget_plan = $budget_plan;
                $this->currentImage = $budget_plan;
            } else {
                $budget_plan = $this->currentImage->store('budget_plan');
                $this->budget_plan = $budget_plan;
                $this->currentImage = $budget_plan;
            }
            BudgetPlanFile::updateOrCreate(['fiscal_year_id' => $this->fiscalYearId], [
                'fiscal_year_id' => $this->fiscalYearId,
                'budget_plan' => $this->budget_plan
            ]);
            $this->dispatch('pond-reset');
            $this->dispatch('toast', message: 'Berhasil diperbaharui');
        } catch (Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal diperbaharui : ' . $e->getMessage());
        }

    }

    public function delete(): void
    {
        $this->showConfirmModal = true;
    }

    public function confirmDelete(): void
    {
        $budget_plan = BudgetPlanFile::find(1);
        if ($budget_plan) {
            Storage::delete($budget_plan->budget_plan);
            $budget_plan->budget_plan = null;
            $budget_plan->save();
            $this->dispatch('pond-reset');
            $this->reset(['budget_plan', 'currentImage']);
        }
        $this->showConfirmModal = false;
    }

}; ?>

<div>
    <x-activity.breadcrumb active="Penyusunan / Anggaran Tahun {{ $this->year }}">
        <x-slot name="action">
            <flux:button variant="primary" class="w-[100px]" size="sm" icon="save-all" wire:click="save">Simpan</flux:button>
        </x-slot>
    </x-activity.breadcrumb>
        <x-confirm wire:model.self="showConfirmModal"/>
        <div class="p-6 border border-zinc-200 dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900">
            <flux:heading size="xl" level="1">Penyusunan Anggaran</flux:heading>
            <flux:subheading class="mb-2">Penyusunan Anggaran Kampung Padang Raharja</flux:subheading>
            <div class="mt-4">
                <x-filepond wire:model="budget_plan" allowImagePreview/>
            </div>
            <div class="relative mt-3 rounded-xl overflow-hidden ">
                @if($this->currentImage ?? false)
                    <iframe
                        src="{{ asset('storage/' . $this->currentImage) }}"
                        width="100%"
                        height="600px"
                        style="border: none;">
                        Browser Anda tidak mendukung tampilan PDF.
                        <a href="{{ asset('storage/' . $this->currentImage) }}">Download PDF</a>
                    </iframe>
                    <div class="absolute top-0 right-0 p-1" data-popover-target="popover-left"
                         data-popover-placement="left" wire:click="delete">
                        <flux:tooltip position="bottom">
                            <flux:button variant="ghost" icon="x-mark" size="sm" alt="Close modal"
                                         class="text-zinc-400! hover:text-zinc-800! dark:text-zinc-500! "></flux:button>
                            <flux:tooltip.content class="max-w-[20rem] space-y-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Menghapus Gambar</h3>
                                <p class="">Tindakan ini akan menghapus gambar Penyusunan Anggaran.</p>
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </div>
                @endif
            </div>
        </div>
</div>
