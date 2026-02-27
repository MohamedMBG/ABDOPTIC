@extends('layouts.app')
@section('title', __('product.add_new_product'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('product.add_new_product')</h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @php
    $form_class = empty($duplicate_product) ? 'create' : '';
    $is_image_required = !empty($common_settings['is_product_image_required']);
    @endphp
    {!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'store']), 'method' => 'post',
    'id' => 'product_add_form','class' => 'product_form ' . $form_class, 'files' => true ]) !!}
    <!-- General Information Section -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-mb-6 tw-overflow-hidden tw-border tw-border-gray-100">
        <div class="tw-bg-gray-50 tw-px-6 tw-py-4 tw-border-b tw-border-gray-100">
            <h4 class="tw-text-gray-800 tw-font-bold tw-text-lg tw-m-0">
                <i class="fas fa-box tw-text-indigo-500 tw-mr-2"></i> Informations Générales
            </h4>
        </div>
        
        <div class="tw-p-6">
            <div class="row">
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __('product.product_name') . ':*', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        {!! Form::text('name', !empty($duplicate_product->name) ? $duplicate_product->name : null, ['class' => 'form-control tw-rounded-md focus:tw-border-indigo-500', 'required', 'placeholder' => __('product.product_name')]); !!}
                    </div>
                </div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        {!! Form::label('sku', __('product.sku') . ':', ['class' => 'tw-text-gray-700 tw-font-medium']) !!} @show_tooltip(__('tooltip.sku'))
                        {!! Form::text('sku', null, ['class' => 'form-control tw-rounded-md focus:tw-border-indigo-500', 'placeholder' => __('product.sku')]); !!}
                    </div>
                </div>
                
                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        {!! Form::label('image', __('lang_v1.product_image') . ':', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        {!! Form::file('image', ['id' => 'upload_image', 'accept' => 'image/*', 'required' => $is_image_required, 'class' => 'upload-element']); !!}
                    </div>
                </div>
                
                <div class="clearfix"></div>

                <div class="col-sm-6 col-md-4">
                    <div class="form-group">
                        {!! Form::label('unit_id', __('product.unit') . ':*', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        <div class="input-group">
                            {!! Form::select('unit_id', $units, !empty($duplicate_product->unit_id) ? $duplicate_product->unit_id : session('business.default_unit'), ['class' => 'form-control select2 tw-w-full', 'required']); !!}
                            <span class="input-group-btn">
                                <button type="button" @if(!auth()->user()->can('unit.create')) disabled @endif class="btn btn-default bg-white btn-flat btn-modal" data-href="{{action([\App\Http\Controllers\UnitController::class, 'create'], ['quick_add' => true])}}" title="@lang('unit.add_unit')" data-container=".view_modal"><i class="fa fa-plus-circle tw-text-indigo-600 fa-lg"></i></button>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 @if(!session('business.enable_category')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('category_id', __('product.category') . ':', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        {!! Form::select('category_id', $categories, !empty($duplicate_product->category_id) ? $duplicate_product->category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2 tw-w-full']); !!}
                    </div>
                </div>

                <div class="col-sm-6 col-md-4 @if(!(session('business.enable_category') && session('business.enable_sub_category'))) hide @endif">
                    <div class="form-group">
                        {!! Form::label('sub_category_id', __('product.sub_category') . ':', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        {!! Form::select('sub_category_id', $sub_categories, !empty($duplicate_product->sub_category_id) ? $duplicate_product->sub_category_id : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2 tw-w-full']); !!}
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <!-- Hidden crucial fields -->
                @php
                $default_location = null;
                if(count($business_locations) == 1){
                    $default_location = array_key_first($business_locations->toArray());
                }
                @endphp
                <div class="hide">
                    {!! Form::select('product_locations[]', $business_locations, $default_location, ['class' => 'form-control select2', 'multiple', 'id' => 'product_locations']); !!}
                    {!! Form::select('barcode_type', $barcode_types, !empty($duplicate_product->barcode_type) ? $duplicate_product->barcode_type : $barcode_default, ['class' => 'form-control select2', 'required']); !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Section -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-mb-6 tw-overflow-hidden tw-border tw-border-gray-100">
        <div class="tw-bg-gray-50 tw-px-6 tw-py-4 tw-border-b tw-border-gray-100">
            <h4 class="tw-text-gray-800 tw-font-bold tw-text-lg tw-m-0">
                <i class="fas fa-boxes tw-text-indigo-500 tw-mr-2"></i> Inventaire & Stock
            </h4>
        </div>
        
        <div class="tw-p-6">
            <div class="row">
                <div class="col-sm-12 col-md-4 tw-mb-4 md:tw-mb-0">
                    <div class="tw-bg-indigo-50 tw-p-4 tw-rounded-lg tw-border tw-border-indigo-100 tw-h-full tw-flex tw-flex-col tw-justify-center">
                        <label class="tw-flex tw-items-center tw-cursor-pointer tw-m-0">
                            {!! Form::checkbox('enable_stock', 1, !empty($duplicate_product) ? $duplicate_product->enable_stock : true, ['class' => 'input-icheck tw-mr-3', 'id' => 'enable_stock']); !!} 
                            <span class="tw-font-bold tw-text-indigo-800 tw-text-base">Gérer le stock pour cet article</span>
                        </label>
                        <p class="tw-text-sm tw-text-indigo-600 tw-mt-2 tw-mb-0 tw-pl-8">Activez cette option si vous souhaitez garder une trace des quantités exactes de ce produit en magasin.</p>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8">
                    <div class="row @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) hide @endif" id="alert_quantity_div">
                        <div class="col-sm-6">
                            <div class="form-group tw-mb-0">
                                {!! Form::label('alert_quantity', "Quantité d'alerte (Stock Minimum):", ['class' => 'tw-text-gray-700 tw-font-medium']) !!} @show_tooltip(__('tooltip.alert_quantity'))
                                <div class="input-group">
                                    <span class="input-group-addon tw-bg-gray-100 tw-border-gray-300"><i class="fas fa-bell tw-text-yellow-500"></i></span>
                                    {!! Form::text('alert_quantity', !empty($duplicate_product->alert_quantity) ? @format_quantity($duplicate_product->alert_quantity) : null , ['class' => 'form-control tw-rounded-md tw-border-gray-300 focus:tw-border-indigo-500 input_number', 'placeholder' => "Nombre minimum avant d'être alerté", 'min' => '0']); !!}
                                </div>
                                <small class="tw-text-gray-500 tw-block tw-mt-1">Vous serez averti quand le stock atteindra ce seuil.</small>
                            </div>
                        </div>
                        
                        <!-- Include module fields quietly here if needed -->
                        @if(!empty($pos_module_data))
                        @foreach($pos_module_data as $key => $value)
                        @if(!empty($value['view_path']))
                        @includeIf($value['view_path'], ['view_data' => $value['view_data']])
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing & Taxation Section -->
    <div class="tw-bg-white tw-rounded-xl tw-shadow-sm tw-mb-6 tw-overflow-hidden tw-border tw-border-gray-100">
        <div class="tw-bg-gray-50 tw-px-6 tw-py-4 tw-border-b tw-border-gray-100">
            <h4 class="tw-text-gray-800 tw-font-bold tw-text-lg tw-m-0">
                <i class="fas fa-tags tw-text-indigo-500 tw-mr-2"></i> Prix et Taxation
            </h4>
        </div>
        
        <div class="tw-p-6">
            <div class="row">
                <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('tax', __('product.applicable_tax') . ':', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        {!! Form::select('tax', $taxes, !empty($duplicate_product->tax) ? $duplicate_product->tax : null, ['placeholder' => __('messages.please_select'), 'class' => 'form-control select2 tw-w-full'], $tax_attributes); !!}
                    </div>
                </div>

                <div class="col-sm-4 @if(!session('business.enable_price_tax')) hide @endif">
                    <div class="form-group">
                        {!! Form::label('tax_type', __('product.selling_price_tax_type') . ':*', ['class' => 'tw-text-gray-700 tw-font-medium']) !!}
                        {!! Form::select('tax_type', ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')], !empty($duplicate_product->tax_type) ? $duplicate_product->tax_type : 'exclusive',
                        ['class' => 'form-control select2 tw-w-full', 'required']); !!}
                    </div>
                </div>
                
                <div class="clearfix"></div>

                <div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('type', __('product.product_type') . ':*', ['class' => 'tw-text-gray-700 tw-font-medium']) !!} @show_tooltip(__('tooltip.product_type'))
                        {!! Form::select('type', $product_types, !empty($duplicate_product->type) ? $duplicate_product->type : null, ['class' => 'form-control select2 tw-w-full',
                        'required', 'data-action' => !empty($duplicate_product) ? 'duplicate' : 'add', 'data-product_id' => !empty($duplicate_product) ? $duplicate_product->id : '0']); !!}
                    </div>
                </div>

                <div class="form-group col-sm-12" id="product_form_part">
                    @include('product.partials.single_product_form_part', ['profit_percent' => $default_profit_percent])
                </div>

                <input type="hidden" id="variation_counter" value="1">
                <input type="hidden" id="default_profit_percent" value="{{ $default_profit_percent }}">
            </div>
        </div>
    </div>
    
    <div class="row tw-mt-8 tw-mb-10">
        <div class="col-sm-12">
            <input type="hidden" name="submit_type" id="submit_type">
            <div class="tw-flex tw-justify-center tw-gap-4">
                @if($selling_price_group_count)
                <button type="submit" value="submit_n_add_selling_prices" class="tw-dw-btn tw-dw-btn-warning tw-text-white tw-rounded-lg tw-px-6 tw-shadow-sm submit_product_form hover:tw-scale-105 tw-transition tw-duration-200">@lang('lang_v1.save_n_add_selling_price_group_prices')</button>
                @endif

                @can('product.opening_stock')
                <button id="opening_stock_button" @if(!empty($duplicate_product) && $duplicate_product->enable_stock == 0) disabled @endif type="submit" value="submit_n_add_opening_stock" class="tw-dw-btn tw-text-white tw-bg-purple-600 hover:tw-bg-purple-700 tw-border-none tw-rounded-lg tw-px-6 tw-shadow-sm submit_product_form hover:tw-scale-105 tw-transition tw-duration-200">@lang('lang_v1.save_n_add_opening_stock')</button>
                @endcan

                <button type="submit" value="save_n_add_another" class="tw-dw-btn tw-text-white tw-bg-rose-600 hover:tw-bg-rose-700 tw-border-none tw-rounded-lg tw-px-6 tw-shadow-sm submit_product_form hover:tw-scale-105 tw-transition tw-duration-200">@lang('lang_v1.save_n_add_another')</button>

                <button type="submit" value="submit" class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-rounded-lg tw-px-8 tw-shadow-md submit_product_form hover:tw-scale-105 tw-transition tw-duration-200 tw-font-bold tw-text-lg">@lang('messages.save') <i class="fas fa-check-circle tw-ml-2"></i></button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

</section>
<!-- /.content -->

@endsection

@section('javascript')

<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        __page_leave_confirmation('#product_add_form');
        onScan.attachTo(document, {
            suffixKeyCodes: [13], // enter-key expected at the end of a scan
            reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
            onScan: function(sCode, iQty) {
                $('input#sku').val(sCode);
            },
            onScanError: function(oDebug) {
                console.log(oDebug);
            },
            minLength: 2,
            ignoreIfFocusOn: ['input', '.form-control']
        });

        // Optical product type toggle logic
        function toggleOpticalSections() {
            var selectedType = $('#optical_product_type').val();
            if (selectedType === 'frame') {
                $('#frame_details_section').slideDown();
                $('#lens_details_section').slideUp();
            } else if (selectedType === 'lens' || selectedType === 'contact_lens') {
                $('#lens_details_section').slideDown();
                $('#frame_details_section').slideUp();
            } else {
                $('#frame_details_section').slideUp();
                $('#lens_details_section').slideUp();
            }
        }
        
        $('#optical_product_type').on('change', toggleOpticalSections);
        toggleOpticalSections(); // Initial check
    });
</script>
@endsection