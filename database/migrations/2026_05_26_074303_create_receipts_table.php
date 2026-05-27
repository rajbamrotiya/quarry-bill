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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id(); // This will be the pass number
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_number');
            $table->foreignId('material_type_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->decimal('gross_weight', 10, 3);
            $table->decimal('tare_weight', 10, 3);
            $table->decimal('net_weight', 10, 3);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
