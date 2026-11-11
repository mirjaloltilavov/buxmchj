<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** Rollar va ular nima qila olishi (admin panelda ko'rsatiladi) */
    public const ROLES = [
        'super_admin' => 'Bosh admin',
        'admin' => 'Admin',
        'editor' => 'Kontent muharriri',
        'manager' => 'Menejer',
    ];

    public const ROLE_HINTS = [
        'super_admin' => 'Hammasi: kontent, arizalar, sozlamalar, foydalanuvchilar va jurnal.',
        'admin' => 'Kontent, arizalar va sozlamalar. Foydalanuvchilarni boshqara olmaydi.',
        'editor' => 'Faqat sayt kontenti: kurslar, xizmatlar, maqolalar, sahifa matnlari va boshqalar.',
        'manager' => 'Faqat arizalar: ko‘rish, holatini o‘zgartirish, izoh yozish.',
    ];

    public const ROLE_COLORS = ['super_admin' => 'danger', 'admin' => 'warning', 'editor' => 'info', 'manager' => 'success'];

    protected $fillable = ['name', 'username', 'email', 'phone', 'avatar', 'password', 'role', 'is_active', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /** Admin panelga faqat faol foydalanuvchilar kiradi (ro'yxatdan o'tish yo'q). */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active && array_key_exists((string) $this->role, self::ROLES);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? media_url($this->avatar) : null;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }
}
