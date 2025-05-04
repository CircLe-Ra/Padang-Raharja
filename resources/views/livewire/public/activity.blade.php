<?php

use App\Models\Activity;
use Livewire\Volt\Component;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Kegiatan Kampung')]
class extends Component {
    use \Livewire\WithPagination;
    public $search;

    #[\Livewire\Attributes\Computed]
    public function activities()
    {
        return Activity::where('name', 'like', '%' . $this->search . '%')->latest()->paginate(5, pageName: 'pertal-activity-page');
    }
}; ?>

<div>
    <section class=" bg-white dark:bg-zinc-800 w-full px-4">
        <flux:text class=" px-8 mt-4 -mb-4 mx-auto max-w-screen-xl  font-bold text-2xl dark:text-white text-zinc-900">
            Kegiatan Kampung
        </flux:text>
        <div class="flex gap-8 max-w-screen-xl mx-auto py-10 px-8 lg:flex-row flex-col">
            <div class="flex flex-col gap-8 {{ $this->activities->count() > 0 ? '' : 'w-full' }}">
                @if($this->activities->count() > 0)
                    @foreach($this->activities as $activity)
                        <div class="flex gap-4 items-center">
                            <div class="rounded-xl overflow-hidden max-w-64 max-h-48 ">
                                @if ($activity->image ?? false)
                                    <img class="object-cover rounded-xl" src="{{ asset('storage/' . $activity->image) }}" alt="News Image"/>
                                @endif
                            </div>
                            <div class="">
                                <a wire:navigate href="{{ route('activity-read', ['id' => $activity->id]) }}"
                                   class="hover:underline text-2xl font-bold text-zinc-900 dark:text-white text-justify">{{ str()->limit(str_replace('&nbsp;', ' ', strip_tags($activity->name ?? '')),40,'...') }}</a>
                                <div class="text-zinc-700 dark:text-zinc-400 text-justify text-lg">
                                    {{ str()->limit(str_replace('&nbsp;', ' ', strip_tags($activity->description ?? '')),100,'...') }}
                                </div>
                                <div class="pt-4 flex justify-end">
                                    <a wire:navigate href="{{ route('activity-read', ['id' => $activity->id]) }}"
                                       class="text-blue-600 dark:text-blue-500 hover:underline text-lg font-semibold ">Baca
                                        Selengkapnya
                                        <svg aria-hidden="true" class="w-4 h-4 -mt-1 ml-2 inline" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-zinc-500 dark:text-zinc-400 text-lg text-center">
                        Berita Tidak Ditemukan.
                    </div>
                @endif
            </div>
            <div class="lg:w-2/5 w-full lg:order-last -order-1 ">
                <div
                    class="bg-zinc-50 rounded-xl dark:bg-zinc-800 dark:border-zinc-700 border border-zinc-200 shadow-xl p-4 sticky top-24">
                    <form class="flex items-center max-w-sm mx-auto">
                        <label for="simple-search" class="sr-only">Cari</label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-zinc-500 dark:text-zinc-400" xmlns="http://www.w3.org/2000/svg"
                                     width="24" height="24" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                          d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8zM6 4h7l5 5v8.58l-1.84-1.84a4.99 4.99 0 0 0-.64-6.28A4.96 4.96 0 0 0 12 8a5 5 0 0 0-3.53 1.46a4.98 4.98 0 0 0 0 7.05a4.98 4.98 0 0 0 6.28.63L17.6 20H6zm8.11 11.1c-.56.56-1.31.88-2.11.88s-1.55-.31-2.11-.88c-.56-.56-.88-1.31-.88-2.11s.31-1.55.88-2.11c.56-.57 1.31-.88 2.11-.88s1.55.31 2.11.88c.56.56.88 1.31.88 2.11s-.31 1.55-.88 2.11"/>
                                </svg>
                            </div>
                            <input type="text" wire:model.live="search" id="simple-search"
                                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-zinc-700 dark:border-zinc-600 dark:placeholder-zinc-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   placeholder="Cari Bedasarkan Judul..." required/>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
    @if($this->activities->count() > 5)
        <section
            class="bg-zinc-100 dark:bg-zinc-900 border-t border-b w-full border-zinc-200 dark:border-zinc-700 px-4">
            <div class="mx-auto max-w-screen-xl py-6 flex flex-nowrap items-center justify-center">
                {{ $this->activities->links('livewire.pagination-portal') }}
            </div>
        </section>
    @endif
</div>
