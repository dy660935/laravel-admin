@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>攻略展示</legend>
    </fieldset>
    <form class="layui-form">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:80px;height:40px;'>
                <span style="margin-left: 15px;">攻略名称</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
            <div class="layui-input-label" style='width:200px;height:40px;'>
                <input id="name" type="text" name="name"
                       value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" placeholder=""
                       autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">确定</button>
        <a href="/admin/strategy/import" class="layui-btn layui-btn-normal" style="float:right">导入攻略</a>
        <a href="/admin/strategy" class="layui-btn layui-btn-normal" style="float:right;margin-right: 15px;">添加新增</a>
    </form>
    <div style='margin-left:30px;'>
        <table class="layui-table" lay-skin="nob">
            <thead>
            <tr>
                <th style='width:5px;'>编号</th>
                <th style='width:50px;'>作者 <a href="/admin/author/list" style="cursor: pointer;color: #00f;margin-left: 5px;">管理</a></th>
                <th style='width:50px;'>标题</th>
                <th style='width:50px;'>点击量</th>
                <th style='width:50px;'>日点击量</th>
                <th style='width:50px;'>分类</th>
                <th style='width:50px;'>标签</th>
                <th style='width:50px;'>权重</th>
                <th style='width:50px;'>是否热门</th>
                <th style='width:50px;'>状态</th>
                <th style='width:50px;'>操作</th>
            </tr>
            </thead>
            <tbody id="data">
            @foreach($data as $k=>$v)
            <tr>
                <td>{{$v->id}}</td>
                <td>{{$v->author_name}}</td>
                <td>{{$v->strategy_title}}</td>
                <td>{{$v->strategy_clicks}}</td>
                <td>{{$v->strategy_daily_clicks}}</td>
                <td>{{$v->category_name}}</td>
                <td>{{$v->strategy_label}}</td>
                <td>{{$v->strategy_weight}}</td>
                <td>
                    @if($v->is_hot == 1)
                        是
                    @else
                        否
                    @endif
                </td>
                <td>
                    @if($v->strategy_status == 1)
                        上线
                    @else
                        已下线
                    @endif
                </td>
                <td>
                    <a href="/admin/strategy/detail/{{$v->id}}" style="font-color:"red" ">详情</a>
                    <a href="/admin/strategy/update/{{$v->id}}" style="font-color:"red" ">修改</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @if(!empty($search['name']))
            {{$data->appends(['name'=>"$search[name]"])->links()}}
        @else
            {{$data->links()}}
        @endif
        <div id='test1'></div>
    </div>


@endsection
