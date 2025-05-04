<?php

use App\Models\BudgetRealization;
use Livewire\Volt\Component;
use App\Models\FiscalYear;

new
#[\Livewire\Attributes\Title('Realisasi Anggaran')]
class extends Component {
    use \Livewire\WithFileUploads;

    #[\Livewire\Attributes\Locked]
    public ?int $fiscalYearId;
    public string $year = '';

    public $realization = null;
    public $currentImage = null;

    public function mount($fiscalYearId): void
    {
        $this->fiscalYearId = $fiscalYearId;
        $this->year = FiscalYear::find($fiscalYearId)->year;
        $realization = BudgetRealization::where('fiscal_year_id', $fiscalYearId)->first()->budget_realization ?? null;
        $this->currentImage = $realization;
    }

    public function save(): void
    {
        $validation = $this->validate([
            'realization' => ['required', 'file', 'max:2048']
        ]);
        try {
            if ($this->realization) {
                $realization = BudgetRealization::where('fiscal_year_id', $this->fiscalYearId)->first();
                if($realization) {
                    if ($this->currentImage) {
                        Storage::delete($this->currentImage);
                    }
                }
                $realization = $this->realization->store('realization');
                $this->realization = $realization;
                $this->currentImage = $realization;
            } else {
                $realization = $this->currentImage->store('realization');
                $this->realization = $realization;
                $this->currentImage = $realization;
            }
            BudgetRealization::updateOrCreate(['fiscal_year_id' => $this->fiscalYearId], [
                'fiscal_year_id' => $this->fiscalYearId,
                'budget_realization' => $this->realization
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
        $realization = BudgetRealization::find(1);
        if ($realization) {
            Storage::delete($realization->realization);
            $realization->realization = null;
            $realization->save();
            $this->dispatch('pond-reset');
            $this->reset(['realization', 'currentImage']);
        }
        $this->showConfirmModal = false;
    }

}; ?>

<div>
    <x-activity.breadcrumb active="Realisasi / Anggaran Tahun {{ $this->year }}">
        <x-slot name="action">
            <flux:button variant="primary" class="w-[100px]" size="sm" icon="save-all" wire:click="save">Simpan</flux:button>
        </x-slot>
    </x-activity.breadcrumb>
        <x-confirm wire:model.self="showConfirmModal"/>
        <div class="p-6 border border-zinc-200 dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900">
            <flux:heading size="xl" level="1">Realisasi Anggaran</flux:heading>
            <flux:subheading class="mb-2">Realisasi Anggaran Kampung Padang Raharja</flux:subheading>
            <div class="mt-4">
                <x-filepond wire:model="realization" allowImagePreview/>
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
                @endif
            </div>
        </div>
</div>
