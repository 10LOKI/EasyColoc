<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $fillable = ['colocation_id','paid_by','description','amount'];

    public function colocation() : BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id')->withDefault([
            'name' => 'Unknown user',
        ]);
    }

    public function category()
    {
        return $this->belongsTo(User::class,'paid_by');
    }
}
