@extends("admin.layout.main")
@section("content")
    <section class="content">
        <fieldset class="layui-elem-field layui-field-title">
            <legend>商品列表</legend>
        </fieldset>
            <form class="layui-form" action="/admin/products" method="get">
            <div class="layui-form-item" style="display:inline-block;margin-left:10px;">
                <div class="layui-input-label" style='width:71px;'>
                    <span style="margin-left: 15px;">商品名称</span>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;margin-left:22px;">
                <div class="layui-input-label" style='width:200px;margin-left: -20px;'>
                    <input id="name" type="text" name="product_name" value="@if(!empty($search['product_name'])) {{$search['product_name']}} @else  @endif" placeholder="" autocomplete="off" class="layui-input">
                </div>
            </div>
            {{--<div class="layui-form-item" style="display:inline-block;">--}}
                {{--<div class="layui-input-label" style='width:50px;height:40px;'>--}}
                    {{--<span style="margin-left: 15px;">分类</span>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="layui-form-item" style="display:inline-block;">--}}
                {{--<div class="layui-input-label" style='width:200px;'>--}}
                    {{--<select name="category_name" id="" lay-verify="">--}}
                        {{--<option value="1" selected>上架</option>--}}
                        {{--<option value="2">下架</option>--}}
                    {{--</select>--}}
                {{--</div>--}}
            {{--</div>--}}
            <div class="layui-form-item" style="display:inline-block;">
                <div class="layui-input-label" style='width:50px;height:40px;'>
                    <span style="margin-left: 15px;">品牌</span>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;">
                <div class="layui-input-label" style='width:100px;height:40px;'>
                    <select name="brand_name" id="" lay-verify="" lay-search>
                        <option value="">请选择</option>
                        @foreach($brand_data as $b_k=>$b_v)
                            @if(!empty($search['brand_name']) && $search['brand_name'] == $b_v->id)
                                <option value="{{$b_v->id}}" selected >{{$b_v->brand_chinese_name}}</option>
                            @else
                                <option value="{{$b_v->id}}">{{$b_v->brand_chinese_name}}</option>
                            @endif
                         @endforeach
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;">
                <div class="layui-input-label" style='width:50px;height:40px;'>
                    <span style="margin-left: 15px;">状态</span>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;">
                <div class="layui-input-label" style='width:100px;height:40px;'>
                    <select name="status" id="" lay-verify="">
                        @if(!empty($search['status']) && $search['status'] == 1)
                            <option value="1" selected>上架</option>
                            <option value="2">下架</option>
                        @elseif(!empty($search['status']) && $search['status'] == 2)
                            <option value="1">上架</option>
                            <option value="2" selected>下架</option>
                        @else
                            <option value="1" selected>上架</option>
                            <option value="2">下架</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;">
                <div class="layui-input-label" style='width:100px;height:40px;'>
                    <span style="margin-left: 15px;">匹配状态</span>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;">
                <div class="layui-input-label" style='width:100px;'>
                    <select name="is_import" id="" lay-verify="">
                        <option value="2" <?php if($search['is_import'] == 2):?> selected <?php endif;?>>全部</option>
                        <option value="0" <?php if($search['is_import'] == 0):?> selected <?php endif;?>>已匹配</option>
                        <option value="1" <?php if($search['is_import'] == 1):?> selected <?php endif;?>>未匹配</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-left: 10px;">确定</button>
            <a href="/admin/products/create" class="layui-btn layui-btn-normal" style="float: right">新增</a>
            </form>
        <div style='margin-left:30px;'>
            <div class="layui-form-item" style="display:inline-block;margin-left:10px;">
                <div class="layui-input-label" style='width:300px;'>
                    <span style="margin-left: 15px;">共<?=$product->toArray()['total']?>条记录</span>
                </div>
            </div>
            <table class="layui-table" lay-skin="nob">
                <thead>
                <tr>
                    <th>编号</th>
                    <th>品牌</th>
                    <th>分类</th>
                    <th>商品名称</th>
                    <th>规格参数</th>
                    <th>价格</th>
                    <th>来源网站</th>
                    <th>封面图片</th>
                    <th>匹配状态</th>
                    <th>待处理</th>
                    {{--<th style="width: 80px;text-align: center; vertical-align: middle;">待处理价格</th>--}}
                    {{--<th style="text-align: center; vertical-align: middle;">原价</th>--}}
                    <th>点击量</th>
                    {{--<th style="text-align: center; vertical-align: middle;">备注</th>--}}
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($product as $pro_k=>$pro_v)
                    <tr >
                        <td style="vertical-align: middle;text-align: center;">{{$pro_v->id}}</td>
                        <td style="vertical-align: middle;text-align: center;">
                            @if(!empty($pro_v->brand->brand_chinese_name)) {{$pro_v->brand->brand_chinese_name}} @else 暂无  @endif
                        </td>
                        <td style="font-size:12px;vertical-align: middle;text-align: center;width: 120px;">
                            @if(!empty($pro_v->category->category_name)) {{$pro_v->category->category_name}} @else 暂无  @endif
                        </td>
                        <td style="width:160px;vertical-align: middle;text-align: center;">
                            {{$pro_v->product_name}}
                        </td>
                        <td style="vertical-align: middle;">
                            @if(!empty($pro_v->goods->good_specs)) {{$pro_v->goods->good_specs}} @else 暂无  @endif
                        </td>
                        <td style="vertical-align: middle;">
                            @if(!empty($pro_v->goods->shop_price)) {{$pro_v->goods->shop_price}} @else 暂无  @endif
                        </td>
                        <td style=" vertical-align: middle;">
                            @if(!empty($pro_v->website->website_name)) {{$pro_v->website->website_name}} @else 暂无  @endif
                        </td>

                        <td style="vertical-align: middle;text-align: center;">
                            <img src="{{$pro_v->product_image}}" style="width:80px;height:80px;">
                        </td>
                        <td style="width:50px; vertical-align: middle;text-align: center;">
                            @if(!empty($pro_v->goods->is_import))
                                @if ($pro_v->goods->is_import == 1)
                                    未匹配
                                @else
                                    已匹配
                                @endif
                            @else
                                已匹配
                            @endif


                        </td>

                        <td style="vertical-align: middle;">

                        </td>

                        <td style="vertical-align: middle;text-align: center;">
                            {{$pro_v->click_number}}
                        </td>
                        {{--<td style="vertical-align: middle;text-align: center;">--}}
                            {{--<input type="text" required lay-verify="required" num="{{$pro_v->id}}" id=""--}}
                                   {{--autocomplete="off"--}}
                                   {{--placeholder="备注"--}}
                                   {{--class="layui-input remarkWords" style="width: 60px;" value="{{$pro_v->brand_remark}}">--}}
                        {{--</td>--}}
                        <td style="vertical-align: middle;text-align: center;">
                            @if ($pro_v->product_status == 1)
                                正常
                            @else
                                下架
                            @endif
                        </td>
                        <td style="vertical-align: middle;text-align: center;">
                            <a href='/admin/products/update/{{$pro_v->id}}'>修改</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {!! $product->appends(['product_name'=>$product->products_name, 'brand_name'=>$product->brands_name,'status'=>$product->status])->render() !!}
        {{--{{$product->links()}}--}}
    </section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
<script src="/js/brand/brand.js"></script>


