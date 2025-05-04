@props([
    'items' => [],
    'multiple' => false,
    'activeKey' => null,
])

<div
    x-data="{
        activeKey: @js($activeKey),
        multiple: @js($multiple),
        toggle(key) {
            if (this.multiple) {
                this.activeKey = this.activeKey.includes(key) ? this.activeKey.filter(k => k !== key) : [...this.activeKey, key];
            } else {
                this.activeKey = this.activeKey === key ? null : key;
            }
        },
        isActive(key) {
            return this.multiple ? this.activeKey.includes(key) : this.activeKey === key;
        }
    }"
    class="space-y-2 w-full"
>
    @foreach($items as $key => $item)
        <div
            x-data="{ key: @js($key) }"
            class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden transition-all duration-200"
            :class="{ 'ring-2 ring-primary-500 dark:ring-primary-400': isActive(key) }"
        >
            <button
                @click="toggle(key)"
                class="flex items-center justify-between w-full px-4 py-3 text-left font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors duration-150"
                :aria-expanded="isActive(key)"
            >
                <span>{{ $item['question'] }}</span>
                <svg
                    class="w-5 h-5 transform transition-transform duration-200"
                    :class="{ 'rotate-180': isActive(key) }"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>

            <div
                x-show="isActive(key)"
                x-collapse
                class="px-4 pb-4 pt-2 bg-white dark:bg-zinc-900"
            >
                <div class="prose prose-zinc dark:prose-invert max-w-none">
                    {!! $item['answer'] !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
