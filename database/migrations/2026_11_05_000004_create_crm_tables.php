<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('crm_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('assigned_user_id');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('contact_id');
            $table->decimal('monthly_potential', 22, 4)->default(0);
            $table->decimal('monthly_sales_target', 22, 4)->default(0);
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->text('remarks')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();
            $table->index(['business_id', 'year', 'month']);
            $table->unique(['business_id', 'assigned_user_id', 'contact_id', 'month', 'year'], 'crm_plan_unique_employee_business_period');
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('crm_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('contact_id');
            $table->date('visit_date');
            $table->time('visit_time');
            $table->longText('description');
            $table->timestamps();
            $table->index(['business_id', 'visit_date']);
            $table->index(['user_id', 'visit_date']);
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('crm_visit_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('visit_id');
            $table->string('document_path');
            $table->string('original_file_name');
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
            $table->foreign('visit_id')->references('id')->on('crm_visits')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('crm_visit_documents');
        Schema::dropIfExists('crm_visits');
        Schema::dropIfExists('crm_plans');
    }
};
