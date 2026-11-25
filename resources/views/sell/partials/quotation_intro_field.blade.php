<div class="col-md-12">
    <div class="form-group">
        {!! Form::label('quotation_intro', 'Quotation Introduction Text:') !!}
        {{-- Lets the controller tell "cleared" apart from "field not on this form". --}}
        <input type="hidden" name="quotation_intro_submitted" value="1">
        {!! Form::textarea('quotation_intro', old('quotation_intro', $transaction->quotation_intro ?? \App\Transaction::DEFAULT_QUOTATION_INTRO), ['class' => 'form-control', 'rows' => 2, 'maxlength' => 2000]) !!}
        <p class="help-block">Printed under "Dear Sir," above the product table.</p>
    </div>
</div>
