<flux:navbar class="-mb-px max-lg:hidden">
    <flux:navbar.item icon="layout-grid" :href="route('personal-dashboard')" :current="request()->routeIs('personal-dashboard')" wire:navigate>
        Dashboard
    </flux:navbar.item>
    <flux:navbar.item icon="identification" :href="route('personal-data')" :current="request()->routeIs('personal-data')" wire:navigate>
       Data Pribadi
    </flux:navbar.item>
    <flux:navbar.item icon="rss" :href="route('personal-aspiration')" :current="request()->routeIs('personal-aspiration') || request()->routeIs('portal.aspiration-detail')" wire:navigate>
       Aspirasi Anda
    </flux:navbar.item>
</flux:navbar>
