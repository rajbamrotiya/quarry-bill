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
        Schema::table('clients', function (Blueprint $table) {
            $table->text('address')->nullable()->after('email');
            $table->string('gst_number', 15)->nullable()->after('address');
            $table->string('pan_number', 10)->nullable()->after('gst_number');
            $table->string('country')->default('India')->after('pan_number');
            $table->string('state')->nullable()->after('country');
            $table->string('district')->nullable()->after('state');
            $table->text('other_information')->nullable()->after('district');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'gst_number',
                'pan_number',
                'country',
                'state',
                'district',
                'other_information',
            ]);
        });
    }
};
