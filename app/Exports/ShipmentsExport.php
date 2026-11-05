<?php

namespace App\Exports;

use App\Models\Shipment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(protected array $filters = []) {}

    public function query()
    {
        $query = Shipment::with(['destination', 'driver'])->orderBy('created_at', 'desc');

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['driver_id'])) {
            $query->where('driver_id', $this->filters['driver_id']);
        }
        if (!empty($this->filters['from'])) {
            $query->whereDate('created_at', '>=', $this->filters['from']);
        }
        if (!empty($this->filters['to'])) {
            $query->whereDate('created_at', '<=', $this->filters['to']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'رقم الشحنة',
            'نوع البضاعة',
            'الوزن',
            'الكمية',
            'الوحدة',
            'الميناء',
            'الوجهة',
            'السائق',
            'الحالة',
            'تاريخ الإنشاء',
            'تاريخ التسليم',
        ];
    }

    public function map($shipment): array
    {
        return [
            $shipment->shipment_number,
            $shipment->cargo_type,
            $shipment->weight,
            $shipment->quantity,
            $shipment->unit,
            $shipment->origin_port,
            $shipment->destination?->name ?? '—',
            $shipment->driver?->name ?? '—',
            Shipment::statusLabel($shipment->status),
            $shipment->created_at?->format('Y-m-d'),
            $shipment->delivered_at?->format('Y-m-d') ?? '—',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
