<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products','discount_type')) {
                $table->enum('discount_type', ['percent','flat'])->nullable()->after('price');
            }
            if (!Schema::hasColumn('products','discount_value')) {
                $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            }
            if (!Schema::hasColumn('products','discount_starts_at')) {
                $table->timestamp('discount_starts_at')->nullable()->after('discount_value');
            }
            if (!Schema::hasColumn('products','discount_ends_at')) {
                $table->timestamp('discount_ends_at')->nullable()->after('discount_starts_at');
            }
            if (!Schema::hasColumn('products','is_discount_active')) {
                $table->boolean('is_discount_active')->default(false)->after('discount_ends_at');
            }
        });
    }
    public function down(): void {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type','discount_value','discount_starts_at','discount_ends_at','is_discount_active'
            ]);
        });
    }
};
