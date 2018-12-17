@extends("admin.layout.main")
@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <fieldset class="layui-elem-field layui-field-title">
            <legend>商品修改</legend>
        </fieldset>
        <form class="layui-form" role="form" action="/admin/products/{{$product->id}}/updatefinish" method="POST" >
            <input type="hidden" name='returnUrl' value="{{$returnUrl}}" >
            {{csrf_field()}}
            <div style="float: right;margin-top: -75px;margin-right: 39px;">
                <button type="submit" class="btn btn-primary">完成</button>
            </div>
        </form>
            <div class="layui-tab layui-tab-card">
                <ul class="layui-tab-title">
                    <li <?php if($tabName == 'base'):?>class="layui-this"<?php endif;?>>基本信息</li>
                    <li <?php if($tabName == 'match'):?>class="layui-this"<?php endif;?>>商品匹配</li>
                    <li <?php if($tabName == 'price'):?>class="layui-this"<?php endif;?>>价格信息</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item <?php if($tabName == 'base'):?>layui-show<?php endif;?>">
                        <form class="layui-form" role="form" action="/admin/products/{{$product->id}}/updateinfo" method="POST" >
                            <input type="hidden" name='id' value="{{$product->id}}" >
                            <input type="hidden" name='goods_id' value="{{$product->goods_id}}" >
                            <input type="hidden" name='returnUrl' value="{{$returnUrl}}" >
                            {{csrf_field()}}
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: inherit;">来源网站:</label>
                                <div class="layui-input-inline" style='width:100px;height:20px;'>
                                    <input type="text"  placeholder="请输入商品名称" value="{{$product->website_name}}" disabled autocomplete="off" class="layui-input" style="border-width:0px;">
                                </div>
                                <label class="layui-form-label" style="width: inherit;">品牌:</label>
                                <div class="layui-input-inline" style='width:100px;height:20px;'>
                                    <input type="text"  placeholder="请输入商品名称" value="{{$product->brand_name}}" disabled autocomplete="off" class="layui-input" style="border-width:0px;">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input" required value="@if(isset($product->price_updated_at)){{$product->price_updated_at}}更新 @else 暂无 @endif" disabled autocomplete="off" style="border-width:0px;">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: inherit;">商品分类:</label>
                                <div class="layui-input-inline">
                                    <select  id="level_one" lay-filter="level_one" name="category_id" lay-verify="required" lay-search autocomplete="off">
                                        @if(isset($product->one_category_id))
                                            @foreach($category_one as $categorys_one)
                                                @if($categorys_one->id==$product->one_category_id)
                                                    <option value="{{$categorys_one->id}}" selected >{{$categorys_one->category_name}}</option>
                                                @else
                                                    <option value="{{$categorys_one->id}}" >{{$categorys_one->category_name}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="">请选择</option>
                                            @foreach($category_one as $categorys_one)
                                                <option value="{{$categorys_one->id}}" >{{$categorys_one->category_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select  id="level_two" lay-filter="level_two" lay-search lay-verify="required" autocomplete="off">
                                        @if(isset($product->two_category_id) && $product->two_category_id != null)
                                            @foreach($category_two as $categorys_two)
                                                @if($categorys_two->id==$product->two_category_id)
                                                    <option value="{{$categorys_two->id}}" selected >{{$categorys_two->category_name}}</option>
                                                @else
                                                    <option value="{{$categorys_two->id}}" >{{$categorys_two->category_name}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="">请选择</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="layui-input-inline">
                                    <select name="three_category_id" id="level_three" lay-filter="" lay-search lay-verify="required" autocomplete="off">
                                        @if(isset($product->three_category_id) && $product->three_category_id != null)
                                            @foreach($category_three as $categorys_three)
                                                @if($categorys_three->id==$product->three_category_id)
                                                    <option value="{{$categorys_three->id}}" selected >{{$categorys_three->category_name}}</option>
                                                @else
                                                    <option value="{{$categorys_three->id}}" >{{$categorys_three->category_name}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="">请选择</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item" style="width: inherit;">
                                <label class="layui-form-label" style="width: inherit;">商品标签:</label>
                                <div class="layui-input-inline" style="width: 250px">
                                    @foreach($product_label as $product_labels)
                                        <input type="text" class="layui-input box" value="{{$product_labels}}" required  id="tab_input" name="product_label[]"  placeholder="商品标签" autocomplete="off" style="width: 80px; float: right">
                                    @endforeach
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: inherit;">商品名称:</label>
                                <div class="layui-input-inline" style="width: 50%">
                                    <input type="text" name="product_name" required  placeholder="请输入商品名称" value="{{$product->product_name}}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item" style="float: left;">
                                <label class="layui-form-label" style="width: inherit;">商品规格:</label>
                                <div class="layui-input-inline" style="width:500px;">
                                    @if($product->good_specs)
                                        @foreach($product->good_specs as $spec_v)
                                            <input type="text" class="layui-input box" value="{{$spec_v}}" required  id="tab_input" name="good_specs[]"  autocomplete="off" style="width:130px;display:inline-block;" >
                                        @endforeach
                                    @endif
                                </div>
                                <label class="layui-form-label" style="width: inherit;">专柜价:</label>
                                <div class="layui-input-inline">
                                    <input type="text" class="layui-input box" value="{{$product->market_price}}" required  id="tab_input" name="market_price"  autocomplete="off" style="width:60px;display:inline-block;" > 元
                                </div>
                            </div>
                            {{--<div class="layui-form-item">--}}
                                {{--<label class="layui-form-label" style="width: inherit;">商品属性</label>--}}
                                {{--<div class="layui-input-inline" style='width:700px;height:20px;'>--}}
                                    {{--<input type="text" name="product_name"  placeholder="请输入商品名称" value="{{$product->product_name}}" autocomplete="off" class="layui-input" style="border-width:0px;">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="layui-form-item" style="float:right; margin-top:-20%; margin-right:50px;">
                                <label class="layui-form-inline">缩略图:</label>
                                <div style="margin-left:110px;">
                                    <img width='80px' height='80px' src="{{$product->product_image}}">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: inherit;">商品评论数量:</label>
                                <div class="layui-input-inline" style='width:80px;height:20px;'>
                                    <input type="number" min="0" name="comment_count" required placeholder="商品评论数量" value="{{$product->comment_count}}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            {{--<div class="layui-form-item">--}}
                                {{--<label class="layui-form-label" style="width: inherit;">商品详细信息:</label>--}}
                                {{--<div class="layui-input-inline" style="width: 50%">--}}
                                    {{--<input type="text" name="product_name"  placeholder="数字" value="{{$product->product_name}}" autocomplete="off" class="layui-input" >--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: inherit;">用户点击量:</label>
                                <div class="layui-input-inline" style='width:80px;height:20px;'>
                                    <input type="number" min="0" required name="click_number"  placeholder="数字" value="{{$product->click_number}}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label" style="width: inherit;">权重:</label>
                                <div class="layui-input-inline" style='width:80px;height:20px;'>
                                    <input type="number" min="0" required name="product_weight"  placeholder="数字" value="{{$product->product_weight}}" autocomplete="off" class="layui-input">
                                </div>
                            </div>

                            <div class="layui-form-item" >
                                <div class="layui-input-block" id='aaa'>
                                    @if ($product->product_status == 1)
                                        <input type="radio" name="product_status" value='1' checked title="上架">
                                        <input type="radio" name="product_status" value="2" title="下架" >
                                    @else
                                        <input type="radio" name="product_status" value='1' title="上架">
                                        <input type="radio" name="product_status" value="2" checked title="下架" >
                                    @endif
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-input-block">
                                    <button type="submit" class="btn btn-primary">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="layui-tab-item<?php if($tabName == 'match'):?> layui-show<?php endif;?>" >
                        @include("admin.product.product_match")
                    </div>
                    <div class="layui-tab-item<?php if($tabName == 'price'):?> layui-show<?php endif;?>" >
                        @include("admin.product.price")
                    </div>
                </div>
            </div>
        @include('admin.layout.error')
    </section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/product/product.js"></script>
<script src="/js/common/common.js"></script>
<script>
    layui.use('element', function(){
        var $ = layui.jquery
            ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        // $('.site-demo-active').on('click', function(){
        //     var othis = $(this), type = othis.data('type');
        //     active[type] ? active[type].call(this, othis) : '';
        // });

        //Hash地址的定位
        // var layid = location.hash.replace(/^#test=/, '');
        // element.tabChange('test', layid);
        //
        // element.on('tab(test)', function(elem){
        //     location.hash = 'test='+ $(this).attr('lay-id');
        // });

    });
</script>

