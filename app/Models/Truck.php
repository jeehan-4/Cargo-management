<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Truck extends Model
{
    protected $fillable = [
        'driver_id', 'plate_number', 'type', 'capacity', 'status', 'notes',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'active'      => 'نشطة',
            'maintenance' => 'في الصيانة',
            'inactive'    => 'غير نشطة',
            default       => $status,
        };
    }

    public static function statusBadge(string $status): string
    {
        return match ($status) {
            'active'      => 'success',
            'maintenance' => 'warning',
            'inactive'    => 'secondary',
            default       => 'secondary',
        };
    }
}
