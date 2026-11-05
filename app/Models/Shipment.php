<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_number', 'cargo_type', 'weight', 'quantity', 'unit',
        'origin_port', 'destination_id', 'driver_id', 'status', 'notes',
        'assigned_at', 'started_at', 'delivered_at',
    ];

    protected $casts = [
        'assigned_at'  => 'datetime',
        'started_at'   => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ShipmentLog::class)->orderBy('created_at', 'asc');
    }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $seq = $last ? ((int) substr($last->shipment_number, -4)) + 1 : 1;
        return 'SHIP-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'pending'    => 'بانتظار التوزيع',
            'assigned'   => 'قيد التوصيل',
            'in_transit' => 'قيد التوصيل',
            'delivered'  => 'تم التسليم',
            'failed'     => 'متعذر التسليم',
            'cancelled'  => 'ملغاة',
            'archived'   => 'مؤرشفة',
            default      => $status,
        };
    }

    public static function statusBadge(string $status): string
    {
        return match ($status) {
            'pending'    => 'secondary',
            'assigned'   => 'primary',
            'in_transit' => 'warning',
            'delivered'  => 'success',
            'failed'     => 'danger',
            'cancelled'  => 'dark',
            'archived'   => 'info',
            default      => 'secondary',
        };
    }

    public static function statusIcon(string $status): string
    {
        return match ($status) {
            'pending'    => 'bi-clock',
            'assigned'   => 'bi-person-check',
            'in_transit' => 'bi-truck',
            'delivered'  => 'bi-check-circle',
            'failed'     => 'bi-x-circle',
            'cancelled'  => 'bi-slash-circle',
            'archived'   => 'bi-archive',
            default      => 'bi-circle',
        };
    }
}
