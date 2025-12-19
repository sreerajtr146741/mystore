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
            if (!Schema::hasColumn('products', 'banner')) {
                $table->string('banner')->nullable()->after('image');
            }
            if (!Schema::hasColumn('products', 'banner_start_at')) {
                $table->timestamp('banner_start_at')->nullable()->after('banner');
            }
            if (!Schema::hasColumn('products', 'banner_end_at')) {
                $table->timestamp('banner_end_at')->nullable()->after('banner_start_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
             if (Schema::hasColumn('products', 'banner')) {
                $table->dropColumn('banner');
            }
            if (Schema::hasColumn('products', 'banner_start_at')) {
                $table->dropColumn('banner_start_at');
            }
            if (Schema::hasColumn('products', 'banner_end_at')) {
                $table->dropColumn('banner_end_at');
            }
        });
    }
};
