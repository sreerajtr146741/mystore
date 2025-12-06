<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // php artisan make:migration add_avatar_to_users_table --table=users
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('avatar')->nullable()->after('email');
    });
}
public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('avatar');
    });
}

};
