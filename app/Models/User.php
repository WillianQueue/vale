<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ CÓDIGO CORRIGIDO
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
