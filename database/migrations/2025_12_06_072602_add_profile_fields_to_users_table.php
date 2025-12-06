<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) $table->string('first_name')->nullable()->after('name');
            if (!Schema::hasColumn('users', 'last_name')) $table->string('last_name')->nullable()->after('first_name');
            if (!Schema::hasColumn('users', 'phone')) $table->string('phone')->nullable()->after('email');
            if (!Schema::hasColumn('users', 'address')) $table->text('address')->nullable()->after('phone');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone', 'address']);
        });
    }
}