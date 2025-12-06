<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) $table->string('first_name')->nullable();
            if (!Schema::hasColumn('users', 'last_name')) $table->string('last_name')->nullable();
            if (!Schema::hasColumn('users', 'address')) $table->text('address')->nullable();
            if (!Schema::hasColumn('users', 'phone')) $table->string('phone')->nullable();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'address', 'phone']);
        });
    }
};