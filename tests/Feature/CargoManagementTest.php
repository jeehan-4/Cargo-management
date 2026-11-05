<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Driver;
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
        $response = $this->actingAs($this->user)->post(route('drivers.store'), [
            'name' => 'محمد السائق',
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

        // Export PDF
        $response = $this->actingAs($this->user)->get(route('reports.export'));
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
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
}
