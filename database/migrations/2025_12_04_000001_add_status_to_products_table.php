<?php

// database/migrations/2025_12_04_000001_add_status_to_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'status')) {
                // simple string; or use enum if you prefer
                $table->string('status', 20)->default('active')->after('price'); // active|draft
            }
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
