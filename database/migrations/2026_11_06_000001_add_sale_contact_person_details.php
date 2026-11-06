<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('contact_person_id')->nullable()->after('customer_group_id');
            $table->string('contact_person_email')->nullable()->after('contact_person_id');
            $table->string('contact_person_mobile', 30)->nullable()->after('contact_person_email');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['contact_person_id', 'contact_person_email', 'contact_person_mobile']);
        });
    }
};
