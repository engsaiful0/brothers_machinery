<?php

namespace App\Http\Controllers;

use App\CustomerGroup;
use App\SellingPriceGroup;
use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CustomerGroupController extends Controller
{
    /**
     * Constructor
     *
     * @param  Util  $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $customer_group = CustomerGroup::where('customer_groups.business_id', $business_id)
                                    ->leftjoin('selling_price_groups as spg', 'spg.id', '=', 'customer_groups.selling_price_group_id')
                                ->select([
                                    'customer_groups.name', 'customer_groups.contact_person', 'customer_groups.designation',
                                    'customer_groups.mobile', 'customer_groups.email',
                                    'customer_groups.assigned_to_users', 'customer_groups.assigned_to',
                                    'customer_groups.amount', 'spg.name as selling_price_group', 'customer_groups.id', 'price_calculation_type',
                                ]);

            $users_by_id = User::where('business_id', $business_id)
                ->select('id', 'surname', 'first_name', 'last_name')
                ->get()
                ->mapWithKeys(function ($user) {
                    return [$user->id => trim(implode(' ', array_filter([$user->surname, $user->first_name, $user->last_name])))];
                })
                ->all();

            return Datatables::of($customer_group)
                    ->addColumn(
                        'action',
                        '@can("customer.update")
                            <button data-href="{{action(\'App\Http\Controllers\CustomerGroupController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary tw-m-0.5 edit_customer_group_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                        &nbsp;
                        @endcan

                        @can("customer.delete")
                            <button data-href="{{action(\'App\Http\Controllers\CustomerGroupController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error tw-m-0.5 delete_customer_group_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                        @endcan'
                    )
                    ->addColumn('assigned_person', function ($row) use ($users_by_id) {
                        $assigned_ids = $row->assigned_to_users ?: (! empty($row->assigned_to) ? [$row->assigned_to] : []);
                        $names = [];
                        foreach ($assigned_ids as $assigned_id) {
                            if (isset($users_by_id[$assigned_id])) {
                                $names[] = e($users_by_id[$assigned_id]);
                            }
                        }

                        return implode(', ', $names) ?: '--';
                    }, 1)
                    ->editColumn('selling_price_group', '@if($price_calculation_type=="selling_price_group") {{$selling_price_group}} @else -- @endif ')
                    ->editColumn('amount', '@if($price_calculation_type=="percentage") {{$amount}} @else -- @endif ')
                    ->removeColumn('assigned_to_users')
                    ->removeColumn('assigned_to')
                    ->removeColumn('id')
                    ->removeColumn('price_calculation_type')
                    ->rawColumns([8])
                    ->make(false);
        }

        return view('customer_group.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $price_groups = SellingPriceGroup::forDropdown($business_id, false);
        $users = User::forDropdown($business_id, false);

        return view('customer_group.create')->with(compact('price_groups', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $this->validateContactFields($request, $business_id);

        try {
            $input = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id', 'contact_person', 'designation', 'mobile', 'email']);
            $input['assigned_to_users'] = array_values(array_unique(array_filter((array) $request->input('assigned_to', []))));
            $input['assigned_to'] = null;
            $input['business_id'] = $business_id;
            $input['created_by'] = $request->session()->get('user.id');
            $input['amount'] = ! empty($input['amount']) ? $this->commonUtil->num_uf($input['amount']) : 0;

            $customer_group = CustomerGroup::create($input);
            $output = ['success' => true,
                'data' => $customer_group,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerGroup  $customerGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $customer_group = CustomerGroup::where('business_id', $business_id)->find($id);

            if (empty($customer_group->assigned_to_users) && ! empty($customer_group->assigned_to)) {
                $customer_group->assigned_to_users = [(string) $customer_group->assigned_to];
            }
            $customer_group->assigned_to_users = array_map('strval', $customer_group->assigned_to_users ?: []);

            $business_id = request()->session()->get('user.business_id');
            $price_groups = SellingPriceGroup::forDropdown($business_id, false);
            $users = User::forDropdown($business_id, false);

            return view('customer_group.edit')
                ->with(compact('customer_group', 'price_groups', 'users'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = $request->session()->get('user.business_id');
            $this->validateContactFields($request, $business_id);

            try {
                $input = $request->only(['name', 'amount', 'price_calculation_type', 'selling_price_group_id', 'contact_person', 'designation', 'mobile', 'email']);
                $input['assigned_to_users'] = array_values(array_unique(array_filter((array) $request->input('assigned_to', []))));
                $input['assigned_to'] = null;

                $input['amount'] = ! empty($input['amount']) ? $this->commonUtil->num_uf($input['amount']) : 0;

                $customer_group = CustomerGroup::where('business_id', $business_id)->findOrFail($id);

                $customer_group->update($input);

                $output = ['success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    private function validateContactFields(Request $request, $business_id)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'assigned_to' => ['sometimes', 'array'],
            'assigned_to.*' => ['integer', Rule::exists('users', 'id')->where('business_id', $business_id)],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ])->validate();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->user()->business_id;

                $cg = CustomerGroup::where('business_id', $business_id)->findOrFail($id);
                $cg->delete();

                $output = ['success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }
}
