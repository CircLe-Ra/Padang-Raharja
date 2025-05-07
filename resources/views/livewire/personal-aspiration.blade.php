<?php

use App\Models\Aspiration;
use App\Models\PersonalData;
use Livewire\Volt\Component;

new class extends Component {

    public $allSelfAspiration;
    public $personalDataStatus;
    public bool $showImageModal = false;
    public string $imageSrc = '';

    public function mount(): void
    {
        $this->personalDataStatus = PersonalData::where('user_id', auth()->user()->id)->count() ?? 0;
        $this->allSelfAspiration = Aspiration::where('user_id', auth()->user()->id)->latest()->get() ?? '';
    }

    public function showImage($src)
    {
        $this->imageSrc = $src;
        $this->showImageModal = true;
    }


}; ?>

<div>
    <x-personal-data.breadcrumb active="Aspirasi Saya"/>
    <livewire:public.aspiration/>
    <div>
        <div class="flex flex-1 flex-col gap-2 ">
            <div class="flex justify-between items-center my-2">
                <flux:heading size="xl">Daftar Seluruh Aspirasi Anda</flux:heading>
            </div>
            <div
                class="bg-zinc-50 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
                @if($this->personalDataStatus > 0)
                    @foreach($this->allSelfAspiration as $allAspiration)
                        <div class="p-6 flex justify-between gap-4">
                            <div class="">
                                <flux:heading size="xl" level="1">{{ $allAspiration->title }}</flux:heading>
                                <div class="px-4 bg-zinc-200 dark:bg-zinc-600">
                                    <table class="w-full mt-2  ">
                                        <tr>
                                            <td class="w-40 py-1 pr-2">Pelapor</td>
                                            <td class="px-1 py-1">:</td>
                                            <td class="w-40 py-1">{{ $allAspiration->user->name ?? $allAspiration->name }}</td>
                                            <td class="w-40 py-1 pr-2">Nomor Telepon</td>
                                            <td class="px-1 py-1">:</td>
                                            <td class="w-40 py-1">{{ $allAspiration->user->personalData->phone_number ?? $allAspiration->contact }}</td>
                                            <td class="w-40 py-1 pr-2">Kategori Aspirasi</td>
                                            <td class="px-1 py-1">:</td>
                                            <td class="w-40 py-1">{{ $allAspiration->category }}</td>
                                            <td class="py-4">
                                                <flux:button variant="primary" size="sm"
                                                             href="{{ route('portal.aspiration-detail', $allAspiration->ticket) }}">
                                                    Lihat Selengkapnya
                                                </flux:button>
                                            </td>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="relative h-48 content-center">
                        <flux:text
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-zinc-900 dark:text-zinc-100 text-center">
                            Belum ada aspirasi anda.
                        </flux:text>
                    </div>
                @endif
            </div>
        </div>
        <flux:modal wire:model="showImageModal" @close="$wire.imageSrc = null;">
            <div>
                <flux:heading size="lg">Gambar Aspirasi</flux:heading>
            </div>
            <div class="flex justify-center p-6  content-center">
                <img src="{{ asset('storage/' . $this->imageSrc) }}" alt="image"/>
            </div>
        </flux:modal>
    </div>
</div>
