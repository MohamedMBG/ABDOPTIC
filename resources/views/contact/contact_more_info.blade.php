@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp

@if(
    !empty($contact->custom_field1) || !empty($contact->custom_field2) ||
    !empty($contact->custom_field3) || !empty($contact->custom_field4) ||
    !empty($contact->custom_field5) || !empty($contact->custom_field6) ||
    !empty($contact->custom_field7) || !empty($contact->custom_field8) ||
    !empty($contact->custom_field9) || !empty($contact->custom_field10)
)
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-light border-bottom-0 py-2">
            <h5 class="mb-0">Custom Fields</h5>
        </div>
        <div class="card-body p-4">
            <div class="container-fluid px-0">

                {{-- Row 1: V.L + EP --}}
                @if(!empty($contact->custom_field1) || !empty($contact->custom_field2) || !empty($contact->custom_field6) || !empty($contact->custom_field7))
                    <div class="row g-4 mb-5">
                        @if(!empty($contact->custom_field1) || !empty($contact->custom_field2))
                            <div class="col-md-6">
                                <div class="custom-section h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-info me-2">V.L</span>
                                    </div>
                                    <div class="row g-3">
                                        @if(!empty($contact->custom_field1))
                                            <div class="col-6">
                                                <label class="small text-muted custom-label">
                                                    {{ $custom_labels['contact']['custom_field_1'] ?? __('lang_v1.contact_custom_field1') }}
                                                </label>
                                                <div class="form-control form-control-sm">{{ $contact->custom_field1 }}</div>
                                            </div>
                                        @endif
                                        @if(!empty($contact->custom_field2))
                                            <div class="col-6">
                                                <label class="small text-muted custom-label">
                                                    {{ $custom_labels['contact']['custom_field_2'] ?? __('lang_v1.contact_custom_field2') }}
                                                </label>
                                                <div class="form-control form-control-sm">{{ $contact->custom_field2 }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($contact->custom_field6) || !empty($contact->custom_field7))
                            <div class="col-md-6">
                                <div class="custom-section h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-info me-2">E.P</span>
                                    </div>
                                    <div class="row g-3">
                                        @if(!empty($contact->custom_field6))
                                            <div class="col-6">
                                                <label class="small text-muted custom-label">
                                                    {{ $custom_labels['contact']['custom_field_6'] ?? __('lang_v1.contact_custom_field6') }}
                                                </label>
                                                <div class="form-control form-control-sm">{{ $contact->custom_field6 }}</div>
                                            </div>
                                        @endif
                                        @if(!empty($contact->custom_field7))
                                            <div class="col-6">
                                                <label class="small text-muted custom-label">
                                                    {{ $custom_labels['contact']['custom_field_7'] ?? __('lang_v1.contact_custom_field7') }}
                                                </label>
                                                <div class="form-control form-control-sm">{{ $contact->custom_field7 }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Row 2: ADD --}}
                @if(!empty($contact->custom_field3))
                    <div class="row g-4 mb-5 justify-content-center">
                        <div class="col-md-6">
                            <div class="custom-section">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-info me-2">ADD</span>
                                </div>
                                <label class="small text-muted custom-label">
                                    {{ $custom_labels['contact']['custom_field_3'] ?? __('lang_v1.contact_custom_field3') }}
                                </label>
                                <div class="form-control form-control-sm">{{ $contact->custom_field3 }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Row 3: V.P --}}
                @if(!empty($contact->custom_field4) || !empty($contact->custom_field5))
                    <div class="row g-4 justify-content-center">
                        <div class="col-md-6">
                            <div class="custom-section">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-info me-2">V.P</span>
                                </div>
                                <div class="row g-3">
                                    @if(!empty($contact->custom_field4))
                                        <div class="col-6">
                                            <label class="small text-muted custom-label">
                                                {{ $custom_labels['contact']['custom_field_4'] ?? __('lang_v1.contact_custom_field4') }}
                                            </label>
                                            <div class="form-control form-control-sm">{{ $contact->custom_field4 }}</div>
                                        </div>
                                    @endif
                                    @if(!empty($contact->custom_field5))
                                        <div class="col-6">
                                            <label class="small text-muted custom-label">
                                                {{ $custom_labels['contact']['custom_field_5'] ?? __('lang_v1.contact_custom_field5') }}
                                            </label>
                                            <div class="form-control form-control-sm">{{ $contact->custom_field5 }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Row 4: H.P --}}
                @if(!empty($contact->custom_field8))
                    <div class="row g-4 justify-content-center mt-4">
                        <div class="col-md-6">
                            <div class="custom-section">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-info me-2">H.P</span>
                                </div>
                                <label class="small text-muted custom-label">
                                    {{ $custom_labels['contact']['custom_field_8'] ?? __('lang_v1.custom_field', ['number' => 8]) }}
                                </label>
                                <div class="form-control form-control-sm">{{ $contact->custom_field8 }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Row 5: Other fields --}}
                @if(!empty($contact->custom_field9) || !empty($contact->custom_field10))
                    <div class="row g-4 justify-content-center mt-4">
                        <div class="col-md-6">
                            <div class="custom-section">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge bg-info me-2">Other</span>
                                </div>
                                @if(!empty($contact->custom_field9))
                                    <label class="small text-muted custom-label">
                                        {{ $custom_labels['contact']['custom_field_9'] ?? __('lang_v1.custom_field', ['number' => 9]) }}
                                    </label>
                                    <div class="form-control form-control-sm mb-3">{{ $contact->custom_field9 }}</div>
                                @endif
                                @if(!empty($contact->custom_field10))
                                    <label class="small text-muted custom-label">
                                        {{ $custom_labels['contact']['custom_field_10'] ?? __('lang_v1.custom_field', ['number' => 10]) }}
                                    </label>
                                    <div class="form-control form-control-sm">{{ $contact->custom_field10 }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endif


<!-- Custom Fields Card -->
            <style>
    .form-control-sm {
        height: 32px;
        font-size: 0.8rem;
        padding: 0.25rem 0.4rem;
        border-radius: 0.3rem;
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
</style>