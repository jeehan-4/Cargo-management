<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$reportCtrl = app(App\Http\Controllers\ReportController::class);
$req = Illuminate\Http\Request::create('/reports/export', 'GET');
$resp = $reportCtrl->export($req);
file_put_contents(__DIR__ . '/exports/report.xlsx', $resp->getContent());
echo 'report.xlsx bytes=' . strlen($resp->getContent()) . ' type=' . $resp->headers->get('Content-Type') . "\n";

$shipCtrl = app(App\Http\Controllers\ShipmentController::class);
$resp2 = $shipCtrl->exportExcel();
file_put_contents(__DIR__ . '/exports/shipments.xlsx', $resp2->getContent());
echo 'shipments.xlsx bytes=' . strlen($resp2->getContent()) . ' type=' . $resp2->headers->get('Content-Type') . "\n";