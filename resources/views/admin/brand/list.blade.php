@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>品牌展示</legend>
    </fieldset>
    <form class="layui-form" action="/admin/brand/list" method="get">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:50px;height:40px;'>
                <span style="margin-left: 15px;">名称</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                <input id="name" type="text" name="name"
                       value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" placeholder=""
                       autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:50px;height:40px;'>
                <span style="margin-left: 15px;">地区</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:200px;height:40px;'>
                <input type="text" name="country"
                       value="@if(!empty($search['country'])) {{$search['country']}} @else  @endif"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
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
        <button type="submit" class="btn btn-primary">确定</button>
        <a href="/admin/brand" class="layui-btn layui-btn-normal" style="float:right">新增</a>
    </form>
    <div style='margin-left:20px;'>
        <table class="layui-table" lay-skin="nob">
            <colgroup>
                <col width="90">
                <col width="150">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>序号</th>
                <th>品牌名称</th>
                <th>logo</th>
                <th>国家</th>
                <th>适合性别</th>
                <th>消费水平</th>
                <th>商品数量</th>
                <th>攻略数量</th>
                <th>备注</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="v">
            @foreach($data as $v)
                <tr>
                    <td>{{$v->id}}</td>
                    <td>{{$v->brand_chinese_name}}</td>
                    <td><img src="{{$v->orginal_brand_logo}}" style="width: 50px;"></td>
                    <td style="font-size:12px;">{{$v->country}}</td>
                    <td>
                        @if ($v->brand_suitable_genter == 1)
                            女
                        @elseif($v->brand_suitable_genter == 2)
                            男
                        @else
                            无差别
                        @endif
                    </td>
                    <td>
                        @if ($v->brand_consumption_level == 1)
                            顶级/高级
                        @elseif ($v->brand_consumption_level == 2)
                            轻奢
                        @elseif ($v->brand_consumption_level == 3)
                            高端
                        @elseif ($v->brand_consumption_level == 4)
                            大众
                        @elseif ($v->brand_consumption_level == 5) {
                        平价
                        @else
                            廉价
                        @endif
                    </td>
                    <td>{{$v->product_num}}</td>
                    <td>{{$v->strategy_num}}</td>
                    <td>
                        <input type="text" required lay-verify="required" num="{{$v->id}}" id=""
                               autocomplete="off"
                               placeholder="备注"
                               class="layui-input remarkWords" style="width: 60px;" value="{{$v->brand_remark}}">
                    </td>
                    <td>
                        @if ($v->brand_status == 1)
                            上架
                        @else
                            下架
                        @endif
                    </td>
                    <td>
                        <a href='/admin/brand/update/{{$v->id}}'>修改</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(!empty($search['name']) && !empty($search['country']) && !empty($search['status']))
            {{$data->appends(['name'=>"$search[name]" , 'country' => $search['country'] , 'status'=>$search['status']])
            ->links
            ()}}
        @elseif(!empty($search['name']) && !empty($search['status']))
            {{$data->appends(['name'=>"$search[name]" , 'status'=>$search['status']])
           ->links
           ()}}
        @elseif(!empty($search['country']) && !empty($search['status']))
            {{$data->appends(['country' => $search['country'] , 'status'=>$search['status']])
           ->links
           ()}}
        @elseif(!empty($search['status']) && empty($search['country']) && empty($search['name']))
            {{$data->appends(['status'=>$search['status']])
           ->links
           ()}}
        @else
            {{$data->links()}}
        @endif

        <div id='test1'></div>
    </div>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
<script src="/js/brand/brand.js"></script>
