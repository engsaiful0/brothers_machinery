@php
    // Printed on the pre-printed Brothers Machinery pad: no header/footer of our own,
    // content stays inside the pad's blank area (same margins as the invoice).
    $contact = $transaction->contact;
    $contact_person = $transaction->contact_person;
    $sales_rep = $transaction->sales_person;

    $customer_name = optional($contact)->name ?: (optional($contact)->supplier_business_name ?? '');
    $office_address = implode(', ', array_filter(array_map('trim', [
        optional($contact)->address_line_1 ?? '',
        optional($contact)->address_line_2 ?? '',
        optional($contact)->city ?? '',
        optional($contact)->state ?? '',
    ])));
    $delivery_address = trim($transaction->shipping_address() ?: strip_tags($transaction->shipping_address ?? ''));
    $contact_name = optional($contact_person)->name ?? '';
    $contact_phone = $transaction->contact_person_mobile ?: (optional($contact_person)->mobile ?: (optional($contact)->mobile ?? ''));
    $quotation_date = ! empty($transaction->transaction_date)
        ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d-M-y')
        : ($receipt_details->invoice_date ?? '');
    $sales_rep_name = $sales_rep ? trim("{$sales_rep->surname} {$sales_rep->first_name} {$sales_rep->last_name}") : '';
    $sales_rep_contact = optional($sales_rep)->contact_number ?? '';
    $signature_name = trim($receipt_details->sales_person ?? $receipt_details->signature_user_name ?? $receipt_details->added_by ?? '');
    $signature_designation = trim($receipt_details->signature_user_designation ?? '');
    $signature_company = trim($branding['company_name'] ?? 'Brothers Machinery');
    $title = ! empty($sub_status) && $sub_status === 'proforma' ? 'PROFORMA INVOICE' : 'Price Quotation';
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $receipt_details->invoice_no ?? '' }}</title>
    <style>
        {{-- Browser print only: mPDF breaks onto dozens of pages with @page and sets its own margins.
             Page margins are 0 so the pad can cover the whole sheet (Chrome clips anything in the margins);
             the pad's header/footer space comes from the .page-frame thead/tfoot, repeated on every page. --}}
        @if(!empty($browser_print))
        @page { size: A4; margin: 0; }
        body { padding: 0 15mm; }
        .page-frame > thead > tr > td, .page-frame > tfoot > tr > td, .page-frame > tbody > tr > td { padding: 0; border: 0; }
        .page-frame > thead > tr > td { height: 40mm; }
        .page-frame > tfoot > tr > td { height: 32mm; }
        .pad-bg { position: fixed; top: 0; left: 0; width: 210mm; height: 297mm; z-index: -1; }
        .pad-bg img { display: block; width: 100%; height: 100%; }
        @media screen {
            body { position: relative; width: 210mm; max-width: 210mm; min-height: 297mm; margin: 18px auto; padding: 0; box-shadow: 0 0 5px #aaa; }
            .pad-bg { position: absolute; top: 0; left: 0; width: 100%; height: 297mm; transform: none; }
            .page-frame { width: calc(100% - 30mm); margin: 0 15mm; }
        }
        @endif
        * { box-sizing: border-box; }
        body { margin: 0; color: #000; font: 12px Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        .meta { margin-bottom: 12px; }
        .meta td { padding: 1px 0; border: 0; vertical-align: top; }
        .meta .meta-label { width: 20%; white-space: nowrap; }
        .meta .meta-colon { width: 3%; text-align: center; }
        .meta .meta-value { width: 37%; padding-right: 12px; }
        .meta .meta-label-right { width: 22%; text-align: right; white-space: nowrap; }
        .meta .meta-value-right { width: 15%; white-space: nowrap; }
        .meta .doc-title { padding-bottom: 6px; font-size: 15px; font-weight: bold; text-align: center; text-decoration: underline; }
        .meta .doc-title-spacer { height: 1.35em; padding: 0; }
        .items th, .items td { border: 1px solid #777; padding: 6px; vertical-align: top; text-align: left; }
        .items th { background: #edf1f7; }
        .number { white-space: nowrap; }
        .items .number { text-align: right; }
        .items .total-label, .items .total-value { text-align: right; font-weight: bold; }
        .terms { margin-top: 12px; white-space: pre-line; }
        .quotation-signature { margin-top: 28px; line-height: 1.8; }
        .quotation-signature-company { display: inline-block; margin-bottom: 2.5em; }
        .greeting { margin: 0 0 8px; line-height: 1.5; }
        .commercial-terms-title { margin: 18px 0 8px; text-align: center; font-weight: bold; text-decoration: underline; }
        .commercial-terms td { padding: 1px 0; border: 0; vertical-align: top; }
        .commercial-terms .term-label { width: 20%; white-space: nowrap; }
        .commercial-terms .term-colon { width: 3%; text-align: center; }
        .no-print { position: absolute; top: 0; left: 0; right: 0; margin: 12px 0; text-align: center; z-index: 1; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>
    @if(!empty($browser_print))
        <div class="no-print"><button type="button" onclick="window.print()">Print Quotation</button></div>
        {{-- The PDF draws the pad itself (see downloadQuotationPdf). --}}
        @if(!empty($branding['pad_image']))
            <div class="pad-bg"><img src="{{ $branding['pad_image'] }}" alt=""></div>
        @endif
        <table class="page-frame">
            <thead><tr><td></td></tr></thead>
            <tfoot><tr><td></td></tr></tfoot>
            <tbody><tr><td>
    @endif

    <table class="meta">
        <tr>
            <td colspan="6" class="doc-title">{{ $title }}</td>
        </tr>
        <tr>
            <td colspan="6" class="doc-title-spacer">&nbsp;</td>
        </tr>
        <tr>
            <td class="meta-label">Customer Name</td>
            <td class="meta-colon">:</td>
            <td class="meta-value"><strong>{{ $customer_name }}</strong></td>
            <td class="meta-label-right">Date</td>
            <td class="meta-colon">:</td>
            <td class="meta-value-right">{{ $quotation_date }}</td>
        </tr>
        <tr>
            <td class="meta-label">Office Address</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $office_address }}</td>
            <td class="meta-label-right">Quotation No</td>
            <td class="meta-colon">:</td>
            <td class="meta-value-right">{{ $receipt_details->invoice_no ?? $transaction->invoice_no }}</td>
        </tr>
        <tr>
            <td class="meta-label">Requisition No</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $transaction->requisition_number }}</td>
            <td class="meta-label-right">Sales Rep. Name</td>
            <td class="meta-colon">:</td>
            <td class="meta-value-right">{{ $sales_rep_name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Delivery Address</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $delivery_address }}</td>
            <td class="meta-label-right">Sales Rep. Contact</td>
            <td class="meta-colon">:</td>
            <td class="meta-value-right">{{ $sales_rep_contact }}</td>
        </tr>
        <tr>
            <td class="meta-label">Contact Name</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $contact_name }}</td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td class="meta-label">Phone No</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $contact_phone }}</td>
            <td colspan="3"></td>
        </tr>
    </table>

    @php
        $subtotal = max(0, (float) ($transaction->total_before_tax ?? 0));
        $discount_value = (float) ($transaction->discount_amount ?? 0);
        $discount = $transaction->discount_type === 'percentage' ? $subtotal * $discount_value / 100 : $discount_value;
        $discount = min(max(0, $discount), $subtotal);
        $net_total = max(0, $subtotal - $discount);
        $vat = (float) ($transaction->vat_amount ?? 0);
        $grand_total = (float) ($transaction->final_total ?? 0);
        $order_tax = (float) ($transaction->tax_amount ?? 0);
        $shipping = (float) ($transaction->shipping_charges ?? 0);
        $packing_charge = (float) ($transaction->packing_charge ?? 0);
        $amount_in_words = $receipt_details->total_in_words ?: app(\App\Utils\Util::class)->numToWord($grand_total, null, 'indian');
        $amount_in_words = format_amount_in_words(str_ireplace(['lakh', 'lakhs'], 'LAC', $amount_in_words));
        $vat_label = $transaction->vat_type === 'percentage'
            ? 'VAT on Total Amount ('.rtrim(rtrim(number_format((float) $transaction->vat_rate, 2, '.', ''), '0'), '.').'%)'
            : 'VAT on Total Amount';

        // Printed as the last rows of the product table (tbody, not tfoot, so Chrome doesn't repeat them per page).
        $total_rows = [['Total Amount', $subtotal]];
        if ($discount > 0) {
            $total_rows[] = ['Discount', $discount];
            $total_rows[] = ['Net Total', $net_total];
        }
        if (! empty($transaction->vat_type) || $vat > 0) $total_rows[] = [$vat_label, $vat];
        if ($order_tax > 0) $total_rows[] = ['Order Tax ('.(optional($transaction->tax)->name ?: 'Tax').')', $order_tax];
        if ($shipping > 0) $total_rows[] = ['Shipping', $shipping];
        if ($packing_charge > 0) $total_rows[] = ['Packing Charge', $packing_charge];
    @endphp
    @php $quotation_intro = trim((string) ($transaction->quotation_intro ?? '')) ?: \App\Transaction::DEFAULT_QUOTATION_INTRO; @endphp
    <p class="greeting">Dear Sir,<br>{!! nl2br(e($quotation_intro)) !!}</p>
    <table class="items">
        <thead><tr><th style="width:5%">Sl No.</th><th>Description</th><th style="width:10%">Qty</th><th style="width:16%" class="number">Unit Price</th><th style="width:18%" class="number">Amount</th></tr></thead>
        <tbody>
        @foreach($transaction->sell_lines as $index => $line)
            @php
                $product = $line->product;
                $variation = $line->variations;
                $quantity = (float) $line->quantity;
                // Discount is not shown, so the unit price is the net (after-discount) price.
                $unit_price = (float) ($line->unit_price_inc_tax ?? $line->unit_price ?? 0);
                $line_total = $unit_price * $quantity;
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
                        @if($brand_name)<br>BRAND: {{ mb_strtoupper($brand_name) }}@endif
                        {{-- This line's own IMEI / serial no. / other info typed on the quotation form. --}}
                        @if(trim((string) $line->sell_line_note) !== '')<br>{!! nl2br(e(trim($line->sell_line_note))) !!}@endif
                        @if($description)<br>{{ strip_tags($description) }}@endif
                    </td>
                    <td>{{ format_quantity_with_unit($quantity, optional(optional($product)->unit)->short_name, rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.')) }}</td>
                    <td class="number">{{ number_format($unit_price, 2) }}</td>
                    <td class="number">{{ number_format($line_total, 2) }}</td>
                </tr>
            @endif
        @endforeach
        @foreach($total_rows as [$label, $amount])
            <tr>
                <td colspan="4" class="total-label">{{ $label }}</td>
                <td class="number total-value">{{ number_format($amount, 2) }}</td>
            </tr>
        @endforeach
            <tr>
                <td colspan="5"><strong>IN WORDS: {{ format_amount_in_words($amount_in_words) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @if(!empty($transaction->commercial_terms))
        <div class="commercial-terms-title">COMMERCIAL TERMS &amp; CONDITIONS</div>
        <table class="commercial-terms">
            @foreach($transaction->commercial_terms as $term)
                <tr>
                    <td class="term-label">{{ $term['title'] }}</td>
                    <td class="term-colon">:</td>
                    <td>{{ $term['description'] }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if(!empty($transaction->payment_terms))<div class="terms"><strong>Payment Terms:</strong> {{ $transaction->payment_terms }}</div>@endif
    @if(!empty($transaction->shipping_details))<div class="terms"><strong>Shipping Details:</strong> {{ $transaction->shipping_details }}</div>@endif
    @if(!empty($transaction->additional_notes))<div class="terms"><strong>Terms / Notes:</strong><br>{{ $transaction->additional_notes }}</div>@endif
    <div class="quotation-signature">
        <strong>Best Regards</strong><br>
        From <span class="quotation-signature-company"><strong>{{ $signature_company }}</strong></span><br>
        @if($signature_name)<strong>{{ $signature_name }}</strong><br>@endif
        @if($signature_designation){{ $signature_designation }}@endif
    </div>
    @if(!empty($browser_print))
            </td></tr></tbody>
        </table>
    @endif
    @if(!empty($browser_print))<script>window.addEventListener('load', function () { window.print(); });</script>@endif
</body>
</html>
