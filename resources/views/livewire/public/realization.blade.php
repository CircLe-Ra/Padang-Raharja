<?php

use App\Models\BudgetPlan;
use App\Models\AccountCode;
use App\Models\BudgetRealization;
use App\Models\Comment;
use App\Models\FiscalYear;
use App\Models\FundingSource;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Layout('components.layouts.public')]
#[\Livewire\Attributes\Title('Realisasi Anggaran')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 100;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';

    #[\Livewire\Attributes\Locked]
    public ?int $id = null;
    public ?int $fiscalYearId = null;
    public $budget_plan_id = null;
    public string $year = '';
    public string $name = '';
    public string $comment = '';
    public string $aboriginal;
    public string $accountCodeComment = '';

    public bool $commentModal = false;

    public function mount(): void
    {
        $ficalYear = FiscalYear::where('status', 1)->first();
        $this->fiscalYearId = $ficalYear->id ?? 0;
        $this->year = $ficalYear->year ?? '-';
    }

    #[\Livewire\Attributes\Computed]
    public function accountCodesWithBudgets()
    {
        $ficalYear = FiscalYear::where('status', 1)->first();
        $this->fiscalYearId = $ficalYear->id ?? 0;

        return AccountCode::with(['budgetPlans' => function ($query) {
            $query->where('fiscal_year_id', $this->fiscalYearId)
                ->with(['fundingSource', 'budgetRealization'])
                ->when($this->search, function ($q) {
                    $q->where(function ($subQuery) {
                        $subQuery->where('volume', 'like', '%' . $this->search . '%')
                            ->orWhere('unit', 'like', '%' . $this->search . '%')
                            ->orWhere('budget', 'like', '%' . $this->search . '%')
                            ->orWhereHas('fundingSource', function ($fundingQuery) {
                                $fundingQuery->where('name', 'like', '%' . $this->search . '%');
                            });
                    });
                });
        }])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id')
            ->paginate($this->show, pageName: 'budget-realization-public-page');
    }

    public function store(): void
    {
        $validated = $this->validate([
            'name' => ['required'],
            'aboriginal' => ['required'],
            'comment' => ['required'],
        ]);
        try {
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', 'staff');
            })->get();
            $validated['budget_plan_id'] = $this->budget_plan_id;
            Comment::create($validated);
            Notification::sendNow($users, new NewCommentNotification($this->name, $this->aboriginal, $this->comment));
            unset($this->accountCodesWithBudgets);
            $this->reset(['budget_plan_id', 'name', 'aboriginal', 'comment', 'accountCodeComment']);
            $this->commentModal = false;
            $this->dispatch('toast', message: 'Komentar anda telah dikirimkan');
        } catch (Exception $e) {
            $this->commentModal = false;
            $this->dispatch('toast', type: 'error', message: 'Komentar gagal dikirimkan, terjadi kesalahan : ' . $e->getMessage());
            dd($e->getMessage());
        }
    }

    public function giveComment($id, $accountId): void
    {
        $this->budget_plan_id = $id;
        $accountCode = AccountCode::find($accountId);
        $this->accountCodeComment = $accountCode->code . ' - ' . $accountCode->name;
        $this->commentModal = true;
    }


}; ?>

<div>
    <x-table
        thead="#, Kode Rekening, Uraian, Volume, Satuan, Permintaan Anggaran, Jumlah Realisasi(Rp), Lebih/Kurang(Rp), Sumber Dana"
        searchable
        label="Realisasi Anggaran Tahun {{ $this->year }}" sub-label="Anggaran dana yang telah direalisasikan">
        <x-slot name="filter">
            <x-filter wire:model.live="show"/>
            <div class="flex gap-8">
                <div class="flex relative">
                    <span
                        class="absolute border border-zinc-500 top-1.5 -left-4 flex h-3 w-3 shrink-0 overflow-hidden rounded-sm bg-orange-200 dark:bg-orange-950"></span>
                    <p>Perencanaan Kampung</p>
                </div>
                <div class="flex relative">
                    <span
                        class="absolute border border-zinc-500 top-1.5 -left-4 flex h-3 w-3 shrink-0 overflow-hidden rounded-sm bg-green-200 dark:bg-green-950"></span>
                    <p>Aspirasi Masyarakat</p>
                </div>
            </div>
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->accountCodesWithBudgets->count())
            @foreach($this->accountCodesWithBudgets as $accountCode)
                @forelse($accountCode->budgetPlans as $plan)
                    <tr class="{{ $plan->category == 'village' ? 'bg-orange-200 text-zinc-800 dark:bg-orange-950 dark:text-white' : ($plan->category == 'people' ? 'bg-green-200 text-zinc-800 dark:bg-green-950 dark:text-white' : '') }}">
                        <td class="px-6 py-4">
                            {{ $loop->parent->iteration }}
                        </td>
                        <td class="px-6 py-4 text-nowrap">
                            {{ $accountCode->code }}
                        </td>
                        <td class="px-6 py-4 ">
                            {{ $accountCode->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $plan->volume ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $plan->unit ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-nowrap">
                            {{ $plan->budget ? 'Rp ' . number_format($plan->budget, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-nowrap">
                            {{ $plan->budgetRealization ? 'Rp ' . number_format($plan->budgetRealization->budget ?? 0, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-nowrap">
                            {{ $plan->budgetRealization ? 'Rp ' . number_format($plan->budgetRealization->more_or_less ?? 0, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $plan->fundingSource->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <flux:tooltip position="left" content="Beri Masukan">
                                    <flux:button variant="primary" size="xs" icon="message-square-more"
                                                 wire:click="giveComment({{ $plan->id }}, {{ $accountCode->id }})"/>
                                </flux:tooltip>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-nowrap">
                            {{ $accountCode->code }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $accountCode->name }}
                        </td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4">-</td>
                        <td class="px-6 py-4">-</td>
                    </tr>
                @endforelse
            @endforeach
        @else
            <tr>
                <td colspan="9" class="px-6 py-4 text-center">
                    Data tidak ditemukan
                </td>
            </tr>
        @endif
    </x-table>
    {{ $this->accountCodesWithBudgets->links('components.pagination') }}
    <flux:modal wire:model.self="commentModal" class="md:w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Berikan Masukan</flux:heading>
                <flux:text class="mt-2">{{ $this->accountCodeComment }}</flux:text>
            </div>
            <form wire:submit="store" class="space-y-4">
                <flux:input label="Nama" wire:model="name" required></flux:input>
                <flux:select wire:model="aboriginal" label="Status Penduduk">
                    <flux:select.option value="">Pilih?</flux:select.option>
                    <flux:select.option value="yes">Penduk Padang Raharja</flux:select.option>
                    <flux:select.option value="no">Bukan Penduduk Padang Raharja</flux:select.option>
                </flux:select>
                <flux:textarea label="Masukan" wire:model="comment" rows="6"></flux:textarea>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
