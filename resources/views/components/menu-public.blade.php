<flux:navbar class="-mb-px max-lg:hidden ">
    <flux:navbar.item icon="home" :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
        Beranda
    </flux:navbar.item>
    <flux:navbar.item icon="calendar-days" :href="route('portal.activity')" :current="request()->routeIs('portal.activity')" wire:navigate>
        Kegiatan Kampung
    </flux:navbar.item>
    <flux:navbar.item icon="banknote-arrow-up" :href="route('portal.budget')" :current="request()->routeIs('portal.budget')" wire:navigate>
        Penyusunan Anggaran
    </flux:navbar.item>
    <flux:navbar.item icon="handshake" :href="route('portal.realization')" :current="request()->routeIs('portal.realization')" wire:navigate>
        Realisasi Anggaran
    </flux:navbar.item>
    <flux:navbar.item icon="rss" :href="route('portal.aspiration')" :current="request()->routeIs('portal.aspiration')" wire:navigate>
        Aspirasi Masyarakat
    </flux:navbar.item>
</flux:navbar>
