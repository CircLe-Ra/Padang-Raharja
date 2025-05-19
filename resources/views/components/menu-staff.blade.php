<flux:navbar class="-mb-px max-lg:hidden">
    <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
        Dashboard
    </flux:navbar.item>
    <flux:navbar.item icon="circle-stack" :href="route('master-data.account-code')" :current="request()->routeIs('master-data*')" wire:navigate>
        Master Data
    </flux:navbar.item>
    <flux:navbar.item icon="calendar-days" :href="route('activity')" :current="request()->routeIs('activity')" wire:navigate>
        Kegiatan Kampung
    </flux:navbar.item>
    <flux:navbar.item icon="banknote-arrow-up" :href="route('budget.plan.fiscal-years')" :current="request()->routeIs('budget.plan*')" wire:navigate>
        Penyusunan
    </flux:navbar.item>
    <flux:navbar.item icon="handshake" :href="route('budget.realization.fiscal-years')" :current="request()->routeIs('budget.realization*')" wire:navigate>
        Realisasi
    </flux:navbar.item>
    <flux:navbar.item icon="square-library" :href="route('suggestion-box')" :current="request()->routeIs('suggestion-box')" wire:navigate>
        Kotak Saran
    </flux:navbar.item>
    <flux:navbar.item icon="file-text" :href="route('report.realization')" :current="request()->routeIs('report.realization')" wire:navigate>
        Laporan
    </flux:navbar.item>
</flux:navbar>
