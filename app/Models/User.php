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
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
