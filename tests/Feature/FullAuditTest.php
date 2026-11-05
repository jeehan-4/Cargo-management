<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@sl.com',
            'password' => bcrypt('12345678'),
        ]);
    }

    /* ════════════════════════════════════════════
       1. تسجيل الدخول والخروج
    ════════════════════════════════════════════ */

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('تسجيل الدخول');
    }

    public function test_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@sl.com',
            'password' => '12345678',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->user);
    }

    public function test_login_with_wrong_password(): void
    {
        $this->post('/login', [
            'email' => 'admin@sl.com',
            'password' => 'wrongpassword',
        ]);
        $this->assertGuest();
    }

    public function test_logout(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /* ════════════════════════════════════════════
       2. حماية الصفحات من الوصول غير المصرح به
    ════════════════════════════════════════════ */

    public function test_guest_cannot_access_protected_routes(): void
    {
        $routes = [
            '/dashboard', '/shipments', '/shipments/create',
            '/drivers', '/drivers/create',
            '/trucks', '/trucks/create',
            '/destinations', '/destinations/create',
            '/reports', '/reports/export',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $this->actingAs($this->user)->get('/dashboard')->assertStatus(200);
    }

    /* ════════════════════════════════════════════
       3. إضافة الشحنات وتعديلها وعرض تفاصيلها
    ════════════════════════════════════════════ */

    public function test_create_shipment(): void
    {
        $dest = Destination::create(['name' => 'ميناء جدة', 'city' => 'جدة', 'is_active' => true]);

        $response = $this->actingAs($this->user)->post(route('shipments.store'), [
            'cargo_type' => 'أرز',
            'weight' => 500,
            'quantity' => 100,
            'unit' => 'طن',
            'origin_port' => 'ميناء الملك عبد العزيز',
            'destination_id' => $dest->id,
        ]);

        $shipment = Shipment::latest('id')->first();
        $this->assertNotNull($shipment);
        $this->assertEquals('pending', $shipment->status);
        $response->assertRedirect(route('shipments.show', $shipment));
    }

    public function test_edit_shipment(): void
    {
        $dest = Destination::create(['name' => 'ميناء ينبع', 'city' => 'ينبع', 'is_active' => true]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-2026-9999',
            'cargo_type' => 'سكر',
            'weight' => 200,
            'unit' => 'طن',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->put(route('shipments.update', $shipment), [
            'cargo_type' => 'دقيق',
            'weight' => 300,
            'unit' => 'طن',
            'origin_port' => 'الميناء الرئيسي',
            'destination_id' => $dest->id,
        ]);

        $response->assertRedirect(route('shipments.show', $shipment));
        $shipment->refresh();
        $this->assertEquals('دقيق', $shipment->cargo_type);
        $this->assertEquals(300, $shipment->weight);
    }

    public function test_show_shipment_details(): void
    {
        $dest = Destination::create(['name' => 'ميناء الدمام', 'city' => 'الدمام', 'is_active' => true]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-2026-0001',
            'cargo_type' => 'مواد غذائية',
            'unit' => 'طرد',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->get(route('shipments.show', $shipment));
        $response->assertStatus(200);
        $response->assertSee('SHIP-2026-0001');
        $response->assertSee('مواد غذائية');
    }

    /* ════════════════════════════════════════════
       4. توليد أرقام فريدة للشحنات
    ════════════════════════════════════════════ */

    public function test_shipment_number_generation_unique(): void
    {
        $dest = Destination::create(['name' => 'الرياض', 'city' => 'الرياض', 'is_active' => true]);

        $this->actingAs($this->user)->post(route('shipments.store'), [
            'cargo_type' => 'بضاعة 1',
            'unit' => 'طرد',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
        ]);
        $this->actingAs($this->user)->post(route('shipments.store'), [
            'cargo_type' => 'بضاعة 2',
            'unit' => 'طرد',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
        ]);

        $shipments = Shipment::all();
        $this->assertEquals(2, $shipments->count());
        $this->assertNotEquals($shipments[0]->shipment_number, $shipments[1]->shipment_number);
        $this->assertMatchesRegularExpression('/^SHIP-\d{4}-\d{4}$/', $shipments[0]->shipment_number);
        $this->assertMatchesRegularExpression('/^SHIP-\d{4}-\d{4}$/', $shipments[1]->shipment_number);
    }

    /* ════════════════════════════════════════════
       5. إدارة السائقين والشاحنات والوجهات
    ════════════════════════════════════════════ */

    public function test_drivers_crud(): void
    {
        // Create
        $response = $this->actingAs($this->user)->post(route('drivers.store'), [
            'name' => 'محمد أحمد',
            'phone' => '0501234567',
            'license_number' => 'LIC-001',
            'status' => 'available',
        ]);
        $response->assertRedirect(route('drivers.index'));
        $driver = Driver::where('phone', '0501234567')->first();
        $this->assertNotNull($driver);

        // Show
        $this->actingAs($this->user)->get(route('drivers.show', $driver))->assertStatus(200);

        // Update
        $this->actingAs($this->user)->put(route('drivers.update', $driver), [
            'name' => 'محمد أحمد المطيري',
            'phone' => '0501234567',
            'license_number' => 'LIC-001',
            'status' => 'available',
        ]);
        $driver->refresh();
        $this->assertEquals('محمد أحمد المطيري', $driver->name);

        // Delete
        $this->actingAs($this->user)->delete(route('drivers.destroy', $driver))
            ->assertRedirect(route('drivers.index'));
        $this->assertDatabaseMissing('drivers', ['id' => $driver->id]);
    }

    public function test_trucks_crud(): void
    {
        // Create
        $response = $this->actingAs($this->user)->post(route('trucks.store'), [
            'plate_number' => 'ABC-1234',
            'type' => 'تريلا 40 قدم',
            'capacity' => 25,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('trucks.index'));
        $truck = Truck::where('plate_number', 'ABC-1234')->first();
        $this->assertNotNull($truck);

        // Show
        $this->actingAs($this->user)->get(route('trucks.show', $truck))->assertStatus(200);

        // Update
        $this->actingAs($this->user)->put(route('trucks.update', $truck), [
            'plate_number' => 'ABC-1234',
            'type' => 'تريلا 20 قدم',
            'capacity' => 15,
            'status' => 'active',
        ]);
        $truck->refresh();
        $this->assertEquals('تريلا 20 قدم', $truck->type);

        // Delete
        $this->actingAs($this->user)->delete(route('trucks.destroy', $truck))
            ->assertRedirect(route('trucks.index'));
        $this->assertDatabaseMissing('trucks', ['id' => $truck->id]);
    }

    public function test_destinations_crud(): void
    {
        // Create
        $response = $this->actingAs($this->user)->post(route('destinations.store'), [
            'name' => 'مستودع الرياض',
            'city' => 'الرياض',
            'contact_name' => 'خالد',
            'contact_phone' => '0509876543',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('destinations.index'));
        $dest = Destination::where('name', 'مستودع الرياض')->first();
        $this->assertNotNull($dest);

        // Show
        $this->actingAs($this->user)->get(route('destinations.show', $dest))->assertStatus(200);

        // Update
        $this->actingAs($this->user)->put(route('destinations.update', $dest), [
            'name' => 'مستودع الرياض الرئيسي',
            'city' => 'الرياض',
            'is_active' => '1',
        ]);
        $dest->refresh();
        $this->assertEquals('مستودع الرياض الرئيسي', $dest->name);

        // Delete
        $this->actingAs($this->user)->delete(route('destinations.destroy', $dest))
            ->assertRedirect(route('destinations.index'));
    }

    /* ════════════════════════════════════════════
       6. منع تعيين سائق لديه رحلة غير مكتملة
    ════════════════════════════════════════════ */

    public function test_busy_driver_cannot_be_assigned(): void
    {
        $dest = Destination::create(['name' => 'الدمام', 'city' => 'الدمام', 'is_active' => true]);
        $driver = Driver::create([
            'name' => 'سائق مشغول',
            'phone' => '0551112222',
            'license_number' => 'LIC-BUSY',
            'status' => 'busy',
        ]);

        // Create shipment1 already assigned to the driver
        $shipment1 = Shipment::create([
            'shipment_number' => 'SHIP-2026-BUSY1',
            'cargo_type' => 'شحنة 1',
            'unit' => 'طن',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
            'driver_id' => $driver->id,
            'status' => 'in_transit',
        ]);

        // Create shipment2 pending
        $shipment2 = Shipment::create([
            'shipment_number' => 'SHIP-2026-BUSY2',
            'cargo_type' => 'شحنة 2',
            'unit' => 'طن',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
            'status' => 'pending',
        ]);

        // Try to assign the busy driver
        $response = $this->actingAs($this->user)->post(route('shipments.assign', $shipment2), [
            'driver_id' => $driver->id,
        ]);

        $response->assertSessionHas('error');
        $shipment2->refresh();
        $this->assertEquals('pending', $shipment2->status);
        $this->assertNull($shipment2->driver_id);
    }

    /* ════════════════════════════════════════════
       7. تحديث حالات الشحنات بطريقة صحيحة
    ════════════════════════════════════════════ */

    public function test_shipment_status_flow(): void
    {
        $dest = Destination::create(['name' => 'جدة', 'city' => 'جدة', 'is_active' => true]);
        $driver = Driver::create([
            'name' => 'سائق التسليم',
            'phone' => '0559998877',
            'license_number' => 'LIC-FLOW',
            'status' => 'available',
        ]);

        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-2026-FLOW',
            'cargo_type' => 'شحنة اختبار التدفق',
            'unit' => 'طرد',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
            'status' => 'pending',
        ]);

        // Step 1: Assign driver
        $this->actingAs($this->user)->post(route('shipments.assign', $shipment), [
            'driver_id' => $driver->id,
        ]);
        $shipment->refresh();
        $this->assertEquals('assigned', $shipment->status);
        $this->assertNotNull($shipment->assigned_at);

        // Step 2: Start transit
        $this->actingAs($this->user)->post(route('shipments.status', $shipment), [
            'status' => 'in_transit',
        ]);
        $shipment->refresh();
        $this->assertEquals('in_transit', $shipment->status);
        $this->assertNotNull($shipment->started_at);

        // Step 3: Deliver
        $this->actingAs($this->user)->post(route('shipments.status', $shipment), [
            'status' => 'delivered',
        ]);
        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);
        $this->assertNotNull($shipment->delivered_at);

        // Driver should be available again
        $driver->refresh();
        $this->assertEquals('available', $driver->status);
    }

    /* ════════════════════════════════════════════
       8. تسجيل العمليات مع التاريخ والوقت
    ════════════════════════════════════════════ */

    public function test_shipment_logs_recorded(): void
    {
        $dest = Destination::create(['name' => 'ميناء', 'city' => 'مدينة', 'is_active' => true]);

        $this->actingAs($this->user)->post(route('shipments.store'), [
            'cargo_type' => 'بضاعة مسجلة',
            'unit' => 'طرد',
            'origin_port' => 'الميناء',
            'destination_id' => $dest->id,
        ]);

        $shipment = Shipment::latest('id')->first();
        $log = ShipmentLog::where('shipment_id', $shipment->id)->first();
        $this->assertNotNull($log);
        $this->assertNull($log->old_status);
        $this->assertEquals('pending', $log->new_status);
        $this->assertEquals($this->user->id, $log->user_id);
        $this->assertNotNull($log->created_at);
    }

    public function test_activity_log_observer_records(): void
    {
        $driver = Driver::create([
            'name' => 'سائق المراقب',
            'phone' => '0544443333',
            'license_number' => 'LIC-OBS',
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action_type' => 'create_driver',
            'driver_id' => $driver->id,
        ]);
    }

    /* ════════════════════════════════════════════
       9. صحة الإحصائيات في لوحة التحكم
    ════════════════════════════════════════════ */

    public function test_dashboard_statistics(): void
    {
        $dest = Destination::create(['name' => 'وجهة', 'city' => 'مدينة', 'is_active' => true]);

        Shipment::create([
            'shipment_number' => 'SHIP-2026-S001',
            'cargo_type' => 'أرز', 'unit' => 'طن', 'origin_port' => 'الميناء',
            'destination_id' => $dest->id, 'status' => 'pending',
        ]);
        Shipment::create([
            'shipment_number' => 'SHIP-2026-S002',
            'cargo_type' => 'سكر', 'unit' => 'طن', 'origin_port' => 'الميناء',
            'destination_id' => $dest->id, 'status' => 'delivered',
            'delivered_at' => now(),
        ]);
        Shipment::create([
            'shipment_number' => 'SHIP-2026-S003',
            'cargo_type' => 'دقيق', 'unit' => 'طن', 'origin_port' => 'الميناء',
            'destination_id' => $dest->id, 'status' => 'in_transit',
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        // The view receives stats from the controller
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 3
                && $stats['pending'] === 1
                && $stats['delivered'] === 1
                && $stats['in_transit'] === 1;
        });
    }

    /* ════════════════════════════════════════════
       10. دقة التقارير اليومية والأسبوعية والشهرية
    ════════════════════════════════════════════ */

    public function test_reports_periods(): void
    {
        // Today
        $this->actingAs($this->user)
            ->get(route('reports.index', ['period' => 'today']))
            ->assertStatus(200);

        // Week
        $this->actingAs($this->user)
            ->get(route('reports.index', ['period' => 'week']))
            ->assertStatus(200);

        // Month
        $this->actingAs($this->user)
            ->get(route('reports.index', ['period' => 'month']))
            ->assertStatus(200);

        // Custom
        $this->actingAs($this->user)
            ->get(route('reports.index', [
                'period' => 'custom',
                'from' => now()->subDays(30)->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
            ]))
            ->assertStatus(200);
    }

    /* ════════════════════════════════════════════
       11. تصدير تقارير PDF
    ════════════════════════════════════════════ */

    public function test_export_pdf(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.export'));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    /* ════════════════════════════════════════════
       12. سلامة العلاقات بين الجداول
    ════════════════════════════════════════════ */

    public function test_cannot_delete_destination_with_shipments(): void
    {
        $dest = Destination::create(['name' => 'وجهة مرتبطة', 'city' => 'مدينة', 'is_active' => true]);
        Shipment::create([
            'shipment_number' => 'SHIP-2026-REL1',
            'cargo_type' => 'بضاعة', 'unit' => 'طن', 'origin_port' => 'الميناء',
            'destination_id' => $dest->id, 'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)->delete(route('destinations.destroy', $dest));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('destinations', ['id' => $dest->id]);
    }

    public function test_cannot_delete_driver_with_active_shipments(): void
    {
        $dest = Destination::create(['name' => 'وجهة', 'city' => 'مدينة', 'is_active' => true]);
        $driver = Driver::create([
            'name' => 'سائق نشط',
            'phone' => '0566677788',
            'license_number' => 'LIC-ACT',
            'status' => 'busy',
        ]);
        Shipment::create([
            'shipment_number' => 'SHIP-2026-DEL1',
            'cargo_type' => 'بضاعة', 'unit' => 'طن', 'origin_port' => 'الميناء',
            'destination_id' => $dest->id, 'driver_id' => $driver->id,
            'status' => 'in_transit',
        ]);

        $response = $this->actingAs($this->user)->delete(route('drivers.destroy', $driver));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('drivers', ['id' => $driver->id]);
    }

    public function test_cannot_delete_active_shipment(): void
    {
        $dest = Destination::create(['name' => 'وجهة', 'city' => 'مدينة', 'is_active' => true]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-2026-NODR',
            'cargo_type' => 'بضاعة', 'unit' => 'طن', 'origin_port' => 'الميناء',
            'destination_id' => $dest->id, 'status' => 'in_transit',
        ]);

        $response = $this->actingAs($this->user)->delete(route('shipments.destroy', $shipment));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('shipments', ['id' => $shipment->id]);
    }

    /* ════════════════════════════════════════════
       13. التحقق من المدخلات (Validation)
    ════════════════════════════════════════════ */

    public function test_shipment_validation_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(route('shipments.store'), []);
        $response->assertSessionHasErrors(['cargo_type', 'unit', 'origin_port', 'destination_id']);
    }

    public function test_driver_validation_unique_phone(): void
    {
        Driver::create([
            'name' => 'سائق1', 'phone' => '0512345678', 'license_number' => 'LIC-U1', 'status' => 'available',
        ]);

        $response = $this->actingAs($this->user)->post(route('drivers.store'), [
            'name' => 'سائق2', 'phone' => '0512345678', 'license_number' => 'LIC-U2', 'status' => 'available',
        ]);
        $response->assertSessionHasErrors('phone');
    }

    public function test_truck_validation_unique_plate(): void
    {
        Truck::create(['plate_number' => 'XYZ-1111', 'type' => 'شاحنة', 'status' => 'active']);

        $response = $this->actingAs($this->user)->post(route('trucks.store'), [
            'plate_number' => 'XYZ-1111', 'type' => 'تريلا', 'status' => 'active',
        ]);
        $response->assertSessionHasErrors('plate_number');
    }

    /* ════════════════════════════════════════════
       14. عدم وجود أزرار بدون وظائف — لا شحنة تجريبية
    ════════════════════════════════════════════ */

    public function test_dashboard_no_hardcoded_data(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 0;
        });
    }

    public function test_dashboard_quick_actions_work(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee(route('shipments.create'));
        $response->assertSee(route('drivers.create'));
        $response->assertSee(route('trucks.create'));
        $response->assertSee(route('destinations.create'));
        $response->assertSee(route('reports.index'));
    }

    /* ════════════════════════════════════════════
       15. عرض RTL والعربية
    ════════════════════════════════════════════ */

    public function test_views_are_rtl_and_arabic(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);
    }

    public function test_login_page_is_rtl(): void
    {
        $response = $this->get('/login');
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);
    }

    /* ════════════════════════════════════════════
       16. عدم وجود رابط تسجيل مفتوح (register disabled)
    ════════════════════════════════════════════ */

    public function test_register_page_not_accessible(): void
    {
        $response = $this->get('/register');
        // Should NOT return 200 (either 404 or redirect)
        $this->assertNotEquals(200, $response->getStatusCode());
    }
}
