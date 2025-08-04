@extends('layouts.app')
@section('title', __('Manual Sell'))

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Product Row</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>
                                    <input class="form-control product_name" required placeholder="Product Name" name="products[{{$row_index}}][name]" type="text" value="{{$product_name}}">
                                </td>
                                <td>
                                    <input class="form-control quantity" required min="1" step="any" name="products[{{$row_index}}][quantity]" type="number" value="{{$quantity}}">
                                </td>
                                <td>
                                    <select class="form-control unit" required name="products[{{$row_index}}][unit]">
                                        <option value="Pc(s)" @if($unit == 'Pc(s)') selected @endif>Pc(s)</option>
                                        <option value="Box" @if($unit == 'Box') selected @endif>Box</option>
                                        <option value="Kg" @if($unit == 'Kg') selected @endif>Kg</option>
                                        <option value="Ltr" @if($unit == 'Ltr') selected @endif>Ltr</option>
                                        <option value="Dozen" @if($unit == 'Dozen') selected @endif>Dozen</option>
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control price" required min="0" step="any" name="products[{{$row_index}}][price]" type="number" value="{{$price}}">
                                </td>
                                <td>
                                    <span class="subtotal">{{$quantity * $price}}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-xs remove_product_row"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
