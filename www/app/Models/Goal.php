<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'month',
        'year',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeOfUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}

