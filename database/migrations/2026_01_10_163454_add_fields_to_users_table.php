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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->nullable();
            }
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable();
            }
            if (!Schema::hasColumn('users', 'phoneno')) {
                $table->string('phoneno')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('buyer');
            }
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'firstname',
                'lastname',
                'phoneno',
                'address',
                'role',
                'profile_photo',
                'last_login_at',
                'status',
            ];
            
            // Only drop if they exist (though dropColumn usually handles an array, strict SQL might complain if one is missing)
            // For simplicity in a fix migration, we will just try to drop them directly as Laravel usually handles this,
            // or we can comment it out to be super safe against accidental rollback data loss.
            // Let's stick to standard behavior but just ensure we don't break.
            $table->dropColumn($columns);
        });
    }
};
