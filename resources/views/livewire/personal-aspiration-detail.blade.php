<?php

use App\Models\Aspiration;
use Livewire\Volt\Component;

new
#[\Livewire\Attributes\Title('Detail Aspirasi')]
class extends Component {

    public $ticket;
    public $user;

    public function mount($ticket): void
    {
        $this->ticket = Aspiration::where('ticket', $ticket)->first();
        if(auth()->user()){
            $this->user = auth()->user();
        }
    }
}; ?>

<div class="flex gap-2 ">
    @if(auth()->user())
        <div class=" flex flex-col gap-2 border border-neutral-200 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 dark:border-neutral-700">
        <flux:heading size="xl">Detail Pelapor</flux:heading>
            <div class="flex items-start">
                <div class="w-40 pr-2">Nama Lengkap</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->name }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Alamat Email</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->email }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">KTP</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->identity_number }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Nomor Telepon</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->phone_number }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Tempat Lahir</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->place_of_birth }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Tanggal Lahir</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->date_of_birth }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Jenis Kelamin</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->gender }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Agama</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->religion }}</div>
            </div>
            <div class="flex items-start">
                <div class="w-40 pr-2">Status Menikah</div>
                <div class="px-1">:</div>
                <div class="flex-1">{{ auth()->user()->personalData->marital_status == 'married' ? 'Menikah' : 'Belum Menikah' }}</div>
            </div>
        </div>
    @endif
</div>
