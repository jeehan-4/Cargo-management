<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number')->unique(); // SHIP-2026-0001
            $table->string('cargo_type');               // أرز، دقيق، سكر ...
            $table->decimal('weight', 10, 2)->nullable(); // بالطن
            $table->integer('quantity')->nullable();      // بالكيس / الوحدة
            $table->string('unit')->default('طن');
            $table->string('origin_port')->default('الميناء الرئيسي');
            $table->foreignId('destination_id')->constrained('destinations');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->enum('status', [
                'pending',    // بانتظار التوزيع
                'assigned',   // تم التكليف
                'in_transit', // قيد النقل
                'delivered',  // تم التسليم
                'failed',     // متعذر التسليم
            ])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
