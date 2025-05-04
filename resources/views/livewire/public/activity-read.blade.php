<?php

use App\Models\Activity;
use Livewire\Volt\Component;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Kegiatan Kampung')]
class extends Component {
    public $activity;

    public function mount($id): void
    {
        $this->activity = Activity::find($id);
    }
}; ?>

<section class=" bg-white dark:bg-zinc-800 w-full px-4">
    <div class="flex gap-8 max-w-screen-xl mx-auto lg:flex-row flex-col">
        <div class="w-full  max-w-3xl mx-auto">
            @if($this->activity->image)
                <div class="h-96 overflow-hidden rounded-xl my-5    ">
                    <img src="{{ asset('storage/' . $this->activity->image) }}" class="object-cover" alt="News Image">
                </div>
            @endif
            <h1 class="text-2xl  font-bold  text-zinc-900  dark:text-white mb-2">
                {{ $this->activity->name }}
            </h1>

            <ul class="max-w-md space-y-1 text-zinc-500 list-disc list-inside dark:text-zinc-400">
                <li class="flex items-center gap-2">
                    <span class="text-lg">Tanggal</span>
                    <span class="text-lg">:</span>
                    <span class="text-lg">{{ \Carbon\Carbon::parse($this->activity->date)->format('d / m / Y') }}</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="text-lg">Lokasi Kegiatan</span>
                    <span class="text-lg">:</span>
                    <span class="text-lg">{{ $this->activity->location }}</span>
                </li>
            </ul>

                <div class="text-zinc-700 dark:text-zinc-400 text-justify text-lg py-4">
                {!! $this->activity->description !!}
            </div>
        </div>
    </div>
</section>
