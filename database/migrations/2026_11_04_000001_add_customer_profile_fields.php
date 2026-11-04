<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->decimal('monthly_business_potential', 15, 2)->nullable();
            $table->text('head_office_address')->nullable();
            $table->text('factory_address')->nullable();
            $table->unsignedInteger('machine_quantity')->nullable();
            $table->text('machine_details')->nullable();
        });

        Schema::create('customer_contact_persons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contact_id');
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('customer_machine_types', function (Blueprint $table) {
            $table->unsignedInteger('contact_id');
            $table->string('machine_type', 50);
            $table->primary(['contact_id', 'machine_type']);
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('customer_machine_brands', function (Blueprint $table) {
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('brand_id');
            $table->primary(['contact_id', 'brand_id']);
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_machine_brands');
        Schema::dropIfExists('customer_machine_types');
        Schema::dropIfExists('customer_contact_persons');
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['monthly_business_potential', 'head_office_address', 'factory_address', 'machine_quantity', 'machine_details']);
        });
    }
};
