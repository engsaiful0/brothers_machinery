@php
    $packing_slip_names = array_filter([
        trim((string) ($receipt_details->business_name ?? '')),
        trim((string) ($receipt_details->location_name ?? '')),
    ]);
    $packing_slip_unique_names = [];
    $packing_slip_seen_names = [];
    foreach ($packing_slip_names as $name) {
        $normalized_name = mb_strtolower($name);
        if (!in_array($normalized_name, $packing_slip_seen_names, true)) {
            $packing_slip_unique_names[] = $name;
            $packing_slip_seen_names[] = $normalized_name;
        }
    }
    $packing_slip_display_names = array_values(array_filter($packing_slip_unique_names, function ($name) {
        return strcasecmp($name, 'Brothers Machinery') !== 0;
    }));
    $packing_slip_display_name = implode(', ', $packing_slip_display_names);
    $packing_slip_address = trim((string) ($receipt_details->address ?? ''));
    foreach ($packing_slip_unique_names as $name) {
        $packing_slip_address = preg_replace('/^\s*'.preg_quote($name, '/').'\b(?:\s*[,;:|–—-]\s*|\s*<br\s*\/?\s*>\s*)?/i', '', $packing_slip_address, 1);
    }
    $packing_shipping_address = trim((string) ($receipt_details->shipping_address ?? ''));
    $packing_shipping_address = preg_replace('/^\s*Brothers Machinery\b(?:\s*[,;:|–—-]\s*|\s*<br\s*\/?\s*>\s*)?/i', '', $packing_shipping_address);
@endphp
<style>
    .packing-slip {
        max-width: 210mm;
        margin: 0 auto;
        padding: 24px;
        background: #fff;
        color: #222;
        font-family: Arial, sans-serif;
        font-size: 13px;
        line-height: 1.5;
    }
    .packing-slip, .packing-slip * { box-sizing: border-box; }
    .packing-slip .row { margin: 0; }
    .packing-slip .row::after { content: ""; display: table; clear: both; }
    .packing-slip .invoice-col { float: left; width: 50%; padding: 0 16px 0 0; }
    .packing-slip .invoice-col + .invoice-col { padding: 0 0 0 16px; }
    .packing-slip .col-xs-12 { float: none; width: 100%; padding: 0; }
    .packing-slip p { margin: 0 0 8px; }
    .packing-slip .color-555 { color: #222 !important; }
    .packing-slip .font-30 { font-size: 18px; line-height: 1.4; }
    .packing-slip .font-23 { font-size: 13px; line-height: 1.5; }
    .packing-slip .text-right { text-align: right; }
    .packing-slip .text-center { text-align: center; }
    .packing-slip .pull-left { float: left; }
    .packing-slip .word-wrap { overflow-wrap: anywhere; }
    .packing-slip-title {
        padding-bottom: 14px;
        border-bottom: 2px solid #333;
        margin-bottom: 20px !important;
        font-size: 24px !important;
        font-weight: bold;
        letter-spacing: 1px;
        text-align: left !important;
    }
    .packing-slip-business { margin-bottom: 18px !important; }
    .packing-slip-business img { max-height: 80px; max-width: 100%; width: auto; margin-bottom: 8px; }
    .packing-slip-business-name { font-size: 20px; font-weight: bold; color: #222; }
    .packing-slip-reference p { padding-bottom: 8px; border: 0; }
    .packing-slip-parties {
        margin-bottom: 20px !important;
        border: 0;
        page-break-inside: avoid;
    }
    .packing-slip-parties td { padding-top: 16px; }
    .packing-slip-info-table { width: 100%; border: 0; border-collapse: collapse; table-layout: fixed; }
    .packing-slip-info-table td { width: 50%; border: 0; padding: 0 16px 0 0; vertical-align: top; }
    .packing-slip-info-table td + td { padding: 0 0 0 16px; }
    .packing-slip-reference { text-align: left; }
    .salan_info { margin: 0 0 8px; padding: 0; font-size: 18px; font-weight: normal; line-height: 1.5; }
    .salan_info strong { font-weight: bold; }
    .packing-slip-items { width: 100%; border-collapse: collapse; table-layout: fixed; margin: 0; }
    .packing-slip-items th, .packing-slip-items td {
        padding: 10px 12px;
        border: 1px solid #ccc;
        vertical-align: top;
        font-size: 13px;
        overflow-wrap: anywhere;
        word-break: normal;
    }
    .packing-slip-items th { background: #f1f3f5 !important; color: #222 !important; font-weight: bold; }
    .packing-slip-items thead { display: table-header-group; }
    .packing-slip-items tr { page-break-inside: avoid; }
    .packing-slip-items .packing-slip-number { width: 5%; text-align: center; }
    .packing-slip-items .packing-slip-name { width: 22%; text-align: left; }
    .packing-slip-items .packing-slip-brand { width: 13%; text-align: left; }
    .packing-slip-items .packing-slip-part { width: 14%; text-align: left; }
    .packing-slip-items .packing-slip-description { width: 30%; text-align: left; }
    .packing-slip-items .packing-slip-quantity { width: 16%; text-align: right; }
    .packing-slip-product-name { font-weight: bold; }
    .packing-slip-product-detail { margin-top: 2px; font-size: 12px; }
    .packing-slip-reference-table { font-size: 18px; }
    .packing-slip-signature { padding-top: 86px; margin-bottom: 20px !important; page-break-inside: avoid; }
    .packing-slip-signature b { display: inline-block; padding-top: 8px; border-top: 1px solid #555; min-width: 180px; }
    .packing-slip .center-block { display: block; max-width: 100%; margin: 0 auto; }
    .packing-slip-footer { margin-top: 20px !important; padding-top: 12px; border-top: 1px solid #ddd; }
    @media screen and (max-width: 600px) {
        .packing-slip { padding: 16px; }
        .packing-slip .invoice-col { float: none; width: 100%; padding: 0; }
        .packing-slip .invoice-col + .invoice-col { padding: 16px 0 0; }
        .packing-slip-items th, .packing-slip-items td { padding: 8px 6px; }
    }
    @media print {
        .packing-slip { max-width: none; padding: 0; font-size: 11pt; }
        .packing-slip-document > thead { display: table-row-group; }
    }
</style>
<div class="packing-slip">
<table class="packing-slip-document" style="width:100%; color: #000000 !important;">
	<thead>
		<tr>
			<td>

			<p class="packing-slip-title text-right color-555 font-30">
				@lang('lang_v1.packing_slip')
			</p>

			</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>

<!-- business information here -->
	<table class="packing-slip-info-table packing-slip-business">
	<tr>
	<td class="color-555">
		<!-- Logo -->
		@if(!empty($receipt_details->logo))
			<img src="{{$receipt_details->logo}}" class="img">
			<br/>
		@endif

		<!-- Shop & Location Name  -->
		@if(!empty($packing_slip_display_name) || !empty($packing_slip_address) || !empty($receipt_details->contact) || !empty($receipt_details->website) || !empty($receipt_details->tax_info1) || !empty($receipt_details->tax_info2) || !empty($receipt_details->location_custom_fields))
			<p>
				<span class="packing-slip-business-name">{{$packing_slip_display_name}}</span>
				@if(!empty($packing_slip_address))
					<br/>{!! $packing_slip_address !!}
				@endif

				@if(!empty($receipt_details->contact))
					<br/>{!! $receipt_details->contact !!}
				@endif

				@if(!empty($receipt_details->website))
					<br/>{{ $receipt_details->website }}
				@endif

				@if(!empty($receipt_details->tax_info1) && stripos((string) ($receipt_details->tax_label1 ?? ''), 'vat') === false)
					<br/>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
				@endif

				@if(!empty($receipt_details->tax_info2) && stripos((string) ($receipt_details->tax_label2 ?? ''), 'vat') === false)
					<br/>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
				@endif

				@if(!empty($receipt_details->location_custom_fields))
					<br/>{{ $receipt_details->location_custom_fields }}
				@endif
			</p>
		@endif
	</td>

	<td class="packing-slip-reference color-555">
		<p class="salan_info">
			<strong>Salan No:</strong> {{ $receipt_details->delivery_challan_no ?: '--' }}
			@if(!empty($receipt_details->date_label))
				<br><strong>{{ $receipt_details->date_label }}</strong> {{ $receipt_details->invoice_date }}
			@endif
			<br><strong>@lang('sale.invoice_no'):</strong> {{ $receipt_details->invoice_no }}
		</p>
	</td>
	</tr>
	</table>

	<table class="packing-slip-info-table color-555 packing-slip-parties">
	<tr>
	<td class="word-wrap">
		<strong>Group Name:</strong> {{ $receipt_details->packing_customer_group_name ?: '--' }}<br>
		<strong>Customer Name:</strong> {{ $receipt_details->packing_customer_name ?: '--' }}<br>
		<strong>Contact Person:</strong> {{ $receipt_details->customer_contact_person_name ?: '--' }}<br>
		<strong>Mobile:</strong> {{ $receipt_details->packing_contact_mobile ?: '--' }}<br>
		<strong>Email:</strong> {{ $receipt_details->packing_contact_email ?: '--' }}
	</td>
	<td class="word-wrap">
		<strong>@lang('lang_v1.shipping_address'):</strong><br>
		{!! $packing_shipping_address !!}
		@if(!empty($receipt_details->shipping_custom_field_1_label))
			<br><strong>{!!$receipt_details->shipping_custom_field_1_label!!} :</strong> {!!$receipt_details->shipping_custom_field_1_value ?? ''!!}
		@endif

		@if(!empty($receipt_details->shipping_custom_field_2_label))
			<br><strong>{!!$receipt_details->shipping_custom_field_2_label!!}:</strong> {!!$receipt_details->shipping_custom_field_2_value ?? ''!!}
		@endif

		@if(!empty($receipt_details->shipping_custom_field_3_label))
			<br><strong>{!!$receipt_details->shipping_custom_field_3_label!!}:</strong> {!!$receipt_details->shipping_custom_field_3_value ?? ''!!}
		@endif

		@if(!empty($receipt_details->shipping_custom_field_4_label))
			<br><strong>{!!$receipt_details->shipping_custom_field_4_label!!}:</strong> {!!$receipt_details->shipping_custom_field_4_value ?? ''!!}
		@endif

		@if(!empty($receipt_details->shipping_custom_field_5_label))
			<br><strong>{!!$receipt_details->shipping_custom_field_2_label!!}:</strong> {!!$receipt_details->shipping_custom_field_5_value ?? ''!!}
		@endif
	</td>
	</tr>
	</table>

<div class="row color-555">
	<div class="col-xs-12">
		<table class="packing-slip-items">
			<thead>
                <tr>
                    <th scope="col" class="packing-slip-number">#</th>
                    <th scope="col" class="packing-slip-name">Product Name</th>
                    <th scope="col" class="packing-slip-brand">Brand</th>
                    <th scope="col" class="packing-slip-part">Part Number</th>
                    <th scope="col" class="packing-slip-description">Description</th>
                    <th scope="col" class="packing-slip-quantity">{{$receipt_details->table_qty_label}}</th>
                </tr>
			</thead>
			<tbody>
				@foreach($receipt_details->lines as $line)
					@php
						$product_name = html_entity_decode(strip_tags($line['name'] ?? ''), ENT_QUOTES, 'UTF-8');
						$part_number = $line['part_number'] ?? '';
						if ($part_number === '') $part_number = $line['sub_sku'] ?? '';
						$product_name = preg_replace('/\bBD\s*-\s*\d+\b/i', '', $product_name);
						$product_name = trim(preg_replace('/\s{2,}/u', ' ', $product_name));
						$product_description = html_entity_decode(strip_tags($line['product_description'] ?? ''), ENT_QUOTES, 'UTF-8');
						$product_description = preg_replace('/\bBD\s*-\s*\d+\b/i', '', $product_description);
						$product_description = trim(preg_replace('/\s{2,}/u', ' ', $product_description));
					@endphp
					<tr>
						<td class="text-center">
							{{$loop->iteration}}
						</td>
						<td><div class="packing-slip-product-name">{{ $product_name }}@if(!empty($line['product_variation']) && $line['product_variation'] != 'DUMMY') ({{ $line['product_variation'] }})@endif @if(!empty($line['variation']) && $line['variation'] != 'DUMMY') {{ $line['variation'] }}@endif</div></td>
						<td>{{ $line['brand'] ?? '' }}</td>
						<td>{{ $part_number }}</td>
						<td>
                            {{ trim($product_description, " \t\n\r,;:-") }}
                            @if(!empty($line['product_custom_fields']))<div class="packing-slip-product-detail">{{ $line['product_custom_fields'] }}</div>@endif
                            @if(!empty($line['sell_line_note']))<div class="packing-slip-product-detail">{{ strip_tags($line['sell_line_note']) }}</div>@endif
                            @if(!empty($line['lot_number']))<div class="packing-slip-product-detail">{{ $line['lot_number_label'] }}: {{ $line['lot_number'] }}</div>@endif
                            @if(!empty($line['product_expiry']))<div class="packing-slip-product-detail">{{ $line['product_expiry_label'] }}: {{ $line['product_expiry'] }}</div>@endif
                            @if(!empty($line['modifiers']))
                                @foreach($line['modifiers'] as $modifier)
                                    <div class="packing-slip-product-detail">{{ $modifier['name'] }} {{ $modifier['variation'] ?? '' }} ({{ $modifier['quantity'] }} {{ $modifier['units'] ?? '' }})</div>
                                @endforeach
                            @endif
						</td>
						<td class="text-right">
							{{$line['quantity']}} {{$line['units']}}
						</td>
					</tr>
				@endforeach

			</tbody>
		</table>
	</div>
</div>

<div class="row invoice-info color-555 packing-slip-signature">
	<div class="col-md-6 invoice-col width-50">
		<b class="pull-left">@lang('lang_v1.authorized_signatory')</b>
	</div>
	<div class="col-md-6 invoice-col width-50 text-right">
		<b>Delivery Person: {{ $receipt_details->delivery_person ?: '--' }}</b>
	</td>
	</tr>
	</table>

{{-- Barcode --}}
@if($receipt_details->show_barcode)
<br>
<div class="row">
		<div class="col-xs-12">
			<img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}">
		</div>
</div>
@endif

@if(!empty($receipt_details->footer_text))
	<div class="row color-555 packing-slip-footer">
		<div class="col-xs-12">
			{!! $receipt_details->footer_text !!}
		</div>
	</div>
@endif

			</td>
		</tr>
	</tbody>
</table>
</div>
