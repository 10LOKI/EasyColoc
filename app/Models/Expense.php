<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $fillable = ['colocation_id', 'paid_by', 'category_id', 'description', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function colocation() : BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by')->withDefault([
            'name' => 'Unknown user',
        ]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
