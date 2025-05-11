<?php

use App\Models\AccountCode;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Title('Master Data - Kode Rekening')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 5;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';
    public bool $showConfirmModal = false;

    public ?int $id = null;
    public string $code;
    public string $name;

    #[\Livewire\Attributes\Computed]
    public function accountCodes()
    {
        return AccountCode::where('code', 'like', '%' . $this->search . '%')->orWhere('name', 'like', '%' . $this->search . '%')->paginate((int)$this->show, pageName: 'account-code-page');
    }

    public function resetBagAndField(): void
    {
        $this->resetValidation();
        $this->reset(['id', 'code', 'name']);
    }

    public function store(): void
    {
        $this->validate([
            'code' => ['required', 'string', 'max:50'], //, Rule::unique(AccountCode::class)->ignore($this->id ?? null)
            'name' => ['required', 'string', 'max:150'],
        ]);

        try {
            AccountCode::updateOrCreate(['id' => $this->id], [
                'code' => $this->code,
                'name' => $this->name,
            ]);
            unset($this->accountCodes);
            Flux::modal('account-code-modal')->close();
            $this->dispatch('toast', message: 'Data berhasil disimpan');
        } catch (Exception $e) {
            Flux::modal('account-code-modal')->close();
            $this->dispatch('toast', type: 'error', message: 'Data gagal disimpan');
        }
    }

    public function edit($id): void
    {
        $accountCode = AccountCode::find($id);
        $this->id = $accountCode->id;
        $this->code = $accountCode->code;
        $this->name = $accountCode->name;
        Flux::modal('account-code-modal')->show();
    }

    public function delete($id): void
    {
        $this->showConfirmModal = true;
        $this->id = $id;
    }

    public function confirmDelete(): void
    {
        try {
            AccountCode::find($this->id)->delete();
            $this->resetBagAndField();
            unset($this->accountCodes);
            $this->dispatch('toast', message: 'Data berhasil dihapus');
        } catch (Exception $e) {
            $this->resetBagAndField();
            $this->dispatch('toast', type: 'error', message: 'Data gagal dihapus ' . $e->getMessage());
        }
        $this->showConfirmModal = false;
    }

}; ?>

<x-master-data.sidebar active="Kode Rekening">
    <x-slot name="action">
        <flux:modal.trigger name="account-code-modal">
            <flux:button variant="primary" class="w-[100px]" size="sm" icon="plus">Tambah</flux:button>
        </flux:modal.trigger>
    </x-slot>
    {{-- modal --}}
    <flux:modal name="account-code-modal" class="md:w-96" @close="resetBagAndField">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Kode Rekening Anggaran</flux:heading>
                <flux:text class="mt-2">Tambah atau Ubah Kode Rekening Anggaran disini.</flux:text>
            </div>
            <form wire:submit="store" class="space-y-4">
                <flux:input label="Kode Rekening" wire:model="code" autocomplete="off"/>
                <flux:input label="Nama Rekening" wire:model="name" autocomplete="off"/>
                <div class="flex ">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    <x-confirm wire:model.self="showConfirmModal" />
    {{-- table --}}
    <x-table thead="#, Kode Rekening, Nama Rekening" searchable label="Kode Rekening Anggaran"
             sub-label="Daftar Kode Rekening Anggaran yang telah didaftarkan">
        <x-slot name="filter">
            <x-filter wire:model.live="show" />
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->accountCodes->count())
            @foreach($this->accountCodes as $accountCode)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $accountCode->code }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $accountCode->name }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" size="xs" icon="pencil" wire:click="edit({{ $accountCode->id }})"/>
                            <flux:button variant="danger" size="xs" icon="trash" wire:click="delete({{ $accountCode->id }})"/>
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
    {{ $this->accountCodes->links('components.pagination') }}
</x-master-data.sidebar>
