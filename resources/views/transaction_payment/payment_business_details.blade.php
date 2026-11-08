@lang('business.business'):
@php
    $business_name = trim((string) optional($transaction->business)->name);
    $business_details = array_filter([
        optional($transaction->location)->name,
        optional($transaction->location)->landmark,
        implode(', ', array_filter([
            optional($transaction->location)->city,
            optional($transaction->location)->state,
            optional($transaction->location)->country,
        ])),
    ]);
    $seen_business_details = $business_name !== '' ? [mb_strtolower($business_name)] : [];
    $unique_business_details = [];
    foreach ($business_details as $detail) {
        $detail = trim((string) $detail);
        $normalized_detail = mb_strtolower($detail);
        if ($detail !== '' && !in_array($normalized_detail, $seen_business_details, true)) {
            $unique_business_details[] = $detail;
            $seen_business_details[] = $normalized_detail;
        }
    }
@endphp
<address>
    <strong>{{ $business_name }}</strong>
    @foreach($unique_business_details as $detail)
        <br>{{ $detail }}
    @endforeach
  
    @if(!empty($transaction->business->tax_number_1))
        <br>{{$transaction->business->tax_label_1}}: {{$transaction->business->tax_number_1}}
    @endif

    @if(!empty($transaction->business->tax_number_2))
        <br>{{$transaction->business->tax_label_2}}: {{$transaction->business->tax_number_2}}
    @endif

    @if(!empty($transaction->location->mobile))
        <br>@lang('contact.mobile'): {{$transaction->location->mobile}}
    @endif
    @if(!empty($transaction->location->email))
        <br>@lang('business.email'): {{$transaction->location->email}}
    @endif
</address>
