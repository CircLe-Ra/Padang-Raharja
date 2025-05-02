<?php

use App\Models\Activity;
use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 5;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';

    public ?int $id = null;
    public string $name = '';
    public string $date = '';
    public string $location = '';
    public string $description = '';

    public bool $showConfirmModal = false;

    #[\Livewire\Attributes\Computed]
    public function activities()
    {
        return Activity::where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('date', 'like', '%' . $this->search . '%')
                        ->orWhere('location', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->paginate(5, pageName: 'activity-page');
    }

    public function resetBagAndField(): void
    {
        $this->resetValidation();
        $this->reset(['id', 'name', 'date', 'location', 'description']);
    }

    public function store(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
        ]);

        try {
            Activity::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
                'date' => $this->date,
                'location' => $this->location,
                'description' => $this->description,
            ]);
            unset($this->activities);
            Flux::modal('activity-modal')->close();
            $this->dispatch('toast', message: 'Data berhasil disimpan');
        } catch (Exception $e) {
            Flux::modal('activity-modal')->close();
            $this->dispatch('toast', type: 'error', message: 'Data gagal disimpan');
        }
    }

    public function edit($id): void
    {
        $activity = Activity::find($id);
        $this->id = $activity->id;
        $this->name = $activity->name;
        $this->date = $activity->date;
        $this->location = $activity->location;
        $this->description = $activity->description;
        Flux::modal('activity-modal')->show();
    }

    public function delete($id): void
    {
        $this->showConfirmModal = true;
        $this->id = $id;
    }

    public function confirmDelete(): void
    {
        try {
            Activity::find($this->id)->delete();
            unset($this->activities);
            $this->resetBagAndField();
            $this->dispatch('toast', message: 'Data berhasil dihapus');
        } catch (Exception $e) {
            $this->resetBagAndField();
            $this->dispatch('toast', type: 'error', message: 'Data gagal dihapus');
        }
        $this->showConfirmModal = false;
    }

}; ?>

<div>
    <x-activity.breadcrumb active="Data Kegiatan">
        <x-slot name="action">
            <flux:modal.trigger name="activity-modal">
                <flux:button variant="primary" class="w-[100px]" size="sm" icon="plus">Tambah</flux:button>
            </flux:modal.trigger>
        </x-slot>
    </x-activity.breadcrumb>
    {{-- modal --}}
    <flux:modal name="activity-modal" variant="flyout" @close="resetBagAndField">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl">Kegiatan Kampung</flux:heading>
                <flux:text class="mt-2">Tambah atau Ubah Kegiatan Kampung disini.</flux:text>
            </div>
            <form wire:submit="store" class="space-y-4">
                <flux:input label="Nama Kegiatan" wire:model="name"/>
                <flux:input label="Tanggal Kegiatan" wire:model="date" type="date"/>
                <flux:input label="Lokasi Kegiatan" wire:model="location"/>
                <flux:textarea label="Deskripsi Kegiatan" wire:model="description"/>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    <x-confirm wire:model.self="showConfirmModal" />
    <x-table thead="#, Nama Kegiatan, Tanggal Mulai, Tanggal Selesai" searchable label="Data Kegiatan"
             sub-label="Daftar Kegiatan seluruh kegiatan">
        <x-slot name="filter">
            <x-filter wire:model.live="show"/>
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->activities->count())
            @foreach($this->activities as $activity)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $activity->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $activity->date }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $activity->location }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" size="xs" icon="pencil" wire:click="edit({{ $activity->id }})"/>
                            <flux:button variant="danger" size="xs" icon="trash" wire:click="delete({{ $activity->id }})"/>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="5" class="px-6 py-4 text-center">
                    Data tidak ditemukan
                </td>
            </tr>
        @endif
    </x-table>
    {{ $this->activities->links('components.pagination') }}
</div>
