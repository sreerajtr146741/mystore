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
            // New columns
            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable()->after('firstname');
            }
            if (!Schema::hasColumn('users', 'phoneno')) {
                $table->string('phoneno')->nullable()->after('email');
            }

            // Cleanup old columns (ignoring if they don't exist)
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->change(); // Make nullable instead of dropping to be safe
            }
            
            // We can rename phone to phoneno if we wanted data migration, 
            // but since we added phoneno above, we'll just check if phone exists
            // and perhaps migrate data or just leave it. 
            // For this quick fix, we just ensure new columns exist.
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['firstname', 'lastname', 'phoneno']);
        });
    }
};
