<?php

use Livewire\Volt\Component;

new class extends Component {
    public $user;
    public $unreadCount;
    public $allNotifications;

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->unreadCount = $this->user->unreadNotifications->count();
        $this->allNotifications = $this->user->notifications()->latest()->get();
    }

    public function markAsRead($notificationId) : void
    {
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();
        if ($notification->unread()) {
            $notification->markAsRead();
            $this->unreadCount--;
        }
        $this->redirect(route('suggestion-box'), navigate: true);
    }
}; ?>

<div>
    <x-budget.breadcrumb active="Notifikasi" />
    <x-table thead="#, Notifikasi, Status, Pada" :action="true" label="Notifikasi">
        <x-slot name="subLabel">
            @if($unreadCount > 0)
                Anda memiliki {{ $unreadCount }} notifikasi belum dibaca
            @else
                Semua notifikasi telah dibaca
            @endif
        </x-slot>
        @forelse($allNotifications as $notification)
            <tr class="@if($notification->unread()) bg-blue-50 dark:bg-zinc-800 @else bg-white dark:bg-zinc-900 @endif border-b dark:border-zinc-700">
                <td class="px-6 py-4">
                    {{ $loop->iteration }}
                </td>
                <th scope="row" class="px-6 py-4 text-zinc-900 whitespace-nowrap dark:text-white">
                    <div class="ps-3">
                        <div class="text-base font-semibold">Notifikasi</div>
                        <div class="font-normal text-zinc-500">
                            {{ $notification->data['message'] ?? '' }}<br/>
                            @isset($notification->data['sender_name'])
                                oleh {{ $notification->data['sender_name'] }}
                            @endisset
                        </div>
                    </div>
                </th>
                <td class="px-6 py-4">
                    @if($notification->unread())
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-blue-900 dark:text-blue-300"> Belum Dibaca</span>
                    @else
                        <span class="text-green-800 text-xs font-medium px-2.5 py-0.5 dark:text-green-300">Sudah Dibaca</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    {{ $notification->created_at->diffForHumans() }}
                </td>
                <td class="px-6 py-4 text-nowrap">
                    <flux:button variant="danger" icon="eye" size="xs" wire:click="markAsRead('{{ $notification->id }}')">
                        {{ $notification->unread() ? 'Baca' : 'Lihat' }}
                    </flux:button>
                </td>
            </tr>
        @empty
            <tr class="bg-white dark:bg-zinc-900 border-b dark:border-zinc-700">
                <td class="px-6 py-4 text-center" colspan="5">
                    Tidak Ada Notifikasi
                </td>
            </tr>
        @endforelse
    </x-table>
</div>

@script
<script>
    Livewire.on('mark-all-read', () => {
        Livewire.dispatch('mark-all-as-read');
    });
</script>
@endscript
