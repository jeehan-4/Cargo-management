<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('pending','assigned','in_transit','delivered','failed','cancelled','archived') NOT NULL DEFAULT 'pending'");
        } else {
            // SQLite (tests) does not support MODIFY COLUMN — widen the column instead
            Schema::table('shipments', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('pending','assigned','in_transit','delivered','failed') NOT NULL DEFAULT 'pending'");
        } else {
            Schema::table('shipments', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }
};
