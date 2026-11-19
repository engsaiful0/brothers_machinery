@php
    $quotation_terms = \App\QuotationTerm::forBusiness(request()->session()->get('user.business_id'));
    $selected_term_ids = collect($transaction->commercial_terms ?? [])->pluck('id')->map('intval')->all();
@endphp
<div class="col-md-12">
    <div class="form-group">
        {!! Form::label('commercial_term_ids', 'Commercial Terms & Conditions:') !!}
        {{-- Lets the controller tell "nothing ticked" apart from "field not on this form". --}}
        <input type="hidden" name="commercial_terms_submitted" value="1">
        @forelse($quotation_terms as $term)
            <div class="checkbox" style="margin-top: 0;">
                <label>
                    <input type="checkbox" name="commercial_term_ids[]" value="{{ $term->id }}" @checked(in_array($term->id, $selected_term_ids))>
                    <strong>{{ $term->title }}</strong>: {{ $term->description }}
                </label>
            </div>
        @empty
            <p class="help-block">No terms yet.</p>
        @endforelse
        @can('direct_sell.access')
            <a href="{{ action([\App\Http\Controllers\QuotationTermController::class, 'index']) }}" target="_blank">Manage commercial terms</a>
        @endcan
    </div>
</div>
