<?php

use App\Models\Activity;
use App\Models\VillageProfile;
use Livewire\Volt\Component;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Beranda')]
class extends Component {
    public $oneEvent;
    public $manyEvent;
    public $profile;

    public function mount(): void
    {
        $this->oneEvent = Activity::latest()->first();
        $this->manyEvent = Activity::latest()->skip(1)->take(3)->get();
        $this->profile = VillageProfile::find(1);
    }
}; ?>

<div class="">
    <section class="bg-white dark:bg-zinc-800 w-full">
        <div class="flex justify-between items-center mt-3 -mb-4 max-w-screen-xl mx-auto">
            <flux:text class=" font-bold text-2xl dark:text-white text-zinc-900 ">
                Kegiatan Kampung
            </flux:text>
            <flux:button icon:trailing="arrow-long-right" variant="ghost" href="{{ route('portal.activity') }}" wire:navigate>Kegiatan Lain </flux:button>
        </div>
        <div
            class="grid py-10 mx-auto max-w-screen-xl grid-cols-1 {{ $this->manyEvent->count() >= 1 ? 'lg:grid-cols-2' : 'lg:grid-cols-1' }} gap-6">
            <div class="">
                <div class=" overflow-hidden max-w-full h-96 rounded-xl">
                    @if ($this->oneEvent->image ?? false)
                        <img class="object-cover rounded-xl " src="{{ asset('storage/' . $this->oneEvent->image) }}"
                             alt="News Image"/>
                    @endif
                </div>
                @if($this->manyEvent->count() >= 1)
                    <div class="-mt-6">
                        <a href="{{ route('activity-read', ['id' => $this->oneEvent->id]) }}" wire:navigate
                           class="hover:underline text-2xl font-bold text-zinc-900 dark:text-white text-justify"> {!! Str::of($this->oneEvent->name ?? '')->limit(60) !!}</a>
                        <div class="text-zinc-700 dark:text-zinc-400 text-justify text-lg">
                            {{ str()->limit(str_replace('&nbsp;', ' ', strip_tags($this->oneEvent->description ?? '')),100,'...') }}
                        </div>
                        <div class="pt-4">
                            <a href="{{ route('activity-read', ['id' => $this->oneEvent->id]) }}" wire:navigate
                               class="text-blue-600 dark:text-blue-500 hover:underline text-lg font-semibold ">Baca
                                Selengkapnya
                                <svg aria-hidden="true" class="w-4 h-4 -mt-1 ml-2 inline" fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                @foreach($this->manyEvent as $event)
                    <div class="mb-8">
                        <a href="{{ route('activity-read', ['id' => $event->id]) }}" wire:navigate
                           class="hover:underline text-2xl font-bold text-zinc-900 dark:text-white text-justify">{{ str()->limit(str_replace('&nbsp;', ' ', strip_tags($event->name ?? '')),45,'...') }}</a>
                        <div class="text-zinc-700 dark:text-zinc-400 text-justify text-lg py-4">
                            {{ str()->limit(str_replace('&nbsp;', ' ', strip_tags($event->description ?? '')),100,'...') }}
                        </div>
                        <div class="">
                            <a href="{{ route('activity-read', ['id' => $event->id]) }}" wire:navigate
                               class="text-blue-600 dark:text-blue-500 hover:underline text-lg font-semibold ">Baca
                                Selengkapnya
                                <svg aria-hidden="true" class="w-4 h-4 -mt-1 ml-2 inline" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <section class="bg-zinc-100 dark:bg-zinc-900 w-full">
        <flux:text class="p-8 mx-auto max-w-screen-xl -mb-4 font-bold text-2xl dark:text-white text-zinc-900">
            Sejarah
        </flux:text>
        <div class="grid px-8 pb-8 mx-auto max-w-screen-xl grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="text-justify">
                {!! $this->profile->history ?? '' !!}
            </div>
            <div class="mt-2">
                <img class="object-cover rounded-xl w-full" src="{{ asset('img/sejarah.webp') }}" alt="News Image"/>
                <p class="text-center mt-2">Sumber : Balai Desa Padang Raharja</p>
            </div>
        </div>
    </section>
    <section class="bg-white dark:bg-zinc-800 w-full">
        <flux:text class="p-8 mx-auto max-w-screen-xl -mb-4 font-bold text-2xl dark:text-white text-zinc-900">
            Geografis & Demografi
        </flux:text>
        <div class="grid px-8 pb-8 mx-auto max-w-screen-xl grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="text-justify">
                {!! $this->profile->geography_demographics ?? '' !!}
            </div>
            <div class="mt-2">
                <img class="object-cover rounded-xl w-full" src="{{ asset('img/geodem.png') }}" alt="News Image"/>
                <p class="text-center mt-2">Sumber : Google Maps</p>
            </div>
        </div>
    </section>
    <section class="bg-zinc-100 dark:bg-zinc-900 w-full">
        <flux:text class="p-8 mx-auto max-w-screen-xl -mb-4 font-bold text-2xl dark:text-white text-zinc-900">
            Visi & Misi
        </flux:text>
        <div class="grid px-8 pb-8 mx-auto max-w-screen-xl grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="text-justify">
                {!! $this->profile->vision ?? '' !!}
            </div>
            <div class="text-justify">
                {!! $this->profile->mission ?? '' !!}
            </div>
        </div>
    </section>
    <section class="bg-white dark:bg-zinc-800 w-full">
        <flux:text class="p-8 mx-auto max-w-screen-xl -mb-4 font-bold text-2xl dark:text-white text-zinc-900">
            Struktur Organisasi
        </flux:text>
        <div class="grid px-8 mx-auto max-w-screen-xl pb-8">
            @if($this->profile->structure)
                <img class="object-cover rounded-xl w-full" src="{{  $this->profile->structure ? asset('storage/' . $this->profile->structure) : '' }}" alt="News Image"/>
                <p class="text-center my-2">Sumber : Balai Kampung Padang Raharja</p>
            @else
                Belum ada Struktur Organisasi yang diunggah
            @endif
        </div>
    </section>
</div>
