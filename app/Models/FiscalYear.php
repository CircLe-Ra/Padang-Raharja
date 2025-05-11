<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FiscalYear extends Model
{
    protected $guarded = ['id'];

    public function budgetPlans(): HasMany
    {
        return $this->hasMany(BudgetPlan::class);
    }
}
