<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make role a short string we control in app logic
            $table->string('role', 20)->default('buyer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // If you previously had tinyint/boolean, revert it here if needed:
            // $table->boolean('role')->default(0)->change();
            // or comment this out if unsure
        });
    }
};
