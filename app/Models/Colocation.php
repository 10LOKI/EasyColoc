<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colocation extends Model
{
    protected $fillable = ['name','invite_code','status'];

   public function memberships()
   {
       return $this->hasMany(\App\Models\Membership::class);
   }

   public function users() :BelongsToMany{
       return $this->belongsToMany(User::class, 'memberships')
        ->withPivot('role','left_at')
        ->withTimestamps()
        ; 
   }
   public function expenses()
   {
        return $this->hasMany(Expense::class);
   }

   public function isMember($user)
   {
       return $this->users()->where('user_id', $user->id)->exists();
   }

   public function isOwner(User $user): bool
   {
       return $this->memberships()
           ->where('user_id', $user->id)
           ->where('role', 'owner')
           ->whereNull('left_at')
           ->exists();
   }
}
