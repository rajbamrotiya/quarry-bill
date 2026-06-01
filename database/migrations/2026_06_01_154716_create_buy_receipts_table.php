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
        Schema::create('buy_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_number');
            $table->foreignId('material_type_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time');
            $table->integer('gross_weight');
            $table->integer('tare_weight');
            $table->integer('net_weight');
            $table->string('pass_number')->nullable();
            $table->string('royalty_number')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_receipts');
    }
};
