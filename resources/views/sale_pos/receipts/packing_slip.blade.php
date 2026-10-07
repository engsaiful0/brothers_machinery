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
    .packing-slip-reference p { padding-bottom: 8px; border-bottom: 1px solid #ddd; }
    .packing-slip-parties {
        padding: 16px 0;
        margin-bottom: 20px !important;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        page-break-inside: avoid;
    }
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
    .packing-slip-items .packing-slip-number { width: 7%; text-align: center; }
    .packing-slip-items .packing-slip-product { width: 73%; text-align: left; }
    .packing-slip-items .packing-slip-quantity { width: 20%; text-align: right; }
    .packing-slip-items .packing-slip-modifier td { color: #555; font-size: 12px; }
    .packing-slip-items .packing-slip-modifier td:nth-child(2) { padding-left: 24px; }
    .packing-slip-signature { padding-top: 56px; margin-bottom: 20px !important; page-break-inside: avoid; }
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
<div class="row invoice-info packing-slip-business">

	<div class="col-md-6 invoice-col width-50 color-555">
		
		<!-- Logo -->
		@if(!empty($receipt_details->logo))
			<img src="{{$receipt_details->logo}}" class="img">
			<br/>
		@endif

		<!-- Shop & Location Name  -->
		@if(!empty($receipt_details->display_name))
			<p>
				<span class="packing-slip-business-name">{{$receipt_details->display_name}}</span>
				@if(!empty($receipt_details->address))
					<br/>{!! $receipt_details->address !!}
				@endif

				@if(!empty($receipt_details->contact))
					<br/>{!! $receipt_details->contact !!}
				@endif

				@if(!empty($receipt_details->website))
					<br/>{{ $receipt_details->website }}
				@endif

				@if(!empty($receipt_details->tax_info1))
					<br/>{{ $receipt_details->tax_label1 }} {{ $receipt_details->tax_info1 }}
				@endif

				@if(!empty($receipt_details->tax_info2))
					<br/>{{ $receipt_details->tax_label2 }} {{ $receipt_details->tax_info2 }}
				@endif

				@if(!empty($receipt_details->location_custom_fields))
					<br/>{{ $receipt_details->location_custom_fields }}
				@endif
			</p>
		@endif
	</div>

	<div class="col-md-6 invoice-col width-50 packing-slip-reference">

		<p class="text-right font-30">
			@if(!empty($receipt_details->invoice_no_prefix))
				<span class="pull-left">{!! $receipt_details->invoice_no_prefix !!}</span>
			@endif

			{{$receipt_details->invoice_no}}
		</p>
		<!-- Date-->
		@if(!empty($receipt_details->date_label))
			<p class="text-right font-23 color-555">
				<span class="pull-left">
					{{$receipt_details->date_label}}
				</span>

				{{$receipt_details->invoice_date}}
			</p>
		@endif
	</div>
</div>

<div class="row invoice-info color-555 packing-slip-parties">
	<div class="col-md-6 invoice-col width-50 word-wrap">
		@if(!empty($receipt_details->customer_label))
			<b>{{ $receipt_details->customer_label }}</b><br/>
		@endif

		<!-- customer info -->
		@if(!empty($receipt_details->customer_name))
			{{ $receipt_details->customer_name }}<br>
		@endif
		@if(!empty($receipt_details->customer_info))
			{!! $receipt_details->customer_info !!}
		@endif
		@if(!empty($receipt_details->client_id_label))
			<br/>
			<strong>{{ $receipt_details->client_id_label }}</strong> {{ $receipt_details->client_id }}
		@endif
		@if(!empty($receipt_details->customer_tax_label))
			<br/>
			<strong>{{ $receipt_details->customer_tax_label }}</strong> {{ $receipt_details->customer_tax_number }}
		@endif
		@if(!empty($receipt_details->customer_custom_fields))
			<br/>{!! $receipt_details->customer_custom_fields !!}
		@endif
		@if(!empty($receipt_details->sales_person_label))
			<br/>
			<strong>{{ $receipt_details->sales_person_label }}</strong> {{ $receipt_details->sales_person }}
		@endif
	</div>
	<div class="col-md-6 invoice-col width-50 word-wrap">
		<strong>@lang('lang_v1.shipping_address'):</strong><br>
		{!! $receipt_details->shipping_address !!}
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
	</div>
</div>

<div class="row color-555">
	<div class="col-xs-12">
		<table class="packing-slip-items">
			<thead>
                <tr>
                    <th scope="col" class="packing-slip-number">#</th>
                    <th scope="col" class="packing-slip-product">{{$receipt_details->table_product_label}}</th>
                    <th scope="col" class="packing-slip-quantity">{{$receipt_details->table_qty_label}}</th>
                </tr>
			</thead>
			<tbody>
				@foreach($receipt_details->lines as $line)
					<tr>
						<td class="text-center">
							{{$loop->iteration}}
						</td>
						<td>
                            {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
                            @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif
                            @if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
                            @if(!empty($line['sell_line_note']))({!!$line['sell_line_note']!!}) @endif
                            @if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
                            @if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif 
                        </td>
						<td class="text-right">
							{{$line['quantity']}} {{$line['units']}}
						</td>
					</tr>
					@if(!empty($line['modifiers']))
						@foreach($line['modifiers'] as $modifier)
							<tr class="packing-slip-modifier">
								<td class="text-center">
									&nbsp;
								</td>
								<td>
		                            {{$modifier['name']}} {{$modifier['variation']}} 
		                            @if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif 
		                            @if(!empty($modifier['sell_line_note']))({!!$modifier['sell_line_note']!!}) @endif 
		                        </td>
								<td class="text-right">
									{{$modifier['quantity']}} {{$modifier['units']}}
								</td>
							</tr>
						@endforeach
					@endif
				@endforeach

				@php
					$lines = count($receipt_details->lines);
				@endphp

				@for ($i = $lines; $i < 7; $i++)
    				<tr>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    				</tr>
				@endfor

			</tbody>
		</table>
	</div>
</div>

<div class="row invoice-info color-555 packing-slip-signature">
	<div class="col-md-6 invoice-col width-50">
		<b class="pull-left">@lang('lang_v1.authorized_signatory')</b>
	</div>
</div>

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
