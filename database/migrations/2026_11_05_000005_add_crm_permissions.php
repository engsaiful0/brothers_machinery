<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up()
    {
        foreach (['crm.plan.index','crm.plan.create','crm.plan.edit','crm.plan.delete','crm.plan.view','crm.visit.index','crm.visit.create','crm.visit.edit','crm.visit.delete','crm.visit.view','crm.report.index','crm.report.plan','crm.report.visit','crm.report.plan-vs-visit','crm.report.dashboard','crm.report.export'] as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    public function down()
    {
        Permission::where('name', 'like', 'crm.%')->delete();
    }
};
