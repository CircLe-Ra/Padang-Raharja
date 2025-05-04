<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetPlan extends Model
{
    protected $guarded = ['id'];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class);
    }

    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class);
    }

}
