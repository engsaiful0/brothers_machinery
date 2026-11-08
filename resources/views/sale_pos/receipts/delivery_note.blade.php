@php
	$delivery_note_business_name = trim((string) ($receipt_details->display_name ?? ''));
	$delivery_note_address = trim((string) ($receipt_details->address ?? ''));
	$delivery_note_address = preg_replace('/^\s*Brothers Machinery\b(?:\s*[,;:|\-]\s*|\s*<br\s*\/?\s*>\s*)?/i', '', $delivery_note_address);
@endphp
<table style="width:100%;">
	<thead>
		<tr>
			<td>

			<p class="text-right color-555 font-30">
              <b>@lang('lang_v1.delivery_note')</b>
			</p>

			</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>

<!-- business information here -->
<div class="row invoice-info">

	<div class="col-md-6 invoice-col width-50 color-555">
		
		<!-- Logo -->
		@if(!empty($receipt_details->logo))
			<img style="max-height: 120px; width: auto;" src="{{$receipt_details->logo}}" class="img">
			<br/>
		@endif

		<!-- Shop & Location Name  -->
		@if(!empty($delivery_note_business_name) || !empty($delivery_note_address) || !empty($receipt_details->contact) || !empty($receipt_details->website) || !empty($receipt_details->tax_info1) || !empty($receipt_details->tax_info2) || !empty($receipt_details->location_custom_fields))
			<p><span style="font-size:24px; font-weight:900; color:black;">
				{{$delivery_note_business_name}}</span>
				@if(!empty($delivery_note_address))
					<br/>{!! $delivery_note_address !!}
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

	<div class="col-md-6 invoice-col width-50">

		<p class="text-right font-17">
			@if(!empty($receipt_details->invoice_no_prefix))
				<span class="pull-left">{!! $receipt_details->invoice_no_prefix !!}</span>
			@endif

			{{$receipt_details->invoice_no}}
		</p>
		<!-- Date-->
		@if(!empty($receipt_details->date_label))
			<p class="text-right font-17">
				<span class="pull-left">
					{{$receipt_details->date_label}}
				</span>

				{{$receipt_details->invoice_date}}
			</p>
		@endif

		
	</div>
	<div class="col-md-6 invoice-col width-50 word-wrap">
		<strong>Group Name:</strong> {{ $receipt_details->packing_customer_group_name ?: '--' }}<br/>
		<strong>Customer Name:</strong> {{ $receipt_details->packing_customer_name ?: ($receipt_details->customer_name ?? '--') }}<br/>
		<strong>Contact Person:</strong> {{ $receipt_details->customer_contact_person_name ?: '--' }}<br/>
		<strong>Mobile:</strong> {{ $receipt_details->packing_contact_mobile ?: ($receipt_details->customer_mobile ?? '--') }}<br/>
		<strong>Email:</strong> {{ $receipt_details->packing_contact_email ?: ($receipt_details->customer_email ?? '--') }}
		@if(!empty($receipt_details->client_id_label))
			<br/>
			<strong>{{ $receipt_details->client_id_label }}</strong> {{ $receipt_details->client_id }}
		@endif
		@if(!empty($receipt_details->customer_tax_number))
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
</div>
<!-- 
<div class="row invoice-info color-555">
	<br/>
	<div class="col-md-6 invoice-col width-50 word-wrap">
		@if(!empty($receipt_details->customer_label))
			<b>{{ $receipt_details->customer_label }}</b><br/>
		@endif

		
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
 -->

<div class="row color-555">
	<div class="col-xs-12">
		<br/>
		<table class="table table-bordered table-no-top-cell-border">
			<thead>
				<tr style="background-color: #357ca5 !important; color: white !important; font-size: 20px !important" class="table-no-side-cell-border table-no-top-cell-border text-center">
					<td style="background-color: #357ca5 !important; color: white !important; width: 75% !important">
						Product
					</td>
					<td style="background-color: #357ca5 !important; color: white !important; width: 25% !important;">
						Quantity
					</td>
				</tr>
			</thead>
			<tbody>
				@foreach($receipt_details->lines as $line)
					@php
						$product_name = trim(($line['name'] ?? '').' '.($line['product_variation'] ?? '').' '.($line['variation'] ?? ''));
						$product_name = preg_replace('/\bBD\s*-\s*\d+\s*[A-Z]?\b/i', '', $product_name);
						$product_name = trim(preg_replace('/\s{2,}/u', ' ', $product_name), " \t\n\r,;:-");
					@endphp
					<tr>
						<td style="word-break: break-word;">
							<strong>{{ $product_name }}</strong>
							@if(!empty($line['brand']))<br>Brand Name: {{ $line['brand'] }}@endif
							@if(!empty($line['product_description']))<br>Product Description: {{ strip_tags($line['product_description']) }}@endif
						</td>
						<td class="text-right">
							{{$line['quantity']}} {{$line['units']}}
						</td>
					</tr>
					@if(!empty($line['modifiers']))
						@foreach($line['modifiers'] as $modifier)
							<tr>
								<td>
			                    {{ trim(preg_replace('/\s{2,}/u', ' ', preg_replace('/\bBD\s*-\s*\d+\s*[A-Z]?\b/i', '', trim(($modifier['name'] ?? '').' '.($modifier['variation'] ?? ''))))) }}
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

				@for ($i = $lines; $i < 1; $i++)
    				<tr>
    					<td>&nbsp;</td>
    					<td>&nbsp;</td>
    				</tr>
				@endfor

			</tbody>
		</table>
	</div>
</div>

<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
	<div class="col-md-6 invoice-col width-50">
		<b class="pull-left">@lang('lang_v1.above_mentioned_items_received_in_good_condition')</b>
	</div>
</div>
</br>
<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
	<div class="col-md-6 invoice-col width-80">
		<b class="pull-left">@lang('lang_v1.received_by') : </b>
	</div>
</div>
</br>
<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
	<div class="col-md-6 invoice-col width-50">
		<b class="pull-left">@lang('lang_v1.date'):</b>
	</div>
</div>
</br>
<div class="row invoice-info color-555" style="page-break-inside: avoid !important">
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
	<div class="row color-555">
		<div class="col-xs-12">
			{!! $receipt_details->footer_text !!}
		</div>
	</div>
@endif

			</td>
		</tr>
	</tbody>
</table>
