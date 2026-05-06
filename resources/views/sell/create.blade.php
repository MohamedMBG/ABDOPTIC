@extends('layouts.app')

@php
	if (!empty($status) && $status == 'quotation') {
		$title = __('lang_v1.add_quotation');
	} else if (!empty($status) && $status == 'draft') {
		$title = __('lang_v1.add_draft');
	} else {
		$title = __('sale.add_sale');
	}

	if($sale_type == 'sales_order') {
		$title = __('lang_v1.sales_order');
	}
@endphp

@section('title', $title)

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">{{$title}}</h1>
</section>
<!-- Main content -->
<section class="content no-print simple-sale-page">
<input type="hidden" id="amount_rounding_method" value="{{$pos_settings['amount_rounding_method'] ?? ''}}">
@if(!empty($pos_settings['allow_overselling']))
	<input type="hidden" id="is_overselling_allowed">
@endif
@if(session('business.enable_rp') == 1)
    <input type="hidden" id="reward_point_enabled">
@endif
@if(count($business_locations) > 0)
<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
			<div class="input-group">
				<span class="input-group-addon">
					<i class="fa fa-map-marker"></i>
				</span>
			{!! Form::select('select_location_id', $business_locations, $default_location->id ?? null, ['class' => 'form-control input-sm',
			'id' => 'select_location_id', 
			'required', 'autofocus'], $bl_attributes); !!}
			<span class="input-group-addon">
					@show_tooltip(__('tooltip.sale_location'))
				</span> 
			</div>
		</div>
	</div>
</div>
@endif

@php
	$custom_labels = json_decode(session('business.custom_labels'), true);
	$common_settings = session()->get('business.common_settings');
@endphp
<input type="hidden" id="item_addition_method" value="{{$business_details->item_addition_method}}">
	{!! Form::open(['url' => action([\App\Http\Controllers\SellPosController::class, 'store']), 'method' => 'post', 'id' => 'add_sell_form', 'files' => true ]) !!}
	 @if(!empty($sale_type))
	 	<input type="hidden" id="sale_type" name="type" value="{{$sale_type}}">
	 @endif
	<div class="row">
		<div class="col-md-12 col-sm-12">
			@component('components.widget', ['class' => 'box-solid'])
				{!! Form::hidden('location_id', !empty($default_location) ? $default_location->id : null , ['id' => 'location_id', 'data-receipt_printer_type' => !empty($default_location->receipt_printer_type) ? $default_location->receipt_printer_type : 'browser', 'data-default_payment_accounts' => !empty($default_location) ? $default_location->default_payment_accounts : '']); !!}

				@php
					$has_advanced_sale_fields = (!empty($price_groups) && count($price_groups) > 1)
						|| (in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
						|| in_array('subscription', $enabled_modules)
						|| !empty($commission_agent)
						|| ($sale_type != 'sales_order')
						|| !empty($custom_field_1_label ?? null)
						|| !empty($custom_field_2_label ?? null)
						|| !empty($custom_field_3_label ?? null)
						|| !empty($custom_field_4_label ?? null);
				@endphp

				<details class="simple-sale-details simple-sale-advanced-box">
					<summary>Parametres de vente</summary>
					<p class="simple-sale-help">Reglez ici les options de paiement, prix et service seulement si besoin.</p>
				@if(!empty($price_groups))
					@if(count($price_groups) > 1)
						<div class="col-sm-4 simple-sale-advanced-field">
							<div class="form-group">
								{!! Form::label('price_group', __('lang_v1.price_group') . ':', ['class' => 'simple-sale-label']) !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fas fa-money-bill-alt"></i>
									</span>
									@php
										reset($price_groups);
										$selected_price_group = !empty($default_price_group_id) && array_key_exists($default_price_group_id, $price_groups) ? $default_price_group_id : null;
									@endphp
									{!! Form::hidden('hidden_price_group', key($price_groups), ['id' => 'hidden_price_group']) !!}
									{!! Form::select('price_group', $price_groups, $selected_price_group, ['class' => 'form-control select2', 'id' => 'price_group']); !!}
									<span class="input-group-addon">
										@show_tooltip(__('lang_v1.price_group_help_text'))
									</span> 
								</div>
							</div>
						</div>
						
					@else
						@php
							reset($price_groups);
						@endphp
						{!! Form::hidden('price_group', key($price_groups), ['id' => 'price_group']) !!}
					@endif
				@endif

				{!! Form::hidden('default_price_group', null, ['id' => 'default_price_group']) !!}

				@if(in_array('types_of_service', $enabled_modules) && !empty($types_of_service))
					<div class="col-md-4 col-sm-6 simple-sale-advanced-field">
						<div class="form-group">
							{!! Form::label('types_of_service_id', __('lang_v1.select_types_of_service') . ':', ['class' => 'simple-sale-label']) !!}
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-external-link-square-alt text-primary service_modal_btn"></i>
								</span>
								{!! Form::select('types_of_service_id', $types_of_service, null, ['class' => 'form-control', 'id' => 'types_of_service_id', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.select_types_of_service')]); !!}

								{!! Form::hidden('types_of_service_price_group', null, ['id' => 'types_of_service_price_group']) !!}

								<span class="input-group-addon">
									@show_tooltip(__('lang_v1.types_of_service_help'))
								</span> 
							</div>
							<small><p class="help-block hide" id="price_group_text">@lang('lang_v1.price_group'): <span></span></p></small>
						</div>
					</div>
				@endif
				
				@if(in_array('subscription', $enabled_modules))
					<div class="col-md-4 pull-right col-sm-6 simple-sale-advanced-field">
						<div class="checkbox">
							<label>
				              {!! Form::checkbox('is_recurring', 1, false, ['class' => 'input-icheck', 'id' => 'is_recurring']); !!} @lang('lang_v1.subscribe')?
				            </label><button type="button" data-toggle="modal" data-target="#recurringInvoiceModal" class="btn btn-link"><i class="fa fa-external-link"></i></button>@show_tooltip(__('lang_v1.recurring_invoice_help'))
						</div>
					</div>
				@endif
				<div class="clearfix"></div>
				</details>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('contact_id', __('contact.customer') . ':*', ['class' => 'simple-sale-label']) !!}
						<p class="simple-sale-help">Choisissez le client pour remplir la vente plus rapidement.</p>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-user"></i>
							</span>
							<input type="hidden" id="default_customer_id" 
							value="{{ $walk_in_customer['id']}}" >
							<input type="hidden" id="default_customer_name" 
							value="{{ $walk_in_customer['name']}}" >
							<input type="hidden" id="default_customer_balance" value="{{ $walk_in_customer['balance'] ?? ''}}" >
							<input type="hidden" id="default_customer_address" value="{{ $walk_in_customer['shipping_address'] ?? ''}}" >
							@if(!empty($walk_in_customer['price_calculation_type']) && $walk_in_customer['price_calculation_type'] == 'selling_price_group')
								<input type="hidden" id="default_selling_price_group" 
							value="{{ $walk_in_customer['selling_price_group_id'] ?? ''}}" >
							@endif
							{!! Form::select('contact_id', 
								[], null, ['class' => 'form-control mousetrap', 'id' => 'customer_id', 'placeholder' => 'Nom ou telephone du client', 'required']); !!}
							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat add_new_customer" data-name=""><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
						<small class="text-danger hide contact_due_text"><strong>@lang('account.customer_due'):</strong> <span></span></small>
					</div>
					<details class="simple-sale-details">
						<summary>Adresses du client</summary>
						<div class="simple-sale-addresses">
							<strong>@lang('lang_v1.billing_address'):</strong>
							<div id="billing_address_div">
								{!! $walk_in_customer['contact_address'] ?? '' !!}
							</div>
						</div>
					</details>
				</div>

				<details class="simple-sale-details simple-sale-advanced-box">
					<summary>Paiement et options client</summary>
					<p class="simple-sale-help">Affichez ces champs seulement si la vente a des conditions particulieres.</p>
				<div class="col-md-4 simple-sale-advanced-field">
		          <div class="form-group">
		          	{!! Form::label('pay_term_number', __('contact.pay_term') . ':', ['class' => 'simple-sale-label']) !!} @show_tooltip(__('tooltip.pay_term'))
		            <div class="multi-input simple-sale-multi-input">
		            @php
						$is_pay_term_required = !empty($pos_settings['is_pay_term_required']);
						$pay_term_type = !empty($walk_in_customer['pay_term_type']) ? $walk_in_customer['pay_term_type'] : 'months';
					@endphp
		              {!! Form::number('pay_term_number', $walk_in_customer['pay_term_number'], ['class' => 'form-control simple-sale-pay-term-number', 'placeholder' => __('contact.pay_term'), 'required' => $is_pay_term_required]); !!}
					  {!! Form::hidden('pay_term_type', 'months') !!}
					  <div class="form-control simple-sale-pay-term-type simple-sale-readonly-field">Mois</div>
		            </div>
		          </div>
		        </div>
				

				

				@if(!empty($commission_agent))
				@php
					$is_commission_agent_required = !empty($pos_settings['is_commission_agent_required']);
				@endphp
				<div class="col-sm-3 simple-sale-advanced-field">
					<div class="form-group">
					{!! Form::label('commission_agent', __('lang_v1.commission_agent') . ':', ['class' => 'simple-sale-label']) !!}
					{!! Form::select('commission_agent', 
								$commission_agent, null, ['class' => 'form-control select2', 'id' => 'commission_agent', 'required' => $is_commission_agent_required]); !!}
					</div>
				</div>
				@endif
				</details>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('transaction_date', __('sale.sale_date') . ':*', ['class' => 'simple-sale-label']) !!}
						<p class="simple-sale-help">Date de la vente. Ce champ reste visible en permanence.</p>
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('transaction_date', $default_datetime, ['class' => 'form-control', 'readonly', 'required']); !!}
						</div>
					</div>
				</div>

				


				@if(!empty($status))
					<input type="hidden" name="status" id="status" value="{{$status}}">

					@if(in_array($status, ['draft', 'quotation']))
						<input type="hidden" id="disable_qty_alert">
					@endif
				@else
					<div class="col-sm-4">
						<div class="form-group">
							{!! Form::label('status', __('sale.status') . ':*', ['class' => 'simple-sale-label']) !!}
							<p class="simple-sale-help">Choisissez uniquement le statut final voulu pour cette vente.</p>
							{!! Form::select('status', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
						</div>
					</div>
				@endif
				<div class="clearfix"></div>
				<details class="simple-sale-details simple-sale-advanced-box">
					<summary>Options avancees</summary>
					<p class="simple-sale-help">Ces champs sont secondaires. Ouvrez-les seulement si necessaire.</p>
				@if($sale_type != 'sales_order')
					<div class="col-sm-3 simple-sale-advanced-field">
						<div class="form-group">
							{!! Form::label('invoice_scheme_id', __('invoice.invoice_scheme') . ':', ['class' => 'simple-sale-label']) !!}
							{!! Form::select('invoice_scheme_id', $invoice_schemes, $default_invoice_schemes->id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
						</div>
					</div>
				@endif
					@can('edit_invoice_number')
					<div class="col-sm-3 simple-sale-advanced-field">
						<div class="form-group">
							{!! Form::label('invoice_no', $sale_type == 'sales_order' ? __('restaurant.order_no') : __('sale.invoice_no') . ':', ['class' => 'simple-sale-label']) !!}
							{!! Form::text('invoice_no', null, ['class' => 'form-control', 'placeholder' => $sale_type == 'sales_order' ? __('restaurant.order_no') : __('sale.invoice_no')]); !!}
							<p class="help-block">@lang('lang_v1.keep_blank_to_autogenerate')</p>
						</div>
					</div>
					@endcan
				
				@php
			        $custom_field_1_label = !empty($custom_labels['sell']['custom_field_1']) ? $custom_labels['sell']['custom_field_1'] : '';

			        $is_custom_field_1_required = !empty($custom_labels['sell']['is_custom_field_1_required']) && $custom_labels['sell']['is_custom_field_1_required'] == 1 ? true : false;

			        $custom_field_2_label = !empty($custom_labels['sell']['custom_field_2']) ? $custom_labels['sell']['custom_field_2'] : '';

			        $is_custom_field_2_required = !empty($custom_labels['sell']['is_custom_field_2_required']) && $custom_labels['sell']['is_custom_field_2_required'] == 1 ? true : false;

			        $custom_field_3_label = !empty($custom_labels['sell']['custom_field_3']) ? $custom_labels['sell']['custom_field_3'] : '';

			        $is_custom_field_3_required = !empty($custom_labels['sell']['is_custom_field_3_required']) && $custom_labels['sell']['is_custom_field_3_required'] == 1 ? true : false;

			        $custom_field_4_label = !empty($custom_labels['sell']['custom_field_4']) ? $custom_labels['sell']['custom_field_4'] : '';

			        $is_custom_field_4_required = !empty($custom_labels['sell']['is_custom_field_4_required']) && $custom_labels['sell']['is_custom_field_4_required'] == 1 ? true : false;
		        @endphp
		        @if(!empty($custom_field_1_label))
		        	@php
		        		$label_1 = $custom_field_1_label . ':';
		        		if($is_custom_field_1_required) {
		        			$label_1 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4 simple-sale-advanced-field">
				        <div class="form-group">
				            {!! Form::label('custom_field_1', $label_1, ['class' => 'simple-sale-label']) !!}
				            {!! Form::text('custom_field_1', null, ['class' => 'form-control','placeholder' => $custom_field_1_label, 'required' => $is_custom_field_1_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($custom_field_2_label))
		        	@php
		        		$label_2 = $custom_field_2_label . ':';
		        		if($is_custom_field_2_required) {
		        			$label_2 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4 simple-sale-advanced-field">
				        <div class="form-group">
				            {!! Form::label('custom_field_2', $label_2, ['class' => 'simple-sale-label']) !!}
				            {!! Form::text('custom_field_2', null, ['class' => 'form-control','placeholder' => $custom_field_2_label, 'required' => $is_custom_field_2_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($custom_field_3_label))
		        	@php
		        		$label_3 = $custom_field_3_label . ':';
		        		if($is_custom_field_3_required) {
		        			$label_3 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4 simple-sale-advanced-field">
				        <div class="form-group">
				            {!! Form::label('custom_field_3', $label_3, ['class' => 'simple-sale-label']) !!}
				            {!! Form::text('custom_field_3', null, ['class' => 'form-control','placeholder' => $custom_field_3_label, 'required' => $is_custom_field_3_required]); !!}
				        </div>
				    </div>
		        @endif
		        @if(!empty($custom_field_4_label))
		        	@php
		        		$label_4 = $custom_field_4_label . ':';
		        		if($is_custom_field_4_required) {
		        			$label_4 .= '*';
		        		}
		        	@endphp

		        	<div class="col-md-4 simple-sale-advanced-field">
				        <div class="form-group">
				            {!! Form::label('custom_field_4', $label_4, ['class' => 'simple-sale-label']) !!}
				            {!! Form::text('custom_field_4', null, ['class' => 'form-control','placeholder' => $custom_field_4_label, 'required' => $is_custom_field_4_required]); !!}
				        </div>
				    </div>
		        @endif
		        <div class="col-sm-3 simple-sale-advanced-field">
	                <div class="form-group">
	                    {!! Form::label('upload_document', __('purchase.attach_document') . ':', ['class' => 'simple-sale-label']) !!}
	                    {!! Form::file('sell_document', ['id' => 'upload_document', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types')))]); !!}
	                    <p class="help-block">
	                    	@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
	                    	@includeIf('components.document_help_text')
	                    </p>
	                </div>
	            </div>
		        <div class="clearfix"></div>

		        @if((!empty($pos_settings['enable_sales_order']) && $sale_type != 'sales_order') || $is_order_request_enabled)
					<div class="col-sm-3">
						<div class="form-group">
							{!! Form::label('sales_order_ids', __('lang_v1.sales_order').':', ['class' => 'simple-sale-label']) !!}
							{!! Form::select('sales_order_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'id' => 'sales_order_ids']); !!}
						</div>
					</div>
					<div class="clearfix"></div>
				@endif
				</details>

				<div class="row">
   
</div>



				<!-- Call restaurant module if defined -->
		        @if(in_array('tables' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
		        	<span id="restaurant_module_span">
		        	</span>
		        @endif
			@endcomponent

			@component('components.widget', ['class' => 'box-solid'])
				<div class="col-sm-10 col-sm-offset-1">
					<div class="form-group">
						{!! Form::label('search_product', __('sale.product') . ':', ['class' => 'simple-sale-label']) !!}
						<p class="simple-sale-help">Tapez ou scannez un produit pour l'ajouter.</p>
						<div class="input-group">
							<div class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat" data-toggle="modal" data-target="#configure_search_modal" title="{{__('lang_v1.configure_product_search')}}"><i class="fas fa-search-plus"></i></button>
							</div>
							{!! Form::text('search_product', null, ['class' => 'form-control mousetrap', 'id' => 'search_product', 'placeholder' => 'Rechercher ou scanner un produit',
							'disabled' => is_null($default_location)? true : false,
							'autofocus' => is_null($default_location)? false : true,
							]); !!}
							<span class="input-group-btn">
								<button type="button" class="btn btn-default bg-white btn-flat pos_add_quick_product" data-href="{{action([\App\Http\Controllers\ProductController::class, 'quickAdd'])}}" data-container=".quick_add_product_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-12 simple-sale-product-browser">
					<div class="simple-sale-product-browser-header">
						<h3 class="simple-sale-subtitle">Produits existants</h3>
						<p class="simple-sale-help">Cliquez sur un produit existant pour l'ajouter rapidement a la vente.</p>
					</div>
					<div class="row">
						@if(!empty($categories))
							<div class="col-sm-6">
								<div class="form-group">
									{!! Form::label('simple_sale_product_category', __('category.category') . ':', ['class' => 'simple-sale-label']) !!}
									<select class="form-control select2" id="simple_sale_product_category">
										<option value="all">@lang('lang_v1.all_category')</option>
										@foreach($categories as $category)
											<option value="{{$category['id']}}">{{$category['name']}}</option>
											@if(!empty($category['sub_categories']))
												@foreach($category['sub_categories'] as $sc)
													<option value="{{$sc['id']}}">&nbsp;&nbsp;{{$sc['name']}}</option>
												@endforeach
											@endif
										@endforeach
									</select>
								</div>
							</div>
						@endif
						@if(!empty($brands))
							<div class="col-sm-6">
								<div class="form-group">
									{!! Form::label('simple_sale_product_brand', __('brand.brands') . ':', ['class' => 'simple-sale-label']) !!}
									{!! Form::select('simple_sale_product_brand', ['' => __('messages.all')] + $brands, null, ['id' => 'simple_sale_product_brand', 'class' => 'form-control select2', 'name' => null]) !!}
								</div>
							</div>
						@endif
					</div>
					<input type="hidden" id="suggestion_page" value="1">
					<div class="simple-sale-product-grid-wrap">
						<div class="row eq-height-row" id="product_list_body"></div>
						<div class="text-center" id="suggestion_page_loader" style="display: none;">
							<i class="fa fa-spinner fa-spin fa-2x"></i>
						</div>
					</div>
				</div>

				<div class="row col-sm-12 pos_product_div" style="min-height: 0">

					<input type="hidden" name="sell_price_tax" id="sell_price_tax" value="{{$business_details->sell_price_tax}}">

					<!-- Keeps count of product rows -->
					<input type="hidden" id="product_row_count" 
						value="0">
					@php
						$hide_tax = '';
						if( session()->get('business.enable_inline_tax') == 0){
							$hide_tax = 'hide';
						}
					@endphp
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped table-responsive" id="pos_table">
						<thead>
							<tr>
								<th class="text-center">	
									@lang('sale.product')
								</th>
								<th class="text-center">
									@lang('sale.qty')
								</th>
								@if(!empty($pos_settings['inline_service_staff']))
									<th class="text-center">
										@lang('restaurant.service_staff')
									</th>
								@endif
								<th class="@if(!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif">
									@lang('sale.unit_price')
								</th>
								<th class="@if(!auth()->user()->can('edit_product_discount_from_sale_screen')) hide @endif">
									@lang('receipt.discount')
								</th>
								<th class="text-center {{$hide_tax}}">
									@lang('sale.tax')
								</th>
								<th class="text-center {{$hide_tax}}">
									@lang('sale.price_inc_tax')
								</th>
								@if(!empty($common_settings['enable_product_warranty']))
									<th>@lang('lang_v1.warranty')</th>
								@endif
								<th class="text-center">
									@lang('sale.subtotal')
								</th>
								<th class="text-center"><i class="fas fa-times" aria-hidden="true"></i></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					</div>
					<div class="table-responsive">
					<table class="table table-condensed table-bordered table-striped">
						<tr>
							<td>
								<div class="pull-right">
								<b>@lang('sale.item'):</b> 
								<span class="total_quantity">0</span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<b>@lang('sale.total'): </b>
									<span class="price_total">0</span>
								</div>
							</td>
						</tr>
					</table>
					</div>
				</div>
			@endcomponent
			@component('components.widget', ['class' => 'box-solid'])
				<div class="col-md-4  @if($sale_type == 'sales_order') hide @endif">
			        <div class="form-group">
			            {!! Form::label('discount_type', __('sale.discount_type') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('discount_type', ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], 'percentage' , ['class' => 'form-control','placeholder' => __('messages.please_select'), 'required', 'data-default' => 'percentage']); !!}
			            </div>
			        </div>
			    </div>
			    @php
			    	$max_discount = !is_null(auth()->user()->max_sales_discount_percent) ? auth()->user()->max_sales_discount_percent : '';

			    	//if sale discount is more than user max discount change it to max discount
			    	$sales_discount = $business_details->default_sales_discount;
			    	if($max_discount != '' && $sales_discount > $max_discount) $sales_discount = $max_discount;

			    	$default_sales_tax = $business_details->default_sales_tax;

			    	if($sale_type == 'sales_order') {
			    		$sales_discount = 0;
			    		$default_sales_tax = null;
			    	}
			    @endphp
			    <div class="col-md-4 @if($sale_type == 'sales_order') hide @endif">
			        <div class="form-group">
			            {!! Form::label('discount_amount', __('sale.discount_amount') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::text('discount_amount', @num_format($sales_discount), ['class' => 'form-control input_number', 'data-default' => $sales_discount, 'data-max-discount' => $max_discount, 'data-max-discount-error_msg' => __('lang_v1.max_discount_error_msg', ['discount' => $max_discount != '' ? @num_format($max_discount) : '']) ]); !!}
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4 @if($sale_type == 'sales_order') hide @endif"><br>
			    	<b>@lang( 'sale.discount_amount' ):</b>(-) 
					<span class="display_currency" id="total_discount">0</span>
			    </div>
			    <div class="clearfix"></div>
			    <div class="col-md-12 well well-sm bg-light-gray @if(session('business.enable_rp') != 1 || $sale_type == 'sales_order') hide @endif">
			    	<input type="hidden" name="rp_redeemed" id="rp_redeemed" value="0">
			    	<input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount" value="0">
			    	<div class="col-md-12"><h4>{{session('business.rp_name')}}</h4></div>
			    	<div class="col-md-4">
				        <div class="form-group">
				            {!! Form::label('rp_redeemed_modal', __('lang_v1.redeemed') . ':' ) !!}
				            <div class="input-group">
				                <span class="input-group-addon">
				                    <i class="fa fa-gift"></i>
				                </span>
				                {!! Form::number('rp_redeemed_modal', 0, ['class' => 'form-control direct_sell_rp_input', 'data-amount_per_unit_point' => session('business.redeem_amount_per_unit_rp'), 'min' => 0, 'data-max_points' => 0, 'data-min_order_total' => session('business.min_order_total_for_redeem') ]); !!}
				                <input type="hidden" id="rp_name" value="{{session('business.rp_name')}}">
				            </div>
				        </div>
				    </div>
				    <div class="col-md-4">
				    	<p><strong>@lang('lang_v1.available'):</strong> <span id="available_rp">0</span></p>
				    </div>
				    <div class="col-md-4">
				    	<p><strong>@lang('lang_v1.redeemed_amount'):</strong> (-)<span id="rp_redeemed_amount_text">0</span></p>
				    </div>
			    </div>
			    <div class="clearfix"></div>
			    <div class="col-md-4  @if($sale_type == 'sales_order') hide @endif">
			    	<div class="form-group">
			            {!! Form::label('tax_rate_id', __('sale.order_tax') . ':*' ) !!}
			            <div class="input-group">
			                <span class="input-group-addon">
			                    <i class="fa fa-info"></i>
			                </span>
			                {!! Form::select('tax_rate_id', $taxes['tax_rates'], $default_sales_tax, ['placeholder' => __('messages.please_select'), 'class' => 'form-control', 'data-default'=> $default_sales_tax], $taxes['attributes']); !!}

							<input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount" 
							value="@if(empty($edit)) {{@num_format($business_details->tax_calculation_amount)}} @else {{@num_format($transaction->tax?->amount)}} @endif" data-default="{{$business_details->tax_calculation_amount}}">
			            </div>
			        </div>
			    </div>
			    <div class="col-md-4 col-md-offset-4  @if($sale_type == 'sales_order') hide @endif">
			    	<b>@lang( 'sale.order_tax' ):</b>(+) 
					<span class="display_currency" id="order_tax">0</span>
			    </div>				
				
			    <div class="col-md-12">
			    	<div class="form-group">
						{!! Form::label('sell_note',__('sale.sell_note')) !!}
						{!! Form::textarea('sale_note', null, ['class' => 'form-control', 'rows' => 3]); !!}
					</div>
			    </div>
				<input type="hidden" name="is_direct_sale" value="1">
			@endcomponent
			@component('components.widget', ['class' => 'box-solid'])
	        <div class="col-md-12 text-center">
				<button type="button" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-sm tw-text-white" id="toggle_additional_expense"> <i class="fas fa-plus"></i> @lang('lang_v1.add_additional_expenses') <i class="fas fa-chevron-down"></i></button>
			</div>
			<div class="col-md-8 col-md-offset-4" id="additional_expenses_div" style="display: none;">
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>@lang('lang_v1.additional_expense_name')</th>
							<th>@lang('sale.amount')</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_1', null, ['class' => 'form-control', 'id' => 'additional_expense_key_1']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_1', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_1']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_2', null, ['class' => 'form-control', 'id' => 'additional_expense_key_2']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_2', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_2']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_3', null, ['class' => 'form-control', 'id' => 'additional_expense_key_3']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_3', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_3']); !!}
							</td>
						</tr>
						<tr>
							<td>
								{!! Form::text('additional_expense_key_4', null, ['class' => 'form-control', 'id' => 'additional_expense_key_4']); !!}
							</td>
							<td>
								{!! Form::text('additional_expense_value_4', 0, ['class' => 'form-control input_number', 'id' => 'additional_expense_value_4']); !!}
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		    <div class="col-md-4 col-md-offset-8">
		    	@if(!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
		    	<small id="round_off"><br>(@lang('lang_v1.round_off'): <span id="round_off_text">0</span>)</small>
				<br/>
				<input type="hidden" name="round_off_amount" 
					id="round_off_amount" value=0>
				@endif
		    	<div><b>@lang('sale.total_payable'): </b>
					<input type="hidden" name="final_total" id="final_total_input">
					<span id="total_payable">0</span>
				</div>
		    </div>
			@endcomponent
		</div>
	</div>
	@if(!empty($common_settings['is_enabled_export']) && $sale_type != 'sales_order')
		@component('components.widget', ['class' => 'box-solid', 'title' => __('lang_v1.export')])
			<div class="col-md-12 mb-12">
                <div class="form-check">
                    <input type="checkbox" name="is_export" class="form-check-input" id="is_export" @if(!empty($walk_in_customer['is_export'])) checked @endif>
                    <label class="form-check-label" for="is_export">@lang('lang_v1.is_export')</label>
                </div>
            </div>
	        @php
	            $i = 1;
	        @endphp
	        @for($i; $i <= 6 ; $i++)
	            <div class="col-md-4 export_div" @if(empty($walk_in_customer['is_export'])) style="display: none;" @endif>
	                <div class="form-group">
	                    {!! Form::label('export_custom_field_'.$i, __('lang_v1.export_custom_field'.$i).':') !!}
	                    {!! Form::text('export_custom_fields_info['.'export_custom_field_'.$i.']', !empty($walk_in_customer['export_custom_field_'.$i]) ? $walk_in_customer['export_custom_field_'.$i] : null, ['class' => 'form-control','placeholder' => __('lang_v1.export_custom_field'.$i), 'id' => 'export_custom_field_'.$i]); !!}
	                </div>
	            </div>
	        @endfor
		@endcomponent
	@endif
	@php
		$is_enabled_download_pdf = config('constants.enable_download_pdf');
		$payment_body_id = 'payment_rows_div';
		if ($is_enabled_download_pdf) {
			$payment_body_id = '';
		}
	@endphp
	@if((empty($status) || (!in_array($status, ['quotation', 'draft'])) || $is_enabled_download_pdf) && $sale_type != 'sales_order')
		@can('sell.payments')
			@component('components.widget', ['class' => 'box-solid', 'id' => $payment_body_id, 'title' => __('purchase.add_payment')])
			@if($is_enabled_download_pdf)
				<div class="well row">
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label("prefer_payment_method" , __('lang_v1.prefer_payment_method') . ':') !!}
							@show_tooltip(__('lang_v1.this_will_be_shown_in_pdf'))
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fas fa-money-bill-alt"></i>
								</span>
								{!! Form::select("prefer_payment_method", $payment_types, 'cash', ['class' => 'form-control','style' => 'width:100%;']); !!}
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							{!! Form::label("prefer_payment_account" , __('lang_v1.prefer_payment_account') . ':') !!}
							@show_tooltip(__('lang_v1.this_will_be_shown_in_pdf'))
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fas fa-money-bill-alt"></i>
								</span>
								{!! Form::select("prefer_payment_account", $accounts, null, ['class' => 'form-control','style' => 'width:100%;']); !!}
							</div>
						</div>
					</div>
				</div>
			@endif
			@if(empty($status) || !in_array($status, ['quotation', 'draft']))
				<div class="payment_row" @if($is_enabled_download_pdf) id="payment_rows_div" @endif>
					<div class="row">
						<div class="col-md-12 mb-12">
							<strong>@lang('lang_v1.advance_balance'):</strong> <span id="advance_balance_text"></span>
							{!! Form::hidden('advance_balance', null, ['id' => 'advance_balance', 'data-error-msg' => __('lang_v1.required_advance_balance_not_available')]); !!}
						</div>
					</div>
					@include('sale_pos.partials.payment_row_form', ['row_index' => 0, 'show_date' => true, 'show_denomination' => true])
                </div>
                <div class="payment_row">
					<div class="row">
						<div class="col-md-12">
			        		<hr>
			        		<strong>
			        			@lang('lang_v1.change_return'):
			        		</strong>
			        		<br/>
			        		<span class="lead text-bold change_return_span">0</span>
			        		{!! Form::hidden("change_return", $change_return['amount'], ['class' => 'form-control change_return input_number', 'required', 'id' => "change_return"]); !!}
			        		<!-- <span class="lead text-bold total_quantity">0</span> -->
			        		@if(!empty($change_return['id']))
			            		<input type="hidden" name="change_return_id" 
			            		value="{{$change_return['id']}}">
			            	@endif
						</div>
					</div>
					<div class="row hide payment_row" id="change_return_payment_data">
						<div class="col-md-4">
							<div class="form-group">
								{!! Form::label("change_return_method" , __('lang_v1.change_return_payment_method') . ':*') !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fas fa-money-bill-alt"></i>
									</span>
									@php
										$_payment_method = empty($change_return['method']) && array_key_exists('cash', $payment_types) ? 'cash' : $change_return['method'];

										$_payment_types = $payment_types;
										if(isset($_payment_types['advance'])) {
											unset($_payment_types['advance']);
										}
									@endphp
									{!! Form::select("payment[change_return][method]", $_payment_types, $_payment_method, ['class' => 'form-control col-md-12 payment_types_dropdown', 'id' => 'change_return_method', 'style' => 'width:100%;']); !!}
								</div>
							</div>
						</div>
						@if(!empty($accounts))
						<div class="col-md-4">
							<div class="form-group">
								{!! Form::label("change_return_account" , __('lang_v1.change_return_payment_account') . ':') !!}
								<div class="input-group">
									<span class="input-group-addon">
										<i class="fas fa-money-bill-alt"></i>
									</span>
									{!! Form::select("payment[change_return][account_id]", $accounts, !empty($change_return['account_id']) ? $change_return['account_id'] : '' , ['class' => 'form-control select2', 'id' => 'change_return_account', 'style' => 'width:100%;']); !!}
								</div>
							</div>
						</div>
						@endif
						@include('sale_pos.partials.payment_type_details', ['payment_line' => $change_return, 'row_index' => 'change_return'])
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-12">
							<div class="pull-right"><strong>@lang('lang_v1.balance'):</strong> <span class="balance_due">0.00</span></div>
						</div>
					</div>
				</div>
			@endif
			@endcomponent
		@endcan
	@endif
	
	<div class="row">
		{!! Form::hidden('is_save_and_print', 0, ['id' => 'is_save_and_print']); !!}
		<div class="col-sm-12 text-center tw-mt-4">
			<button type="button" id="submit-sell" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white">@lang('messages.save')</button>
			<button type="button" id="save-and-print" class="tw-dw-btn tw-dw-btn-success tw-dw-btn-lg tw-text-white">@lang('lang_v1.save_and_print')</button>
		</div>
	</div>
	
	@if(empty($pos_settings['disable_recurring_invoice']))
		@include('sale_pos.partials.recurring_invoice_modal')
	@endif
	
	{!! Form::close() !!}
</section>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
@include('contact.create', ['quick_add' => true])
</div>
<!-- /.content -->
<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
	aria-labelledby="gridSystemModalLabel">
</div>

<!-- quick product modal -->
<div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

<div class="modal fade types_of_service_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@include('sale_pos.partials.configure_search_modal')

@stop

@section('css')
<style>
	.simple-sale-page {
		--sale-border: #d7dde5;
		--sale-text: #1f2937;
		--sale-muted: #6b7280;
		--sale-bg: #f7f8fa;
	}

	.simple-sale-page.content {
		padding-bottom: 48px;
	}

	.simple-sale-page .box.box-solid {
		border: 1px solid var(--sale-border);
		border-radius: 18px;
		box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
		background: #fff;
		margin-bottom: 24px;
	}

	.simple-sale-page .box-body {
		padding: 24px 24px 20px;
	}

	.simple-sale-page .row {
		margin-bottom: 12px;
	}

	.simple-sale-page .row:last-child {
		margin-bottom: 0;
	}

	.simple-sale-page [class*="col-"] {
		margin-bottom: 18px;
	}

	.simple-sale-page .form-group label,
	.simple-sale-page .simple-sale-label {
		display: block;
		margin-bottom: 10px;
		color: var(--sale-text);
		font-size: 15px;
		font-weight: 700;
	}

	.simple-sale-page .simple-sale-help {
		margin: 0 0 12px;
		color: var(--sale-muted);
		font-size: 13px;
		line-height: 1.5;
	}

	.simple-sale-page .simple-sale-subtitle {
		margin: 0 0 6px;
		color: var(--sale-text);
		font-size: 18px;
		font-weight: 700;
	}

	.simple-sale-page .form-control,
	.simple-sale-page .input-group-addon,
	.simple-sale-page .input-group-btn > .btn,
	.simple-sale-page .select2-container--default .select2-selection--single {
		min-height: 50px;
		border-color: var(--sale-border);
		box-shadow: none;
	}

	.simple-sale-page .form-control,
	.simple-sale-page .select2-container--default .select2-selection--single {
		font-size: 16px;
		color: var(--sale-text);
		border-radius: 12px;
		padding-top: 10px;
		padding-bottom: 10px;
	}

	.simple-sale-page .input-group-addon,
	.simple-sale-page .input-group-btn > .btn {
		background: #fff;
		color: var(--sale-muted);
	}

	.simple-sale-page .select2-container {
		width: 100% !important;
	}

	.simple-sale-page .select2-container .select2-selection--single .select2-selection__rendered {
		line-height: 48px;
		padding-left: 16px;
		padding-right: 36px;
		font-size: 16px;
		color: var(--sale-text);
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.simple-sale-page .select2-container .select2-selection--single .select2-selection__arrow {
		height: 48px;
		right: 10px;
	}

	.simple-sale-page .simple-sale-multi-input {
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
	}

	.simple-sale-page .simple-sale-pay-term-number {
		flex: 1 1 120px;
		width: auto !important;
	}

	.simple-sale-page .simple-sale-pay-term-type {
		flex: 1 1 180px;
		width: auto !important;
	}

	.simple-sale-page .simple-sale-readonly-field {
		display: flex;
		align-items: center;
		background: #f9fafb;
		color: var(--sale-text);
		font-weight: 600;
		cursor: default;
	}

	.simple-sale-page .simple-sale-details,
	.simple-sale-page .simple-sale-advanced-box {
		margin-top: 18px;
		padding-top: 14px;
		border-top: 1px solid #eef2f7;
	}

	.simple-sale-page .simple-sale-details summary,
	.simple-sale-page .simple-sale-advanced-box summary {
		cursor: pointer;
		font-weight: 700;
		color: var(--sale-text);
		list-style: none;
	}

	.simple-sale-page .simple-sale-details summary::-webkit-details-marker,
	.simple-sale-page .simple-sale-advanced-box summary::-webkit-details-marker {
		display: none;
	}

	.simple-sale-page .simple-sale-details summary::before,
	.simple-sale-page .simple-sale-advanced-box summary::before {
		content: "+";
		display: inline-flex;
		width: 20px;
		height: 20px;
		align-items: center;
		justify-content: center;
		margin-right: 8px;
		border: 1px solid var(--sale-border);
		border-radius: 999px;
		color: var(--sale-muted);
		font-size: 13px;
	}

	.simple-sale-page .simple-sale-details[open] summary::before,
	.simple-sale-page .simple-sale-advanced-box[open] summary::before {
		content: "-";
	}

	.simple-sale-page .simple-sale-addresses {
		margin-top: 16px;
		color: var(--sale-text);
		font-size: 14px;
		line-height: 1.8;
	}

	.simple-sale-page #upload_document {
		display: block;
		width: 100%;
		padding: 12px;
		border: 1px dashed var(--sale-border);
		border-radius: 12px;
		background: var(--sale-bg);
	}

	.simple-sale-page .simple-sale-product-browser {
		margin-top: 6px;
		margin-bottom: 24px;
		padding: 22px;
		border: 1px solid #eef2f7;
		border-radius: 16px;
		background: #fbfcfd;
	}

	.simple-sale-page .simple-sale-product-browser-header {
		margin-bottom: 18px;
	}

	.simple-sale-page .simple-sale-product-grid-wrap {
		max-height: 420px;
		overflow-y: auto;
		padding: 6px 6px 0 0;
	}

	.simple-sale-page .pos_product_div {
		margin-top: 8px;
	}

	.simple-sale-page #pos_table {
		margin-top: 10px;
	}

	.simple-sale-page #pos_table th,
	.simple-sale-page #pos_table td {
		padding: 14px 10px;
		vertical-align: middle;
	}

	.simple-sale-page .table-responsive + .table-responsive {
		margin-top: 16px;
	}

	.simple-sale-page #additional_expenses_div {
		margin-top: 18px;
	}

	.simple-sale-page .well {
		padding: 18px;
		border-radius: 14px;
	}

	.simple-sale-page .tw-dw-btn-lg,
	.simple-sale-page .btn-lg {
		padding: 12px 22px;
	}

	@media (max-width: 767px) {
		.simple-sale-page .box-body {
			padding: 18px 16px 14px;
		}

		.simple-sale-page .simple-sale-multi-input {
			flex-direction: column;
		}

		.simple-sale-page [class*="col-"] {
			margin-bottom: 14px;
		}

		.simple-sale-page .simple-sale-product-browser {
			padding: 16px;
		}
	}
</style>
@endsection

@section('javascript')
	<script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
	<script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>

	<!-- Call restaurant module if defined -->
    @if(in_array('tables' ,$enabled_modules) || in_array('modifiers' ,$enabled_modules) || in_array('service_staff' ,$enabled_modules))
    	<script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script type="text/javascript">
    	$(document).ready( function() {
    		$('#status').change(function(){
    			if ($(this).val() == 'final') {
    				$('#payment_rows_div').removeClass('hide');
    			} else {
    				$('#payment_rows_div').addClass('hide');
    			}
    		});
    		$('.paid_on').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });
		    $(document).on('change', '#prefer_payment_method', function(e) {
			    var default_accounts = $('select#select_location_id').length ? 
			                $('select#select_location_id')
			                .find(':selected')
			                .data('default_payment_accounts') : $('#location_id').data('default_payment_accounts');
			    var payment_type = $(this).val();
			    if (payment_type) {
			        var default_account = default_accounts && default_accounts[payment_type]['account'] ? 
			            default_accounts[payment_type]['account'] : '';
			        var account_dropdown = $('select#prefer_payment_account');
			        if (account_dropdown.length && default_accounts) {
			            account_dropdown.val(default_account);
			            account_dropdown.change();
			        }
			    }
			});

		    function setPreferredPaymentMethodDropdown() {
			    var payment_settings = $('#location_id').data('default_payment_accounts');
			    payment_settings = payment_settings ? payment_settings : [];
			    enabled_payment_types = [];
			    for (var key in payment_settings) {
			        if (payment_settings[key] && payment_settings[key]['is_enabled']) {
			            enabled_payment_types.push(key);
			        }
			    }
			    if (enabled_payment_types.length) {
			        $("#prefer_payment_method > option").each(function() {
		                if (enabled_payment_types.indexOf($(this).val()) != -1) {
		                    $(this).removeClass('hide');
		                } else {
		                    $(this).addClass('hide');
		                }
			        });
			    }
			}
			
			setPreferredPaymentMethodDropdown();

			$('#is_export').on('change', function () {
	            if ($(this).is(':checked')) {
	                $('div.export_div').show();
	            } else {
	                $('div.export_div').hide();
	            }
	        });

			if($('.payment_types_dropdown').length){
				$('.payment_types_dropdown').change();
			}

			$(document).on('change', '#simple_sale_product_category', function() {
				var category_id = $(this).val();
				global_p_category_id = category_id === 'all' ? null : category_id;
				$('input#suggestion_page').val(1);
				get_product_suggestion_list(
					global_p_category_id,
					global_brand_id,
					$('input#location_id').val(),
					null
				);
			});

			$(document).on('change', '#simple_sale_product_brand', function() {
				var brand_id = $(this).val();
				global_brand_id = brand_id ? brand_id : null;
				$('input#suggestion_page').val(1);
				get_product_suggestion_list(
					global_p_category_id,
					global_brand_id,
					$('input#location_id').val(),
					null
				);
			});

    	});
    </script>
@endsection
