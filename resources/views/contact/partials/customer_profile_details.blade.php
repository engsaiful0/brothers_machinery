<div class="col-md-12 customer-profile-details">
    @php
        $assignedUsers = config('constants.enable_contact_assign')
            ? $contact->userHavingAccess()->get()->map(function ($user) {
                return trim($user->first_name . ' ' . $user->last_name);
            })->filter()->implode(', ')
            : null;
        $customerDetails = [
            'Contact ID' => $contact->contact_id,
            'Customer Group' => $customer_group->name ?? null,
            'Contact Type' => $contact->contact_type,
            'Contact Name' => $contact->name,
            'Prefix' => $contact->prefix,
            'First Name' => $contact->first_name,
            'Middle Name' => $contact->middle_name,
            'Last Name' => $contact->last_name,
            'Business Name' => $contact->supplier_business_name,
            'Email Address' => $contact->email,
            'Mobile Number' => $contact->mobile,
            'Alternate Number' => $contact->alternate_number,
            'Landline' => $contact->landline,
            'Date of Birth' => $contact->dob,
            'Tax Number' => $contact->tax_number,
            'Address Line 1' => $contact->address_line_1,
            'Address Line 2' => $contact->address_line_2,
            'City' => $contact->city,
            'State' => $contact->state,
            'Country' => $contact->country,
            'ZIP Code' => $contact->zip_code,
            'Landmark' => $contact->land_mark,
            'Street Name' => $contact->street_name,
            'Building Number' => $contact->building_number,
            'Additional Number' => $contact->additional_number,
            'Assigned To' => $assignedUsers,
            'Credit Limit' => $contact->credit_limit,
            'Payment Term' => $contact->pay_term_number ? $contact->pay_term_number . ' ' . ($contact->pay_term_type ? __('lang_v1.' . $contact->pay_term_type) : '') : null,
            'Shipping Address' => $contact->shipping_address,
            'Shipping Location' => $contact->position,
        ];
    @endphp
    <hr>
    <h4>Customer Information</h4>
    <div class="row">
        @foreach($customerDetails as $label => $value)
            @if($value !== null && $value !== '')
                <div class="col-sm-3">
                    <strong>{{ $label }}</strong>
                    <p class="text-muted">
                        @if($label === 'Credit Limit')
                            <span class="display_currency" data-currency_symbol="true">{{ $value }}</span>
                        @else
                            {!! nl2br(e($value)) !!}
                        @endif
                    </p>
                </div>
            @endif
        @endforeach
    </div>

    @if(!empty($contact->shipping_custom_field_details))
        <h4>Shipping Details</h4>
        <div class="row">
            @foreach($contact->shipping_custom_field_details as $field => $value)
                @if($value !== null && $value !== '')
                    <div class="col-sm-3"><strong>{{ str_replace('_', ' ', ucfirst($field)) }}</strong><p class="text-muted">{{ $value }}</p></div>
                @endif
            @endforeach
        </div>
    @endif

    @if($contact->is_export)
        <h4>Export Information</h4>
        <div class="row">
            @for($i = 1; $i <= 6; $i++)
                @php($field = 'export_custom_field_' . $i)
                @if(!empty($contact->$field))
                    <div class="col-sm-3"><strong>{{ __('lang_v1.export_custom_field' . $i) }}</strong><p class="text-muted">{{ $contact->$field }}</p></div>
                @endif
            @endfor
        </div>
    @endif

    <hr>
    <h4>Business Information</h4>
    <div class="row">
        @if($contact->monthly_business_potential !== null)
            <div class="col-sm-4"><strong>Monthly Business Potential</strong><p class="text-muted"><span class="display_currency" data-currency_symbol="true">{{ $contact->monthly_business_potential }}</span></p></div>
        @endif
        @if($contact->head_office_address)<div class="col-sm-4"><strong>Head Office Address</strong><p class="text-muted">{!! nl2br(e($contact->head_office_address)) !!}</p></div>@endif
        @if($contact->factory_address)<div class="col-sm-4"><strong>Factory Address</strong><p class="text-muted">{!! nl2br(e($contact->factory_address)) !!}</p></div>@endif
    </div>

    @if($contact->customerContactPersons->isNotEmpty())
        <h4>Contact Persons</h4>
        <div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr><th>Name</th><th>Designation</th><th>Mobile</th><th>Email</th></tr></thead><tbody>
            @foreach($contact->customerContactPersons as $person)
                <tr><td>{{ $person->name }}</td><td>{{ $person->designation }}</td><td>{{ $person->mobile }}</td><td>{{ $person->email }}</td></tr>
            @endforeach
        </tbody></table></div>
    @endif

    @php($machineTypes = $contact->machineTypeValues)
    @if(count($machineTypes) || $contact->machineBrands->isNotEmpty() || $contact->machine_quantity !== null || $contact->machine_details)
        <h4>Machine Information</h4>
        <div class="row">
            @if(count($machineTypes))<div class="col-sm-3"><strong>Machine Types</strong><p class="text-muted">{{ implode(', ', $machineTypes) }}</p></div>@endif
            @if($contact->machineBrands->isNotEmpty())<div class="col-sm-3"><strong>Machine Brands</strong><p class="text-muted">{{ $contact->machineBrands->pluck('name')->implode(', ') }}</p></div>@endif
            @if($contact->machine_quantity !== null)<div class="col-sm-3"><strong>Machine Quantity</strong><p class="text-muted">{{ $contact->machine_quantity }}</p></div>@endif
            @if($contact->machine_details)<div class="col-sm-12"><strong>Machine Details</strong><p class="text-muted">{!! nl2br(e($contact->machine_details)) !!}</p></div>@endif
        </div>
    @endif
</div>
