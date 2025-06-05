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
#[\Livewire\Attributes\Title('Laporan - Realisasi Anggaran')]
class extends Component {
    use WithPagination;

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
            ->orderBy('id')->get();
    }

    #[\Livewire\Attributes\On('print')]
    public function print()
    {

    }


}; ?>

<div>
    <x-budget.breadcrumb active="Laporan realisasi">
        <x-slot name="action">
            <flux:button variant="primary" class="w-[100px]" size="sm" icon="cloud-download" wire:click="dispatch('print')">Cetak</flux:button>
        </x-slot>
    </x-budget.breadcrumb>
    <div id="print-area">
        <x-table
        thead="#, Kode Rekening, Uraian, Volume, Satuan, Permintaan Anggaran, Jumlah Realisasi(Rp), Lebih/Kurang(Rp), Sumber Dana"
        :action="false"
        label="Realisasi Anggaran Tahun {{ $this->year }}" sub-label="Anggaran dana yang telah direalisasikan">
        <x-slot name="filter">
            <div class="flex gap-8 print:hidden">
                <div class="flex relative ml-5">
                    <span
                        class="absolute border border-zinc-500 top-1.5 -left-4 flex h-3 w-3 shrink-0 overflow-hidden rounded-sm bg-orange-200 dark:bg-orange-950"></span>
                    <p>Perencanaan Kampung</p>
                </div>
                <div class="flex relative">
                    <span class="absolute border border-zinc-500 top-1.5 -left-4 flex h-3 w-3 shrink-0 overflow-hidden rounded-sm bg-green-200 dark:bg-green-950"></span>
                    <p>Aspirasi Masyarakat</p>
                </div>
            </div>
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px] print:hidden"/>
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
    </div>
</div>
@pushonce('scripts')
    @script
        <script>
            window.addEventListener('livewire:navigated', () => {
                Livewire.on('print', () => {
                    let printContents = document.getElementById('print-area').innerHTML;
                    document.body.innerHTML = printContents;
                    window.print();
                    window.location.reload();
                });
            }, {once: true});
        </script>
    @endscript
@endpushonce
