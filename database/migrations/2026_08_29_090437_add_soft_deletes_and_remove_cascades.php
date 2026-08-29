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
        $tables = [
            'users',
            'clients',
            'material_types',
            'suppliers',
            'receipts',
            'receipt_histories',
            'buy_receipts',
            'buy_receipt_histories',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->foreign('client_id')->references('id')->on('clients');

            $table->dropForeign(['material_type_id']);
            $table->foreign('material_type_id')->references('id')->on('material_types');
        });

        Schema::table('receipt_histories', function (Blueprint $table) {
            $table->dropForeign(['receipt_id']);
            $table->foreign('receipt_id')->references('id')->on('receipts');
        });

        Schema::table('buy_receipts', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->foreign('supplier_id')->references('id')->on('suppliers');

            $table->dropForeign(['material_type_id']);
            $table->foreign('material_type_id')->references('id')->on('material_types');
        });

        Schema::table('buy_receipt_histories', function (Blueprint $table) {
            $table->dropForeign(['buy_receipt_id']);
            $table->foreign('buy_receipt_id')->references('id')->on('buy_receipts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();

            $table->dropForeign(['material_type_id']);
            $table->foreign('material_type_id')->references('id')->on('material_types')->cascadeOnDelete();
        });

        Schema::table('receipt_histories', function (Blueprint $table) {
            $table->dropForeign(['receipt_id']);
            $table->foreign('receipt_id')->references('id')->on('receipts')->cascadeOnDelete();
        });

        Schema::table('buy_receipts', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();

            $table->dropForeign(['material_type_id']);
            $table->foreign('material_type_id')->references('id')->on('material_types')->cascadeOnDelete();
        });

        Schema::table('buy_receipt_histories', function (Blueprint $table) {
            $table->dropForeign(['buy_receipt_id']);
            $table->foreign('buy_receipt_id')->references('id')->on('buy_receipts')->cascadeOnDelete();
        });

        $tables = [
            'users',
            'clients',
            'material_types',
            'suppliers',
            'receipts',
            'receipt_histories',
            'buy_receipts',
            'buy_receipt_histories',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
