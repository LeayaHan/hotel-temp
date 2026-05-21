<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'department',
        'guest_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // Role helper methods
    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isManager(): bool     { return $this->role === 'manager'; }
    public function isStaff(): bool       { return $this->role === 'staff'; }
    public function isFrontDesk(): bool   { return $this->role === 'front_desk'; }
    public function isCustomer(): bool    { return $this->role === 'customer'; }

    public function isAdminOrManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function canAccessStaffFeatures(): bool
    {
        return in_array($this->role, ['admin', 'staff', 'front_desk']);
    }

    // Relationships
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }
}