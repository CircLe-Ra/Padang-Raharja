<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundingSource extends Model
{
    protected $guarded = ['id'];

    public function budgetPlans()
    {
        return $this->hasMany(BudgetPlan::class);
    }
}
