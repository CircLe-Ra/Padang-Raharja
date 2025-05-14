<?php

use App\Models\BudgetPlan;
use App\Models\AccountCode;
use App\Models\BudgetRealization;
use App\Models\FiscalYear;
use App\Models\FundingSource;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Title('Realisasi Anggaran')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = '';
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';

    #[\Livewire\Attributes\Locked]
    public ?int $fiscalYearId;
    public string $year = '';
    public ?int $id = null;
    public $budget_plan_id = null;
    public string $budget = '';
    public string $more_or_less = '';

    public bool $budgetRealizationModal = false;

    public function mount($fiscalYearId): void
    {
        $this->fiscalYearId = $fiscalYearId;
        $this->year = FiscalYear::find($fiscalYearId)->year;
    }

    #[\Livewire\Attributes\Computed]
    public function budgetPlans()
    {
        return BudgetPlan::with(['accountCode', 'fundingSource'])
            ->where(function ($query) {
                $query->whereHas('accountCode', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('fundingSource', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('volume', 'like', '%' . $this->search . '%')
                    ->orWhere('unit', 'like', '%' . $this->search . '%')
                    ->orWhere('budget', 'like', '%' . $this->search . '%');
            })->where('fiscal_year_id', $this->fiscalYearId)
            ->paginate($this->show, pageName: 'budget-plan-page');
    }


    public function store(): void
    {
        $validated = $this->validate([
            'budget_plan_id' => ['required'],
            'budget' => ['required', 'numeric'],
            'more_or_less' => ['required', 'numeric'],
        ]);
        try {
            BudgetRealization::updateOrCreate(['budget_plan_id' => $this->budget_plan_id], $validated);
            BudgetPlan::where('id', $this->budget_plan_id)->update(['realization' => 'already']);
            unset($this->budgetPlans);
           $this->budgetRealizationModal = false;
            $this->dispatch('toast', message: 'Data berhasil disimpan');
        } catch (Exception $e) {
           $this->budgetRealizationModal = false;
            $this->dispatch('toast', type: 'error', message: 'Data gagal disimpan ' . $e->getMessage());
        }
    }

    public function realization($id): void
    {
        $this->budget_plan_id = $id;
        $budgetPlan = BudgetPlan::find($id);
        $this->budget = $budgetPlan->budgetRealization->budget ?? '';
        $this->more_or_less = $budgetPlan->budgetRealization->more_or_less ?? '';
        $this->budgetRealizationModal = true;
    }


}; ?>

<div>
    <x-budget.breadcrumb :back="route('budget.realization.fiscal-years')" active="Realisasi / Anggaran Tahun {{ $this->year }}"/>
    <x-table
        thead="#, Kode Rekening, Volume, Satuan, Permintaan Anggaran, Jumlah Realisasi(Rp), Lebih/Kurang(Rp), Sumber Dana"
        searchable
        label="Realisasi Anggaran" sub-label="Daftar anggaran yang belum dan sudah direalisasi">
        <x-slot name="filter">
            <x-filter wire:model.live="show"/>
            <div class="flex gap-8">
                <div class="flex relative">
                <span class="absolute border border-zinc-500 top-1.5 -left-4 flex h-3 w-3 shrink-0 overflow-hidden rounded-sm bg-orange-200 dark:bg-orange-950"></span>
                    <p>Perencanaan Kampung</p>
                </div>
                <div class="flex relative">
                    <span class="absolute border border-zinc-500 top-1.5 -left-4 flex h-3 w-3 shrink-0 overflow-hidden rounded-sm bg-green-200 dark:bg-green-950"></span>
                    <p>Aspirasi Masyarakat</p>
                </div>
            </div>
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->budgetPlans->count())
            @foreach($this->budgetPlans as $plan)
                <tr class="{{ $plan->category == 'village' ? 'bg-orange-200 text-zinc-800 dark:bg-orange-950 dark:text-white' : ($plan->category == 'people' ? 'bg-green-200 text-zinc-800 dark:bg-green-950 dark:text-white' : '') }}">
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $plan->accountCode->code }} - {{ $plan->accountCode->name }}
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
                            <flux:tooltip position="left" content="Realisasikan">
                                <flux:button variant="primary" size="xs" icon="handshake"
                                             wire:click="realization({{ $plan->id }})"/>
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
    <flux:modal wire:model.self="budgetRealizationModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Realisasi Anggaran</flux:heading>
                <flux:text class="mt-2">Realiasasikan Anggaran Kampung Padang Raharja</flux:text>
            </div>
            <form wire:submit="store" class="space-y-4">
                <flux:field>
                    <div x-data="{ formattedBudget: '' }"
                         x-init="() => {
                        $watch('$wire.budget', (value) => {
                             if (value) {
                                 formattedBudget = new Intl.NumberFormat('id-ID', { style: 'currency',currency: 'IDR' }).format(value);
                             } else {
                                 formattedBudget = '';
                             }
                         });
                     }">
                        <flux:label>Total Realisasi&nbsp;<div class="bg-zinc-600 text-white py-1 px-2 rounded -mb-1" x-show="$wire.budget" x-text="formattedBudget"></div></flux:label>
                        <flux:input
                            class="mt-2"
                            wire:model="budget"
                            x-model="$wire.budget"
                            x-on:input="
                        let num = $event.target.value.replace(/[^0-9]/g, '');
                        $wire.budget = num ? parseInt(num) : null;
                    " />
                    </div>
                </flux:field>

                <flux:field>
                    <div x-data="{ formattedBudget: '' }"
                         x-init="() => {
                        $watch('$wire.more_or_less', (value) => {
                             if (value) {
                                 formattedBudget = new Intl.NumberFormat('id-ID', { style: 'currency',currency: 'IDR' }).format(value);
                             } else {
                                 formattedBudget = '';
                             }
                         });
                     }">
                        <flux:label>Lebih/Kurang&nbsp;<div class="bg-zinc-600 text-white py-1 px-2 rounded -mb-1" x-show="$wire.more_or_less" x-text="formattedBudget"></div></flux:label>
                        <flux:input
                            class="mt-2"
                            wire:model="more_or_less"
                            x-model="$wire.more_or_less"
                            x-on:input="
                        let num = $event.target.value.replace(/[^0-9]/g, '');
                        $wire.more_or_less = num ? parseInt(num) : null;
                    " />
                    </div>
                </flux:field>
                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
    {{ $this->budgetPlans->links('components.pagination') }}
</div>
