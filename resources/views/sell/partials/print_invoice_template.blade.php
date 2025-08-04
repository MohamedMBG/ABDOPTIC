<!-- Print Template -->
<div class="print-invoice" style="width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <div class="invoice-header" style="text-align: center; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between;">
            <div style="text-align: left; width: 40%;">
                <div style="font-size: 24px; font-weight: bold;">
                <img src="{{ asset('uploads/business_logos/1736212821_LOGO OPTICS 2.png') }}" 
                style="max-height: 80px; width: auto; display: block; margin: 0 auto;">
                </div>
                <div style="font-size: 16px;">Opticien - Optométriste</div>
                <div style="font-size: 16px;">Contactologue</div>
                <div style="font-size: 12px; margin-top: 10px;">
                    شارع أحمد بولعريف قرب البنك الشعبي رقم 2<br>
                    مضيق - الدريوش - الناظور<br>
                    abdel.loukili98@gmail.com
                </div>
            </div>
            <div style="text-align: right; width: 40%;">
                <div style="font-size: 20px; font-weight: bold;">Abderahim EL OUAKILI</div>
                <div style="font-size: 16px;">opticien Optométriste</div>
                <div style="font-size: 16px;">Contacologue</div>
                <div style="font-size: 18px; margin-top: 20px;">
                    <strong>FACTURE N°: <span style="color: #d9534f;">{{ $transaction->invoice_no }}</span></strong>
                </div>
            </div>
        </div>
        <div style="text-align: left; margin-top: 20px;">
            <div style="font-size: 16px;">
                Nom: {{ $transaction->contact->name }}
            </div>
        </div>
    </div>

    @php
        $custom_field_data = json_decode($transaction->custom_field_1, true);
    @endphp

    <div class="eye-measurements" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 48%;">
                <strong>OD (Right Eye):</strong> {{ $custom_field_data['od_measurement'] ?? '' }}
            </div>
            <div style="width: 48%;">
                <strong>OG (Left Eye):</strong> {{ $custom_field_data['og_measurement'] ?? '' }}
            </div>
        </div>
    </div>

    <div class="invoice-body">
        <table style="width: 100%; border-collapse: collapse; border: 2px solid #333;">
            <thead>
                <tr>
                    <th style="border: 1px solid #333; padding: 8px; text-align: center; width: 15%;">Quantité</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: center; width: 50%;">Désignation</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: center; width: 15%;">Prix</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: center; width: 20%;">Prix Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->sell_lines as $line)
                <tr>
                    <td style="border: 1px solid #333; padding: 8px; text-align: center;">{{ $line->quantity }}</td>
                    <td style="border: 1px solid #333; padding: 8px;">{{ $line->product ? $line->product->name : $line->sell_line_note }}</td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ @num_format($line->unit_price) }}</td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ @num_format($line->quantity * $line->unit_price) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="border: 1px solid #333; padding: 8px; text-align: right;"><strong>Total:</strong></td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: center;">{{ @num_format($transaction->final_total) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="invoice-footer" style="margin-top: 30px; font-size: 12px; text-align: center;">
        <div>I.C.E: 002896657000003 Patente: 50609015 Tel:0628388045</div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){ window.print(); }, 1000);
    });
</script> 