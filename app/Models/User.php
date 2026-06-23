<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'en33_users';
    public $timestamps = false;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'url', 'user_regdate', 'city',
        'user_sig', 'pass', 'posts', 'attachsig', 'rank', 'timezone_offset',
        'last_login', 'birth_day', 'gender', 'user_group', 'country', 'mobile',
        'fax', 'phone', 'address', 'company', 'avatar', 'status', 'fb_id',
        'auth_secret', 'permission',
    ];

    protected $hidden = ['pass', 'auth_secret'];

    /**
     * Get the password for authentication (maps 'pass' column to Laravel's 'password').
     */
    public function getAuthPassword()
    {
        return $this->pass;
    }

    /**
     * Get unserialized permissions array.
     */
    public function getPermissionsAttribute()
    {
        if (!empty($this->permission)) {
            return unserialize($this->permission, ['allowed_classes' => false]);
        }
        return [];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        $perms = $this->permissions;
        return isset($perms['admin']) && $perms['admin'] == 1;
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $section): bool
    {
        $perms = $this->permissions;
        // Admin users get all permissions (same as legacy system)
        if (isset($perms['admin']) && $perms['admin'] == 1) {
            return true;
        }
        return isset($perms[$section]) && $perms[$section] == 1;
    }

    public function bookings()
    {
        return $this->hasMany(TourBooking::class, 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    public function venderDetail()
    {
        return $this->hasOne(VenderDetail::class, 'vender_id');
    }

    public function venderBalance()
    {
        return $this->hasOne(ServiceVenderBalance::class, 'vender_id');
    }

    public function venderExpenses()
    {
        return $this->hasMany(InvoiceExpense::class, 'vender');
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
