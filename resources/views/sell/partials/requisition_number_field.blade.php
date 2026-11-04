<div class="col-sm-3">
    <div class="form-group">
        {!! Form::label('requisition_number', __('sale.requisition_number') . ':') !!}
        {!! Form::text('requisition_number', old('requisition_number', $transaction->requisition_number ?? null), ['class' => 'form-control', 'maxlength' => 191]) !!}
        @error('requisition_number') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
