<div class="modal-dialog" role="document">
  <div class="modal-content">

    @if($term->exists)
      {!! Form::open(['url' => action([\App\Http\Controllers\QuotationTermController::class, 'update'], [$term->id]), 'method' => 'PUT', 'id' => 'quotation_term_form']) !!}
    @else
      {!! Form::open(['url' => action([\App\Http\Controllers\QuotationTermController::class, 'store']), 'method' => 'post', 'id' => 'quotation_term_form']) !!}
    @endif

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">{{ $term->exists ? 'Edit Commercial Term' : 'Add Commercial Term' }}</h4>
    </div>

    <div class="modal-body">
      <div class="alert alert-danger quotation-term-errors" style="display:none;"></div>
      <div class="form-group">
        {!! Form::label('title', 'Title:*') !!}
        {!! Form::text('title', $term->title, ['class' => 'form-control', 'required', 'maxlength' => 191, 'placeholder' => 'e.g. Delivery']); !!}
      </div>
      <div class="form-group">
        {!! Form::label('description', 'Description:*') !!}
        {!! Form::textarea('description', $term->description, ['class' => 'form-control', 'required', 'rows' => 3, 'maxlength' => 2000, 'placeholder' => 'e.g. From Ready Stock.']); !!}
      </div>
      <div class="form-group">
        {!! Form::label('sort_order', 'Order:') !!}
        {!! Form::number('sort_order', $term->sort_order, ['class' => 'form-control', 'min' => 0]); !!}
        <p class="help-block">Terms print on the quotation from the lowest order number to the highest.</p>
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white">@lang('messages.save')</button>
      <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
    </div>

    {!! Form::close() !!}

  </div>
</div>
