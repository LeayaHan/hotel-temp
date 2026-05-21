<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'full_name',
        'email',
        'phone',
        'room_number',
        'floor_number',
        'check_in',
        'check_out',
        'status',
    ];

    /**
     * Accessor: $guest->full_name always returns the composed name.
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_initial ? $this->middle_initial . '.' : null,
            $this->last_name,
        ]);
        return implode(' ', $parts) ?: ($this->attributes['full_name'] ?? '');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}