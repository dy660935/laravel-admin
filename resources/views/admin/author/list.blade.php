@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>作者列表</legend>
    </fieldset>
    <form class="layui-form" action="/admin/author/list" method="get">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:70px;height:40px;'>
                <span style="margin-left: 5px;">作者昵称</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 10px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                <input id="name" type="text" name="name" value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">确定</button>

        <a href="/admin/author" class="layui-btn layui-btn-normal" style="float:right;margin-left: 15px; ">新增</a>
        <a href="/admin/strategy/list" class="layui-btn layui-btn-normal" style="float:right">返回</a>
    </form>
    <div style='margin-left:30px;'>
        <table class="layui-table" lay-skin="nob" style="">
            <colgroup>
                <col width="90">
                <col width="150">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th style='width:10px;'>序号</th>
                <th style='width:50px;'>作者昵称</th>
                <th style='width:50px;'>作者头像</th>
                <th style='width:120px;'>作者状态</th>
                <th style='width:120px;'>创建时间</th>
                <th style='width:50px;'>操作</th>
            </tr>
            </thead>
            <tbody id="v">
            @foreach($author as $v)
                <tr>
                    <td>{{$v->id}}</td>
                    <td>{{$v->author_name}}</td>
                    <td style="width: 150px;">
                        <img src="{{$v->author_head_portrait}}" style="width: 50px;height: 50px;" alt="">
                    </td>
                    <td>
                        @if ($v->author_status == 1)
                            启用中
                        @elseif($v->author_status == 2)
                            未启用
                        @endif
                    </td>
                    <td style='width:120px; width: 180px;'>
                       {{$v->created_at}}
                    </td>
                    <td>
                        <a href='/admin/author/update/{{$v->id}}'>修改</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {!! $author->appends(['name'=>$author->name])->render() !!}

        <div id='test1'></div>
    </div>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
