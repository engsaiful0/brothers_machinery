<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quotation_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('business_id')->index();
            $table->string('title', 191);
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Copy of the terms chosen on a quotation ([{id, title, description}]), so later
        // edits to a term don't change quotations that were already sent.
        Schema::table('transactions', function (Blueprint $table) {
            $table->text('commercial_terms')->nullable();
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('commercial_terms');
        });
        Schema::dropIfExists('quotation_terms');
    }
};
