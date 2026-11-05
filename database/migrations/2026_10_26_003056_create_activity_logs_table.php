<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->string('action_type');
        $table->foreignId('shipment_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->text('old_values')->nullable();
        $table->text('new_values')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}
};
