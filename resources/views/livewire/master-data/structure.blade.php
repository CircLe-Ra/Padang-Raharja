<?php

use App\Models\VillageProfile;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new
#[\Livewire\Attributes\Title('Master Data - Struktur Organisasi')]
class extends Component {
    use WithFileUploads;

    public $structure;
    public $currentImage;

    public bool $showConfirmModal = false;

    public function mount() {
        $structure = VillageProfile::where('id', 1)->first()->structure;
        $this->currentImage = $structure;
    }

    public function save(): void
    {
        try {
            if ($this->structure) {
                if($this->currentImage){
                    Storage::delete($this->currentImage);
                }
                $structure = $this->structure->store('structure');
                $this->structure = $structure;
                $this->currentImage = $structure;
            }else{
                $structure = $this->currentImage->store('structure');
                $this->structure = $structure;
                $this->currentImage = $structure;
            }
            VillageProfile::updateOrCreate(['id' => 1], [
                'structure' => $this->structure
            ]);
            $this->dispatch('pond-reset');
            $this->dispatch('toast', message: 'Berhasil diperbaharui');
        }catch (Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal diperbaharui : ' . $e->getMessage());
        }

    }

    public function delete(): void
    {
        $this->showConfirmModal = true;
    }

    public function confirmDelete(): void
    {
        $structure = VillageProfile::find(1);
        if ($structure) {
            Storage::delete($structure->structure);
            $structure->structure = null;
            $structure->save();
            $this->dispatch('pond-reset');
            $this->reset(['structure', 'currentImage']);
        }
        $this->showConfirmModal = false;
    }

}; ?>

<x-master-data.sidebar active="Struktur Organisasi">
    <x-slot name="action">
        <flux:button variant="primary" class="w-[100px]" size="sm" icon="save-all" wire:click="save">Simpan</flux:button>
    </x-slot>
    <x-confirm wire:model.self="showConfirmModal" />
    <div class="p-6 border border-zinc-200 dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900">
        <flux:heading size="xl" level="1">Struktur Organisasi</flux:heading>
        <flux:subheading class="mb-2">Struktur Organisasi Kampung Padang Raharja</flux:subheading>
        <div class="mt-4">
           <x-filepond wire:model="structure" allowImagePreview />
        </div>
        <div class="relative mt-3 rounded-xl overflow-hidden ">
            @if($this->currentImage ?? false)
                <img class="object-cover rounded-xl mx-auto" src="{{ asset('storage/' . $this->currentImage) }}" alt="Foto Utama" />
                <div class="absolute top-0 right-0 p-1" data-popover-target="popover-left" data-popover-placement="left" wire:click="delete">
                    <flux:tooltip position="bottom">
                        <flux:button variant="ghost" icon="x-mark" size="sm" alt="Close modal" class="text-zinc-400! hover:text-zinc-800! dark:text-zinc-500! "></flux:button>
                        <flux:tooltip.content class="max-w-[20rem] space-y-2">
                            <h3 class="font-semibold text-gray-900 dark:text-white">Menghapus Gambar</h3>
                            <p class="">Tindakan ini akan menghapus gambar Struktur Organisasi.</p>
                        </flux:tooltip.content>
                    </flux:tooltip>
                </div>
            @endif
        </div>
    </div>
</x-master-data.sidebar>

