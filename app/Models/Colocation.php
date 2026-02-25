<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Colocation extends Model
{
    protected $fillable = ['name','status'];

    protected $casts = ['created_at' => 'datetime' , 'updated_at' => 'datetime',];

    // Auto-generate invite code
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($colocation) {
            if (empty($colocation->invite_code)) {
                $colocation->invite_code = strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'memberships')
            ->withPivot('role', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper Methods
    public function isOwner(User $user): bool
    {
        return $this->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->exists();
    }
}
