<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'shipment_id',
        'driver_id',
        'user_id',
        'old_values',
        'new_values',
        'notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Arabic labels for the action verbs encoded in action_type. */
    protected const VERB_LABELS = [
        'create' => 'إضافة',
        'update' => 'تعديل',
        'delete' => 'حذف',
    ];

    protected const ENTITY_LABELS = [
        'shipment'    => 'شحنة',
        'driver'      => 'سائق',
        'truck'       => 'شاحنة',
        'destination' => 'وجهة',
    ];

    protected const FIELD_LABELS = [
        'name' => 'الاسم', 'status' => 'الحالة', 'cargo_type' => 'نوع البضاعة',
        'weight' => 'الوزن', 'quantity' => 'الكمية', 'unit' => 'الوحدة',
        'origin_port' => 'ميناء المنشأ', 'destination_id' => 'الوجهة', 'driver_id' => 'السائق',
        'notes' => 'ملاحظات', 'phone' => 'الهاتف', 'city' => 'المدينة', 'address' => 'العنوان',
        'plate_number' => 'رقم اللوحة', 'license_number' => 'رقم الرخصة', 'is_active' => 'التفعيل',
        'assigned_at' => 'تاريخ الإسناد', 'started_at' => 'تاريخ البدء', 'delivered_at' => 'تاريخ التسليم',
        'shipment_number' => 'رقم الشحنة', 'contact_name' => 'جهة الاتصال', 'contact_phone' => 'هاتف الاتصال',
    ];

    protected function verb(): string
    {
        return explode('_', (string) $this->action_type, 2)[0] ?? '';
    }

    protected function entity(): string
    {
        return explode('_', (string) $this->action_type, 2)[1] ?? '';
    }

    public function getActionLabelAttribute(): string
    {
        $verb   = self::VERB_LABELS[$this->verb()] ?? $this->verb();
        $entity = self::ENTITY_LABELS[$this->entity()] ?? $this->entity();
        return trim($verb . ' ' . $entity);
    }

    public function getActionBadgeAttribute(): string
    {
        return match ($this->verb()) {
            'create' => 'sbadge-available',
            'update' => 'sbadge-busy',
            'delete' => 'sbadge-failed',
            default  => 'sbadge-inactive',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->verb()) {
            'create' => 'bi-plus-circle',
            'update' => 'bi-pencil',
            'delete' => 'bi-trash',
            default  => 'bi-circle',
        };
    }

    public function getEntityNameAttribute(): string
    {
        if ($this->shipment) {
            return $this->shipment->shipment_number;
        }
        if ($this->driver) {
            return $this->driver->name;
        }
        return $this->new_values['name']
            ?? $this->old_values['name']
            ?? $this->new_values['shipment_number']
            ?? '—';
    }

    public function getChangesSummaryAttribute(): string
    {
        if ($this->notes) {
            return $this->notes;
        }
        if ($this->verb() === 'update' && is_array($this->new_values)) {
            $fields = array_keys($this->new_values);
            $fields = array_diff($fields, ['updated_at']);
            $labels = array_map(fn($f) => self::FIELD_LABELS[$f] ?? $f, $fields);
            return $labels ? implode('، ', $labels) : '—';
        }
        return '—';
    }
}
