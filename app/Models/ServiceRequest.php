<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'guest_id',
        'user_id',
        'floor',
        'room_number',
        'service_type',
        'details',
        'priority',
        'status',
        'quantities',
        'scheduled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'quantities'   => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}