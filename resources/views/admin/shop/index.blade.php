@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>店铺管理</legend>
    </fieldset>
    <form class="layui-form" action="/admin/shop" method="get">
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
        <button type="submit" class="btn btn-primary">确定</button>
        <a href="/admin/shop" class="layui-btn layui-btn-normal" style="float:right">新增</a>
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
                <th>店铺名称</th>
                <th>logo</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody id="v">
            @foreach($shopList as $v)
                <tr>
                    <td>{{$v->id}}</td>
                    <td>{{$v->shop_name}}</td>
                    <td><img src="{{$v->shop_thumbnail}}" style="width: 50px;"></td>
                    <td>
                        <a href='/admin/shop/update/{{$v->id}}'>修改</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if(!empty($search['name']))
            {{$shopList->appends(['name'=>"$search[name]"])
            ->links
            ()}}
        @else
            {{$shopList->links()}}
        @endif

        <div id='test1'></div>
    </div>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
