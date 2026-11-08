<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $receipt_details->invoice_no ?? '' }}</title>
    <style>
        @page { size: A4; margin: 12mm 12mm 22mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #222; font: 12px Arial, sans-serif; }
        .header { display: table; width: 100%; table-layout: fixed; border-bottom: 2px solid #2f5597; padding-bottom: 10px; }
        .header-cell { display: table-cell; vertical-align: middle; }
        .header-left, .header-right { width: 22%; text-align: center; }
        .header-center { width: 56%; text-align: center; }
        .header img { max-width: 100%; max-height: 75px; object-fit: contain; }
        .brand-logo { max-width: 48%; max-height: 52px !important; margin: 2px; vertical-align: middle; }
        .company-name { margin: 0 0 5px; color: #1f3763; font-size: 20px; }
        .company-info { line-height: 1.5; }
        .email-website { display: block; white-space: nowrap; font-size: 10px; }
        h2 { margin: 12px 0 8px; text-align: center; font-size: 18px; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; }
        .info { margin: 10px 0 14px; }
        .info td { width: 50%; padding: 4px 7px; border: 0; }
        .info strong { color: #333; }
        .items th, .items td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        .items th { background: #edf1f7; text-align: left; }
        .number { text-align: right; white-space: nowrap; }
        .totals { width: 43%; margin: 10px 0 0 auto; }
        .totals td { padding: 4px 6px; }
        .grand-total { border-top: 2px solid #333; font-size: 14px; font-weight: bold; }
        .terms { margin-top: 12px; white-space: pre-line; }
        .footer-image { position: fixed; bottom: -16mm; left: 0; width: 100%; text-align: center; }
        .footer-image img { display: block; width: 100%; height: auto; max-height: 25mm; object-fit: contain; }
        .no-print { margin: 12px 0; text-align: center; }
        @media screen { body { max-width: 210mm; margin: 18px auto; padding: 12mm; box-shadow: 0 0 5px #aaa; } .footer-image { display: none; } }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    @if(!empty($browser_print))
        <div class="no-print"><button type="button" onclick="window.print()">Print Quotation</button></div>
    @endif
    <div class="header">
        <div class="header-cell header-left">
            @if($branding['primary_logo'])<img src="{{ $branding['primary_logo'] }}" alt="Company Logo">@endif
        </div>
        <div class="header-cell header-center">
            <h1 class="company-name">{{ $branding['company_name'] ?: ($receipt_details->business_name ?? '') }}</h1>
            <div class="company-info">
                @if($branding['address']){{ $branding['address'] }}<br>@endif
                @if($branding['mobile'])Mobile: {{ $branding['mobile'] }}<br>@endif
                @if($branding['email'] || $branding['website'])
                    <span class="email-website">
                        @if($branding['email'])Email: {{ $branding['email'] }}@endif
                        @if($branding['email'] && $branding['website']) &nbsp;|&nbsp; @endif
                        @if($branding['website']){{ $branding['website'] }}@endif
                    </span>
                @endif
            </div>
        </div>
        <div class="header-cell header-right">
            @if($branding['bd_logo'])<img class="brand-logo" src="{{ $branding['bd_logo'] }}" alt="B&amp;D">@endif
            @if($branding['atlas_logo'])<img class="brand-logo" src="{{ $branding['atlas_logo'] }}" alt="Atlas Copco">@endif
        </div>
    </div>

    <h2>{{ !empty($sub_status) && $sub_status === 'proforma' ? 'PROFORMA INVOICE' : 'QUOTATION' }}</h2>
    <table class="info">
        <tr>
            <td><strong>Quotation No:</strong> {{ $receipt_details->invoice_no ?: '--' }}</td>
            <td><strong>Date:</strong> {{ $receipt_details->invoice_date ?: '--' }}</td>
        </tr>
        <tr>
            <td><strong>Requisition Number:</strong> {{ $transaction->requisition_number ?: '--' }}</td>
            <td><strong>Group Name:</strong> {{ $customer_group_name ?: '--' }}</td>
        </tr>
        <tr>
            <td><strong>Customer Name:</strong> {{ optional($transaction->contact)->name ?: '--' }}</td>
            <td><strong>Contact Person:</strong> {{ optional($transaction->contact_person)->name ?: '--' }}</td>
        </tr>
        <tr>
            <td><strong>Mobile:</strong> {{ $transaction->contact_person_mobile ?: (optional($transaction->contact_person)->mobile ?: optional($transaction->contact)->mobile ?: '--') }}</td>
            <td><strong>Email:</strong> {{ $transaction->contact_person_email ?: (optional($transaction->contact_person)->email ?: optional($transaction->contact)->email ?: '--') }}</td>
        </tr>
    </table>

    <table class="items">
        <thead><tr><th style="width:5%">#</th><th>Description</th><th style="width:10%">Qty</th><th style="width:15%" class="number">Unit Price</th><th style="width:14%" class="number">Discount</th><th style="width:17%" class="number">Amount</th></tr></thead>
        <tbody>
        @foreach($transaction->sell_lines as $index => $line)
            @php
                $product = $line->product;
                $variation = $line->variations;
                $quantity = (float) $line->quantity;
                $unit_price = (float) ($line->unit_price_before_discount ?? $line->unit_price ?? 0);
                $line_discount = (float) $line->get_discount_amount() * $quantity;
                $line_total = (float) ($line->unit_price_inc_tax ?? $line->unit_price ?? 0) * $quantity;
                $brand_name = optional(optional($product)->brand)->name;
                $product_name = preg_replace('/\bBD\s*-\s*\d+\s*[A-Z]?\b/i', '', optional($product)->name ?? '');
                $product_name = trim(preg_replace('/\s{2,}/u', ' ', $product_name), " \t\n\r,;:-");
                $variation_name = $variation && $variation->name !== 'DUMMY'
                    ? preg_replace('/\bBD\s*-\s*\d+\s*[A-Z]?\b/i', '', $variation->name)
                    : '';
                $description = strip_tags(optional($product)->product_description ?? '');
                $description = preg_replace('/\bBD\s*-\s*\d+\s*[A-Z]?\b/i', '', $description);
                $description = trim(preg_replace('/\s{2,}/u', ' ', $description), " \t\n\r,;:-");
            @endphp
            @if(empty($line->parent_sell_line_id))
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product_name ?: '--' }}{{ $variation_name ? ' - '.trim($variation_name) : '' }}</strong>
                        @if($brand_name)<br>Brand: {{ $brand_name }}@endif
                        @if($description)<br>{{ strip_tags($description) }}@endif
                    </td>
                    <td>{{ rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.') }} {{ optional(optional($product)->unit)->short_name }}</td>
                    <td class="number">{{ number_format($unit_price, 2) }}</td>
                    <td class="number">{{ $line_discount > 0 ? number_format($line_discount, 2) : '--' }}</td>
                    <td class="number">{{ number_format($line_total, 2) }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>

    @php
        $currency = $receipt_details->currency['symbol'] ?? '';
        $subtotal = (float) ($transaction->total_before_tax ?? 0);
        $discount_value = (float) ($transaction->discount_amount ?? 0);
        $discount = $transaction->discount_type === 'percentage' ? $subtotal * $discount_value / 100 : $discount_value;
        $vat = (float) ($transaction->vat_amount ?? 0);
        $grand_total = (float) ($transaction->final_total ?? 0);
        $order_tax = (float) ($transaction->tax_amount ?? 0);
        $shipping = (float) ($transaction->shipping_charges ?? 0);
        $packing_charge = (float) ($transaction->packing_charge ?? 0);
        $vat_label = $transaction->vat_type === 'percentage'
            ? 'VAT ('.rtrim(rtrim(number_format((float) $transaction->vat_rate, 2, '.', ''), '0'), '.').'%)'
            : 'VAT (Flat)';
    @endphp
    <table class="totals">
        <tr><td>Subtotal</td><td class="number">{{ $currency }}{{ number_format($subtotal, 2) }}</td></tr>
        @if($discount > 0)<tr><td>Discount{{ $transaction->discount_type === 'percentage' ? ' ('.rtrim(rtrim(number_format($discount_value, 2, '.', ''), '0'), '.').'%)' : '' }}</td><td class="number">- {{ $currency }}{{ number_format($discount, 2) }}</td></tr>@endif
        @if($order_tax > 0)<tr><td>Order Tax ({{ optional($transaction->tax)->name ?: 'Tax' }})</td><td class="number">{{ $currency }}{{ number_format($order_tax, 2) }}</td></tr>@endif
        @if(!empty($transaction->vat_type))<tr><td>{{ $vat_label }}</td><td class="number">{{ $currency }}{{ number_format($vat, 2) }}</td></tr>@endif
        @if($shipping > 0)<tr><td>Shipping</td><td class="number">{{ $currency }}{{ number_format($shipping, 2) }}</td></tr>@endif
        @if($packing_charge > 0)<tr><td>Packing Charge</td><td class="number">{{ $currency }}{{ number_format($packing_charge, 2) }}</td></tr>@endif
        <tr class="grand-total"><td>Grand Total</td><td class="number">{{ $currency }}{{ number_format($grand_total, 2) }}</td></tr>
    </table>

    @if(!empty($transaction->payment_terms))<div class="terms"><strong>Payment Terms:</strong> {{ $transaction->payment_terms }}</div>@endif
    @if(!empty($transaction->shipping_details))<div class="terms"><strong>Shipping Details:</strong> {{ $transaction->shipping_details }}</div>@endif
    @if(!empty($transaction->additional_notes))<div class="terms"><strong>Terms / Notes:</strong><br>{{ $transaction->additional_notes }}</div>@endif
    @if($branding['footer_image'])<div class="footer-image"><img src="{{ $branding['footer_image'] }}" alt="Quotation footer"></div>@endif
    @if(!empty($browser_print))<script>window.addEventListener('load', function () { window.print(); });</script>@endif
</body>
</html>
