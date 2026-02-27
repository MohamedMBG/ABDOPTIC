<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content bg-white rounded-lg shadow-sm p-0"> 
        <!-- Modal Header -->
        <div class="modal-header border-bottom-0 bg-white tw-bg-gray-50 tw-rounded-t-2xl tw-pb-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h2 class="modal-title text-center w-100 tw-font-bold tw-text-gray-800">Ajouter un Contact</h2>
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
            <div class="modal-body pb-0">
            <div class="card mb-0 border-0 shadow-sm tw-rounded-xl tw-overflow-hidden">
                <div class="card-header border-bottom-0 tw-bg-gradient-to-r tw-from-blue-50 tw-to-white tw-px-6 tw-py-4">
                    <h4 class="mb-0 tw-text-blue-800 tw-font-bold"><i class="fas fa-user-plus tw-text-blue-500 tw-mr-2"></i> Informations Principales</h4>
                </div>
                <div class="card-body tw-p-6">
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            {!! Form::label('type', 'Type de contact: *', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            <div class="input-group">
                                <span class="input-group-addon tw-bg-gray-50 tw-border-gray-200 tw-border-r-0">
                                    <i class="fas fa-users tw-text-gray-400"></i>
                                </span>
                        {!! Form::select('type', ['customer' => 'Client', 'supplier' => 'Fournisseur', 'both' => 'Les deux'], null, ['class' => 'form-control tw-border-gray-200 tw-border-l-0', 'required', 'id' => 'type', 'tabindex' => '1']) !!}
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            {!! Form::label('mobile', 'Téléphone (Mobile): *', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            <div class="input-group">
                                <span class="input-group-addon tw-bg-gray-50 tw-border-gray-200 tw-border-r-0">
                                    <i class="fas fa-mobile-alt tw-text-gray-400"></i>
                                </span>
                                {!! Form::text('mobile', null, ['class' => 'form-control tw-border-gray-200 tw-border-l-0', 'required', 'placeholder' => 'Ex: 06 00 00 00 00', 'tabindex' => '2']) !!}
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            {!! Form::label('contact_type_radio', 'Personnalité:', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            <div class="tw-flex tw-items-center tw-h-10 tw-gap-4">
                                <label class="radio-inline mb-0 tw-text-gray-600">
                                    {!! Form::radio('contact_type_radio', 'individual', true) !!} Particulier
                                </label>
                                <label class="radio-inline mb-0 tw-text-gray-600">
                                    {!! Form::radio('contact_type_radio', 'business') !!} Société
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2 mb-4">
                            {!! Form::label('prefix', 'Civilité:', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            {!! Form::text('prefix', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Mr/Mme']) !!}
                        </div>

                        <div class="col-md-5 mb-4">
                            {!! Form::label('first_name', 'Prénom: *', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            {!! Form::text('first_name', null, ['class' => 'form-control tw-border-gray-200', 'required', 'placeholder' => 'Prénom', 'tabindex' => '3']) !!}
                        </div>

                        <div class="col-md-5 mb-4">
                            {!! Form::label('last_name', 'Nom:', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Nom de famille', 'tabindex' => '4']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            {!! Form::label('email', 'Email:', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            <div class="input-group">
                                <span class="input-group-addon tw-bg-gray-50 tw-border-gray-200 tw-border-r-0">
                                    <i class="fas fa-envelope tw-text-gray-400"></i>
                                </span>
                                {!! Form::email('email', null, ['class' => 'form-control tw-border-gray-200 tw-border-l-0', 'placeholder' => 'Email (optionnel)', 'tabindex' => '5']) !!}
                            </div>
                        </div>

                        @if(config('constants.enable_contact_assign') && isset($users) && count($users) > 0)
                        <div class="col-md-6 mb-4">
                            {!! Form::label('assigned_to_users', 'Assigné à: *', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                            <div class="input-group">
                                <span class="input-group-addon tw-bg-gray-50 tw-border-gray-200 tw-border-r-0">
                                    <i class="fas fa-users tw-text-gray-400"></i>
                                </span>
                                {!! Form::select('assigned_to_users[]', $users, null, ['class' => 'form-control select2 tw-border-gray-200 tw-border-l-0', 'multiple', 'required', 'id' => 'assigned_to_users']) !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <hr class="tw-my-2 tw-border-gray-100">
                            <div class="tw-flex tw-justify-center tw-mt-4">
                                <button type="button" class="btn btn-link tw-text-blue-600 tw-font-medium tw-p-2 hover:tw-bg-blue-50 tw-rounded tw-transition-colors" data-toggle="collapse" data-target="#more_contact_info">
                                    <i class="fas fa-plus-circle tw-mr-1"></i> Afficher plus de détails (Adresse, Plafond, etc.)
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div id="more_contact_info" class="collapse">

            <!-- Contact Details Card -->
            <div class="card my-4 border-0 shadow-sm tw-rounded-xl tw-overflow-hidden">
                <div class="card-header border-bottom-0 tw-bg-gradient-to-r tw-from-green-50 tw-to-white tw-px-6 tw-py-4">
                    <h4 class="mb-0 tw-text-green-800 tw-font-bold"><i class="fas fa-map-marked-alt tw-text-green-500 tw-mr-2"></i> Détails Supplémentaires & Adresse</h4>
                </div>
                <div class="card-body tw-p-6">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tax_number" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-id-card tw-text-gray-400 tw-mr-1"></i> N° d'identification (ICE/TVA):</label>
                            {!! Form::text('tax_number', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'N° Taxe / ICE']) !!}
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="opening_balance" class="tw-text-gray-700 tw-font-medium mb-1 d-block">
                                <i class="fas fa-money-bill-wave tw-text-gray-400 tw-mr-1"></i> Solde d'ouverture:
                            </label>
                            {!! Form::number('opening_balance', 0, ['class' => 'form-control tw-border-gray-200', 'step' => '0.01']) !!}
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="credit_limit" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-credit-card tw-text-gray-400 tw-mr-1"></i> Plafond de crédit (Limite):</label>
                            {!! Form::text('credit_limit', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Laissez vide pour aucun']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address_line1" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-map-marker-alt tw-text-gray-400 tw-mr-1"></i> Adresse Ligne 1:</label>
                            {!! Form::text('address_line1', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Adresse']) !!}
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address_line2" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-map-marker-alt tw-text-gray-400 tw-mr-1"></i> Adresse Ligne 2:</label>
                            {!! Form::text('address_line2', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Bâtiment, Étage...']) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-city tw-text-gray-400 tw-mr-1"></i> Ville:</label>
                            {!! Form::text('city', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Ville']) !!}
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-map tw-text-gray-400 tw-mr-1"></i> Province / Région:</label>
                            {!! Form::text('state', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Région']) !!}
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="zip_code" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-mail-bulk tw-text-gray-400 tw-mr-1"></i> Code Postal:</label>
                            {!! Form::text('zip_code', null, ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Code Postal']) !!}
                        </div>
                        <div class="col-md-4 mb-3" style="display: none;">
                            <label for="country" class="tw-text-gray-700 tw-font-medium mb-1 d-block"><i class="fas fa-globe tw-text-gray-400 tw-mr-1"></i> Pays:</label>
                            {!! Form::text('country', 'Maroc', ['class' => 'form-control tw-border-gray-200', 'placeholder' => 'Pays']) !!}
                        </div>
                    </div>
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

            <!-- Ordonnance (Prescription) Card -->
            <div class="card mb-4 border-0 shadow-sm tw-rounded-xl tw-overflow-hidden" id="ordonnance_card" style="display: none;">
                <div class="card-header border-bottom-0 tw-bg-gradient-to-r tw-from-indigo-50 tw-to-white tw-px-6 tw-py-4">
                    <h4 class="mb-0 tw-text-indigo-800 tw-font-bold"><i class="fas fa-glasses tw-text-indigo-500 tw-mr-2"></i> Initial Ordonnance (Prescription)</h4>
                </div>
                <div class="card-body tw-p-6">
                    <div class="row">
                        <!-- OS (Left Eye) Form -->
                        <div class="col-md-6">
                            <div class="custom-section h-100" style="background-color: #f6fcff; border-color: #cae8f5;">
                                <h5 class="text-info" style="font-weight: 600;"><i class="far fa-eye"></i> OS - Left Eye (Oeil Gauche)</h5>
                                <div class="row mt-3">
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('os_sphere', 'Sphere (SPH):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('os_sphere', null, ['class' => 'form-control form-control-sm', 'placeholder' => '-1.00']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('os_cylinder', 'Cylinder (CYL):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('os_cylinder', null, ['class' => 'form-control form-control-sm', 'placeholder' => '-0.50']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('os_axis', 'Axis (AXE):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('os_axis', null, ['class' => 'form-control form-control-sm', 'placeholder' => '180']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('os_addition', 'Addition (ADD):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('os_addition', null, ['class' => 'form-control form-control-sm', 'placeholder' => '+2.00']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-0">
                                            {!! Form::label('os_pd', 'Pupillary Distance (PD):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('os_pd', null, ['class' => 'form-control form-control-sm', 'placeholder' => '32.5']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- OD (Right Eye) Form -->
                        <div class="col-md-6">
                            <div class="custom-section h-100" style="background-color: #fffaf0; border-color: #f7e0b5;">
                                <h5 class="text-warning" style="font-weight: 600;"><i class="far fa-eye"></i> OD - Right Eye (Oeil Droit)</h5>
                                <div class="row mt-3">
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('od_sphere', 'Sphere (SPH):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('od_sphere', null, ['class' => 'form-control form-control-sm', 'placeholder' => '-1.00']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('od_cylinder', 'Cylinder (CYL):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('od_cylinder', null, ['class' => 'form-control form-control-sm', 'placeholder' => '-0.50']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('od_axis', 'Axis (AXE):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('od_axis', null, ['class' => 'form-control form-control-sm', 'placeholder' => '180']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <div class="form-group mb-0">
                                            {!! Form::label('od_addition', 'Addition (ADD):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('od_addition', null, ['class' => 'form-control form-control-sm', 'placeholder' => '+2.00']) !!}
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-0">
                                            {!! Form::label('od_pd', 'Pupillary Distance (PD):', ['class' => 'custom-label']) !!}
                                            {!! Form::text('od_pd', null, ['class' => 'form-control form-control-sm', 'placeholder' => '32.5']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="tw-flex tw-justify-end tw-gap-3 tw-mt-6 tw-mb-4">
                <button type="button" class="tw-dw-btn tw-bg-gray-200 hover:tw-bg-gray-300 tw-text-gray-700 tw-border-none tw-rounded-lg tw-px-6" data-dismiss="modal">
                    Fermer
                </button>
                <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-rounded-lg tw-px-8 tw-shadow-md hover:tw-scale-105 tw-transition tw-duration-200">
                    Enregistrer <i class="fas fa-check-circle tw-ml-2"></i>
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
        $('.select2').select2({ width: '100%' });

        // Form validation
        $('#contact_add_form').validate({
            rules: {
                first_name: "required",
                type: "required",
                mobile: "required",
                'assigned_to_users[]': "required",
                image: { extension: "jpg|jpeg|png|gif", filesize: 2000000 }
            }
        });

        // ── Ordonnance toggle ─────────────────────────────────────
        function toggleOrdonnance() {
            var t = $('select#type').val();
            if (t === 'customer' || t === 'both') {
                $('#ordonnance_card').slideDown(180);
            } else {
                $('#ordonnance_card').slideUp(180);
            }
        }
        toggleOrdonnance();
        $('select#type').on('change', toggleOrdonnance);

        // ── Smooth "Show More Details" expand with rotating arrow ─
        var $toggleBtn = $('[data-target="#more_contact_info"], [data-bs-target="#more_contact_info"]');
        var $arrowIcon = $toggleBtn.find('i.fas');
        // Restore accordion state from sessionStorage
        if (sessionStorage.getItem('more_contact_open') === '1') {
            $('#more_contact_info').show();
            $arrowIcon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
            $toggleBtn.find('span:last').text(' Masquer les détails supplémentaires');
        }
        // Convert Bootstrap collapse to jQuery slideToggle for smoother animation
        $toggleBtn.off('click').on('click', function(e) {
            e.preventDefault();
            var $section = $('#more_contact_info');
            if ($section.is(':visible')) {
                $section.slideUp(220);
                $arrowIcon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                $toggleBtn.find('span:last').text(' Afficher plus de détails (Adresse, Plafond, etc.)');
                sessionStorage.setItem('more_contact_open', '0');
            } else {
                $section.slideDown(220, function() {
                    $section.find('input:visible:not([disabled]):first').trigger('focus');
                });
                $arrowIcon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                $toggleBtn.find('span:last').text(' Masquer les détails supplémentaires');
                sessionStorage.setItem('more_contact_open', '1');
            }
        });

        // ── Prescription: Enter key auto-advances to next field ───
        // Ordered: OS SPH → CYL → AXE → ADD → PD → OD SPH → CYL → AXE → ADD → PD
        var prescFields = [
            'os_sphere','os_cylinder','os_axis','os_addition','os_pd',
            'od_sphere','od_cylinder','od_axis','od_addition','od_pd'
        ];
        $.each(prescFields, function(i, name) {
            $('[name="' + name + '"]').on('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    e.preventDefault();
                    var next = prescFields[i + 1];
                    if (next) { $('[name="' + next + '"]').trigger('focus').trigger('select'); }
                }
            });
        });

        // ── Alt+O: instantly expand ordonnance and focus SPH ─────
        $(document).on('keydown', function(e) {
            if (e.altKey && e.key.toLowerCase() === 'o') {
                e.preventDefault();
                $('#ordonnance_card').slideDown(150);
                setTimeout(function() { $('[name="os_sphere"]').trigger('focus').trigger('select'); }, 160);
            }
            // ── Alt+X: clear all prescription fields ──────────────
            if (e.altKey && e.key.toLowerCase() === 'x') {
                e.preventDefault();
                var prescFields = ['os_sphere','os_cylinder','os_axis','os_addition','os_pd',
                                   'od_sphere','od_cylinder','od_axis','od_addition','od_pd'];
                $.each(prescFields, function(i, name) {
                    $('[name="' + name + '"]').val('');
                });
                $('[name="os_sphere"]').trigger('focus');
            }
        });

        // ── Mobile field: auto-format as user types ───────────────
        $('[name="mobile"]').on('input', function() {
            var v = $(this).val().replace(/\D/g, '');
            if (v.length > 10) v = v.slice(0, 10);
            $(this).val(v);
        });

        // ── Contact type select: default to 'customer' instantly ──
        if (!$('select#type').val()) {
            $('select#type').val('customer').trigger('change');
        }
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