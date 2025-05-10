@props(['active', 'action' => null])
<div class="flex max-md:flex-col items-start">
    <div class="w-full md:w-[250px] pb-4 me-2">
        <flux:navlist wire:ignore class=" border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 p-4 rounded-lg">
            <flux:navlist.group expandable heading="Anggaran" >
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.account-code') }}" :current="request()->routeIs('master-data.account-code')">Kode Rekening</flux:navlist.item>
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.funding-source') }}" :current="request()->routeIs('master-data.funding-source')">Sumber Dana</flux:navlist.item>
            </flux:navlist.group>
            <flux:navlist.group expandable heading="Pengguna" >
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.users') }}" :current="request()->routeIs('master-data.users')" >Pengguna Aplikasi</flux:navlist.item>
            </flux:navlist.group>
            <flux:navlist.group expandable heading="Kampung" >
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.history') }}" :current="request()->routeIs('master-data.history')">Sejarah</flux:navlist.item>
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.geography-demographics') }}" :current="request()->routeIs('master-data.geography-demographics')">Geografis & Demografi</flux:navlist.item>
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.vision') }}" :current="request()->routeIs('master-data.vision')">Visi</flux:navlist.item>
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{ route('master-data.mission') }}" :current="request()->routeIs('master-data.mission')">Misi</flux:navlist.item>
                <flux:navlist.item class="py-5 mt-2" wire:navigate href="{{  route('master-data.structure') }}" :current="request()->routeIs('master-data.structure')" >Struktur Organisasi</flux:navlist.item>
            </flux:navlist.group>

        </flux:navlist>
    </div>
    <flux:separator class="md:hidden" />
    <div class="flex-1 max-md:pt-6 self-stretch">
        <x-master-data.breadcrumb :active="$active" :action="$action" />
        <div class="mt-1">
            {{ $slot }}
        </div>
    </div>
</div>
