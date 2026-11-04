<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->json('assigned_to_users')->nullable()->after('assigned_to');
        });
    }

    public function down()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->dropColumn('assigned_to_users');
        });
    }
};
