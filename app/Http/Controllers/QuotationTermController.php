<?php

namespace App\Http\Controllers;

use App\QuotationTerm;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

/**
 * Commercial terms & conditions that can be picked on a quotation.
 */
class QuotationTermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $terms = QuotationTerm::where('business_id', $business_id)
                ->select(['sort_order', 'title', 'description', 'id']);

            return Datatables::of($terms)
                ->addColumn(
                    'action',
                    '<button data-href="{{action(\'App\Http\Controllers\QuotationTermController@edit\', [$id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary tw-m-0.5 edit_quotation_term_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    <button data-href="{{action(\'App\Http\Controllers\QuotationTermController@destroy\', [$id])}}" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error tw-m-0.5 delete_quotation_term_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->removeColumn('id')
                ->rawColumns([3])
                ->make(false);
        }

        return view('quotation_term.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $term = new QuotationTerm(['sort_order' => QuotationTerm::where('business_id', request()->session()->get('user.business_id'))->max('sort_order') + 1]);

        return view('quotation_term.form')->with(compact('term'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $input = $this->validateTerm($request);
        $input['business_id'] = $request->session()->get('user.business_id');
        $input['created_by'] = $request->session()->get('user.id');
        QuotationTerm::create($input);

        return ['success' => true, 'msg' => __('lang_v1.success')];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $term = $this->findTerm($id);

        return view('quotation_term.form')->with(compact('term'));
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
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $this->findTerm($id)->update($this->validateTerm($request));

        return ['success' => true, 'msg' => __('lang_v1.success')];
    }

    /**
     * Remove the specified resource from storage.
     * Quotations keep their own copy of the terms, so they are not affected.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $this->findTerm($id)->delete();

        return ['success' => true, 'msg' => __('lang_v1.success')];
    }

    private function findTerm($id)
    {
        return QuotationTerm::where('business_id', request()->session()->get('user.business_id'))->findOrFail($id);
    }

    private function validateTerm(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'required|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:65535',
        ]);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
