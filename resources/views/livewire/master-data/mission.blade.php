<?php

use App\Models\VillageProfile;
use Livewire\Volt\Component;

new
#[\Livewire\Attributes\Title('Master Data - Misi')]
class extends Component {

    public string $content = '';

    public function mount() {
        $this->content = VillageProfile::where('id', 1)->first()->mission ?? '';
    }

    #[\Livewire\Attributes\On('trix_value_updated')]
    public function visionMissionGoalUpdate($value): void
    {
        $this->content = $value;
    }

    public function save(): void
    {
        try {
            VillageProfile::updateOrCreate(['id' => 1], [
                'mission' => $this->content,
            ]);
            $this->dispatch('toast', message: 'Berhasil diperbaharui');
        } catch (Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal diperbaharui : ' . $e->getMessage());
        }
    }

}; ?>

<x-master-data.sidebar active="Misi">
    <x-slot name="action">
        <flux:button variant="primary" class="w-[100px]" size="sm" icon="save-all" wire:click="save">Simpan</flux:button>
    </x-slot>
    <div class="p-6 border border-zinc-200 dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900">
        <flux:heading size="xl" level="1">Misi</flux:heading>
        <flux:subheading class="mb-2">Misi Kampung Padang Raharja</flux:subheading>
        <div class="mt-4">
            <livewire:editor :value="$this->content" />
        </div>
    </div>
</x-master-data.sidebar>
