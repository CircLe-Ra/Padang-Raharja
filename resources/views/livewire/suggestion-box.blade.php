<?php

use App\Models\BudgetPlan;
use App\Models\AccountCode;
use App\Models\BudgetRealization;
use App\Models\Comment;
use App\Models\FiscalYear;
use App\Models\FundingSource;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Title('Kotak Anggaran')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = '';
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';
    public string $name = '';
    public string $aboriginal = '';
    public string $comment = '';
    public string $account = '';

    public bool $lookCommentModal = false;

    public function mount(): void
    {
//        dd(Comment::with('budgetPlan')->get());
    }

    #[\Livewire\Attributes\Computed]
    public function peopleComments()
    {
        return Comment::with('budgetPlan')->paginate($this->show, pageName: 'comment-page');
    }

    public function openComment($id): void
    {
        $comment = Comment::find($id);
        $this->account = $comment->budgetPlan->accountCode->code . ' - ' . $comment->budgetPlan->accountCode->name;
        $this->name = $comment->name;
        $this->aboriginal = $comment->aboriginal;
        $this->comment = $comment->comment;
        $this->lookCommentModal = true;
    }


}; ?>

<div>
    <x-budget.breadcrumb active="Kotak Saran"/>
    <x-table
        thead="#, Kode Rekening, Nama Pengirim, Status Masyarakat"
        searchable
        label="Penyusunan Anggaran" sub-label="Daftar penyusunan anggaran">
        <x-slot name="filter">
            <x-filter wire:model.live="show"/>
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->peopleComments->count())
            @foreach($this->peopleComments as $comment)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4 text-nowrap">
                        {{ $comment->budgetPlan->accountCode->code }} - {{ $comment->budgetPlan->accountCode->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $comment->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $comment->aboriginal == 'yes' ? 'Penduduk Padang Raharja' : 'Bukan Penduduk Padang Raharja' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:tooltip position="left" content="Baca">
                                <flux:button variant="primary" size="xs" icon="chat-bubble-bottom-center-text"
                                             wire:click="openComment({{ $comment->id }})"/>
                            </flux:tooltip>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="px-6 py-4 text-center">
                    Data tidak ditemukan
                </td>
            </tr>
        @endif
    </x-table>
    <flux:modal wire:model.self="lookCommentModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Kotak Saran</flux:heading>
                <flux:text class="mt-2">{{ $this->account }}</flux:text>
            </div>
            <div class="flex flex-col">
                <div class="w-1/2">
                    <flux:label>Nama Pengirim</flux:label>
                    <flux:text>{{ $this->name }}</flux:text>
                </div>
                <div class="w-1/2">
                    <flux:label>Status Masyarakat</flux:label>
                    <flux:text>{{ $this->aboriginal == 'yes' ? 'Penduduk Padang Raharja' : 'Bukan Penduduk Padang Raharja' }}</flux:text>
                </div>
                <div class="w-full">
                    <flux:label>Pesan</flux:label>
                    <flux:text>{{ $this->comment }}</flux:text>
                </div>
            </div>
            <div class="flex">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="filled">Tutup</flux:button>
                </flux:modal.close>
            </div>
        </div>
    </flux:modal>
    {{ $this->peopleComments->links('components.pagination') }}
</div>
