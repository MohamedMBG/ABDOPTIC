@extends('layouts.app')
@section('title', __('Manual Sell'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Manual Invoice</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\ManualSellController::class, 'store']), 'method' => 'post', 'id' => 'manual_sell_form']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Customer Information</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('contact_id', __('Nom:')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </span>
                                    {!! Form::select('contact_id', [], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => __('Enter Customer name / phone'), 'required', 'style' => 'width: 100%;']); !!}
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('mobile', __('Mobile:')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-mobile"></i>
                                    </span>
                                    <!-- Mobile Field -->
                                    {!! Form::text('mobile', null, ['class' => 'form-control', 'id' => 'customer_mobile']); !!}

                                    <!-- {!! Form::text('mobile', null, ['class' => 'form-control', 'readonly', 'id' => 'customer_mobile']); !!} -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('address', __('Address:')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </span>
                                    
                                    <!-- Address Field -->
                                    {!! Form::text('address', null, ['class' => 'form-control', 'id' => 'customer_address']); !!}
                                    <!-- {!! Form::text('address', null, ['class' => 'form-control', 'readonly', 'id' => 'customer_address']); !!} -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Eye Measurements</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('od_measurement', __('OD (Right Eye):')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    {!! Form::text('od_measurement', null, ['class' => 'form-control', 'id' => 'od_measurement']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('og_measurement', __('OG (Left Eye):')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                    {!! Form::text('og_measurement', null, ['class' => 'form-control', 'id' => 'og_measurement']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Products</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-condensed" id="manual_product_table">
                                <thead>
                                    <tr>
                                        <th>Quantité</th>
                                        <th>Désignation</th>
                                        <th>Prix</th>
                                        <th>Prix Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            {!! Form::number('products[0][quantity]', 1, ['class' => 'form-control quantity', 'required', 'min' => 1, 'step' => 'any']); !!}
                                        </td>
                                        <td>
                                            {!! Form::text('products[0][name]', null, ['class' => 'form-control product_name', 'required', 'placeholder' => __('Product Name')]); !!}
                                        </td>
                                        <td>
                                            {!! Form::number('products[0][price]', 0, ['class' => 'form-control price', 'required', 'min' => 0, 'step' => 'any']); !!}
                                        </td>
                                        <td>
                                            <span class="subtotal">0.00</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-xs remove_product_row"><i class="fa fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <button type="button" class="btn btn-primary btn-sm" id="add_product_row">
                                                <i class="fa fa-plus"></i> Add Product
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right">
                                            <strong>Total:</strong>
                                        </td>
                                        <td>
                                            <span id="total_amount">0.00</span>
                                            {!! Form::hidden('final_total', 0, ['id' => 'final_total']); !!}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Invoice Details</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('invoice_number', __('FACTURE N°:')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-file-text"></i>
                                    </span>
                                    {!! Form::text('invoice_number', null, ['class' => 'form-control', 'readonly', 'id' => 'invoice_number']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('transaction_date', __('Date:') . '*') !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::text('transaction_date', @format_datetime('now'), ['class' => 'form-control', 'readonly', 'required', 'id' => 'transaction_date']); !!}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('additional_notes', __('Additional Notes:')) !!}
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-sticky-note"></i>
                                    </span>
                                    {!! Form::textarea('additional_notes', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'additional_notes']); !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Discount</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('discount_type', __('Discount Type:')) !!}
                            {!! Form::select('discount_type', 
                                ['percentage' => 'Percentage', 'fixed' => 'Fixed Amount'], 
                                'percentage', 
                                ['class' => 'form-control', 'id' => 'discount_type']
                            ) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('discount_amount', __('Discount Amount:')) !!}
                            {!! Form::text('discount_amount', 0, [
                                'class' => 'form-control input_number',
                                'id' => 'discount_amount',
                                'min' => 0
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-lg" id="submit_sell_form">Save Invoice</button>
            <button type="button" class="btn btn-info btn-lg" id="print_invoice">Print Invoice</button>
            <button type="button" class="btn btn-default btn-lg" id="cancel_sell_form">Cancel</button>
        </div>
    </div>
    {!! Form::close() !!}

    <!-- Print Template (Hidden) -->
    <div id="print_template" style="display: none;">
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
                            <strong>FACTURE N°: <span id="print_invoice_number" style="color: #d9534f;"></span></strong>
                        </div>
                    </div>
                </div>
                <div style="text-align: left; margin-top: 20px;">
                    <div style="font-size: 16px;">
                        Nom: <span id="print_customer_name"></span>
                    </div>
                </div>
            </div>

            <div class="eye-measurements" style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between;">
                    <div style="width: 48%;">
                        <strong>OD (Right Eye):</strong> <span id="print_od_measurement"></span>
                    </div>
                    <div style="width: 48%;">
                        <strong>OG (Left Eye):</strong> <span id="print_og_measurement"></span>
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
                    <tbody id="print_products">
                        <!-- Products will be added here dynamically -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="border: 1px solid #333; padding: 8px; text-align: right;"><strong>Total:</strong></td>
                            <td style="border: 1px solid #333; padding: 8px; text-align: center;"><span id="print_total"></span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="invoice-footer" style="margin-top: 30px; font-size: 12px; text-align: center;">
                <div>I.C.E: 002896657000003 Patente: 50609015 Tel:0628388045</div>
            </div>
        </div>
    </div>
</section>
@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2 for customer search
        $('#customer_id').select2({
            ajax: {
                url: '/contacts/customers',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 1,
            escapeMarkup: function(markup) {
                return markup;
            },
            templateResult: function(data) {
                if (data.loading) return data.text;
                var markup = "<div class='clearfix'>";
                markup += "<div>" + data.text + "</div>";
                if (data.mobile) {
                    markup += "<div><small>Mobile: " + data.mobile + "</small></div>";
                }
                markup += "</div>";
                return markup;
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        // Update customer details when customer is selected
        $('#customer_id').on('change', function() {
            var customer_id = $(this).val();
            if (customer_id) {
                $.ajax({
                    url: '/contacts/' + customer_id,
                    dataType: 'json',
                    success: function(data) {
                        $('#customer_mobile').val(data.mobile);
                        $('#customer_address').val(data.address);
                    }
                });
            } else {
                $('#customer_mobile').val('');
                $('#customer_address').val('');
            }
        });

        // Generate invoice number
        $.ajax({
            url: '/sells/get-invoice-number',
            dataType: 'json',
            success: function(data) {
                $('#invoice_number').val(data.invoice_number);
            }
        });

        // Initialize datepicker for transaction date
        $('#transaction_date').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
        });

        // Add product row
        $('#add_product_row').click(function() {
            var row_index = $('#manual_product_table tbody tr').length;
            var new_row = `
                <tr>
                    <td>
                        <input class="form-control quantity" required min="1" step="any" name="products[${row_index}][quantity]" type="number" value="1">
                    </td>
                    <td>
                        <input class="form-control product_name" required placeholder="Product Name" name="products[${row_index}][name]" type="text">
                    </td>
                    <td>
                        <input class="form-control price" required min="0" step="any" name="products[${row_index}][price]" type="number" value="0">
                    </td>
                    <td>
                        <span class="subtotal">0.00</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-xs remove_product_row"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
            `;
            $('#manual_product_table tbody').append(new_row);
        });

        // Remove product row
        $(document).on('click', '.remove_product_row', function() {
            if ($('#manual_product_table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotal();
            } else {
                alert('At least one product is required.');
            }
        });

        // Calculate subtotal when quantity or price changes
        $(document).on('input', '.quantity, .price', function() {
            var row = $(this).closest('tr');
            var quantity = parseFloat(row.find('.quantity').val()) || 0;
            var price = parseFloat(row.find('.price').val()) || 0;
            var subtotal = quantity * price;
            row.find('.subtotal').text(subtotal.toFixed(2));
            calculateTotal();
        });

        // Calculate total amount
        function calculateTotal() {
            var total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).text()) || 0;
            });
            $('#total_amount').text(total.toFixed(2));
            $('#final_total').val(total);
        }

        // Cancel button action
        $('#cancel_sell_form').click(function() {
            if (confirm('Are you sure you want to cancel? All entered data will be lost.')) {
                //window.location.href = '/sells';
                window.history.back();

            }
        });

        // Print invoice button action
        $('#print_invoice').click(function() {
            // Populate print template with form data
            $('#print_invoice_number').text($('#invoice_number').val());
            $('#print_customer_name').text($('#customer_id option:selected').text());
            $('#print_od_measurement').text($('#od_measurement').val());
            $('#print_og_measurement').text($('#og_measurement').val());
            
            // Clear previous products
            $('#print_products').empty();
            
            // Add products to print template
            $('#manual_product_table tbody tr').each(function() {
                var quantity = $(this).find('.quantity').val();
                var name = $(this).find('.product_name').val();
                var price = $(this).find('.price').val();
                var subtotal = $(this).find('.subtotal').text();
                
                var row = `
                    <tr>
                        <td style="border: 1px solid #333; padding: 8px; text-align: center;">${quantity}</td>
                        <td style="border: 1px solid #333; padding: 8px;">${name}</td>
                        <td style="border: 1px solid #333; padding: 8px; text-align: right;">${price}</td>
                        <td style="border: 1px solid #333; padding: 8px; text-align: right;">${subtotal}</td>
                    </tr>
                `;
                
                $('#print_products').append(row);
            });
            
            // Set total
            $('#print_total').text($('#total_amount').text());
            
            // Create a new window for printing
            var printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Invoice</title>');
            printWindow.document.write('<style>@media print { body { margin: 0; padding: 0; } }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write($('#print_template').html());
            printWindow.document.write('</body></html>');
            
            printWindow.document.close();
            printWindow.focus();
            
            // Print after a short delay to ensure content is loaded
            setTimeout(function() {
                printWindow.print();
                printWindow.close();
            }, 500);
        });

        // Form submission
        $('#manual_sell_form').submit(function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to save this invoice?')) {
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            alert('Invoice saved successfully!');
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Error: ' + data.msg);
                        }
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endsection
