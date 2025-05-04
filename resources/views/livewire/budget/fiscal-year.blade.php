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
    public string $year = '';
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

    #[\Livewire\Attributes\Computed]
    public function initializeStatuses(): void
    {
        $this->statuses = [];
        foreach (FiscalYear::all() as $fy) {
            $this->statuses[$fy->id] = $fy->status ? '1' : '0';
        }
    }

    public function resetField(): void
    {
        $this->reset('year');
        $this->resetValidation();
    }

    public function store(): void
    {
        $this->validate([
            'year' => ['required', 'string', 'size:4', 'unique:fiscal_years,year']
        ]);

        try {
            FiscalYear::create(['year' => $this->year]);
            unset($this->fiscalYears);
            $this->initializeStatuses();
            $this->resetField();
            $this->dispatch('toast', message: 'Tahun anggaran berhasil ditambahkan');
        } catch (Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menambahkan tahun anggaran');
        }
    }

    public function changeStatus($id, $status): void
    {
        $status = (bool)$status;
        try {
            if ($status) {
                FiscalYear::query()->update(['status' => false]);
            }
            $fiscalYear = FiscalYear::find($id);
            $fiscalYear->update(['status' => $status]);
            unset($this->fiscalYears);
            $this->initializeStatuses();
            $this->dispatch('toast', message: 'Tahun anggaran ' . $fiscalYear->year . ' berhasil diaktifkan');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal mengubah status');
        }
    }
    public function delete($id): void
    {
        $this->showConfirmModal = true;
        $this->id = $id;
    }

    public function confirmDelete(): void
    {
        try {
            FiscalYear::find($this->id)->delete();
            $this->dispatch('toast', message: 'Tahun anggaran berhasil dihapus');
        } catch (Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menghapus tahun anggaran');
        }
        $this->showConfirmModal = false;
    }
}; ?>

<div>
    <x-activity.breadcrumb active="Penyusunan / Tahun Anggaran" />
    <div class="grid grid-cols-1 gap-2 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="space-y-6 p-6 border border-zinc-200 dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                <div>
                    <flux:heading size="xl" level="1">Tambah Tahun Anggaran</flux:heading>
                    <flux:subheading class="mb-2">Masukkan tahun anggaran baru disini.</flux:text>
                </div>
                <form wire:submit="store" class="space-y-4">
                    <flux:input
                        label="Tahun Anggaran"
                        wire:model="year"
                        placeholder="Contoh: 2024"
                        maxlength="4"
                        required />

                    <div class="flex">
                        <flux:spacer />
                        <flux:button type="submit" variant="primary">Simpan</flux:button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <x-table thead="#, Tahun, Status" searchable label="Daftar Tahun Anggaran"
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
                                @if(isset($statuses[$fy->id]))
                                    <flux:select
                                        class="w-full max-w-[150px]"
                                        wire:model.live="statuses.{{ $fy->id }}"
                                        size="sm"
                                        @change="$wire.changeStatus({{ $fy->id }}, $event.target.value)">
                                        <flux:select.option value="0">Non-Aktif</flux:select.option>
                                        <flux:select.option value="1">Aktif</flux:select.option>
                                    </flux:select>
                                @else
                                    Loading...
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <flux:tooltip position="left" content="Unggah Data Anggaran">
                                        <flux:button
                                            href="{{ route('budget.plan.budget-plan', $fy->id) }}"
                                            variant="primary"
                                            size="xs"
                                            icon="pencil"
                                            wire:navigate />
                                    </flux:tooltip>
                                    <flux:tooltip position="right" content="Hapus">
                                        <flux:button
                                            variant="danger"
                                            size="xs"
                                            icon="trash"
                                            wire:click="delete({{ $fy->id }})" />
                                    </flux:tooltip>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center">
                            Data tidak ditemukan
                        </td>
                    </tr>
                @endif
            </x-table>
            {{ $this->fiscalYears->links('components.pagination') }}
        </div>
    <x-confirm wire:model.self="showConfirmModal" />
    </div>
</div>
