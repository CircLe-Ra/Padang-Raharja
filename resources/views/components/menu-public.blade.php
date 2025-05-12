<flux:navbar class="-mb-px max-lg:hidden ">
    <flux:navbar.item icon="home" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
        Beranda
    </flux:navbar.item>
    <flux:navbar.item icon="calendar-days" :href="route('portal.activity')" :current="request()->routeIs('portal.activity')" wire:navigate>
        Kegiatan Kampung
    </flux:navbar.item>
    <flux:navbar.item icon="handshake" :href="route('portal.realization')" :current="request()->routeIs('portal.realization')" wire:navigate>
        Realisasi Anggaran
    </flux:navbar.item>
</flux:navbar>
