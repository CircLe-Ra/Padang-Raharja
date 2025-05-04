<?php

use App\Models\FiscalYear;
use App\Models\PersonalData;
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

    public string $identity_number = '';
    public string $name;
    public string $date_of_birth;
    public string $place_of_birth;
    public string $gender;
    public string $religion;
    public string $marital_status;
    public string $phone_number;
    public string $address;

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->identity_number = auth()->user()->personalData->identity_number ?? '';
        $this->date_of_birth = auth()->user()->personalData->date_of_birth ?? '';
        $this->place_of_birth = auth()->user()->personalData->place_of_birth ?? '';
        $this->gender = auth()->user()->personalData->gender ?? '';
        $this->religion = auth()->user()->personalData->religion ?? '';
        $this->marital_status = auth()->user()->personalData->marital_status ?? '';
        $this->phone_number = auth()->user()->personalData->phone_number ?? '';
        $this->address = auth()->user()->personalData->address ?? '';
    }

    public function store(): void
    {

        try {
        $validated = $this->validate([
            'identity_number' => ['required', 'string', 'max:16'],
            'date_of_birth' => ['required', 'string'],
            'place_of_birth' => ['required', 'string'],
            'gender' => ['required', 'string'],
            'religion' => ['required', 'string'],
            'marital_status' => ['required', 'string'],
            'phone_number' => ['string', 'min:16', 'max:17','required'],
            'address' => ['required', 'string', 'max:255'],
        ]);
            PersonalData::updateOrCreate(['user_id' => auth()->user()->id], $validated);
            $this->dispatch('toast', message: 'Berhasil memperbaharui data diri');
        } catch (Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal memperbaharui');
            dd($e->getMessage());
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
    <x-personal-data.breadcrumb active="Personal Data">
        <x-slot name="action">
            <flux:button variant="primary" class="w-[100px]" size="sm" icon="save-all" wire:click="store">Simpan
            </flux:button>
        </x-slot>
    </x-personal-data.breadcrumb>
    <div class="grid grid-cols-1 gap-2">
        <div class="lg:col-span-1">
            <div
                class="space-y-6 p-6 border border-zinc-200 dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900">
                <div>
                    <flux:heading size="xl" level="1">Data Pribadi</flux:heading>
                    <flux:subheading class="mb-2">Silahkan inputkan data pribadi dibawah ini dengan benar.</flux:text>
                </div>
                <form wire:submit="store" class="space-y-4">
                    <div class="grid grid-cols-4 gap-2">
                        <flux:input
                            label="Nama Lengkap"
                            wire:model="name"
                            disabled
                            required/>
                        <flux:input
                            label="No. KTP"
                            wire:model="identity_number"
                            maxlength="16"
                            mark="9999999999999999"
                            required/>
                        <flux:input
                            label="Tanggal Lahir"
                            wire:model="date_of_birth"
                            type="date"
                            required/>
                        <flux:input
                            label="Tempat Lahir"
                            wire:model="place_of_birth"
                            required/>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <flux:select wire:model="gender" placeholder="Pilih Jenis Kelamin..." label="Jenis Kelamin">
                            <flux:select.option value="M">Laki-laki</flux:select.option>
                            <flux:select.option value="F">Perempuan</flux:select.option>
                        </flux:select>
                        <flux:select wire:model="religion" placeholder="Pilih Agama..." label="Agama">
                            <flux:select.option value="islam">Islam</flux:select.option>
                            <flux:select.option value="kristen">Kristen</flux:select.option>
                            <flux:select.option value="katolik">Katolik</flux:select.option>
                            <flux:select.option value="hindu">Hindu</flux:select.option>
                            <flux:select.option value="budha">Budha</flux:select.option>
                            <flux:select.option value="konghucu">Konghucu</flux:select.option>
                        </flux:select>
                        <flux:select wire:model="marital_status" placeholder="Pilih Status Perkawinan..."
                                     label="Status Perkawinan">
                            <flux:select.option value="married">Menikah</flux:select.option>
                            <flux:select.option value="single">Belum Menikah</flux:select.option>
                        </flux:select>
                        <flux:input
                            label="Nomor Telepon"
                            wire:model="phone_number"
                            type="phone"
                            mask="+99 999-9999-9999"
                            value="+62"
                            required/>
                    </div>
                    <flux:textarea
                        label="Alamat"
                        wire:model="address"
                        required/>
                </form>
            </div>
        </div>
    </div>
</div>
