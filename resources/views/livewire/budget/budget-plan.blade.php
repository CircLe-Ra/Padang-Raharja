<?php

use App\Models\BudgetPlan;
use App\Models\AccountCode;
use App\Models\FiscalYear;
use App\Models\FundingSource;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new
#[\Livewire\Attributes\Title('Penyusunan Anggaran')]
class extends Component {
    use WithPagination;

    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public $show = 5;
    #[\Livewire\Attributes\Url(history: true, keep: true)]
    public string $search = '';

    #[\Livewire\Attributes\Locked]
    public ?int $fiscalYearId;
    public string $year = '';
    public ?int $id = null;
    public $account_code_id = null;
    public $funding_source_id = null;
    public string $volume = '';
    public string $unit = '';
    public string $budget = '';

    // Untuk combobox
    public string $accountCodeSearch = '';
    public string $fundingSourceSearch = '';

    public bool $showConfirmModal = false;
    public bool $showAccountCode = false;
    public bool $showFundingSource = false;

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

    #[\Livewire\Attributes\Computed]
    public function accountCodes()
    {
        return AccountCode::query()
            ->whereNotIn('id', function($query) {
                $query->select('account_code_id')
                    ->from('budget_plans')
                    ->where('fiscal_year_id', $this->fiscalYearId);
            })
            ->when($this->accountCodeSearch, fn($query) =>
            $query->where('name', 'like', '%' . $this->accountCodeSearch . '%')
                ->orWhere('code', 'like', '%' . $this->accountCodeSearch . '%'))
            ->limit(20)
            ->get();
    }

    #[\Livewire\Attributes\Computed]
    public function fundingSources()
    {
        return FundingSource::query()
            ->when($this->fundingSourceSearch, fn($query) => $query->where('name', 'like', '%' . $this->fundingSourceSearch . '%'))
            ->limit(20)
            ->get();
    }

    public function resetBagAndField(): void
    {
        $this->resetValidation();
        $this->reset(['id', 'account_code_id', 'funding_source_id', 'volume', 'unit', 'budget']);
        $this->reset(['accountCodeSearch', 'fundingSourceSearch']);
    }

    public function store(): void
    {
        $this->validate([
            'account_code_id' => ['required', 'exists:account_codes,id'],
            'funding_source_id' => ['nullable', 'exists:funding_sources,id'],
            'volume' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:50'],
            'budget' => ['nullable', 'numeric'],
        ]);

        try {
            BudgetPlan::updateOrCreate(['id' => $this->id], [
                'fiscal_year_id' => $this->fiscalYearId,
                'account_code_id' => $this->account_code_id,
                'funding_source_id' => $this->funding_source_id,
                'volume' => $this->volume,
                'unit' => $this->unit,
                'budget' => $this->budget,
            ]);
            unset($this->budgetPlans);
            Flux::modal('budget-plan-modal')->close();
            $this->dispatch('toast', message: 'Data berhasil disimpan');
        } catch (Exception $e) {
            Flux::modal('budget-plan-modal')->close();
            $this->dispatch('toast', type: 'error', message: 'Data gagal disimpan ' . $e->getMessage());
        }
    }

    public function edit($id): void
    {
        $budgetPlan = BudgetPlan::find($id);
        $this->id = $budgetPlan->id;
        $this->account_code_id = $budgetPlan->account_code_id;
        $this->funding_source_id = $budgetPlan->funding_source_id;
        $this->volume = $budgetPlan->volume;
        $this->unit = $budgetPlan->unit;
        $this->budget = $budgetPlan->budget;
        Flux::modal('budget-plan-modal')->show();
    }

    public function delete($id): void
    {
        $this->showConfirmModal = true;
        $this->id = $id;
    }

    public function confirmDelete(): void
    {
        try {
            BudgetPlan::find($this->id)->delete();
            unset($this->budgetPlans);
            $this->resetBagAndField();
            $this->dispatch('toast', message: 'Data berhasil dihapus');
        } catch (Exception $e) {
            $this->resetBagAndField();
            $this->dispatch('toast', type: 'error', message: 'Data gagal dihapus');
        }
        $this->showConfirmModal = false;
    }

    #[\Livewire\Attributes\On('account-code-selected')]
    public function selectAccountCode($id, $text): void
    {
        $this->account_code_id = $id;
        $this->accountCodeSearch = $text;
        $this->showAccountCode = false;
    }

    #[\Livewire\Attributes\On('funding-source-selected')]
    public function selectFundingSource($id, $text): void
    {
        $this->funding_source_id = $id;
        $this->fundingSourceSearch = $text;
        $this->showFundingSource = false;
    }

}; ?>

<div>
    <x-activity.breadcrumb active="Penyusunan / Anggaran Tahun {{ $this->year }}">
        <x-slot name="action">
            <flux:modal.trigger name="budget-plan-modal">
                <flux:button variant="primary" class="w-[100px]" size="sm" icon="plus">Tambah</flux:button>
            </flux:modal.trigger>
        </x-slot>
    </x-activity.breadcrumb>

    {{-- modal --}}
    <flux:modal name="budget-plan-modal" variant="flyout" @close="resetBagAndField">
        <div class="space-y-6">
            <div>
                <flux:heading size="xl">Penyusunan Anggaran</flux:heading>
                <flux:text class="mt-2">Tambah atau Ubah Penyusunan Anggaran disini.</flux:text>
            </div>
            <form wire:submit="store" class="space-y-4">
                <flux:field>
                    <flux:label>Kode Akun</flux:label>
                    <div>
                        <flux:input
                            x-on:focus="$wire.showAccountCode = true"
                            @click.outside="$wire.showAccountCode = false"
                            wire:model.live="accountCodeSearch"
                            placeholder="Cari kode akun..."
                            icon:trailing="chevron-down"
                            x-bind:class="{ 'ring-2 ring-primary-500': open }"
                            autocomplete="off" />

                        <div wire:show="showAccountCode"
                             class="absolute z-10 mt-4 bg-white dark:bg-zinc-900 shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm w-[300px]">
                            @foreach($this->accountCodes as $accountCode)
                                <a href="#"
                                   class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white dark:hover:bg-zinc-800 hover:bg-zinc-100"
                                   wire:key="account-{{ $accountCode->id }}"
                                   @click="$dispatch('account-code-selected', { id: '{{ $accountCode->id }}', text: '{{ $accountCode->code }} - {{ $accountCode->name }}' })">
                                    {{ $accountCode->code }} - {{ $accountCode->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </flux:field>

                <flux:field>
                    <flux:label>Sumber Dana</flux:label>
                    <div>
                        <flux:input
                            x-on:focus="$wire.showFundingSource = true"
                            @click.outside="$wire.showFundingSource = false"
                            wire:model.live="fundingSourceSearch"
                            placeholder="Cari sumber dana..."
                            icon:trailing="chevron-down"
                            x-bind:class="{ 'ring-2 ring-primary-500': open }"
                            autocomplete="off"/>

                        <div wire:show="showFundingSource"
                             class="absolute z-10 mt-4 bg-white dark:bg-zinc-900 shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm w-[300px]">
                            @foreach($this->fundingSources as $fundingSource)
                                <a href="#"
                                   class="block px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white dark:hover:bg-zinc-800 hover:bg-zinc-100"
                                   wire:key="funding-{{ $fundingSource->id }}"
                                   @click="$dispatch('funding-source-selected', { id: '{{ $fundingSource->id }}', text: '{{ $fundingSource->name }}' })">
                                    {{ $fundingSource->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </flux:field>

                <flux:input label="Volume" wire:model="volume"/>
                <flux:input label="Satuan" wire:model="unit"/>
                <flux:input label="Anggaran (Rp)" wire:model="budget" type="number"/>

                <div class="flex">
                    <flux:spacer/>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <x-confirm wire:model.self="showConfirmModal"/>
    <x-table thead="#, Kode Akun, Sumber Dana, Volume, Satuan, Anggaran" searchable
             label="Penyusunan Anggaran" sub-label="Daftar penyusunan anggaran">
        <x-slot name="filter">
            <x-filter wire:model.live="show"/>
            <flux:input wire:model.live="search" size="sm" placeholder="Cari" class="w-full max-w-[220px]"/>
        </x-slot>
        @if($this->budgetPlans->count())
            @foreach($this->budgetPlans as $plan)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $plan->accountCode->code }} - {{ $plan->accountCode->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $plan->fundingSource->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $plan->volume ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $plan->unit ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $plan->budget ? 'Rp ' . number_format($plan->budget, 2, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:button variant="primary" size="xs" icon="pencil"
                                         wire:click="edit({{ $plan->id }})"/>
                            <flux:button variant="danger" size="xs" icon="trash"
                                         wire:click="delete({{ $plan->id }})"/>
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
    {{ $this->budgetPlans->links('components.pagination') }}
</div>
