<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentLog extends Model
{
    protected $fillable = [
        'shipment_id', 'old_status', 'new_status', 'user_id', 'notes',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getOldStatusLabelAttribute(): string
    {
        return $this->old_status ? Shipment::statusLabel($this->old_status) : '—';
    }

    public function getNewStatusLabelAttribute(): string
    {
        return Shipment::statusLabel($this->new_status);
    }
}
