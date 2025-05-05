<?php

use App\Models\Aspiration;
use App\Models\PersonalData;
use Livewire\Volt\Component;

new
#[\Livewire\Attributes\Title('Dashboard')]
class extends Component {

    public $selfAspiration;
    public $allAspirationPublic;
    public bool $personalDataStatus;
    public bool $showImageModal = false;
    public string $imageSrc = '';

    public function mount(): void
    {
        $this->personalDataStatus = PersonalData::where('user_id', auth()->user()->id)->count() ?? false;
        $this->selfAspiration = Aspiration::where('user_id', auth()->user()->id)->latest()->first() ?? '';
        $this->allAspirationPublic = Aspiration::with('images')->publicExcludeMe()->get();
    }

    public function showImage($src){
        $this->imageSrc = $src;
        $this->showImageModal = true;
    }


}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-2 rounded-xl">
    <x-personal-data.breadcrumb active="Tentang Aspirasi Anda"/>
    @if(!$this->personalDataStatus)
        <div
            class="flex items-center p-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-zinc-800 dark:text-yellow-300"
            role="alert">
            <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="currentColor" viewBox="0 0 20 20">
                <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">Informasi Penting!</span> Anda belum melengkapi data diri. Silahkan <a
                    href="{{ route('personal-data') }}" wire:navigate class="font-medium text-blue-900 hover:underline">lengkapi
                    data diri</a> sebelum melakukan pengajuan aspirasi.
            </div>
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-2 ">
        <div class="flex justify-between items-center my-2">
            <flux:heading size="xl">Aspirasi Terakhir Yang Anda Ajukan</flux:heading>
        </div>
        <div class="bg-zinc-50 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            @if($this->selfAspiration)
                <div class="p-6 flex justify-between gap-4">
                    <div class="w-1/2">
                        <flux:heading size="xl" level="1">{{ $this->selfAspiration->title }}</flux:heading>
                        <div class="flex flex-col gap-2 mt-2">
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Pelapor</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{ $this->selfAspiration->user->name }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Nomor Telepon</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{ $this->selfAspiration->user->personalData->phone_number }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Kategori Aspirasi</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{ $this->selfAspiration->category }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Lokasi</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{ $this->selfAspiration->location }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Aspirasi</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{ $this->selfAspiration->description }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Terpublikasi?</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{ $this->selfAspiration->is_public ? 'Ya' : 'Tidak' }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-40 pr-2">Status Pengajuan</div>
                                <div class="px-1">:</div>
                                <div class="flex-1">{{
                                        $this->selfAspiration->status == 'pending' ? 'Menunggu' : (
                                             $this->selfAspiration->status == 'process' ? 'Di Proses' : 'Telah direspon'
                                        )}}</div>
                            </div>
                            <div class="flex items-start mt-6">
                                <flux:button variant="primary" size="sm" href="#">Lihat Selengkapnya</flux:button>
                            </div>
                        </div>

                    </div>
                    <div class="w-1/2 grid grid-cols-2 gap-4">
                        @if($this->selfAspiration->images->count() > 3)
                            @foreach($this->selfAspiration->images->take(3) as $aspirationImage)
                                <div class="size-48 content-center">
                                    <img wire:click="showImage('{{ $aspirationImage->image }}')" src="{{ asset('storage/' . $aspirationImage->image) }}" alt="image {{ $loop->iteration }}">
                                </div>
                            @endforeach
                                <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl content-center text-center">
                                    <a href="#" class="hover:underline">Lihat lainnya</a>
                                </div>
                        @else
                            <div class="size-48">
                                <img src="{{ asset('storage/' . $aspirationImage->image) }}" alt="image {{ $loop->iteration }}">
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="relative aspect-video content-center">
                    <x-placeholder-pattern class="absolute size-full stroke-zinc-900/20 dark:stroke-zinc-100/20"/>
                    <flux:text
                        class="h-96 absolute top-35 left-1/2 -translate-x-1/2 -translate-y-1/2 text-zinc-900 dark:text-zinc-100 text-center">
                        Belum ada aspirasi anda.
                    </flux:text>
                </div>
            @endif
        </div>
    </div>
    <div class="flex flex-1 flex-col gap-2 ">
        <div class="flex justify-between items-center my-2">
            <flux:heading size="xl">Daftar Seluruh Aspirasi</flux:heading>
        </div>
        <div class="bg-zinc-50 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900">
            @if($this->allAspirationPublic)
                @foreach($this->allAspirationPublic as $allAspiration)
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
                                        <td class="py-4" >
                                            <flux:button variant="primary" size="sm" href="#">Lihat Selengkapnya</flux:button>
                                        </td>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="relative aspect-video content-center">
                    <x-placeholder-pattern class="absolute size-full stroke-zinc-900/20 dark:stroke-zinc-100/20"/>
                    <flux:text
                        class="h-96 absolute top-35 left-1/2 -translate-x-1/2 -translate-y-1/2 text-zinc-900 dark:text-zinc-100 text-center">
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
            <img src="{{ asset('storage/' . $this->imageSrc) }}" alt="image" />
        </div>
    </flux:modal>
</div>

