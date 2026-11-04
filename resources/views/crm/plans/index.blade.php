@extends('layouts.app')
@section('title','CRM Plans')
@section('content')
<section class="content-header"><h1>Business Plans</h1></section>
<section class="content"><div class="box box-primary"><div class="box-header">@can('crm.plan.create')<a href="{{route('crm.plans.create')}}" class="btn btn-primary">Create Plan</a>@endcan</div><div class="box-body">
<form class="row" method="GET">
    <div class="col-md-2"><select class="form-control select2" name="user_id"><option value="">All people</option>@foreach($users as $id=>$name)<option value="{{$id}}" @selected(request('user_id')==$id)>{{$name}}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-control select2" name="group_id"><option value="">All groups</option>@foreach($groups as $id=>$name)<option value="{{$id}}" @selected(request('group_id')==$id)>{{$name}}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-control select2" name="contact_id"><option value="">All businesses</option>@foreach($allBusinesses as $id=>$name)<option value="{{$id}}" @selected(request('contact_id')==$id)>{{$name}}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-control" name="month"><option value="">All months</option>@foreach($months as $id=>$month)<option value="{{$id}}" @selected(request('month')==$id)>{{$month}}</option>@endforeach</select></div>
    <div class="col-md-2"><input class="form-control" type="number" name="year" placeholder="Year" value="{{request('year')}}"></div><div class="col-md-2"><button class="btn btn-default">Filter</button></div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>SL</th><th>Assign Person</th><th>Group</th><th>Business</th><th>Monthly Potential</th><th>Sales Target</th><th>Month</th><th>Year</th><th>Remarks</th><th>Action</th></tr></thead><tbody>
@foreach($plans as $i=>$p)<tr><td>{{$plans->firstItem()+$i}}</td><td>{{$p->assignedUser->first_name??''}} {{$p->assignedUser->last_name??''}}</td><td>{{$p->group->name??''}}</td><td>{{$p->business->name??''}}</td><td>{{$p->monthly_potential}}</td><td>{{$p->monthly_sales_target}}</td><td>{{$months[$p->month]??$p->month}}</td><td>{{$p->year}}</td><td>{{Str::limit($p->remarks,70)}}</td><td><a class="btn btn-xs btn-info" href="{{route('crm.plans.show',$p->id)}}">View</a> @can('crm.plan.edit')<a class="btn btn-xs btn-primary" href="{{route('crm.plans.edit',$p->id)}}">Edit</a>@endcan @can('crm.plan.delete')<form method="POST" action="{{route('crm.plans.delete',$p->id)}}" style="display:inline" onsubmit="return confirm('Delete this plan?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger">Delete</button></form>@endcan</td></tr>@endforeach
</tbody></table></div>{{$plans->links()}}</div></div></section>
@endsection
