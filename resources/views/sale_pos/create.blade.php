@extends('layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')
    <section class="content no-print simple-pos-page">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        @php
            $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
            $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
        @endphp
        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellPosController::class, 'store']),
            'method' => 'post',
            'id' => 'add_pos_sell_form',
        ]) !!}
        <div class="row mb-12">
            <div class="col-md-12 tw-pt-0 tw-mb-14">
                <div class="row tw-flex lg:tw-flex-row md:tw-flex-col sm:tw-flex-col tw-flex-col tw-items-start md:tw-gap-4 simple-pos-layout">
                    {{-- <div class="@if (empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12"> --}}
                    <div class="tw-px-3 tw-w-full lg:tw-px-0 lg:tw-pr-0 @if(empty($pos_settings['hide_product_suggestion'])) lg:tw-w-[60%]  @else lg:tw-w-[100%] @endif">

                        <div class="tw-shadow-[rgba(17,_17,_26,_0.08)_0px_8px_24px] tw-rounded-2xl tw-bg-white tw-mb-2 md:tw-mb-8 tw-p-2 simple-pos-card">

                            {{-- <div class="box box-solid mb-12 @if (!isMobile()) mb-40 @endif"> --}}
                                <div class="box-body pb-0">
                                    {!! Form::hidden('location_id', $default_location->id ?? null, [
                                        'id' => 'location_id',
                                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                                            ? $default_location->receipt_printer_type
                                            : 'browser',
                                        'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '',
                                    ]) !!}
                                    <!-- sub_type -->
                                    {!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
                                    <input type="hidden" id="item_addition_method"
                                        value="{{ $business_details->item_addition_method }}">
                                    @include('sale_pos.partials.pos_form')

                                    @include('sale_pos.partials.pos_form_totals')

                                    @include('sale_pos.partials.payment_modal')

                                    @if (empty($pos_settings['disable_suspend']))
                                        @include('sale_pos.partials.suspend_note_modal')
                                    @endif

                                    @if (empty($pos_settings['disable_recurring_invoice']))
                                        @include('sale_pos.partials.recurring_invoice_modal')
                                    @endif
                                </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                    @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                        <div class="md:tw-no-padding tw-w-full lg:tw-w-[40%] tw-px-5 simple-pos-sidebar-wrap">
                            @include('sale_pos.partials.pos_sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @include('sale_pos.partials.pos_form_actions')
        {!! Form::close() !!}
    </section>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
        @include('sale_pos.partials.mobile_product_suggestions')
    @endif
    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

    <div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('sale_pos.partials.configure_search_modal')

    @include('sale_pos.partials.recent_transactions_modal')

    @include('sale_pos.partials.weighing_scale_modal')

@stop
@section('css')
    <style>
        .simple-pos-page {
            --pos-bg: #f6f7f9;
            --pos-surface: #ffffff;
            --pos-border: #e5e7eb;
            --pos-border-strong: #d1d5db;
            --pos-text: #111827;
            --pos-muted: #6b7280;
            --pos-accent: #0f172a;
            --pos-success: #166534;
        }

        .simple-pos-page.content {
            background: var(--pos-bg);
            padding-bottom: 96px;
        }

        .simple-pos-page .simple-pos-card,
        .simple-pos-page .pos-form-actions,
        .simple-pos-page .simple-pos-sidebar-panel,
        .simple-pos-page .simple-pos-panel {
            border: 1px solid var(--pos-border);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05) !important;
        }

        .simple-pos-page .box-body {
            padding: 18px;
        }

        .simple-pos-page .form-control,
        .simple-pos-page .select2-container--default .select2-selection--single,
        .simple-pos-page .input-group-addon,
        .simple-pos-page .input-group-btn > .btn {
            min-height: 46px;
            border-color: var(--pos-border-strong);
            box-shadow: none;
        }

        .simple-pos-page .form-control,
        .simple-pos-page .select2-container--default .select2-selection--single {
            border-radius: 12px;
            color: var(--pos-text);
            font-size: 16px;
            font-weight: 500;
        }

        .simple-pos-page .input-group-addon,
        .simple-pos-page .input-group-btn > .btn {
            background: #fff;
            color: var(--pos-muted);
        }

        .simple-pos-page input::placeholder,
        .simple-pos-page textarea::placeholder {
            color: #9ca3af;
            opacity: 1;
            font-weight: 500;
        }

        .simple-pos-page .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 44px;
            padding-left: 14px;
            color: var(--pos-text);
            font-size: 16px;
            font-weight: 500;
        }

        .simple-pos-page .select2-container .select2-selection--single .select2-selection__arrow {
            height: 44px;
        }

        .simple-pos-page .table#pos_table {
            margin-bottom: 0;
            border: 1px solid var(--pos-border);
            border-radius: 14px;
            overflow: hidden;
            background: #fff;
        }

        .simple-pos-page .table#pos_table > thead > tr > th {
            background: #f9fafb;
            border-bottom: 1px solid var(--pos-border);
            color: var(--pos-text);
            font-size: 13px;
            letter-spacing: 0.02em;
            padding: 14px 10px;
            text-transform: uppercase;
        }

        .simple-pos-page .table#pos_table > tbody > tr > td {
            padding: 14px 10px;
            vertical-align: middle;
            border-top-color: var(--pos-border);
            background: #fff;
        }

        .simple-pos-page .pos_form_totals {
            margin-top: 18px;
            padding: 14px 16px;
            background: #f9fafb;
            border: 1px solid var(--pos-border);
            border-radius: 14px;
        }

        .simple-pos-page .pos_form_totals .table {
            margin-bottom: 0;
        }

        .simple-pos-page .pos_form_totals .table > tbody > tr > td {
            border-top: 0;
            padding: 8px 12px 8px 0;
            color: var(--pos-text);
        }

        .simple-pos-page .pos-form-actions {
            border-radius: 18px 18px 0 0;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(10px);
            position: sticky;
            bottom: 0;
            z-index: 30;
        }

        .simple-pos-page .pos-total #total_payable {
            color: var(--pos-success) !important;
        }

        .simple-pos-page .simple-filter-trigger {
            background: #fff !important;
            border: 1px solid var(--pos-border-strong) !important;
            border-radius: 12px !important;
            color: var(--pos-text) !important;
            font-weight: 600;
            box-shadow: none !important;
        }

        .simple-pos-page .simple-filter-drawer {
            border-left: 1px solid var(--pos-border);
        }

        .simple-pos-page .simple-filter-title {
            color: var(--pos-text) !important;
            background: none !important;
            -webkit-text-fill-color: initial;
        }

        .simple-pos-page .simple-filter-card {
            border: 1px solid var(--pos-border) !important;
            box-shadow: none !important;
            border-radius: 14px;
            background: #fff !important;
        }

        .simple-pos-page .simple-filter-card:hover {
            background: #f9fafb !important;
        }

        .simple-pos-page .simple-pos-sidebar-wrap {
            margin-top: 6px;
        }

        .simple-pos-page .simple-pos-panel {
            background: #fff;
            border-radius: 18px;
            padding: 16px;
        }

        .simple-pos-page .simple-pos-panel .row:last-child {
            margin-bottom: 0;
        }

        .simple-pos-page .simple-pos-page-heading {
            margin: 0 0 14px;
            font-size: 18px;
            font-weight: 700;
            color: var(--pos-text);
        }

        .simple-pos-page .simple-pos-label {
            display: block;
            margin-bottom: 6px;
            color: var(--pos-text);
            font-size: 15px;
            font-weight: 700;
        }

        .simple-pos-page .simple-pos-help {
            margin: 0 0 10px;
            color: var(--pos-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .simple-pos-page .simple-pos-advanced {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid var(--pos-border);
        }

        .simple-pos-page .simple-pos-advanced summary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--pos-accent);
            font-size: 14px;
            font-weight: 700;
            list-style: none;
            user-select: none;
        }

        .simple-pos-page .simple-pos-advanced summary::-webkit-details-marker {
            display: none;
        }

        .simple-pos-page .simple-pos-advanced summary::before {
            content: "+";
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--pos-border-strong);
            border-radius: 999px;
            color: var(--pos-muted);
            font-size: 14px;
            font-weight: 700;
        }

        .simple-pos-page .simple-pos-advanced[open] summary::before {
            content: "-";
        }

        .simple-pos-page .simple-pos-help-advanced {
            margin-top: 10px;
        }

        @media (max-width: 991px) {
            .simple-pos-page .box-body {
                padding: 14px;
            }

            .simple-pos-page .pos-form-actions {
                border-radius: 16px 16px 0 0;
            }
        }
    </style>
    <!-- include module css -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@stop
@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    @include('sale_pos.partials.keyboard_shortcuts')

    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
        @endforeach
    @endif
@endsection
