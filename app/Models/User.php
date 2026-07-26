<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Panel; // إضافة

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use  HasFactory,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
        protected $fillable = ['role_id', 'name', 'email', 'password', 'profile_photo_path', 'is_approved'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
     {
        // 1. إذا لم يكن للمستخدم أي دور، نمنعه من الدخول
        if (!$this->role) {
            return false;
        }

        // 2. لوحة الموظف تتطلب موافقة الإدارة (حساب معتمد) - المتقدمون المعلّقون لا يدخلون اللوحة
        if ($panel->getId() === 'employee' && !$this->is_approved) {
            return false;
        }

        $roleName = $this->role->name;

        // 3. توجيه بقية الأدوار للوحاتهم المخصصة فقط
        return match ($panel->getId()) {
            'admin'      => $roleName === 'super_admin',
            'hr'         => $roleName === 'hr_manager',
            'pm'         => $roleName === 'project_manager',
            'accountant' => $roleName === 'accountant',
            'employee'   => $roleName === 'employee',
            default      => false,
        };
    }


        public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

}
