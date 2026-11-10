<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Driver;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CargoManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first() ?? User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@sl.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_dashboard_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_destinations_crud(): void
    {
        // Index
        $response = $this->actingAs($this->user)->get(route('destinations.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->user)->get(route('destinations.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->user)->post(route('destinations.store'), [
            'name' => 'مستودع الرياض',
            'city' => 'الرياض',
            'address' => 'شارع الملك فهد',
            'contact_name' => 'أحمد علي',
            'contact_phone' => '0501234567',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('destinations.index'));

        $destination = Destination::where('name', 'مستودع الرياض')->first();
        $this->assertNotNull($destination);

        // Show
        $response = $this->actingAs($this->user)->get(route('destinations.show', $destination));
        $response->assertStatus(200);

        // Edit
        $response = $this->actingAs($this->user)->get(route('destinations.edit', $destination));
        $response->assertStatus(200);

        // Update
        $response = $this->actingAs($this->user)->put(route('destinations.update', $destination), [
            'name' => 'مستودع الرياض المركزي',
            'city' => 'الرياض',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('destinations.index'));
    }

    public function test_drivers_crud(): void
    {
        // Index
        $response = $this->actingAs($this->user)->get(route('drivers.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->user)->get(route('drivers.create'));
        $response->assertStatus(200);

        // Store
        $uniquePhone = '05' . rand(10000000, 99999999);
        $uniqueLic = 'LIC-' . rand(10000, 99999);
        $uniqueEmail = 'driver' . rand(1000, 9999) . '@cargo.test';
        $response = $this->actingAs($this->user)->post(route('drivers.store'), [
            'name' => 'محمد السائق',
            'email' => $uniqueEmail,
            'password' => 'password123',
            'phone' => $uniquePhone,
            'license_number' => $uniqueLic,
            'status' => 'available',
        ]);
        $response->assertRedirect(route('drivers.index'));

        $driver = Driver::where('phone', $uniquePhone)->first();
        $this->assertNotNull($driver);

        // Show
        $response = $this->actingAs($this->user)->get(route('drivers.show', $driver));
        $response->assertStatus(200);

        // Edit
        $response = $this->actingAs($this->user)->get(route('drivers.edit', $driver));
        $response->assertStatus(200);
    }

    public function test_trucks_crud(): void
    {
        // Index
        $response = $this->actingAs($this->user)->get(route('trucks.index'));
        $response->assertStatus(200);

        // Create page
        $response = $this->actingAs($this->user)->get(route('trucks.create'));
        $response->assertStatus(200);

        // Store
        $plate = 'TRK-' . rand(1000, 9999);
        $response = $this->actingAs($this->user)->post(route('trucks.store'), [
            'plate_number' => $plate,
            'type' => 'تريلا 40 قدم',
            'capacity' => 25.5,
            'status' => 'active',
        ]);
        $response->assertRedirect(route('trucks.index'));

        $truck = Truck::where('plate_number', $plate)->first();
        $this->assertNotNull($truck);

        // Edit
        $response = $this->actingAs($this->user)->get(route('trucks.edit', $truck));
        $response->assertStatus(200);
    }

    public function test_shipments_flow(): void
    {
        $destination = Destination::create([
            'name' => 'ميناء جدة',
            'city' => 'جدة',
            'is_active' => true,
        ]);

        $uniquePhone = '05' . rand(10000000, 99999999);
        $driver = Driver::create([
            'name' => 'سائق الشحنات',
            'phone' => $uniquePhone,
            'license_number' => 'LIC-' . rand(10000, 99999),
            'status' => 'available',
        ]);

        // Index
        $response = $this->actingAs($this->user)->get(route('shipments.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($this->user)->get(route('shipments.create'));
        $response->assertStatus(200);

        // Store
        $response = $this->actingAs($this->user)->post(route('shipments.store'), [
            'cargo_type' => 'مواد غذائية',
            'weight' => 1500,
            'quantity' => 10,
            'unit' => 'طرد',
            'origin_port' => 'ميناء الملك عبد العزيز',
            'destination_id' => $destination->id,
            'notes' => 'شحنة اختبار',
        ]);
        
        $shipment = Shipment::latest('id')->first();
        $this->assertNotNull($shipment);
        $response->assertRedirect(route('shipments.show', $shipment));

        // Show
        $response = $this->actingAs($this->user)->get(route('shipments.show', $shipment));
        $response->assertStatus(200);

        // Assign driver
        $response = $this->actingAs($this->user)->post(route('shipments.assign', $shipment), [
            'driver_id' => $driver->id,
        ]);
        $response->assertSessionHas('success');
        $shipment->refresh();
        $this->assertEquals('assigned', $shipment->status);
        $this->assertEquals($driver->id, $shipment->driver_id);

        // Update status to in_transit
        $response = $this->actingAs($this->user)->post(route('shipments.status', $shipment), [
            'status' => 'in_transit',
            'notes' => 'خرجت الشحنة في الطريق',
        ]);
        $response->assertSessionHas('success');
        $shipment->refresh();
        $this->assertEquals('in_transit', $shipment->status);

        // Update status to delivered
        $response = $this->actingAs($this->user)->post(route('shipments.status', $shipment), [
            'status' => 'delivered',
            'notes' => 'تم التسليم بنجاح',
        ]);
        $response->assertSessionHas('success');
        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);
        $driver->refresh();
        $this->assertEquals('available', $driver->status);
    }

    public function test_reports_page_and_export(): void
    {
        // Index
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);

        // Export Excel (.xlsx)
        $response = $this->actingAs($this->user)->get(route('reports.export'));
        $response->assertStatus(200);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
        $this->assertStringStartsWith('PK', $response->getContent());
    }

    public function test_activity_logs_recorded(): void
    {
        $uniquePhone = '05' . rand(10000000, 99999999);
        $driver = Driver::create([
            'name' => 'سائق النشاط',
            'phone' => $uniquePhone,
            'license_number' => 'LIC-' . rand(10000, 99999),
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action_type' => 'create_driver',
            'driver_id' => $driver->id,
        ]);
    }

    public function test_settings_page_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.edit'));
        $response->assertStatus(200);
        $response->assertSee('إعدادات النظام');
    }

    public function test_settings_update_persists(): void
    {
        $response = $this->actingAs($this->user)->put(route('settings.update'), [
            'company_name'        => 'شركة الاختبار',
            'company_phone'       => '01234567',
            'company_address'     => 'الحديدة',
            'default_origin_port' => 'ميناء عدن',
            'report_footer'       => 'تذييل تجريبي',
        ]);
        $response->assertRedirect(route('settings.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('settings', ['key' => 'company_name', 'value' => 'شركة الاختبار']);
        $this->assertEquals('ميناء عدن', Setting::get('default_origin_port'));

        $this->actingAs($this->user)->get(route('settings.edit'))->assertSee('شركة الاختبار');
    }

    public function test_settings_validation_requires_company_name(): void
    {
        $response = $this->actingAs($this->user)->put(route('settings.update'), [
            'company_name'        => '',
            'default_origin_port' => '',
        ]);
        $response->assertSessionHasErrors(['company_name', 'default_origin_port']);
    }

    public function test_default_origin_port_applied_to_create_form(): void
    {
        Setting::set('default_origin_port', 'ميناء المخا');
        $response = $this->actingAs($this->user)->get(route('shipments.create'));
        $response->assertStatus(200);
        $response->assertSee('ميناء المخا');
    }

    public function test_activity_logs_page_renders_and_filters(): void
    {
        $driver = Driver::create([
            'name' => 'سائق السجل',
            'phone' => '05' . rand(10000000, 99999999),
            'license_number' => 'LIC-' . rand(10000, 99999),
            'status' => 'available',
        ]);

        $response = $this->actingAs($this->user)->get(route('activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('سائق السجل');

        $filtered = $this->actingAs($this->user)->get(route('activity-logs.index', ['action_type' => 'create_driver']));
        $filtered->assertStatus(200);
    }

    public function test_shipments_export_is_xlsx(): void
    {
        $response = $this->actingAs($this->user)->get(route('shipments.export'));
        $response->assertStatus(200);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));
        $this->assertStringStartsWith('PK', $response->getContent());
    }

    public function test_reassign_preserves_previous_driver_in_history(): void
    {
        $d1 = Driver::create(['name' => 'سائق أول', 'phone' => '05' . rand(10000000, 99999999), 'license_number' => 'LIC-' . rand(10000, 99999), 'status' => 'available']);
        $d2 = Driver::create(['name' => 'سائق ثانٍ', 'phone' => '05' . rand(10000000, 99999999), 'license_number' => 'LIC-' . rand(10000, 99999), 'status' => 'available']);
        $dest = Destination::create(['name' => 'وجهة الإسناد', 'city' => 'الحديدة', 'is_active' => true]);
        $shipment = Shipment::create([
            'shipment_number' => 'SHIP-REASSIGN-' . rand(1000, 9999),
            'cargo_type' => 'رز', 'unit' => 'طن', 'origin_port' => 'ميناء الحديدة',
            'destination_id' => $dest->id, 'status' => 'pending',
        ]);

        $this->actingAs($this->user)->post(route('shipments.assign', $shipment), ['driver_id' => $d1->id]);
        $this->actingAs($this->user)->post(route('shipments.assign', $shipment), ['driver_id' => $d2->id]);

        $shipment->refresh(); $d1->refresh(); $d2->refresh();
        $this->assertEquals($d2->id, $shipment->driver_id);
        $this->assertEquals('available', $d1->status);
        $this->assertEquals('busy', $d2->status);

        $log = \App\Models\ShipmentLog::where('shipment_id', $shipment->id)->where('notes', 'like', '%تغيير السائق%')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString($d1->name, $log->notes);
        $this->assertStringContainsString($d2->name, $log->notes);
    }

    private function makeShipment(string $status, ?int $driverId = null): Shipment
    {
        $dest = Destination::first() ?? Destination::create(['name' => 'وجهة', 'city' => 'الحديدة', 'is_active' => true]);
        return Shipment::create([
            'shipment_number' => 'SHIP-T-' . rand(10000, 99999),
            'cargo_type' => 'رز', 'unit' => 'طن', 'origin_port' => 'ميناء الحديدة',
            'destination_id' => $dest->id, 'status' => $status, 'driver_id' => $driverId,
            'assigned_at' => $driverId ? now() : null,
            'delivered_at' => $status === 'delivered' ? now() : null,
        ]);
    }

    public function test_cancel_releases_driver_and_logs(): void
    {
        $driver = Driver::create(['name' => 'سائق الإلغاء', 'phone' => '05' . rand(10000000, 99999999), 'license_number' => 'LIC-' . rand(10000, 99999), 'status' => 'busy']);
        $shipment = $this->makeShipment('assigned', $driver->id);

        $response = $this->actingAs($this->user)->post(route('shipments.cancel', $shipment), ['reason' => 'اختبار']);
        $response->assertSessionHas('success');

        $shipment->refresh(); $driver->refresh();
        $this->assertEquals('cancelled', $shipment->status);
        $this->assertEquals('available', $driver->status);
        $this->assertDatabaseHas('shipment_logs', ['shipment_id' => $shipment->id, 'new_status' => 'cancelled']);
    }

    public function test_cannot_cancel_delivered_shipment(): void
    {
        $shipment = $this->makeShipment('delivered', null);
        $response = $this->actingAs($this->user)->post(route('shipments.cancel', $shipment));
        $response->assertSessionHas('error');
        $shipment->refresh();
        $this->assertEquals('delivered', $shipment->status);
    }

    public function test_archive_completed_shipment_and_block_active(): void
    {
        $delivered = $this->makeShipment('delivered', null);
        $this->actingAs($this->user)->post(route('shipments.archive', $delivered))->assertSessionHas('success');
        $delivered->refresh();
        $this->assertEquals('archived', $delivered->status);
        $this->assertDatabaseHas('shipment_logs', ['shipment_id' => $delivered->id, 'new_status' => 'archived']);

        $active = $this->makeShipment('in_transit', null);
        $this->actingAs($this->user)->post(route('shipments.archive', $active))->assertSessionHas('error');
        $active->refresh();
        $this->assertEquals('in_transit', $active->status);
    }
}
