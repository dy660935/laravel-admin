<form class="layui-form" role="form" action="/admin/products/{{$product->id}}/updateprice" method="POST">
    {{csrf_field()}}
    <input type="hidden" name='goods_id' value="{{$product->goods_id}}">

    <div class="layui-form-item">
        <label class="layui-form-label" style="width: inherit;">页面价格:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->market_price)){{$product->market_price}}@else 暂无 @endif" id="tab_input"
                   name="market_price" autocomplete="off" style="width:60px;display:inline-block;"> 元
        </div>
        <label class="layui-form-label" style="width: inherit;">更新周期:</label>
        <div class="layui-input-inline">
            <select id="level_one" lay-filter="level_one" name="update_type" lay-search>
                <option value="" disabled>请选择</option>
                @if($product->update_type == 1)
                    <option value="1" selected>每周</option>
                    <option value="2">每天</option>
                @else
                    <option value="1">每周</option>
                    <option value="2" selected>每天</option>
                @endif
            </select>
        </div>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->price_updated_at)){{$product->price_updated_at}}更新 @else 暂无 @endif"
                   disabled autocomplete="off" style="border:0px;">
        </div>
    </div>
    <div class="layui-form-item" style="float: left;">
        <label class="layui-form-label" style="width: inherit;">发货国家:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->country)){{$product->country}}@else  @endif"
                   name="country" autocomplete="off">
        </div>
        <label class="layui-form-label" style="width: inherit;">发货地:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->delivery_from) && $product->delivery_from != -1)
                   {{$product->delivery_from}}@else  @endif"
                   name="delivery_from" autocomplete="off">
        </div>
        <label class="layui-form-label" style="width: inherit;">发货仓库:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input"
                   value="@if(isset($product->tax_free_zone) && $product->tax_free_zone != -1){{$product->tax_free_zone}}@else  @endif"
                   name="tax_free_zone" autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item" style="float: left;">
        <label class="layui-form-label" style="width: inherit;">运费:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->postage_price)){{$product->postage_price}}@else 0 @endif"
                   name="postage_price" autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item" style="float: left;">
        <label class="layui-form-label" style="width: inherit;">税费:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->cross_border_tax)){{$product->cross_border_tax}}@else 0 @endif"
                   id="tab_input" name="cross_border_tax" autocomplete="off">
        </div>
    </div>
    @if(isset($product->promotion_info) && $product->promotion_info !=null )
        @foreach($product->promotion_info as $promotion_info_k=> $promotion_info)
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">优惠{{$promotion_info_k +1}}:</label>
                <div class="layui-input-inline" style="width: 50%">
                    <input type="text" class="layui-input" disabled required value="{{$promotion_info}}"
                           name="promotion_info[]" autocomplete="off" style="border-width:0px;">
                </div>
            </div>
        @endforeach
    @else
        <div class="layui-form-item" style="float: left;">
            <label class="layui-form-label" style="width: inherit;">优惠:</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input" disabled="不可编辑" required value="暂无" id="tab_input"
                       name="promotion_info" style="border-width:0px;">
            </div>
        </div>
    @endif
    <div class="layui-form-item" style="float: left;">
        <label class="layui-form-label" style="width: inherit;">最终价:</label>
        <div class="layui-input-inline">
            {{--<input type="text" class="layui-input" required value="@if(isset($product->shop_price) && isset($product->postage_price) && isset($product->cross_border_tax)){{$product->shop_price + $product->postage_price + $product->cross_border_tax}}@else 暂无 @endif"  id="tab_input" name="shop_price"  autocomplete="off" style="width:60px;display:inline-block;"> 元  --}}
            <input type="text" class="layui-input" required
                   value="@if(isset($product->shop_price)){{$product->shop_price}}@else 暂无 @endif" id="tab_input"
                   name="shop_price" autocomplete="off" style="width:60px;display:inline-block;"> 元
        </div>
    </div>
    <div class="layui-form-item" style="float: left;">
        <label class="layui-form-label" style="width: inherit;">商品库存:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required
                   value="@if(isset($product->stock_number)){{$product->stock_number}}@else 暂无 @endif" id="tab_input"
                   name="stock_number" autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item" style="float: left;">
        <label class="layui-form-label" style="width: inherit;">原始网上架状态:</label>
        <div class="layui-input-inline">
            <input type="text" class="layui-input" required value="@if(isset($product->is_onsell))上架 @else 下架 @endif"
                   id="tab_input" disabled autocomplete="off" style="border:0px;">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="btn btn-primary" >提交</button>
            </div>
        </div>
    </div>
</form>
