<?php

use App\Models\FundingSource;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Title('Master Data - Sumber Dana')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 5;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';
    public bool $showConfirmModal = false;

    public ?int $id = null;
    public string $name;

    #[\Livewire\Attributes\Computed]
    public function fundingSources()
    {
        return FundingSource::where('name', 'like', '%' . $this->search . '%')->paginate((int)$this->show, pageName: 'funding-source-page');
    }

    public function resetBagAndField(): void
    {
        $this->resetValidation();
        $this->reset(['id', 'name']);
    }

    public function store(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique(FundingSource::class)->ignore($this->id ?? null)],
        ]);

        try {
            FundingSource::updateOrCreate(['id' => $this->id], [
                'name' => $this->name,
            ]);
            Flux::modal('funding-source-modal')->close();
            $this->dispatch('toast', message: 'Data berhasil disimpan');
        } catch (Exception $e) {
            Flux::modal('funding-source-modal')->close();
            $this->dispatch('toast', type: 'error', message: 'Data gagal disimpan');
        }
    }

    public function edit($id): void
    {
        $fundingSource = FundingSource::find($id);
        $this->id = $fundingSource->id;
        $this->name = $fundingSource->name;
        Flux::modal('funding-source-modal')->show();
    }

    public function delete($id): void
    {
        $this->showConfirmModal = true;
        $this->id = $id;
    }

    public function confirmDelete(): void
    {
        try {
            FundingSource::find($this->id)->delete();
            $this->resetBagAndField();
            $this->dispatch('toast', message: 'Data berhasil dihapus');
        } catch (Exception $e) {
            $this->resetBagAndField();
            $this->dispatch('toast', type: 'error', message: 'Data gagal dihapus ' . $e->getMessage());
        }
        $this->showConfirmModal = false;
    }

}; ?>

<x-master-data.sidebar active="Sumber Dana">
    <x-slot name="action">
        <flux:modal.trigger name="funding-source-modal">
            <flux:button variant="primary" class="w-[100px]" size="sm" icon="plus">Tambah</flux:button>
        </flux:modal.trigger>
    </x-slot>
    {{-- modal --}}
    <flux:modal name="funding-source-modal" class="md:w-96" @close="resetBagAndField">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Sumber Dana</flux:heading>
                <flux:text class="mt-2">Tambah atau Ubah Sumber Dana disini.</flux:text>
            </div>
            <form wire:submit="store" class="space-y-4">
                <flux:input label="Nama Sumber Dana" wire:model="name" autocomplete="off"/>
                <div class="flex ">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    <x-confirm wire:model.self="showConfirmModal" />
    {{-- table --}}
    <x-table thead="#, Nama Sumber Dana" searchable label="Sumber Dana"
             sub-label="Daftar Sumber Dana yang telah didaftarkan">
        <x-slot name="filter">
            <x-filter wire:model.live="show" />
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->fundingSources->count())
            @foreach($this->fundingSources as $fundingSource)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $fundingSource->name }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" size="xs" icon="pencil" wire:click="edit({{ $fundingSource->id }})"/>
                            <flux:button variant="danger" size="xs" icon="trash" wire:click="delete({{ $fundingSource->id }})"/>
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
    {{ $this->fundingSources->links('components.pagination') }}
</x-master-data.sidebar>

