<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->unsignedInteger('assigned_to')->nullable()->after('name');
            $table->string('contact_person')->nullable()->after('assigned_to');
            $table->string('designation')->nullable()->after('contact_person');
            $table->string('mobile', 30)->nullable()->after('designation');
            $table->string('email')->nullable()->after('mobile');
        });
    }

    public function down()
    {
        Schema::table('customer_groups', function (Blueprint $table) {
            $table->dropColumn(['assigned_to', 'contact_person', 'designation', 'mobile', 'email']);
        });
    }
};
