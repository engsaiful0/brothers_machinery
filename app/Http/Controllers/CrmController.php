<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Exports\CrmReportExport;
use App\CrmPlan;
use App\CrmVisit;
use App\CrmVisitDocument;
use App\CustomerGroup;
use App\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CrmController extends Controller
{
    private function businessId() { return request()->session()->get('user.business_id'); }
    private function months() { return [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December']; }
    private function users() { return User::forDropdown($this->businessId(), false); }
    private function groups() { return CustomerGroup::where('business_id', $this->businessId())->orderBy('name')->pluck('name','id'); }
    private function canAny(array $permissions) { foreach ($permissions as $permission) { if (auth()->user()->can($permission)) return true; } return false; }

    private function planQuery(Request $request)
    {
        return CrmPlan::with(['assignedUser','group','business'])->where('crm_plans.business_id', $this->businessId())
            ->when($request->filled('user_id'), fn($q) => $q->where('assigned_user_id', $request->user_id))
            ->when($request->filled('group_id'), fn($q) => $q->where('group_id', $request->group_id))
            ->when($request->filled('contact_id'), fn($q) => $q->where('contact_id', $request->contact_id))
            ->when($request->filled('month'), fn($q) => $q->where('month', $request->month))
            ->when($request->filled('year'), fn($q) => $q->where('year', $request->year));
    }

    private function visitQuery(Request $request)
    {
        $query = CrmVisit::with(['user','group','business','documents'])->where('crm_visits.business_id', $this->businessId());
        if (! auth()->user()->can('crm.visit.view')) $query->where('user_id', auth()->id());
        return $query->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('group_id'), fn($q) => $q->where('group_id', $request->group_id))
            ->when($request->filled('contact_id'), fn($q) => $q->where('contact_id', $request->contact_id))
            ->when($request->filled('month'), fn($q) => $q->whereMonth('visit_date', $request->month))
            ->when($request->filled('year'), fn($q) => $q->whereYear('visit_date', $request->year))
            ->when($request->filled('from_date'), fn($q) => $q->whereDate('visit_date', '>=', $request->from_date))
            ->when($request->filled('to_date'), fn($q) => $q->whereDate('visit_date', '<=', $request->to_date));
    }

    private function formData($record = null)
    {
        $businesses = $record ? Contact::where('business_id',$this->businessId())->where('customer_group_id',$record->group_id)->whereIn('type',['customer','both'])->orderBy('name')->pluck('name','id') : collect();
        $allBusinesses = Contact::where('business_id',$this->businessId())->whereIn('type',['customer','both'])->orderBy('name')->pluck('name','id');
        return compact('record','businesses','allBusinesses') + ['users'=>$this->users(),'groups'=>$this->groups(),'months'=>$this->months(),'years'=>range(now()->year-3,now()->year+5)];
    }

    public function plans(Request $request)
    {
        abort_unless(auth()->user()->can('crm.plan.index'),403);
        $plans=$this->planQuery($request)->latest('year')->latest('month')->paginate(25)->withQueryString();
        return view('crm.plans.index', $this->formData()+compact('plans'));
    }
    public function createPlan() { abort_unless(auth()->user()->can('crm.plan.create'),403); return view('crm.plans.form',$this->formData()); }
    public function storePlan(Request $request)
    {
        abort_unless(auth()->user()->can('crm.plan.create'),403);
        $data=$this->validatePlan($request); $data['business_id']=$this->businessId(); $data['created_by']=auth()->id();
        CrmPlan::create($data);
        return redirect()->route('crm.plans')->with('status',['success'=>1,'msg'=>'Plan created successfully']);
    }
    public function showPlan($id)
    {
        abort_unless(auth()->user()->can('crm.plan.view') || auth()->user()->can('crm.plan.index'),403);
        $plan=CrmPlan::with(['assignedUser','group','business'])->where('business_id',$this->businessId())->findOrFail($id);
        $visits=CrmVisit::with('user')->where('business_id',$this->businessId())->where('contact_id',$plan->contact_id)->where('user_id',$plan->assigned_user_id)->whereYear('visit_date',$plan->year)->whereMonth('visit_date',$plan->month)->latest('visit_date')->get();
        return view('crm.plans.show',compact('plan','visits'));
    }
    public function editPlan($id) { abort_unless(auth()->user()->can('crm.plan.edit'),403); $record=CrmPlan::where('business_id',$this->businessId())->findOrFail($id); return view('crm.plans.form',$this->formData($record)); }
    public function updatePlan(Request $request,$id) { abort_unless(auth()->user()->can('crm.plan.edit'),403); $record=CrmPlan::where('business_id',$this->businessId())->findOrFail($id); $record->update($this->validatePlan($request,$record->id)); return redirect()->route('crm.plans')->with('status',['success'=>1,'msg'=>'Plan updated successfully']); }
    public function deletePlan($id) { abort_unless(auth()->user()->can('crm.plan.delete'),403); CrmPlan::where('business_id',$this->businessId())->findOrFail($id)->delete(); return redirect()->route('crm.plans')->with('status',['success'=>1,'msg'=>'Plan deleted successfully']); }

    private function validatePlan(Request $request,$ignoreId=null)
    {
        $data=$request->validate(['assigned_user_id'=>'required|integer','group_id'=>'required|integer','contact_id'=>'required|integer','monthly_potential'=>'required|numeric|min:0','monthly_sales_target'=>'required|numeric|min:0','month'=>'required|integer|between:1,12','year'=>'required|integer|between:2000,2200','remarks'=>'nullable|string|max:10000']);
        abort_unless(User::where('business_id',$this->businessId())->whereKey($data['assigned_user_id'])->exists(),422);
        abort_unless(CustomerGroup::where('business_id',$this->businessId())->whereKey($data['group_id'])->exists(),422);
        abort_unless(Contact::where('business_id',$this->businessId())->where('customer_group_id',$data['group_id'])->whereIn('type',['customer','both'])->whereKey($data['contact_id'])->exists(),422);
        $duplicate=CrmPlan::where('business_id',$this->businessId())->where('assigned_user_id',$data['assigned_user_id'])->where('contact_id',$data['contact_id'])->where('month',$data['month'])->where('year',$data['year'])->when($ignoreId,fn($q)=>$q->where('id','!=',$ignoreId))->exists();
        if($duplicate) throw \Illuminate\Validation\ValidationException::withMessages(['contact_id'=>'This employee already has a plan for this business and period.']);
        return $data;
    }

    public function visits(Request $request) { abort_unless(auth()->user()->can('crm.visit.index'),403); $visits=$this->visitQuery($request)->latest('visit_date')->paginate(25)->withQueryString(); return view('crm.visits.index',$this->formData()+compact('visits')); }
    public function createVisit() { abort_unless(auth()->user()->can('crm.visit.create'),403); return view('crm.visits.form',$this->formData()+['record'=>null]); }
    public function storeVisit(Request $request)
    {
        abort_unless(auth()->user()->can('crm.visit.create'),403); $data=$this->validateVisit($request); $data['business_id']=$this->businessId(); $data['created_by']=auth()->id(); $data['user_id']=$data['user_id']??auth()->id();
        $visit=CrmVisit::create($data); $this->saveDocuments($request,$visit);
        return redirect()->route('crm.visits')->with('status',['success'=>1,'msg'=>'Visit recorded successfully']);
    }
    private function validateVisit(Request $request)
    {
        $data=$request->validate(['group_id'=>'required|integer','contact_id'=>'required|integer','visit_date'=>'required|date','visit_time'=>'required|date_format:H:i','description'=>'required|string|max:30000','documents.*'=>'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,txt|max:10240']);
        abort_unless(CustomerGroup::where('business_id',$this->businessId())->whereKey($data['group_id'])->exists(),422);
        abort_unless(Contact::where('business_id',$this->businessId())->where('customer_group_id',$data['group_id'])->whereIn('type',['customer','both'])->whereKey($data['contact_id'])->exists(),422);
        if (auth()->user()->can('crm.visit.view')) {
            $data['user_id'] = $request->validate(['user_id'=>'required|integer'])['user_id'];
            abort_unless(User::where('business_id',$this->businessId())->whereKey($data['user_id'])->exists(),422);
        }
        unset($data['documents']); return $data;
    }
    private function visitForUser($id,$permission)
    {
        $required = $permission === 'view' ? ['crm.visit.view','crm.visit.index'] : ['crm.visit.'.$permission];
        abort_unless($this->canAny($required),403);
        $query=CrmVisit::with(['user','group','business','documents'])->where('business_id',$this->businessId());
        if (!auth()->user()->can('crm.visit.view')) $query->where('user_id',auth()->id());
        return $query->findOrFail($id);
    }
    public function showVisit($id) { $visit=$this->visitForUser($id,'view'); return view('crm.visits.show',compact('visit')); }
    public function editVisit($id) { $visit=$this->visitForUser($id,'edit'); return view('crm.visits.form',$this->formData()+['record'=>$visit]); }
    public function updateVisit(Request $request,$id)
    {
        $visit=$this->visitForUser($id,'edit'); $visit->update($this->validateVisit($request)); $this->saveDocuments($request,$visit);
        foreach ((array)$request->input('remove_documents',[]) as $docId) { $doc=$visit->documents()->find($docId); if($doc){Storage::disk('local')->delete($doc->document_path);$doc->delete();} }
        return redirect()->route('crm.visits')->with('status',['success'=>1,'msg'=>'Visit updated successfully']);
    }
    public function deleteVisit($id) { $visit=$this->visitForUser($id,'delete'); foreach($visit->documents as $doc) Storage::disk('local')->delete($doc->document_path); $visit->delete(); return redirect()->route('crm.visits')->with('status',['success'=>1,'msg'=>'Visit deleted successfully']); }
    private function saveDocuments(Request $request,CrmVisit $visit)
    {
        foreach ($request->file('documents',[]) as $file) { $path=$file->store('crm-visits/'.$this->businessId(),'local'); $visit->documents()->create(['document_path'=>$path,'original_file_name'=>$file->getClientOriginalName(),'mime_type'=>$file->getMimeType(),'file_size'=>$file->getSize()]); }
    }
    public function downloadDocument($id)
    {
        abort_unless($this->canAny(['crm.visit.index','crm.visit.view']),403);
        $doc=CrmVisitDocument::with('visit')->findOrFail($id); $visit=$doc->visit;
        abort_unless($visit->business_id==$this->businessId(),404);
        if (!auth()->user()->can('crm.visit.view')) abort_unless($visit->user_id==auth()->id(),403);
        abort_unless(Storage::disk('local')->exists($doc->document_path),404);
        return Storage::disk('local')->download($doc->document_path,$doc->original_file_name);
    }

    public function businesses(Request $request)
    {
        abort_unless(CustomerGroup::where('business_id',$this->businessId())->whereKey($request->group_id)->exists(),404);
        return Contact::where('business_id',$this->businessId())->where('customer_group_id',$request->group_id)->whereIn('type',['customer','both'])->orderBy('name')->get(['id','name','supplier_business_name'])->mapWithKeys(function($contact){ return [$contact->id => trim($contact->name.' '.($contact->supplier_business_name ? '('.$contact->supplier_business_name.')' : ''))]; });
    }

    public function report(Request $request,$type)
    {
        $permission=['plan'=>'crm.report.plan','visit'=>'crm.report.visit','plan-vs-visit'=>'crm.report.plan-vs-visit','dashboard'=>'crm.report.dashboard'][$type]??null;
        abort_unless($permission && auth()->user()->can($permission),403);
        abort_if($request->filled('format') && !auth()->user()->can('crm.report.export'),403);
        $plans=$this->planQuery($request)->get(); $visits=$this->visitQuery($request)->get();
        if($type==='dashboard') return view('crm.reports.dashboard',$this->formData()+compact('plans','visits'));
        if($type==='plan') { $rows=$plans; $title='Plan Report'; $columns=['Employee','Group','Business','Monthly Potential','Monthly Sales Target','Month','Year','Remarks']; }
        elseif($type==='visit') { $rows=$visits; $title='Visit Report'; $columns=['Visit Person','Group','Business','Visit Date','Visit Time','Description','Documents']; }
        else {
            $rows=$plans->map(function($plan){ $plan->period_visits=CrmVisit::where('business_id',$plan->business_id)->where('user_id',$plan->assigned_user_id)->where('contact_id',$plan->contact_id)->whereYear('visit_date',$plan->year)->whereMonth('visit_date',$plan->month)->orderByDesc('visit_date')->get(); return $plan; });
            $title='Plan vs Visit Report'; $columns=['Employee','Group','Business','Monthly Potential','Monthly Sales Target','Total Visits','Last Visit Date','Visit Summary','Status'];
        }
        if($request->input('format')==='excel') return Excel::download(new CrmReportExport($rows,$type,$columns,$title),\Illuminate\Support\Str::slug($title).'.xlsx');
        if($request->input('format')==='pdf') return Pdf::loadView('crm.reports.pdf',compact('rows','type','title','columns'))->setPaper('a4','landscape')->download(\Illuminate\Support\Str::slug($title).'.pdf');
        return view('crm.reports.table',compact('rows','type','title','columns')+$this->formData());
    }
    public function rowValues($r,$type)
    {
        $base=[$r->assignedUser->first_name??$r->user->first_name??'', $r->group->name??'', $r->business->name??''];
        if($type==='plan') return array_merge($base,[$r->monthly_potential,$r->monthly_sales_target,$this->months()[$r->month]??$r->month,$r->year,$r->remarks]);
        if($type==='visit') return array_merge([$r->user->first_name??'', $r->group->name??'', $r->business->name??'', $r->visit_date,$r->visit_time,$r->description,$r->documents->pluck('original_file_name')->implode(', ')]);
        $v=$r->period_visits??collect(); return array_merge($base,[$r->monthly_potential,$r->monthly_sales_target,$v->count(),optional($v->first())->visit_date,$v->pluck('description')->implode(' | '),$v->count()==0?'Not Visited':($v->count()==1?'Visited':'Multiple Visits')]);
    }
}
