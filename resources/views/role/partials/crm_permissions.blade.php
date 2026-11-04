<div class="row check_group">
    <div class="col-md-3"><h4>CRM</h4></div>
    <div class="col-md-9">
        @foreach(['crm.plan.index'=>'View plans','crm.plan.create'=>'Create plans','crm.plan.view'=>'View plan details','crm.plan.edit'=>'Edit plans','crm.plan.delete'=>'Delete plans','crm.visit.index'=>'View visits','crm.visit.create'=>'Create visits','crm.visit.view'=>'View and assign visits for all employees','crm.visit.edit'=>'Edit visits','crm.visit.delete'=>'Delete visits','crm.report.index'=>'Access CRM reports','crm.report.plan'=>'Plan report','crm.report.visit'=>'Visit report','crm.report.plan-vs-visit'=>'Plan vs visit report','crm.report.dashboard'=>'CRM dashboard','crm.report.export'=>'Export CRM reports'] as $permission=>$label)
            <div class="col-md-6"><div class="checkbox"><label>{!! Form::checkbox('permissions[]',$permission,isset($role_permissions) && in_array($permission,$role_permissions),['class'=>'input-icheck']) !!} {{$label}}</label></div></div>
        @endforeach
    </div>
</div>
