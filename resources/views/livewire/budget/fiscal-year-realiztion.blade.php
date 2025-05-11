<?php

use App\Models\FiscalYear;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Title('Tahun Anggaran')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 5;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';
    public bool $showConfirmModal = false;
    public array $statuses = [];

    public function mount(): void
    {
        foreach (FiscalYear::all() as $fy) {
            $this->statuses[$fy->id] = $fy->status ? '1' : '0';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function fiscalYears()
    {
        return FiscalYear::where('year', 'like', '%' . $this->search . '%')
            ->orderBy('year', 'desc')
            ->paginate((int)$this->show, pageName: 'fiscal-year-page');
    }

}; ?>

<div>
    <x-activity.breadcrumb active="Realisasi / Tahun Anggaran" />
    <x-table thead="#, Tahun, Tahun Aktif, Total Perencanaan, Total Realisasi" searchable label="Daftar Tahun Anggaran"
             sub-label="Tahun-tahun anggaran yang telah didaftarkan">
        <x-slot name="filter">
            <x-filter wire:model.live="show" />
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->fiscalYears->count())
            @foreach($this->fiscalYears as $fy)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $fy->year }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $this->statuses[$fy->id] ? 'Aktif' : 'Non-Aktif' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $fy->budgetPlans->count() ?? 0 }} Perencanaan
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:tooltip position="right" content="Unggah Data Realisasi">
                                <flux:button
                                    href="{{ route('budget.realization.data', $fy->id) }}"
                                    variant="primary"
                                    size="xs"
                                    icon="pencil"
                                    wire:navigate />
                            </flux:tooltip>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" class="px-6 py-4 text-center">
                    Data tidak ditemukan
                </td>
            </tr>
        @endif
    </x-table>
    {{ $this->fiscalYears->links('components.pagination') }}
</div>
