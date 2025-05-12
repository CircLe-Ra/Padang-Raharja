<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetRealization extends Model
{
    protected $guarded = ['id'];

    public function budgetPlan()
    {
        return $this->belongsTo(BudgetPlan::class);
    }

}
