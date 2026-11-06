@if(!empty($receipt_details->vat) && $receipt_details->vat !== 0 && $receipt_details->vat !== '0')
    <tr>
        <td>{{ $receipt_details->vat_label }}</td>
        <td class="text-right">(+) {{ $receipt_details->vat }}</td>
    </tr>
@endif
