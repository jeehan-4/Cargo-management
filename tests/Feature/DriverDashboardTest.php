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

/**
 * Comprehensive Driver Dashboard & Security Test Suite
 * Tests all required business workflows, security constraints, and edge cases.
 */
class DriverDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $driverUser;
    private Driver $driver;
    private Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Admin User
        $this->adminUser = User::factory()->create([
            'name'     => 'مدير النظام',
            'email'    => 'admin.test@cargo.test',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        // 2. Create Driver User
        $this->driverUser = User::factory()->create([
            'name'     => 'أحمد السائق',
            'email'    => 'driver.test@cargo.test',
            'password' => bcrypt('password123'),
            'role'     => 'driver',
        ]);

        // 3. Create Driver Record linked to Driver User
        $this->driver = Driver::create([
            'name'           => 'أحمد السائق',
            'phone'          => '0501112233',
            'license_number' => 'DL-2026-001',
            'status'         => 'available',
            'user_id'        => $this->driverUser->id,
        ]);

        // 4. Create Active Destination
        $this->destination = Destination::create([
            'name'          => 'مستودع الرياض المركزي',
            'city'          => 'الرياض',
            'address'       => 'طريق الملك فهد، حي المروج',
            'contact_name'  => 'محمد علي',
            'contact_phone' => '0559988776',
            'is_active'     => true,
        ]);
    }

    // ══════════════════════════════════════════════════════
    //  SCENARIO: FULL END-TO-END WORKFLOW
    // ══════════════════════════════════════════════════════

    /**
     * اختبار: يقوم المسؤول بإنشاء حساب سائق
     */
    public function test_admin_can_create_driver_account(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('drivers.create'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)->post(route('drivers.store'), [
            'name'           => 'سالم الدوسري',
            'email'          => 'salem.test@cargo.test',
            'password'       => 'password123',
            'phone'          => '0507771122',
            'license_number' => 'DL-TEST-777',
            'status'         => 'available',
        ]);

        $response->assertRedirect(route('drivers.index'));
        $this->assertDatabaseHas('drivers', [
            'name'           => 'سالم الدوسري',
            'phone'          => '0507771122',
            'license_number' => 'DL-TEST-777',
        ]);
    }

    /**
     * اختبار: يقوم المسؤول بإضافة شحنة جديدة
     */
    public function test_admin_can_create_new_shipment(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('shipments.store'), [
            'cargo_type'     => 'أجهزة إلكترونية',
            'weight'         => 8.5,
            'quantity'       => 150,
            'unit'           => 'طرد',
            'origin_port'    => 'ميناء جدة الإسلامي',
            'destination_id' => $this->destination->id,
            'notes'          => 'شحنة حساسة قابلة للكسر',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shipments', [
            'cargo_type'     => 'أجهزة إلكترونية',
            'status'         => 'pending',
            'destination_id' => $this->destination->id,
        ]);

        $createdShipment = Shipment::where('cargo_type', 'أجهزة إلكترونية')->first();
        $this->assertNotNull($createdShipment);

        // التحقق من إنشاء سجل أولي للشحنة
        $this->assertDatabaseHas('shipment_logs', [
            'shipment_id' => $createdShipment->id,
            'new_status'  => 'pending',
        ]);
    }

    /**
     * اختبار: يسند المسؤول الشحنة إلى السائق
     */
    public function test_admin_can_assign_shipment_to_driver(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-2026-TEST1',
            'cargo_type'      => 'مواد كيميائية معبأة',
            'unit'            => 'برميل',
            'origin_port'     => 'ميناء الدمام',
            'destination_id'  => $this->destination->id,
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('shipments.assign', $shipment), [
            'driver_id' => $this->driver->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $shipment->refresh();
        $this->assertEquals('assigned', $shipment->status);
        $this->assertEquals($this->driver->id, $shipment->driver_id);
        $this->assertNotNull($shipment->assigned_at);

        // يجب أن تتغير حالة السائق إلى مشغول (busy)
        $this->driver->refresh();
        $this->assertEquals('busy', $this->driver->status);

        // يجب تسجيل الإسناد في سجل العمليات
        $this->assertDatabaseHas('shipment_logs', [
            'shipment_id' => $shipment->id,
            'new_status'  => 'assigned',
        ]);
    }

    /**
     * اختبار: يسجل السائق الدخول وتظهر الشحنة المسندة إليه فقط
     */
    public function test_driver_logs_in_and_sees_only_his_assigned_shipment(): void
    {
        // 1. شحنة مسندة للسائق الحالي
        $myShipment = Shipment::create([
            'shipment_number' => 'MY-SHIP-100',
            'cargo_type'      => 'أدوية ومستلزمات طبية',
            'unit'            => 'صندوق',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $this->driver->id,
            'assigned_at'     => now(),
        ]);

        // 2. سائق آخر وشحنة مسندة له
        $otherDriver = Driver::create([
            'name'           => 'خالد الحربي',
            'phone'          => '0508889900',
            'license_number' => 'DL-OTHER-888',
            'status'         => 'busy',
        ]);
        $otherShipment = Shipment::create([
            'shipment_number' => 'OTHER-SHIP-200',
            'cargo_type'      => 'قطع غيار سيارات سرية',
            'unit'            => 'كرتون',
            'origin_port'     => 'ميناء الجبيل',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $otherDriver->id,
            'assigned_at'     => now(),
        ]);

        // السائق يسجل الدخول ويفتح لوحته
        $response = $this->actingAs($this->driverUser)->get(route('driver.dashboard'));
        $response->assertStatus(200);

        // تظهر شحنته فقط ولا تظهر شحنة السائق الآخر
        $response->assertSee('MY-SHIP-100');
        $response->assertSee('أدوية ومستلزمات طبية');
        $response->assertDontSee('OTHER-SHIP-200');
        $response->assertDontSee('قطع غيار سيارات سرية');

        // صفحة كل الشحنات أيضاً
        $responseAll = $this->actingAs($this->driverUser)->get(route('driver.shipments'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('MY-SHIP-100');
        $responseAll->assertDontSee('OTHER-SHIP-200');
    }

    /**
     * اختبار: يضغط السائق على بدء الرحلة فتتغير حالة الشحنة إلى قيد النقل
     */
    public function test_driver_starts_trip_and_status_changes_to_in_transit(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-TRIP-01',
            'cargo_type'      => 'بضائع استهلاكية',
            'unit'            => 'كرتون',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $this->driver->id,
            'assigned_at'     => now(),
        ]);

        $response = $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $shipment), [
            'status' => 'in_transit',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $shipment->refresh();
        $this->assertEquals('in_transit', $shipment->status);
        $this->assertNotNull($shipment->started_at);

        // التحقق من تدوين السجل باسم السائق وتفاصيل بدء الرحلة
        $this->assertDatabaseHas('shipment_logs', [
            'shipment_id' => $shipment->id,
            'old_status'  => 'assigned',
            'new_status'  => 'in_transit',
            'user_id'     => $this->driverUser->id,
        ]);
    }

    /**
     * اختبار: تظهر الحالة الجديدة (قيد النقل) في لوحة تحكم المسؤول
     */
    public function test_in_transit_status_appears_in_admin_panel(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-ADMIN-CHECK',
            'cargo_type'      => 'حديد تسليح',
            'unit'            => 'طن',
            'origin_port'     => 'ميناء ينبع',
            'destination_id'  => $this->destination->id,
            'status'          => 'in_transit',
            'driver_id'       => $this->driver->id,
            'started_at'      => now(),
        ]);

        // المسؤول يرى الشحنة بحالتها قيد النقل في القائمة العامة
        $responseIndex = $this->actingAs($this->adminUser)->get(route('shipments.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('SHIP-ADMIN-CHECK');
        $responseIndex->assertSee('قيد التوصيل');

        // المسؤول يرى التفاصيل والسائق المسند في صفحة الشحنة
        $responseShow = $this->actingAs($this->adminUser)->get(route('shipments.show', $shipment));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('قيد التوصيل');
        $responseShow->assertSee($this->driver->name);
    }

    /**
     * اختبار: يضغط السائق على تم التسليم فتتغير الحالة إلى تم التسليم
     * ويتم تسجيل وقت التسليم واسم السائق في سجل العمليات
     * وتتحدث حالة السائق إلى متاح إذا لم تكن لديه مهمة أخرى
     */
    public function test_driver_delivers_shipment_logs_details_and_becomes_available(): void
    {
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-DELIVER-OK',
            'cargo_type'      => 'أثاث مكتبي',
            'unit'            => 'قطعة',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'in_transit',
            'driver_id'       => $this->driver->id,
            'assigned_at'     => now()->subHours(4),
            'started_at'      => now()->subHours(2),
        ]);

        $this->driver->update(['status' => 'busy']);

        $response = $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $shipment), [
            'status' => 'delivered',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 1. تغير الحالة إلى تم التسليم وتوثيق وقت التسليم
        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);
        $this->assertNotNull($shipment->delivered_at);

        // 2. التحقق من تسجيل اسم السائق ووقت التسليم في سجل العمليات (shipment_logs)
        $latestLog = ShipmentLog::where('shipment_id', $shipment->id)
            ->where('new_status', 'delivered')
            ->first();

        $this->assertNotNull($latestLog);
        $this->assertEquals($this->driverUser->id, $latestLog->user_id);
        $this->assertStringContainsString($this->driver->name, $latestLog->notes);
        $this->assertStringContainsString($shipment->delivered_at->format('Y-m-d'), $latestLog->notes);

        // 3. تحديث حالة السائق إلى متاح (available) لعدم وجود مهام نشطة أخرى
        $this->driver->refresh();
        $this->assertEquals('available', $this->driver->status);
    }

    /**
     * اختبار: السائق يظل مشغولاً إذا كانت لديه شحنة أخرى غير مكتملة
     */
    public function test_driver_stays_busy_if_another_shipment_is_still_active(): void
    {
        // الشحنة الأولى: يتم تسليمها
        $shipment1 = Shipment::create([
            'shipment_number' => 'SHIP-ACTIVE-1',
            'cargo_type'      => 'شحنة أولى',
            'unit'            => 'طرد',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'in_transit',
            'driver_id'       => $this->driver->id,
            'started_at'      => now()->subHours(2),
        ]);

        // الشحنة الثانية: ما زالت مسندة ولم تنتهِ
        $shipment2 = Shipment::create([
            'shipment_number' => 'SHIP-ACTIVE-2',
            'cargo_type'      => 'شحنة ثانية قائمة',
            'unit'            => 'طرد',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $this->driver->id,
            'assigned_at'     => now(),
        ]);

        $this->driver->update(['status' => 'busy']);

        // تسليم الشحنة الأولى فقط
        $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $shipment1), [
            'status' => 'delivered',
        ]);

        // يجب أن يظل السائق busy لأن لديه شحنة ثانية مسندة
        $this->driver->refresh();
        $this->assertEquals('busy', $this->driver->status);
    }

    // ══════════════════════════════════════════════════════
    //  SECURITY TESTS: اختبارات الأمان
    // ══════════════════════════════════════════════════════

    /**
     * أمان 1: محاولة الدخول إلى لوحة المسؤول بحساب سائق -> يعاد توجيهه لبوابة السائق
     * وصفحات الإدارة تعطي 403 Forbidden
     */
    public function test_driver_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->driverUser)->get(route('dashboard'));
        $response->assertRedirect(route('driver.dashboard'));

        $responseShipments = $this->actingAs($this->driverUser)->get(route('shipments.index'));
        $responseShipments->assertStatus(403);

        $responseDrivers = $this->actingAs($this->driverUser)->get(route('drivers.index'));
        $responseDrivers->assertStatus(403);

        $responseTrucks = $this->actingAs($this->driverUser)->get(route('trucks.index'));
        $responseTrucks->assertStatus(403);

        $responseReports = $this->actingAs($this->driverUser)->get(route('reports.index'));
        $responseReports->assertStatus(403);
    }

    /**
     * أمان 2: محاولة الوصول إلى شحنة مسندة لسائق آخر -> 403 Forbidden
     */
    public function test_driver_cannot_access_shipment_assigned_to_another_driver(): void
    {
        $otherDriver = Driver::create([
            'name'           => 'سائق ثانٍ',
            'phone'          => '0505554433',
            'license_number' => 'DL-SEC-555',
            'status'         => 'busy',
        ]);

        $otherShipment = Shipment::create([
            'shipment_number' => 'SEC-SHIP-OTHER',
            'cargo_type'      => 'شحنة سرية لسائق آخر',
            'unit'            => 'صندوق',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $otherDriver->id,
            'assigned_at'     => now(),
        ]);

        $response = $this->actingAs($this->driverUser)->get(route('driver.shipments.show', $otherShipment));
        $response->assertStatus(403);
    }

    /**
     * أمان 3: محاولة تحديث حالة شحنة غير مسندة للسائق -> 403 Forbidden
     */
    public function test_driver_cannot_update_status_of_unassigned_or_others_shipment(): void
    {
        // شحنة لسائق آخر
        $otherDriver = Driver::create([
            'name'           => 'سائق منافس',
            'phone'          => '0502223344',
            'license_number' => 'DL-SEC-222',
            'status'         => 'busy',
        ]);
        $otherShipment = Shipment::create([
            'shipment_number' => 'SEC-SHIP-UPDATE-1',
            'cargo_type'      => 'بضائع سائق آخر',
            'unit'            => 'طن',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $otherDriver->id,
            'assigned_at'     => now(),
        ]);

        $res1 = $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $otherShipment), [
            'status' => 'in_transit',
        ]);
        $res1->assertStatus(403);
        $otherShipment->refresh();
        $this->assertEquals('assigned', $otherShipment->status);

        // شحنة بدون سائق إطلاقاً (معلقة)
        $unassignedShipment = Shipment::create([
            'shipment_number' => 'SEC-SHIP-UNASSIGNED',
            'cargo_type'      => 'شحنة بدون سائق',
            'unit'            => 'طن',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'pending',
            'driver_id'       => null,
        ]);

        $res2 = $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $unassignedShipment), [
            'status' => 'in_transit',
        ]);
        $res2->assertStatus(403);
    }

    /**
     * أمان 4: محاولة تنفيذ عملية التسليم مرتين (أو بعد اكتمال الشحنة) -> 403 Forbidden
     */
    public function test_driver_cannot_deliver_shipment_twice(): void
    {
        $deliveredTime = now()->subHours(2);
        $shipment = Shipment::create([
            'shipment_number' => 'DELIVER-TWICE-TEST',
            'cargo_type'      => 'شحنة مسلّمة مسبقاً',
            'unit'            => 'طرد',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'delivered', // مسلّمة بالفعل
            'driver_id'       => $this->driver->id,
            'assigned_at'     => now()->subHours(5),
            'started_at'      => now()->subHours(3),
            'delivered_at'    => $deliveredTime,
        ]);

        // محاولة التسليم مرة ثانية
        $response = $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $shipment), [
            'status' => 'delivered',
        ]);

        // يجب الرفض برمز 403
        $response->assertStatus(403);

        // التأكد من عدم تغيير وقت التسليم الأصلي
        $shipment->refresh();
        $this->assertEquals($deliveredTime->format('Y-m-d H:i'), $shipment->delivered_at->format('Y-m-d H:i'));
    }

    /**
     * أمان 5: اختبار حساب السائق بعد إيقافه من المسؤول -> 403 Forbidden
     */
    public function test_driver_cannot_access_or_operate_when_deactivated_by_admin(): void
    {
        // المسؤول يقوم بتعطيل حساب السائق
        $this->driver->update(['status' => 'inactive']);

        // 1. محاولة فتح لوحة التحكم الرئيسية للسائق
        $resDashboard = $this->actingAs($this->driverUser)->get(route('driver.dashboard'));
        $resDashboard->assertStatus(403);

        // 2. محاولة فتح سجل الشحنات
        $resShipments = $this->actingAs($this->driverUser)->get(route('driver.shipments'));
        $resShipments->assertStatus(403);

        // 3. محاولة فتح الملف الشخصي
        $resProfile = $this->actingAs($this->driverUser)->get(route('driver.profile'));
        $resProfile->assertStatus(403);

        // 4. محاولة تحديث شحنة مسندة له أثناء إيقافه
        $shipment = Shipment::create([
            'shipment_number' => 'INACTIVE-DRIVER-SHIP',
            'cargo_type'      => 'شحنة مجمدة',
            'unit'            => 'صندوق',
            'origin_port'     => 'ميناء جدة',
            'destination_id'  => $this->destination->id,
            'status'          => 'assigned',
            'driver_id'       => $this->driver->id,
            'assigned_at'     => now(),
        ]);

        $resStatus = $this->actingAs($this->driverUser)->post(route('driver.shipments.status', $shipment), [
            'status' => 'in_transit',
        ]);
        $resStatus->assertStatus(403);

        // الحالة لم تتغير
        $shipment->refresh();
        $this->assertEquals('assigned', $shipment->status);
    }

    /**
     * أمان إضافي: الزائر غير المسجل لا يمكنه الوصول إلى لوحة السائق
     */
    public function test_guest_is_redirected_to_login_when_accessing_driver_pages(): void
    {
        $response = $this->get(route('driver.dashboard'));
        $response->assertRedirect(route('login'));
    }

    /**
     * أمان إضافي: المسؤول لا يمكنه فتح لوحة السائق (فصل الأدوار)
     */
    public function test_admin_cannot_access_driver_portal(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('driver.dashboard'));
        $response->assertStatus(403);
    }
}
