@extends("admin.layout.main")
@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <fieldset class="layui-elem-field layui-field-title"></fieldset>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">商品名称</label>
            <div class="layui-input-inline" style='width:700px;height:20px;'>
                <input type="text" name="pro_name"  placeholder="请输入商品名称" disabled value="{{$product_data['product_name']}}" autocomplete="off" class="layui-input" style="border-width:0px;">
            </div>
        </div>
        <div class="layui-form-item" style="float:right; margin-top:-50px; margin-right:50px;">
            <label class="layui-form-inline">缩略图</label>
            <div style="margin-left:110px;">
                <img width='80px' height='80px' src="{{$product_data['product_image']}}">
            </div>
        </div>
        <div class="layui-form-item" style="float: left;margin-top: -60px;">
            <label class="layui-form-label" style="width: inherit;">商品标签</label>
            <div class="layui-input-inline">
                <input type="text" class="layui-input box" value="{{$product_data['product_label']}}"  id="tab_input" name="pro_tab" disabled placeholder="商品标签" autocomplete="off" style="width:200px;display:inline-block;border:0px;" >
            </div>
        </div>
        @if($fb_attribute_value)
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">规格名称</label>
                <div class="layui-input-inline">
                    <input type="text" class="layui-input box" value="{{$fb_attribute_value->attribute_value}}"  id="speci_input" name="speci_name" disabled placeholder="例如：颜色" autocomplete="off" style="width:100px;display:inline-block;border:0px;" >
                </div>
            </div>
        @endif
        <form class="layui-form">
            <div class="layui-form" style="display: -webkit-inline-box;margin-top: 0px;">
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                    <legend>智能搜索</legend>
                </fieldset>
                <label class="layui-form-label" style="width:inherit;margin-left:50px;">关键字:</label>
                <div class="layui-input-inline">
                    @foreach($product_data['product_keyword'] as $k=>$v)
                        <input type="text" class="layui-input box search_input search_a"  required lay-verify="required"
                               name="search[]" value="{{$v}}" disabled placeholder="请填入关键字" autocomplete="off"
                               style="width:100px;display:inline-block;border:0px;" >
                    @endforeach
                </div>

            </div>
            <a class="layui-btn" type='button' lay-submit lay-filter="saveWord" style="float: right;
                margin-right: 50px;">确定</a>
            <a class="layui-btn" type='button' id="saveWords" style="float: right;
                margin-right: 50px;">修改</a>

            <input type="hidden" class="layui-input box" value="{{$fb_attribute_value->attribute_value}}"  id="speci_input" name="fb_attribute_value" autocomplete="off" style="width:100px;display:inline-block;border:0px;" >
            <input type="hidden" class="layui-input box" value="{{$product_data['id']}}" name="product_id" disabled autocomplete="off" style="width:100px;display:inline-block;" >
            <input type="hidden" class="layui-input box" value="{{$product_data['brand_id']}}" name="brand_id" disabled autocomplete="off" style="width:100px;display:inline-block;" >
        </form>
        <form class="layui-form">
            <input type="hidden" class="layui-input box" value="{{$product_data['id']}}"  name="product_id" autocomplete="off" style="width:100px;display:inline-block;border:0px;" >
            <input type="hidden" class="layui-input box" value="{{$attribute_name_id}}"  name="attribute_name_id" autocomplete="off" style="width:100px;display:inline-block;border:0px;" >
            <input type="hidden" class="layui-input box" value="{{$attribute_value_id}}"  name="attribute_value_id" autocomplete="off" style="width:100px;display:inline-block;border:0px;" >
        <div id="good_one">
            @if(!empty($goods_data_one))
                @foreach($goods_data_one as $kk=>$vv)
                    @foreach($vv as $k=>$v)
                        <div class="layui-form-item" >
                            <div class="layui-input-inline" style="width: 20px;" name="sousuo">
                                <input type="checkbox" name="sd"  lay-filter="choose"
                                       website_name="{{$v['website_name']}}" original_goods_url="{{$v['good_url']}}"
                                       goods_name="{{$v['title']}}" market_price="{{$v['market_price']}}"
                                       original_goods_id="{{$v['good_id']}}" shop_price="{{$v['shop_price']}}"
                                       stock_number="{{$v['stock_number']}}" goods_status="{{$v['is_onsell']}}"
                                       orignal_website="{{$v['good_from']}}" />
                            </div>
                            <div class="layui-input-inline" style="width: 110px;">
                                <input type="text" placeholder="网站" name="website_name[]" class="layui-input website_name" value="{{$v['website_name']}}" style="width: 100px;margin-left: 12px;" />
                            </div>
                            <div class="layui-input-inline" style="width: 445px;margin-left: 10px ">
                                <a href="{{$v['good_url']}}" target="_blank" style="text-decoration:none" >
                                    <input type="text" placeholder="商品名称" name="good_name[]"  class="layui-input" value="{{$v['title']}}"/>
                                </a>
                            </div>
                            <div class="layui-input-inline" style="width: 50px;margin-left: 10px">
                                <input type="text" placeholder="价格" name="price_text[]" class="layui-input" value="{{$v['price_text']}}" />
                            </div>
                            <input type="hidden" class="layui-input box" value="{{$v['good_id']}}"  name="orignal_website_id" >
                            <input type="hidden" class="layui-input box" value="{{$v['market_price']}}"  name="market_price" >
                            <input type="hidden" class="layui-input box" value="{{$v['shop_price']}}"  name="shop_price" >
                            <input type="hidden" class="layui-input box" value="{{$v['stock_number']}}"  name="stock_number" >
                            <input type="hidden" class="layui-input box" value="{{$v['is_onsell']}}"  name="goods_status" >
                            <div class="layui-input-inline" style="display:inline-block">
                                <a num="1" name="hulue" style="display:inline-block;width: 60px;height: 38px;background:
                            #009688;
                            line-height: 38px;text-align: center;cursor: pointer;">∅忽略</a>
                            </div></div>
                    @endforeach
                @endforeach
            @else
                "暂无唯一商品"
            @endif
       </div>


            <div class="layui-form" style="margin-top:50px;">
                <fieldset class="layui-elem-field layui-field-title">
                    <legend>人工添加 </legend>
                </fieldset>
                {{csrf_field()}}
                <div class="layui-form-item" style="margin-top:-80px;margin-left: 20%">
                    <div class="layui-input-inline" style='width:100px;' id="website_a">
                        <select name="webname" lay-filter="website_name">
                            @foreach($website_data as $v)
                                <option value="{{$v['website_abbreviation']}}" class="website_abbreviation">{{$v['website_name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="layui-form-label" style="width:inherit;margin-left:50px;">商品id:</label>
                    <div class="layui-input-inline">
                        <input type="text" name="ma_id" required  lay-verify="" placeholder="输入商品id" autocomplete="off" class="layui-input" style="width: 120px;">
                    </div>
                    <a class="layui-btn" type='button' lay-submit lay-filter="formDemoId">导入</a>
                </div>
            </div>

            <div id="good_two">
            @if(!empty($goods_data_two))
                @foreach($goods_data_two as $kk=>$vv)
                    @foreach($vv as $k=>$v)
                        <div  class="layui-form-item" >
                            <div class="layui-input-inline" style="width: 20px;" name="sousuo">
                                <input type="checkbox" name="sd"  lay-filter="choose"
                                       website_name="{{$v['website_name']}}" original_goods_url="{{$v['good_url']}}"
                                       goods_name="{{$v['title']}}" market_price="{{$v['market_price']}}"
                                       original_goods_id="{{$v['good_id']}}" shop_price="{{$v['shop_price']}}"
                                       stock_number="{{$v['stock_number']}}" goods_status="{{$v['is_onsell']}}"
                                       orignal_website="{{$v['good_from']}}" />
                            </div>
                            <div class="layui-input-inline" style="width: 90px;margin-left: 12px;">
                                <input type="text" placeholder="网站" name="website_name[]" class="layui-input"
                                       value="{{$v['website_name']}}" />
                            </div>

                            <div class="layui-input-inline" style="width: 600px;margin-left: 10px ">
                                <a href="{{$v['good_url']}}" target="_blank" style="text-decoration:none" >
                                    <input type="text" placeholder="商品名称" name="good_name[]"  class="layui-input"
                                           value="{{$v['title']}}"
                                    />
                                </a>
                            </div>
                            <div class="layui-input-inline" style="width: 66px;margin-left: 10px">
                                <input type="text" placeholder="价格" name="price_text[]" class="layui-input"
                                       value="{{$v['price_text']}}" />
                            </div>
                            <input type="hidden" class="layui-input box" value="{{$v['good_id']}}"  name="orignal_website_id" >
                            <input type="hidden" class="layui-input box" value="{{$v['market_price']}}"  name="market_price" >
                            <input type="hidden" class="layui-input box" value="{{$v['shop_price']}}"  name="shop_price" >
                            <input type="hidden" class="layui-input box" value="{{$v['stock_number']}}"  name="stock_number" >
                            <input type="hidden" class="layui-input box" value="{{$v['is_onsell']}}"  name="goods_status" >

                            <div class="layui-input-inline">
                                <a num="1" name="hulue" style="display:inline-block;width: 60px;height: 38px;background:
                #009688;
                line-height: 38px;text-align: center;cursor: pointer;">∅忽略</a>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                "暂无模糊商品"
            @endif
            </div>

            <input type="hidden" id="goods_id" name="product_id" value="">
            {{csrf_field()}}
            @include('admin.layout.error')
            <div class="layui-form-item" style="margin-left: 385px;margin-top: 30px;">
                {{--<button class="layui-btn" type='button' lay-submit lay-filter="formDemoAdd">添加</button>--}}
                <button class="layui-btn" type='button'  id="formDemo">完成</button>
            </div>
            </div>

        </form>

    </section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/product/product.js"></script>
<script src="/js/common/common.js"></script>
