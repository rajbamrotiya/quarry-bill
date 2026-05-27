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
        Schema::table('receipts', function (Blueprint $table) {
            $table->string('royalty_number')->nullable()->after('material_type_id');
            $table->decimal('payment_value', 10, 2)->nullable()->after('remarks');
            $table->string('payment_type')->nullable()->after('payment_value'); // cash, online
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropColumn(['royalty_number', 'payment_value', 'payment_type']);
        });
    }
};
