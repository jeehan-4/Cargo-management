<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Driver extends Model
{
    protected $fillable = [
        'name', 'phone', 'license_number', 'status', 'national_id', 'notes', 'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function truck(): HasOne
    {
        return $this->hasOne(Truck::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function activeShipments(): HasMany
    {
        return $this->hasMany(Shipment::class)->whereIn('status', ['assigned', 'in_transit']);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && !$this->activeShipments()->exists();
    }

    public function updateAvailability(): void
    {
        if ($this->status === 'inactive') {
            return;
        }

        $hasActive = $this->activeShipments()->exists();
        $this->status = $hasActive ? 'busy' : 'available';
        $this->save();
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'available' => 'متاح',
            'busy'      => 'مشغول',
            'inactive'  => 'غير نشط',
            default     => $status,
        };
    }

    public static function statusBadge(string $status): string
    {
        return match ($status) {
            'available' => 'success',
            'busy'      => 'warning',
            'inactive'  => 'secondary',
            default     => 'secondary',
        };
    }
    public static function statusIcon($status)
{
    return match ($status) {
        'available' => 'bi-check-circle',
        'busy'      => 'bi-truck',
        'inactive'  => 'bi-x-circle',
        default     => 'bi-dash-circle',
    };
}
}
