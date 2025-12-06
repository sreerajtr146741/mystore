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
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained();
            }
            if (!Schema::hasColumn('products', 'original_price')) {
                $table->decimal('original_price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('products', 'discounted_price')) {
                $table->decimal('discounted_price', 10, 2)->nullable();
            }
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
