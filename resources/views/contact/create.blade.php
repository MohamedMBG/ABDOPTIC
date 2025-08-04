<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-white rounded-lg shadow-sm p-0"> 
        <!-- Modal Header -->
        <div class="modal-header border-bottom-0 bg-white">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h2 class="modal-title text-center w-100">Create New Contact</h2>
        </div>

        @php
        $custom_labels = json_decode(session('business.custom_labels'), true);

        $contact_custom_field1 = $custom_labels['contact']['custom_field_1'] ?? __('lang_v1.contact_custom_field1');
        $contact_custom_field2 = $custom_labels['contact']['custom_field_2'] ?? __('lang_v1.contact_custom_field2');
        $contact_custom_field3 = $custom_labels['contact']['custom_field_3'] ?? __('lang_v1.contact_custom_field3');
        $contact_custom_field4 = $custom_labels['contact']['custom_field_4'] ?? __('lang_v1.contact_custom_field4');
        $contact_custom_field5 = $custom_labels['contact']['custom_field_5'] ?? __('lang_v1.contact_custom_field5');
        $contact_custom_field6 = $custom_labels['contact']['custom_field_6'] ?? __('lang_v1.contact_custom_field6');
        $contact_custom_field7 = $custom_labels['contact']['custom_field_7'] ?? __('lang_v1.contact_custom_field7');
        $contact_custom_field8 = $custom_labels['contact']['custom_field_8'] ?? __('lang_v1.custom_field', ['number' => 8]);
        $contact_custom_field9 = $custom_labels['contact']['custom_field_9'] ?? __('lang_v1.custom_field', ['number' => 9]);
        $contact_custom_field10 = $custom_labels['contact']['custom_field_10'] ?? __('lang_v1.custom_field', ['number' => 10]);
        @endphp

        {!! Form::open(['url' => route('contacts.store'), 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'contact_add_form']) !!}
            @csrf

            <!-- Basic Information Card -->
            <div class="modal-body">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0">
                    <h4 class="mb-0">Basic Information</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('type', 'Type:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::select('type', ['customer' => 'Customer', 'supplier' => 'Supplier', 'both' => 'Both'], null, ['class' => 'form-control border-left-0', 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('contact_type_radio', 'Contact Type:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-users text-muted"></i>
                            </span>
                            <div class="form-control border-left-0">
                                <label class="radio-inline mr-3">
                                    {!! Form::radio('contact_type_radio', 'individual', true) !!} Individual
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('contact_type_radio', 'business') !!} Business
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('prefix', 'Prefix:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::text('prefix', null, ['class' => 'form-control border-left-0', 'placeholder' => 'Mr/Mrs/Dr']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('first_name', 'First Name: *') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::text('first_name', null, ['class' => 'form-control border-left-0', 'required', 'placeholder' => 'First Name']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('middle_name', 'Middle Name:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::text('middle_name', null, ['class' => 'form-control border-left-0', 'placeholder' => 'Middle Name']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('last_name', 'Last Name:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::text('last_name', null, ['class' => 'form-control border-left-0', 'placeholder' => 'Last Name']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('email', 'Email:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-envelope text-muted"></i>
                            </span>
                            {!! Form::email('email', null, ['class' => 'form-control border-left-0', 'placeholder' => 'Email']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('mobile', 'Mobile: *') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-mobile text-muted"></i>
                            </span>
                            {!! Form::text('mobile', null, ['class' => 'form-control border-left-0', 'required', 'placeholder' => 'Mobile Number']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('image', 'Profile Image:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-image text-muted"></i>
                            </span>
                            {!! Form::file('image', ['class' => 'form-control border-left-0', 'accept' => 'image/*']) !!}
                        </div>
                        <small class="form-text text-muted">Max file size: 2MB (JPEG, PNG, JPG, GIF)</small>
                    </div>
                </div>
            </div>

            <!-- Assigned Users Card -->
            @if(config('constants.enable_contact_assign') && isset($users) && count($users) > 0)
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0">
                    <h4 class="mb-0">Assigned Users</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        {!! Form::label('user_id', 'Assigned To:*') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::select('user_id[]', $users, null, ['class' => 'form-control select2 border-left-0', 'multiple', 'required']) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('assigned_to_users', 'Additional Assigned Users:') !!}
                        <div class="input-group">
                            <span class="input-group-addon bg-light border-right-0">
                                <i class="fa fa-user text-muted"></i>
                            </span>
                            {!! Form::select('assigned_to_users[]', $users, null, ['class' => 'form-control select2 border-left-0', 'multiple']) !!}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Contact Details Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0">
                    <h4 class="mb-0">Contact Details</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="tax_number"><i class="fa fa-id-card text-muted"></i> Tax Number:</label>
                        {!! Form::text('tax_number', null, ['class' => 'form-control', 'placeholder' => 'Tax number']) !!}
                    </div>
                    <div class="form-group">
                        <label for="opening_balance"><i class="fa fa-money text-muted"></i> Opening Balance:</label>
                        {!! Form::number('opening_balance', 0, ['class' => 'form-control', 'step' => '0.01']) !!}
                    </div>
                    <div class="form-group">
                        <label for="pay_term"><i class="fa fa-calendar text-muted"></i> Pay Term:</label>
                        <div class="row pay-term-row align-items-end">
                            <div class="col-md-6 pe-2">
                                {!! Form::number('pay_term_number', null, [
                                    'class' => 'form-control pay-term-number', 
                                    'placeholder' => 'Number'
                                ]) !!}
                            </div>
                            <div class="col-md-6 ps-2">
                                {!! Form::select('pay_term_type', [
                                    '' => __('messages.please_select'), 
                                    'days' => __('lang_v1.days'), 
                                    'months' => __('lang_v1.months')
                                ], null, [
                                    'class' => 'form-control select2 pay-term-type-select',
                                    'style' => 'width: 100%'
                                ]) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="credit_limit"><i class="fa fa-credit-card text-muted"></i> Credit Limit:</label>
                        {!! Form::text('credit_limit', null, ['class' => 'form-control', 'placeholder' => 'Keep blank for no limit']) !!}
                    </div>
                    <div class="form-group">
                        <label for="address_line1"><i class="fa fa-map-marker text-muted"></i> Address Line 1:</label>
                        {!! Form::text('address_line1', null, ['class' => 'form-control', 'placeholder' => 'Address line 1']) !!}
                    </div>
                    <div class="form-group">
                        <label for="address_line2"><i class="fa fa-map-marker text-muted"></i> Address Line 2:</label>
                        {!! Form::text('address_line2', null, ['class' => 'form-control', 'placeholder' => 'Address line 2']) !!}
                    </div>
                    <div class="form-group">
                        <label for="city"><i class="fa fa-map-marker text-muted"></i> City:</label>
                        {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => 'City']) !!}
                    </div>
                    <div class="form-group">
                        <label for="state"><i class="fa fa-map-marker text-muted"></i> State:</label>
                        {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' => 'State']) !!}
                    </div>
                    <div class="form-group">
                        <label for="country"><i class="fa fa-map-marker text-muted"></i> Country:</label>
                        {!! Form::text('country', null, ['class' => 'form-control', 'placeholder' => 'Country']) !!}
                    </div>
                    <div class="form-group">
                        <label for="zip_code"><i class="fa fa-map-marker text-muted"></i> ZIP Code:</label>
                        {!! Form::text('zip_code', null, ['class' => 'form-control', 'placeholder' => 'ZIP/Postal Code']) !!}
                    </div>
                </div>
            </div>

            <!-- Custom Fields Card -->
            <style>
                /* === BASIC INFORMATION Section Enhancement === */
                .card-body .form-group .form-control.border-left-0 {
                    border: 1.5px solid #ccc !important;
                    border-radius: 0.5rem !important;
                    background-color: #fff !important;
                    padding-left: 1rem !important;
                    box-shadow: 0 0 0 1px rgba(0,0,0,0.04);
                }
                
                .card-body .form-group .form-control.border-left-0:focus {
                    border-color: #2d6cdf !important;
                    box-shadow: 0 0 0 3px rgba(45,108,223,0.15);
                    background-color: #fff;
                    outline: none;
                }





                .form-control-sm {
                    height: 32px;
                    font-size: 0.8rem;
                    padding: 0.25rem 0.4rem;
                    border-radius: 0.3rem;
                    
                    border: 1.5px solid #d0d0d0;
                    background: #fafbfc;
                    font-size: 0.95rem;
                    padding: 0.5rem 0.8rem;
                    box-shadow: 0 0 0 1px rgba(0,0,0,0.04);
                }

                
                .form-control-sm:focus {
                    border-color: #2d6cdf;
                    background: #fff;
                    box-shadow: 0 0 0 3px rgba(45, 108, 223, 0.15);
                    outline: none;
                }

                .badge {
                    font-size: 0.75rem;
                    padding: 0.35em 0.65em;
                    font-weight: 600;
                    border-radius: 0.375rem;
                }

                .small.text-muted {
                    font-size: 0.75rem;
                }

                label.small {
                    font-size: 0.75rem;
                    margin-bottom: 0.25rem;
                    display: block;
                }

                .custom-section {
                    border: 1px solid #e0e0e0;
                    border-radius: 0.5rem;
                    padding: 1rem;
                    background-color: #ffffff;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                    transition: box-shadow 0.2s ease;
                }

                .custom-section:hover {
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                }

                .custom-label {
                    margin-bottom: 0.4rem;
                    font-weight: 500;
                }

                /* Pay Term specific styles */
                .pay-term-row {
                    display: flex;
                    flex-wrap: nowrap;
                    gap: 15px;
                    align-items: flex-end;
                }

                .pay-term-row .col-md-6 {
                    flex: 1;
                    min-width: 0;
                    padding: 0;
                }

                .pay-term-type-select .select2-selection--single {
                    height: 38px;
                    border: 1px solid #e0e0e0;
                    border-radius: 0.5rem;
                    background-color: #fafbfc;
                }

                .pay-term-type-select .select2-selection__rendered {
                    line-height: 36px;
                    padding-left: 12px;
                    padding-right: 25px;
                    white-space: normal !important;
                    text-overflow: clip !important;
                }

                .pay-term-type-select .select2-selection__arrow {
                    height: 36px;
                }

                /* Make sure the select2 dropdown has enough width */
                .select2-container--default .select2-results > .select2-results__options {
                    min-width: 150px;
                }

                /* Force the select to show full text */
                .pay-term-type-select + .select2-container .select2-selection__rendered {
                    white-space: normal !important;
                    text-overflow: clip !important;
                    overflow: visible !important;
                }

                /* Ensure dropdown has enough width */
                .select2-dropdown {
                    min-width: 150px !important;
                }

                /* Minimalist custom fields section improvements */
                .custom-fields-row {
                    display: flex;
                    gap: 2.5rem;
                    margin-bottom: 2.5rem;
                    justify-content: center;
                }
                .custom-fields-row > .col-md-6 {
                    flex: 1 1 0;
                    max-width: 420px;
                    min-width: 260px;
                    margin: 0 auto;
                }
                .custom-section {
                    background: #fcfcfd;
                    border: 1px solid #f2f2f2;
                    border-radius: 0.75rem;
                    box-shadow: 0 1px 4px rgba(0,0,0,0.02);
                    padding: 1.5rem 1.2rem 1.2rem 1.2rem;
                    margin-bottom: 0;
                    display: flex;
                    flex-direction: column;
                    align-items: stretch;
                    min-width: 0;
                }
                .custom-section .badge {
                    background: #e8f0fe;
                    color: #2d6cdf;
                    font-size: 0.9rem;
                    font-weight: 500;
                    border-radius: 0.5rem;
                    margin-bottom: 0.5rem;
                    padding: 0.35em 0.9em;
                }
                .custom-section .row.g-3 {
                    margin-left: 0;
                    margin-right: 0;
                }
                .custom-section .col-6 {
                    padding-left: 0;
                    padding-right: 0;
                }
                .custom-label {
                    font-size: 0.97rem;
                    color: #666;
                    font-weight: 400;
                    margin-bottom: 0.2rem;
                }

                /* Center single custom field rows */
                .custom-fields-single-row {
                    display: flex;
                    justify-content: center;
                    margin-bottom: 2.5rem;
                }
                .custom-fields-single-row > .col-md-6 {
                    max-width: 420px;
                    min-width: 260px;
                }

                @media (max-width: 991.98px) {
                    .custom-fields-row, .custom-fields-single-row {
                        display: block;
                        margin-bottom: 2rem;
                    }
                    .custom-fields-row > .col-md-6, .custom-fields-single-row > .col-md-6 {
                        max-width: 100%;
                        margin-bottom: 1.5rem;
                    }
                }
            </style>

            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light border-bottom-0 py-2">
                    <h5 class="mb-0">Custom Fields</h5>
                </div>
                <div class="card-body p-4">
                    <div class="container-fluid px-0">
                        <!-- Row 1: V.L + EP -->
                        <div class="custom-fields-row">
                            <div class="col-md-6">
                                <div class="custom-section h-100">
                                    <span class="badge">V.L</span>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="custom-label">OD</label>
                                            {!! Form::text('custom_field1', null, ['class' => 'form-control form-control-sm']) !!}
                                        </div>
                                        <div class="col-6">
                                            <label class="custom-label">OG</label>
                                            {!! Form::text('custom_field2', null, ['class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="custom-section h-100">
                                    <span class="badge">EP</span>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="custom-label">EPD</label>
                                            {!! Form::text('custom_field6', null, ['class' => 'form-control form-control-sm']) !!}
                                        </div>
                                        <div class="col-6">
                                            <label class="custom-label">EPG</label>
                                            {!! Form::text('custom_field7', null, ['class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2: ADD (centered and small) -->
                        <div class="custom-fields-single-row">
                            <div class="col-md-6">
                                <div class="custom-section">
                                    <span class="badge">ADD</span>
                                    {!! Form::text('custom_field3', null, ['class' => 'form-control form-control-sm']) !!}
                                </div>
                            </div>
                        </div>
                        <!-- Row 3: V.P (centered and small) -->
                        <div class="custom-fields-single-row">
                            <div class="col-md-6">
                                <div class="custom-section">
                                    <span class="badge">V.P</span>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="custom-label">OD</label>
                                            {!! Form::text('custom_field4', null, ['class' => 'form-control form-control-sm']) !!}
                                        </div>
                                        <div class="col-6">
                                            <label class="custom-label">OG</label>
                                            {!! Form::text('custom_field5', null, ['class' => 'form-control form-control-sm']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-end mt-4 mb-3 gap-2">
                <button type="submit" class="btn btn-minimalist btn-primary-minimalist">
                    save
                </button>
                <button type="button" class="btn btn-minimalist btn-cancel-minimalist" data-dismiss="modal">
                    cancel
                </button>
            </div>
        {!! Form::close() !!}
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(document).ready(function() {
        // Initialize select2 for pay term type first
        $('.pay-term-type-select').select2({
            width: '100%',
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true
        });

        // Then initialize other select2 elements
        $('.select2').select2({
            width: '100%'
        });

        // Form validation
        $('#contact_add_form').validate({
            rules: {
                first_name: "required",
                type: "required",
                mobile: "required",
                'user_id[]': "required",
                image: {
                    extension: "jpg|jpeg|png|gif",
                    filesize: 2000000 // 2MB
                }
            },
            messages: {
                image: {
                    extension: "Please upload valid image file (jpg, jpeg, png, gif)",
                    filesize: "File size must be less than 2MB"
                }
            }
        });
    });
</script>

<style>
    /* Minimalist Modal Content */
    .modal-content {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
    }

    .modal-header, .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem 2rem 1rem 2rem;
        border-radius: 1rem 1rem 0 0;
    }

    .modal-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: #222;
        letter-spacing: 0.01em;
    }

    .modal-body {
        flex-grow: 1;
        overflow-y: auto;
        padding: 2rem;
        background: #fafbfc;
    }

    .card {
        background: #fff;
        border: none;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 2rem;
    }

    .card-header {
        border-bottom: 1px solid #f0f0f0;
        background: #fff;
        padding: 1rem 2rem;
        border-radius: 1rem 1rem 0 0;
    }

    .card-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-control, .select2-container--default .select2-selection--multiple {
    border-radius: 0.5rem;
    border: 1.5px solid #d0d0d0;
    background: #fafbfc;
    font-size: 1rem;
    padding: 0.75rem 1rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-shadow: 0 0 0 1px rgba(0,0,0,0.04);
    }

    .form-control:focus {
        border-color: #2d6cdf;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(45, 108, 223, 0.15);
    }
    /*
    .form-control:focus {
        border-color: #b3b3b3;
        background: #fff;
        outline: none;
        box-shadow: 0 0 0 2px #e0e7ef;
    }
*/
    .input-group-addon {
        background: transparent;
        border: none;
        color: #b3b3b3;
        font-size: 1.2rem;
        padding-right: 0.5rem;
    }

    label, .custom-label {
        font-size: 1rem;
        font-weight: 500;
        color: #444;
        margin-bottom: 0.4rem;
    }

    .badge {
        background: #f3f6fa;
        color: #3a3a3a;
        font-size: 0.85rem;
        padding: 0.4em 0.8em;
        font-weight: 500;
        border-radius: 0.5rem;
        margin-right: 0.5rem;
    }

    .custom-section {
        border: 1px solid #f0f0f0;
        border-radius: 0.75rem;
        padding: 1.5rem 1rem;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        margin-bottom: 1rem;
        transition: box-shadow 0.2s;
    }
    .custom-section:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    .btn {
        border-radius: 0.5rem;
        font-size: 1rem;
        padding: 0.75rem 2rem;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: none;
        transition: background 0.2s, color 0.2s;
    }
    .btn-primary {
        background: #2d6cdf;
        color: #fff;
    }
    .btn-primary:hover {
        background: #1b4fa0;
    }
    .btn-danger {
        background: #f44336;
        color: #fff;
    }
    .btn-danger:hover {
        background: #c62828;
    }

    /* Spacing for buttons */
    .d-flex.justify-content-between.mt-4.mb-3 {
        margin-top: 2.5rem !important;
        margin-bottom: 2rem !important;
    }

    /* Minimal radio/checkbox */
    .radio-inline {
        margin-right: 2rem;
        font-size: 1rem;
        color: #444;
    }
    .form-control-sm {
        height: 2.25rem;
        font-size: 0.95rem;
        padding: 0.25rem 0.8rem;
        border-radius: 0.3rem;
        background: #fafbfc;
    }

    /* Responsive modal */
    .modal-lg {
        max-width: 1100px;
        width: 95%;
    }

    @media (max-width: 1200px) {
        .modal-lg {
            max-width: 98vw;
            width: 98vw;
        }
    }

    .modal-body {
        padding-left: 3rem;
        padding-right: 3rem;
    }

    .card-body {
        padding-left: 2.5rem;
        padding-right: 2.5rem;
    }

    /* Remove extra border from select2 */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e0e0e0;
        background: #fafbfc;
        min-height: 2.5rem;
        border-radius: 0.5rem;
        padding: 0.25rem 0.5rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: #e3eaf7;
        color: #2d6cdf;
        border: none;
        border-radius: 0.4rem;
        padding: 0.2rem 0.7rem;
        margin-top: 0.2rem;
    }

    /* Remove heavy box shadows and borders */
    .shadow-sm, .shadow {
        box-shadow: none !important;
    }

    /* Subtle hover for cards */
    .card:hover {
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    }

    /* Minimal form text */
    .form-text.text-muted {
        color: #888 !important;
        font-size: 0.9rem;
        margin-top: 0.3rem;
    }

    /* Minimal icon style */
    .fa {
        opacity: 0.7;
    }

    /* Fix for select/input truncation */
    .input-group > .form-control,
    .input-group > .form-control.border-left-0,
    .input-group > .form-control-sm {
        min-width: 0;
        width: 100%;
    }

    .input-group {
        flex-wrap: nowrap;
    }

    /* Ensure select fields take full width and text is visible */
    select.form-control,
    select.form-control-sm {
        width: 100% !important;
        min-width: 120px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: visible;
    }

    /* For radio group in Contact Type */
    .input-group .form-control.border-left-0 {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        width: 100%;
        min-width: 0;
        background: none;
        border: none;
        box-shadow: none;
        padding: 0;
    }

    @media (min-width: 768px) {
        /* Make pay term row columns wider */
        .pay-term-row .col-md-6 {
            flex: 0 0 48%;
            max-width: 48%;
        }
        .pay-term-row {
            gap: 4%;
            display: flex;
            flex-wrap: nowrap;
        }
    }
    @media (max-width: 767.98px) {
        .pay-term-row .col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
        .pay-term-row {
            display: block;
        }
    }

    /* ================================== BASIC INFORMATIONS STYLE ========================================================== */

    

    /* ================================== BASIC INFORMATIONS STYLE ========================================================== */

    /* Fix for pay_term_type select text being cut off */
    select[name='pay_term_type'] {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        overflow: visible !important;
        background-clip: padding-box;
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow: visible !important;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .btn-minimalist {
        border: none;
        border-radius: 0.35rem;
        background: none;
        color: #2d6cdf;
        font-size: 1rem;
        font-weight: 500;
        padding: 0.5rem 1.2rem;
        margin-left: 0.5rem;
        margin-right: 0.5rem;
        box-shadow: none;
        transition: background 0.15s, color 0.15s;
        text-transform: lowercase;
    }
    .btn-minimalist:focus {
        outline: 2px solid #e0e7ef;
        outline-offset: 2px;
    }
    .btn-primary-minimalist {
        color: #fff;
        background: #2d6cdf;
    }
    .btn-primary-minimalist:hover, .btn-primary-minimalist:focus {
        background: #1b4fa0;
        color: #fff;
    }
    .btn-cancel-minimalist {
        color: #888;
        background: #f5f5f5;
    }
    .btn-cancel-minimalist:hover, .btn-cancel-minimalist:focus {
        background: #e0e0e0;
        color: #444;
    }
    .d-flex.gap-2 > * + * {
        margin-left: 0.5rem;
    }
</style>