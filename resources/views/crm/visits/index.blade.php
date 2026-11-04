@extends('layouts.app')
@section('title','CRM Visits')
@section('content')
<section class="content-header"><h1>Business Visits</h1></section>
<section class="content"><div class="box box-primary"><div class="box-header">@can('crm.visit.create')<a href="{{route('crm.visits.create')}}" class="btn btn-primary">Add Visit</a>@endcan</div><div class="box-body">
<form class="row" method="GET">
    <div class="col-md-2"><select class="form-control select2" name="user_id"><option value="">All people</option>@foreach($users as $id=>$name)<option value="{{$id}}" @selected(request('user_id')==$id)>{{$name}}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-control select2" name="group_id"><option value="">All groups</option>@foreach($groups as $id=>$name)<option value="{{$id}}" @selected(request('group_id')==$id)>{{$name}}</option>@endforeach</select></div>
    <div class="col-md-2"><select class="form-control select2" name="contact_id"><option value="">All businesses</option>@foreach($allBusinesses as $id=>$name)<option value="{{$id}}" @selected(request('contact_id')==$id)>{{$name}}</option>@endforeach</select></div>
    <div class="col-md-2"><input type="date" name="from_date" class="form-control" value="{{request('from_date')}}" aria-label="From date"></div>
    <div class="col-md-2"><input type="date" name="to_date" class="form-control" value="{{request('to_date')}}" aria-label="To date"></div><div class="col-md-2"><button class="btn btn-default">Filter</button></div>
</form>
<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>SL</th><th>Visit Person</th><th>Group</th><th>Business</th><th>Date</th><th>Time</th><th>Description</th><th>Documents</th><th>Action</th></tr></thead><tbody>
@foreach($visits as $i=>$v)<tr><td>{{$visits->firstItem()+$i}}</td><td>{{$v->user->first_name??''}} {{$v->user->last_name??''}}</td><td>{{$v->group->name??''}}</td><td>{{$v->business->name??''}}</td><td>{{$v->visit_date}}</td><td>{{substr($v->visit_time,0,5)}}</td><td>{{Str::limit($v->description,90)}}</td><td>{{$v->documents->count()}}</td><td><a class="btn btn-xs btn-info" href="{{route('crm.visits.show',$v->id)}}">View</a> @can('crm.visit.edit')<a class="btn btn-xs btn-primary" href="{{route('crm.visits.edit',$v->id)}}">Edit</a>@endcan @can('crm.visit.delete')<form method="POST" action="{{route('crm.visits.delete',$v->id)}}" style="display:inline" onsubmit="return confirm('Delete this visit?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger">Delete</button></form>@endcan</td></tr>@endforeach
</tbody></table></div>{{$visits->links()}}</div></div></section>
@endsection
