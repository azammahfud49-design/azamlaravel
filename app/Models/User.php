<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model User - OOP: Inheritance dari Authenticatable
 * Implement MustVerifyEmail untuk verifikasi email
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    // ===== Encapsulation: atribut yang bisa diisi massal =====
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
    ];

    // ===== Encapsulation: sembunyikan data sensitif =====
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ===== Relasi: User memiliki banyak Mahasiswa =====
    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    // ===== Accessor: cek apakah user adalah admin =====
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    // ===== Accessor: URL avatar =====
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Generate avatar dari inisial nama
        $name = urlencode($this->name);
        return config('app.avatar_url', "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff");
    }
}