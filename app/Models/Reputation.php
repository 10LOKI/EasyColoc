<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Reputation extends Model
{
    protected $table = 'reputation';

    protected $fillable = [
        'user_id',
        'colocation_id',
        'score',
        'reason',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public static function addPositive($user, $colocation, $reason, ?string $description = null): self
    {
        return DB::transaction(function () use ($user, $colocation, $reason, $description) {
            $entry = self::create([
                'user_id' => $user->id,
                'colocation_id' => $colocation?->id,
                'score' => 1,
                'reason' => $reason,
                'description' => $description,
            ]);

            $user->increment('reputation_score', 1);

            return $entry;
        });
    }

    public static function addNegative($user, $colocation, $reason, ?string $description = null): self
    {
        return DB::transaction(function () use ($user, $colocation, $reason, $description) {
            $entry = self::create([
                'user_id' => $user->id,
                'colocation_id' => $colocation?->id,
                'score' => -1,
                'reason' => $reason,
                'description' => $description,
            ]);

            $user->decrement('reputation_score', 1);

            return $entry;
        });
    }

    public static function getTotalScore($user): int
    {
        return (int) self::where('user_id', $user->id)->sum('score');
    }
}
