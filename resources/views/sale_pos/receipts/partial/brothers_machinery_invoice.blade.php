@php
    $invoice_number = trim($receipt_details->invoice_no ?? '');
    $invoice_number = preg_replace('/^invoice\s*no\.?\s*/i', '', $invoice_number);
    $bill_no = 'BILL#' . ltrim($invoice_number, '# ');

    $bill_date = ! empty($receipt_details->transaction_date)
        ? \Carbon\Carbon::parse($receipt_details->transaction_date)->format('d.m.Y')
        : ($receipt_details->invoice_date ?? '');

    $customer_company = ! empty($receipt_details->customer_supplier_business_name)
        ? $receipt_details->customer_supplier_business_name
        : ($receipt_details->customer_name ?? $receipt_details->contact_name ?? '');

    $po_ref = trim($receipt_details->purchase_order_no ?? $receipt_details->brothers_po_ref ?? ($receipt_details->sell_custom_field_1_value ?? ''));
    $po_date = trim($receipt_details->purchase_order_date ?? '');
    $challan_no = trim($receipt_details->brothers_challan_no ?? ($receipt_details->sell_custom_field_2_value ?? ''));
    $second_date = trim($receipt_details->brothers_second_date ?? ($receipt_details->sell_custom_field_3_value ?? ''));
    if ($second_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}/', $second_date)) {
        try {
            $second_date = \Carbon\Carbon::parse($second_date)->format('d.m.Y');
        } catch (\Exception $e) {
            // keep as entered
        }
    }

    $gross_total = $receipt_details->subtotal ?? '0.00';
    $discount_total = $receipt_details->discount ?? '0.00';
    $net_total = $receipt_details->total ?? '0.00';
    $net_unformatted = $receipt_details->total_unformatted ?? 0;

    $util = app(\App\Utils\Util::class);
    if (! empty($receipt_details->total_in_words)) {
        $amount_in_words = 'BDT ' . strtoupper(str_ireplace(['lakh', 'lakhs'], 'LAC', $receipt_details->total_in_words)) . ' ONLY.';
    } else {
        $words_raw = strtoupper(trim($util->numToWord($net_unformatted, null, 'indian')));
        $words_raw = preg_replace('/\s+/', ' ', $words_raw);
        $words_raw = str_ireplace(['lakh', 'lakhs'], 'LAC', $words_raw);
        $amount_in_words = 'BDT ' . $words_raw . ' ONLY.';
    }

    $signature_name = trim($receipt_details->sales_person ?? $receipt_details->signature_user_name ?? $receipt_details->added_by ?? '');
    $signature_designation = trim($receipt_details->signature_user_designation ?? '');
    $signature_contact = trim($receipt_details->signature_user_contact ?? '');

    $default_signature = 'Best regards<br><span class="brothers-signature-from">From <strong>Brothers Machinery</strong></span>';
    if ($signature_name !== '') {
        $default_signature .= '<br><strong>' . e($signature_name) . '</strong>';
    }
    if ($signature_designation !== '') {
        $default_signature .= '<br>' . e($signature_designation);
    }
    if ($signature_contact !== '') {
        $default_signature .= '<br>Cell # ' . e($signature_contact);
    }
@endphp

<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }

    .brothers-invoice {
        color: #000;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.35;
        max-width: 210mm;
        margin: 0 auto;
        padding: 42mm 15mm 32mm 15mm;
        box-sizing: border-box;
        background: transparent;
    }

    .brothers-invoice * {
        color: #000 !important;
    }

    .brothers-bill-title {
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        text-decoration: underline;
        margin: 0 0 14px 0;
        letter-spacing: 0.02em;
    }

    .brothers-meta {
        width: 100%;
        margin-bottom: 12px;
        border-collapse: collapse;
    }

    .brothers-meta td {
        vertical-align: top;
        padding: 0;
        border: none;
    }

    .brothers-meta-left {
        width: 58%;
        padding-right: 12px;
    }

    .brothers-meta-right {
        width: 42%;
        text-align: right;
    }

    .brothers-customer-name {
        font-weight: bold;
        text-transform: uppercase;
    }

    .brothers-customer-address {
        margin-top: 2px;
    }

    .brothers-lines-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
        font-size: 11pt;
    }

    .brothers-lines-table th,
    .brothers-lines-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: top;
    }

    .brothers-lines-table th {
        font-weight: bold;
        text-align: center;
    }

    .brothers-lines-table .col-sl {
        width: 6%;
        text-align: center;
    }

    .brothers-lines-table .col-desc {
        width: 34%;
    }

    .brothers-lines-table .col-part {
        width: 16%;
        text-align: center;
    }

    .brothers-lines-table .col-qty {
        width: 12%;
        text-align: center;
    }

    .brothers-lines-table .col-rate,
    .brothers-lines-table .col-value {
        width: 16%;
        text-align: right;
        white-space: nowrap;
    }

    .brothers-item-name {
        font-weight: bold;
        text-transform: uppercase;
    }

    .brothers-item-model {
        font-style: italic;
        font-weight: normal;
        margin-top: 2px;
    }

    .brothers-totals-row td {
        border-top: 1px solid #000;
    }

    .brothers-totals-label {
        text-align: right;
        font-weight: bold;
        padding-right: 12px !important;
        border-left: none !important;
    }

    .brothers-totals-spacer {
        border-right: none !important;
    }

    .brothers-totals-value {
        text-align: right;
        font-weight: bold;
        white-space: nowrap;
    }

    .brothers-in-words td {
        border: 1px solid #000;
        padding: 8px;
        font-weight: bold;
    }

    .brothers-signature {
        margin-top: 28px;
        font-size: 12pt;
        line-height: 1.5;
    }

    .brothers-signature-from {
        display: inline-block;
        margin-bottom: 2.5em;
    }

    @media print {
        .brothers-invoice {
            padding: 42mm 15mm 32mm 15mm;
        }
    }
</style>

<div class="brothers-invoice">
    <h1 class="brothers-bill-title">{{ $bill_no }}</h1>

    <table class="brothers-meta">
        <tr>
            <td class="brothers-meta-left">
                <div class="brothers-customer-name">{{ $customer_company }}</div>
                @php
                    $customer_address_line = trim($receipt_details->customer_address_line ?? $receipt_details->customer_city ?? '');
                @endphp
                @if ($customer_address_line !== '')
                    <div class="brothers-customer-address">{{ $customer_address_line }}</div>
                @endif
            </td>
            <td class="brothers-meta-right"></td>
        </tr>
        <tr>
            <td class="brothers-meta-left" style="padding-top: 8px;">
                Ref: Purchase Order No: {{ $po_ref }}
            </td>
            <td class="brothers-meta-right" style="padding-top: 8px;">
                Purchase Order Date: {{ $po_date }}
            </td>
        </tr>
        <tr>
            <td class="brothers-meta-left">
                Delivery Challan No: {{ $challan_no }}
            </td>
            <td class="brothers-meta-right">
                Date: {{ $second_date !== '' ? $second_date : $bill_date }}
            </td>
        </tr>
    </table>

    <table class="brothers-lines-table">
        <thead>
            <tr>
                <th class="col-sl">S/L</th>
                <th class="col-desc">Description</th>
                <th class="col-part">Part No.</th>
                <th class="col-qty">Qty.</th>
                <th class="col-rate">Rate</th>
                <th class="col-value">Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($receipt_details->lines as $line)
                @php
                    $sl = str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT);
                    $qty_display = str_pad((string) (int) round($line['quantity_uf'] ?? 0), 2, '0', STR_PAD_LEFT);
                    if (! empty($line['units'])) {
                        $qty_display .= ' ' . strtoupper($line['units']);
                    }
                    $model_line = '';
                    if (! empty($line['sell_line_note'])) {
                        $model_line = strip_tags($line['sell_line_note']);
                        if (stripos($model_line, 'model:') !== 0) {
                            $model_line = 'Model: ' . $model_line;
                        }
                    } elseif (! empty($line['brand'])) {
                        $model_line = 'Model: ' . $line['brand'];
                    } elseif (! empty($line['product_description'])) {
                        $model_line = strip_tags($line['product_description']);
                        if (stripos($model_line, 'model:') !== 0) {
                            $model_line = 'Model: ' . $model_line;
                        }
                    }
                    $part_no = $line['part_number'] ?? '';
                    if ($part_no === '' && ! empty($line['sub_sku'])) {
                        $part_no = $line['sub_sku'];
                    }
                    $rate = $line['unit_price_before_discount'] ?? ($line['unit_price_exc_tax'] ?? '');
                @endphp
                <tr>
                    <td class="col-sl">{{ $sl }}</td>
                    <td class="col-desc">
                        <div class="brothers-item-name">{{ $line['name'] }}</div>
                        @if ($model_line !== '')
                            <div class="brothers-item-model">{{ $model_line }}</div>
                        @endif
                    </td>
                    <td class="col-part">{{ $part_no }}</td>
                    <td class="col-qty">{{ $qty_display }}</td>
                    <td class="col-rate">{{ $rate }}</td>
                    <td class="col-value">{{ $line['line_total'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">&nbsp;</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="brothers-totals-row">
                <td colspan="2" class="brothers-totals-spacer"></td>
                <td colspan="3" class="brothers-totals-label">TOTAL AMOUNT:</td>
                <td class="brothers-totals-value">{{ $gross_total }}</td>
            </tr>
            <tr class="brothers-totals-row">
                <td colspan="2" class="brothers-totals-spacer"></td>
                <td colspan="3" class="brothers-totals-label">DISCOUNT:</td>
                <td class="brothers-totals-value">{{ $discount_total }}</td>
            </tr>
            <tr class="brothers-totals-row">
                <td colspan="2" class="brothers-totals-spacer"></td>
                <td colspan="3" class="brothers-totals-label">NET TOTAL:</td>
                <td class="brothers-totals-value">{{ $net_total }}</td>
            </tr>
            <tr class="brothers-in-words">
                <td colspan="6">IN WORDS: {{ $amount_in_words }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="brothers-signature">
        @if (! empty($receipt_details->footer_text))
            {!! $receipt_details->footer_text !!}
        @else
            {!! $default_signature !!}
        @endif
    </div>
</div>
