<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\LogsAudit;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsAudit, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Login access to /admin itself is still not gated by role/permission
     * (any authenticated staff user can log in) - RBAC (Fase 24) controls
     * what they can DO once inside, not whether they can log in at all.
     * Without this override at all, Filament's Authenticate middleware
     * falls back to gating /admin on `config('app.env') === 'local'` for
     * any model that doesn't implement FilamentUser.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
