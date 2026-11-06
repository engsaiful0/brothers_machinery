@php
    $profileContact = $contact ?? null;
    $isCustomerProfile = $profileContact ? in_array($profileContact->type, ['customer', 'both']) : in_array($type ?? '', ['customer', 'both']);
    $people = $profileContact ? $profileContact->customerContactPersons : collect();
    if ($people->isEmpty()) $people = collect([new \App\CustomerContactPerson()]);
    $selectedMachineTypes = $profileContact ? $profileContact->machineTypeValues : [];
    $selectedMachineBrands = $profileContact ? $profileContact->machineBrands->pluck('id')->all() : [];
    $machineTypeOptions = ['Compressor', 'Dryer', 'Line Filter'];
@endphp
<div class="customer-profile-fields" style="{{ $isCustomerProfile ? '' : 'display:none;' }}">
  <div class="row">
    <div class="col-md-12"><hr><h4>Business Information</h4></div>
    <div class="col-md-4"><div class="form-group">
        {!! Form::label('monthly_business_potential', 'Monthly Business Potential:') !!}
        {!! Form::number('monthly_business_potential', old('monthly_business_potential', $profileContact->monthly_business_potential ?? null), ['class' => 'form-control', 'min' => '0', 'step' => '0.01']) !!}
    </div></div>
    <div class="col-md-4"><div class="form-group">
        {!! Form::label('head_office_address', 'Head Office Address:') !!}
        {!! Form::textarea('head_office_address', old('head_office_address', $profileContact->head_office_address ?? null), ['class' => 'form-control', 'rows' => 3]) !!}
    </div></div>
    <div class="col-md-4"><div class="form-group">
        {!! Form::label('factory_address', 'Factory Address:') !!}
        {!! Form::textarea('factory_address', old('factory_address', $profileContact->factory_address ?? null), ['class' => 'form-control', 'rows' => 3]) !!}
    </div></div>

    <div class="col-md-12"><hr><h4>Contact Persons</h4></div>
    <div class="col-md-12" id="customer-contact-persons">
        @foreach($people as $index => $person)
            <div class="row customer-contact-person" style="border-bottom:1px solid #eee; margin-bottom:10px;">
                <div class="col-md-3"><div class="form-group"><label>Name</label><input class="form-control" name="contact_persons[{{ $index }}][name]" value="{{ old("contact_persons.$index.name", $person->name) }}" maxlength="191"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Designation</label><input class="form-control" name="contact_persons[{{ $index }}][designation]" value="{{ old("contact_persons.$index.designation", $person->designation) }}" maxlength="191"></div></div>
                <div class="col-md-2"><div class="form-group"><label>Mobile Number</label><input class="form-control" name="contact_persons[{{ $index }}][mobile]" value="{{ old("contact_persons.$index.mobile", $person->mobile) }}" maxlength="30"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Email Address</label><input type="email" class="form-control" name="contact_persons[{{ $index }}][email]" value="{{ old("contact_persons.$index.email", $person->email) }}" maxlength="191"></div></div>
                <div class="col-md-1"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-block remove-customer-person" aria-label="Remove contact person" title="Remove contact person"><i class="fa fa-times" aria-hidden="true"></i></button></div>
            </div>
        @endforeach
    </div>
    <div class="col-md-12"><button type="button" class="btn btn-default" id="add-customer-person"><i class="fa fa-plus"></i> Add Contact Person</button></div>

    <div class="col-md-12"><hr><h4>Machine Information</h4></div>
    <div class="col-md-4"><div class="form-group"><label>Machine Type</label><br>
        @foreach($machineTypeOptions as $machineType)
            <label class="checkbox-inline"><input type="checkbox" name="machine_types[]" value="{{ $machineType }}" {{ in_array($machineType, old('machine_types', $selectedMachineTypes)) ? 'checked' : '' }}> {{ $machineType }}</label>
        @endforeach
    </div></div>
    <div class="col-md-4"><div class="form-group"><label>Machine Brand</label>
        {!! Form::select('machine_brands[]', $machine_brands ?? [], old('machine_brands', $selectedMachineBrands), ['class' => 'form-control select2', 'multiple' => true, 'style' => 'width:100%']) !!}
    </div></div>
    <div class="col-md-4"><div class="form-group"><label>Machine Quantity</label>
        {!! Form::number('machine_quantity', old('machine_quantity', $profileContact->machine_quantity ?? null), ['class' => 'form-control', 'min' => '0', 'step' => '1']) !!}
    </div></div>
    <div class="col-md-12"><div class="form-group"><label>Machine Details</label>
        {!! Form::textarea('machine_details', old('machine_details', $profileContact->machine_details ?? null), ['class' => 'form-control', 'rows' => 3]) !!}
    </div></div>
  </div>
</div>
<script>
(function ($) {
    var personIndex = {{ max(0, $people->count() - 1) + 1 }};
    $(document).off('click.customerProfileAdd', '#add-customer-person').on('click.customerProfileAdd', '#add-customer-person', function () {
        if ($(this).closest('#quick_add_contact').length) return;
        var index = personIndex++;
        var row = '<div class="row customer-contact-person" style="border-bottom:1px solid #eee; margin-bottom:10px;">' +
            '<div class="col-md-3"><div class="form-group"><label>Name</label><input class="form-control" name="contact_persons['+index+'][name]" maxlength="191"></div></div>'+
            '<div class="col-md-3"><div class="form-group"><label>Designation</label><input class="form-control" name="contact_persons['+index+'][designation]" maxlength="191"></div></div>'+
            '<div class="col-md-2"><div class="form-group"><label>Mobile Number</label><input class="form-control" name="contact_persons['+index+'][mobile]" maxlength="30"></div></div>'+
            '<div class="col-md-3"><div class="form-group"><label>Email Address</label><input type="email" class="form-control" name="contact_persons['+index+'][email]" maxlength="191"></div></div>'+
            '<div class="col-md-1"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-block remove-customer-person">Remove</button></div></div>';
        $('#customer-contact-persons').append(row);
    });
    $(document).off('click.customerProfileRemove', '.remove-customer-person').on('click.customerProfileRemove', '.remove-customer-person', function () {
        if ($(this).closest('#quick_add_contact').length) return;
        if ($('#customer-contact-persons .customer-contact-person').length > 1) $(this).closest('.customer-contact-person').remove();
        else $(this).closest('.customer-contact-person').find('input').val('');
    });
    $(document).off('change.customerProfileType', 'select#contact_type').on('change.customerProfileType', 'select#contact_type', function () {
        $('.customer-profile-fields').toggle(['customer', 'both'].indexOf($(this).val()) !== -1);
    });
})(jQuery);
</script>
