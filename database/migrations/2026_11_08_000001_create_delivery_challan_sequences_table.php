<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('delivery_challan_sequences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('last_number')->default(0);
            $table->unique(['business_id', 'location_id'], 'delivery_challan_sequences_business_location_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_challan_sequences');
    }
};
