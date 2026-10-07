<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('purchase_order_no', __('sale.purchase_order_no') . ':') !!}
        {!! Form::text('purchase_order_no', old('purchase_order_no', $transaction->purchase_order_no ?? null), ['class' => 'form-control', 'maxlength' => 191]) !!}
        @error('purchase_order_no') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('purchase_order_date', __('sale.purchase_order_date') . ':') !!}
        {!! Form::date('purchase_order_date', old('purchase_order_date', $transaction->purchase_order_date ?? null), ['class' => 'form-control']) !!}
        @error('purchase_order_date') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
