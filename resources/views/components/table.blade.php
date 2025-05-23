@props(['thead', 'action' => true, 'theadCol' => null, 'mainClass' => null, 'filter' => false, 'label' => null, 'subLabel' => null])
@php
    $thead = \Str::of($thead)->explode(',');
@endphp
<div class="p-6 border border-zinc-200 print:border-none dark:border-zinc-700 mt-2 rounded-lg bg-zinc-50 dark:bg-zinc-900 {{ $mainClass }}">
    @isset($label)
        <flux:heading size="xl" level="1" class="print:text-center">{{ $label }}</flux:heading>
    @endisset
    @isset($subLabel)
        <flux:subheading class="mb-2" class="print:text-center">{{ $subLabel }}</flux:subheading>
    @endisset
    <div class="flex items-center justify-between py-2">
        @if ($filter)
            {{ $filter }}
        @endif
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm text-left rtl:text-right text-zinc-500 dark:text-zinc-400 ">
        <thead class="text-xs text-zinc-700 uppercase bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-100 ">
        <tr>
            @foreach ($thead as $key => $th)
                @php
                    $mergeCell = Str::of($th)->explode(':');
                    $count = $mergeCell->count();
                @endphp
                <th scope="col" class="px-6 py-3" colspan="{{ $count ?? '' }}">
                    {{ $th }}
                </th>
            @endforeach
            @if ($action ?? false)
                <th scope="col" class="px-6 py-3">Aksi</th>
            @endif
        </tr>
        </thead>
        <tbody>
        {{ $slot }}
        </tbody>
    </table>
    </div>
</div>
