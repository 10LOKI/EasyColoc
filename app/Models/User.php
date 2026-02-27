<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    protected $fillable = ['name', 'email', 'password', 'role', 'is_banned', 'reputation_score'];
    
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
        ];
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function colocations()
    {
        return $this->belongsToMany(Colocation::class, 'memberships')
            ->withPivot('role', 'joined_at', 'left_at')
            ->wherePivotNull('left_at')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'paid_by');
    }

    public function reputations()
    {
        return $this->hasMany(Reputation::class);
    }

    public function sentSettlements()
    {
        return $this->hasMany(Settlement::class, 'sender_id');
    }

    public function receivedSettlements()
    {
        return $this->hasMany(Settlement::class, 'receiver_id');
    }
}
