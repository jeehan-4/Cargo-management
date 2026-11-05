<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>تقرير الشحنات</title>
    <style>
        /* استخدام خط يدعم العربية بشكل أفضل في الـ PDF إن وجد، أو العودة لـ DejaVu مع الضبط */
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            direction: rtl; 
            text-align: right; 
            font-size: 11px; 
            color: #202833; 
            line-height: 1.4; 
        }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #C7A667; padding-bottom: 10px; }
        .header h1 { margin: 0 0 5px 0; font-size: 20px; color: #202833; }
        .header p { margin: 2px 0; color: #415A66; font-size: 11px; }
        .stats-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats-table td { padding: 8px; border: 1px solid #ddd; text-align: center; background: #fafafa; }
        .stats-table .stat-val { font-size: 14px; font-weight: bold; color: #202833; }
        .stats-table .stat-lbl { font-size: 10px; color: #8A96A3; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #ccc; padding: 6px; text-align: right; }
        table.data-table th { background-color: #202833; color: #fff; font-weight: bold; }
        table.data-table tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #8A96A3; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الشحنات — نظام إدارة الشحنات</h1>
        <p>الفترة: {{ $report['summary']['period_label'] ?? ($report['summary']['from']->format('Y-m-d') . ' إلى ' . $report['summary']['to']->format('Y-m-d')) }}</p>
        <p>تاريخ الإنشاء: {{ $report['summary']['generated_at']->format('Y-m-d H:i') }}</p>
    </div>

    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-val">{{ $report['summary']['registered'] ?? 0 }}</div>
                <div class="stat-lbl">شحنات مسجلة</div>
            </td>
            <td>
                <div class="stat-val">{{ $report['summary']['delivered'] ?? 0 }}</div>
                <div class="stat-lbl">تم التسليم</div>
            </td>
            <td>
                <div class="stat-val">{{ $report['summary']['in_progress'] ?? 0 }}</div>
                <div class="stat-lbl">قيد التنفيذ</div>
            </td>
            <td>
                <div class="stat-val">{{ $report['summary']['failed'] ?? 0 }}</div>
                <div class="stat-lbl">متعذر التسليم</div>
            </td>
        </tr>
    </table>

    <h3 style="margin-bottom: 5px; text-align: right;">قائمة الشحنات</h3>
    @php
        $shipmentsList = $report['allTouched'] ?? $report['registered'] ?? collect();
    @endphp

    @if($shipmentsList->count())
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">رقم الشحنة</th>
                    <th style="width: 20%;">نوع البضاعة</th>
                    <th style="width: 15%;">الوجهة</th>
                    <th style="width: 15%;">السائق</th>
                    <th style="width: 12%;">الحالة</th>
                    <th style="width: 13%;">تاريخ التسجيل</th>
                </tr>
            </thead>
            <tbody>
                @foreach($shipmentsList as $index => $shipment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $shipment->shipment_number }}</td>
                        <td>{{ $shipment->cargo_type }}</td>
                        <td>{{ $shipment->destination->name ?? '—' }}</td>
                        <td>{{ $shipment->driver->name ?? '—' }}</td>
                        <td>{{ \App\Models\Shipment::statusLabel($shipment->status) }}</td>
                        <td>{{ $shipment->created_at ? $shipment->created_at->format('Y-m-d') : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #8A96A3; padding: 20px;">لا توجد شحنات في هذه الفترة.</p>
    @endif

    <div class="footer">
        <p>تم استخراج هذا التقرير آلياً من نظام إدارة الشحنات &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>