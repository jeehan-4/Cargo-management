<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Shipment;
use App\Models\Driver;
use App\Services\ReportService;

$out = fopen(__DIR__ . '/exports/data_integrity.txt', 'w');
$w = function ($s) use ($out) { fwrite($out, $s . "\n"); echo $s . "\n"; };

$active = ['assigned', 'in_transit'];

/* ── DB truth ── */
$total = Shipment::count();
$byStatus = Shipment::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status')->all();
$w("== DB truth ==");
$w("total shipments = $total");
foreach ($byStatus as $s => $c) $w("  status $s = $c");

/* ── Conflict checks: shipment vs driver status ── */
$w("\n== Conflict checks (expect 0) ==");
$c1 = Shipment::whereIn('status', $active)->whereNull('driver_id')->count();
$w("active shipment with NO driver = $c1");
$c2 = Shipment::where('status', 'delivered')->whereNull('delivered_at')->count();
$w("delivered shipment with NULL delivered_at = $c2");
$c3 = Shipment::whereIn('status', ['in_transit', 'delivered'])->whereNull('started_at')->count();
$w("in_transit/delivered with NULL started_at = $c3");
$c4 = Shipment::where('status', 'assigned')->whereNull('assigned_at')->count();
$w("assigned with NULL assigned_at = $c4");

$busyNoActive = 0; $availWithActive = 0;
foreach (Driver::where('status', '!=', 'inactive')->get() as $d) {
    $n = $d->shipments()->whereIn('status', $active)->count();
    if ($d->status === 'busy' && $n === 0) { $busyNoActive++; $w("  CONFLICT driver#{$d->id} {$d->name} busy but 0 active shipments"); }
    if ($d->status === 'available' && $n > 0) { $availWithActive++; $w("  CONFLICT driver#{$d->id} {$d->name} available but $n active shipments"); }
}
$w("busy drivers with no active shipment = $busyNoActive");
$w("available drivers WITH active shipment = $availWithActive");

/* ── ReportService summary vs DB ── */
$w("\n== ReportService(all) vs DB ==");
[$f, $t] = ReportService::parsePeriod('all');
$r = app(ReportService::class)->build($f, $t, 'all');
$s = $r['summary'];
$w("summary.registered={$s['registered']} vs DB total=$total  " . ($s['registered'] === $total ? 'OK' : 'MISMATCH'));
$dbDelivered = Shipment::where('status', 'delivered')->count();
$w("summary.delivered={$s['delivered']} vs DB delivered=$dbDelivered  " . ($s['delivered'] === $dbDelivered ? 'OK' : 'MISMATCH'));
$dbFailed = Shipment::where('status', 'failed')->count();
$w("summary.failed={$s['failed']} vs DB failed=$dbFailed  " . ($s['failed'] === $dbFailed ? 'OK' : 'MISMATCH'));
$w("allTouched rows=" . $r['allTouched']->count() . " vs DB total=$total  " . ($r['allTouched']->count() === $total ? 'OK' : 'MISMATCH'));

/* ── Driver summary vs DB groupBy ── */
$w("\n== driverSummary vs DB ==");
foreach ($r['driverSummary'] as $ds) {
    $did = $ds['driver']->id;
    $dbTotal = Shipment::where('driver_id', $did)->count();
    $dbDel = Shipment::where('driver_id', $did)->where('status', 'delivered')->count();
    $ok = ($ds['total'] === $dbTotal && $ds['delivered'] === $dbDel) ? 'OK' : 'MISMATCH';
    $w("driver#{$did} {$ds['driver']->name}: report total={$ds['total']}/del={$ds['delivered']} vs DB total=$dbTotal/del=$dbDel  $ok");
}

/* ── Password hashing ── */
$w("\n== Password hashing ==");
foreach (\App\Models\User::limit(3)->get() as $u) {
    $hashed = str_starts_with($u->password, '$2y$') || str_starts_with($u->password, '$argon2');
    $w("user#{$u->id} {$u->email} bcrypt/argon2 prefix=" . ($hashed ? 'YES' : 'NO(PLAINTEXT!)'));
}

fclose($out);
$conflicts = $c1 + $c2 + $c3 + $c4 + $busyNoActive + $availWithActive;
echo "\nTOTAL CONFLICTS = $conflicts\n";