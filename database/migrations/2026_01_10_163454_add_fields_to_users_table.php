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
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('phoneno')->nullable();
            $table->string('address')->nullable();
            $table->string('role')->default('buyer');
            $table->string('profile_photo')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'firstname',
                'lastname',
                'phoneno',
                'address',
                'role',
                'profile_photo',
                'last_login_at',
                'status',
            ]);
        });
    }
};
