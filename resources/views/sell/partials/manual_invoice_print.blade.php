@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    $custom_field_data = json_decode($transaction->custom_field_1, true);
@endphp

<div class="row">
    <div class="col-xs-12">
        <h2 class="page-header">
            {{ $transaction->business->name }}
            <small class="pull-right">
                {{ __('Date') }}: {{ @format_date($transaction->transaction_date) }}
            </small>
        </h2>
    </div>
</div>

<div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
        {{ __('To') }}:
        <address>
            <strong>{{ $transaction->contact->name }}</strong>
            @if(!empty($transaction->contact->mobile))
                <br>{{ __('Mobile') }}: {{ $transaction->contact->mobile }}
            @endif
            @if(!empty($transaction->contact->address_line_1))
                <br>{{ $transaction->contact->address_line_1 }}
            @endif
        </address>
    </div>
    <div class="col-sm-4 invoice-col">
        {{ __('Invoice') }} #{{ $transaction->invoice_no }}
    </div>
</div>

@if(!empty($custom_field_data))
<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered">
            <tr>
                @if(!empty($custom_field_data['od_measurement']))
                <th>OD Measurement</th>
                @endif
                @if(!empty($custom_field_data['og_measurement']))
                <th>OG Measurement</th>
                @endif
                @if(!empty($custom_field_data['midar']))
                <th>Midar</th>
                @endif
            </tr>
            <tr>
                @if(!empty($custom_field_data['od_measurement']))
                <td>{{ $custom_field_data['od_measurement'] }}</td>
                @endif
                @if(!empty($custom_field_data['og_measurement']))
                <td>{{ $custom_field_data['og_measurement'] }}</td>
                @endif
                @if(!empty($custom_field_data['midar']))
                <td>{{ $custom_field_data['midar'] }}</td>
                @endif
            </tr>
        </table>
    </div>
</div>
@endif

<div class="row">
    <div class="col-xs-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('Qty') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->sell_lines as $sell_line)
                <tr>
                    <td>{{ $sell_line->quantity }}</td>
                    <td>{{ $sell_line->sell_line_note }}</td>
                    <td>{{ @num_format($sell_line->unit_price) }}</td>
                    <td>{{ @num_format($sell_line->quantity * $sell_line->unit_price) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        @if(!empty($transaction->additional_notes))
            <p><strong>{{ __('Additional Notes') }}:</strong> {{ $transaction->additional_notes }}</p>
        @endif
    </div>
    <div class="col-xs-6">
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>{{ __('Total') }}:</th>
                    <td>{{ @num_format($transaction->final_total) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Payment Status') }}:</th>
                    <td>{{ __('lang_v1.' . $transaction->payment_status) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 text-center">
        <p>{{ __('Thank you for your business!') }}</p>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){ window.print(); }, 1000);
    });
</script>
