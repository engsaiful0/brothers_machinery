@extends('layouts.app')
@section('title',$title)
@section('content')
<section class="content-header"><h1>{{$title}}</h1></section>
<section class="content"><div class="box"><div class="box-body">
    <form method="GET" class="row no-print">
        <div class="col-md-2"><select class="form-control" name="user_id"><option value="">All people</option>@foreach($users as $id=>$name)<option value="{{$id}}" @selected(request('user_id')==$id)>{{$name}}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-control" name="group_id"><option value="">All groups</option>@foreach($groups as $id=>$name)<option value="{{$id}}" @selected(request('group_id')==$id)>{{$name}}</option>@endforeach</select></div>
        <div class="col-md-2"><select class="form-control" name="contact_id"><option value="">All businesses</option>@foreach($allBusinesses as $id=>$name)<option value="{{$id}}" @selected(request('contact_id')==$id)>{{$name}}</option>@endforeach</select></div>
        @if($type === 'visit')
            <div class="col-md-2"><input type="date" name="from_date" class="form-control" value="{{request('from_date')}}" aria-label="From date"></div>
            <div class="col-md-2"><input type="date" name="to_date" class="form-control" value="{{request('to_date')}}" aria-label="To date"></div>
        @else
            <div class="col-md-1"><input type="number" min="1" max="12" name="month" class="form-control" placeholder="Month" value="{{request('month')}}"></div>
            <div class="col-md-1"><input type="number" name="year" class="form-control" placeholder="Year" value="{{request('year')}}"></div>
        @endif
        <div class="col-md-3"><button class="btn btn-default">Filter</button> @can('crm.report.export')<button name="format" value="excel" class="btn btn-success">Excel</button><button name="format" value="pdf" class="btn btn-danger">PDF</button>@endcan <button type="button" onclick="window.print()" class="btn btn-info">Print</button></div>
    </form>
    <p class="text-muted">Generated {{now()->format('Y-m-d H:i')}}</p>
    @if($type === 'plan')<div class="row"><div class="col-md-4"><strong>Total Business Plans:</strong> {{$rows->count()}}</div><div class="col-md-4"><strong>Total Monthly Potential:</strong> {{number_format($rows->sum('monthly_potential'),2)}}</div><div class="col-md-4"><strong>Total Monthly Sales Target:</strong> {{number_format($rows->sum('monthly_sales_target'),2)}}</div></div>@endif
    @if($type === 'visit')<div class="row"><div class="col-md-4"><strong>Total Visits:</strong> {{$rows->count()}}</div><div class="col-md-4"><strong>Employees Involved:</strong> {{$rows->pluck('user_id')->unique()->count()}}</div><div class="col-md-4"><strong>Businesses Visited:</strong> {{$rows->pluck('contact_id')->unique()->count()}}</div></div>@endif
    <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>SL</th>@foreach($columns as $col)<th>{{$col}}</th>@endforeach</tr></thead><tbody>@foreach($rows as $i=>$row)<tr><td>{{$i+1}}</td>@foreach(app(\App\Http\Controllers\CrmController::class)->rowValues($row,$type) as $cell)<td>{{$cell}}</td>@endforeach</tr>@endforeach</tbody></table></div>
</div></div></section>
@endsection
