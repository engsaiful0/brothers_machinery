@if(!empty($receipt_details->additional_notes))
    <tr>
        <td colspan="2" style="white-space: pre-line; padding-top: 8px;"><strong>@lang('sale.sell_note'):</strong><br>{{ $receipt_details->additional_notes }}</td>
    </tr>
@endif
