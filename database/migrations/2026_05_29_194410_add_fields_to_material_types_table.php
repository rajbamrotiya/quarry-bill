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
        Schema::table('material_types', function (Blueprint $table) {
            $table->string('hsn_code')->nullable()->after('name');
            $table->decimal('unit_rate', 15, 2)->nullable()->after('hsn_code');
            $table->text('other_information')->nullable()->after('unit_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_types', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'unit_rate', 'other_information']);
        });
    }
};
