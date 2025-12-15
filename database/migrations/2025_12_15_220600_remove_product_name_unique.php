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
        Schema::table('products', function (Blueprint $table) {
            // Attempt to drop unique index if it exists
            // Common convention: table_column_unique
            try {
                $table->dropUnique(['name']);
            } catch (\Exception $e) {
                // Ignore if index doesn't exist
            }
            try {
                $table->dropUnique('products_name_unique');
            } catch (\Exception $e) {
                // Ignore
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't enforce unique again to avoid breaking duplicates created in the meantime
    }
};
