<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('delivery_challan_no', __('sale.delivery_challan_no') . ':') !!}
        {!! Form::text('delivery_challan_no', old('delivery_challan_no', $transaction->delivery_challan_no ?? null), ['class' => 'form-control', 'maxlength' => 191]) !!}
        @error('delivery_challan_no') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
