<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('vat_type', 20)->nullable()->after('tax_amount');
            $table->decimal('vat_rate', 22, 4)->default(0)->after('vat_type');
            $table->decimal('vat_amount', 22, 4)->default(0)->after('vat_rate');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['vat_type', 'vat_rate', 'vat_amount']);
        });
    }
};
