<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnualBudget extends Model
{
    protected $fillable = [
        'user_id',
        'cost_center_id',
        'category_id',
        'year',
        'planned_expense',
        'planned_income',
    ];

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeOfUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}

