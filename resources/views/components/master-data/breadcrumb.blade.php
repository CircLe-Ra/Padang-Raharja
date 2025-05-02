@props(['active', 'action' => null])
<div class="flex justify-between items-center px-6 {{ $action ? 'py-3' : 'py-[18px]' }} rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
    <flux:breadcrumbs >
        <flux:breadcrumbs.item href="{{ route('dashboard') }}" separator="slash">Dashboard</flux:breadcrumbs.item>
        <flux:breadcrumbs.item separator="slash">Master Data</flux:breadcrumbs.item>
        <flux:breadcrumbs.item separator="slash">{{ $active }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
    @isset($action)
        <flux:spacer />
        {!! $action !!}
    @endisset
</div>
