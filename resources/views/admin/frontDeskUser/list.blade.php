@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>用户展示</legend>
    </fieldset>
    <form class="layui-form" action="/admin/frontDeskUser/list" method="get">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:50px;height:40px;'>
                <span style="margin-left: 15px;">昵称</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>

                <input id="name" type="text" name="name" value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" placeholder="请输入昵称" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">确定</button>

    </form>
    <div style='margin-left:30px;'>
        <table class="layui-table" lay-skin="nob" style="">

            <thead>
            <tr>
                <th style='width:10px;'>序号</th>
                <th style='width:10px;'>注册时间</th>
                <th style='width:60px;'>昵称</th>
                <th style='width:10px;'>最近登录时间</th>
                <th style='width:90px;'>登录次数</th>
                <th style='width:50px;'>关注</th>
                <th style='width:50px;'>评论</th>
                <th style='width:50px;'>收藏</th>
                <th style='width:50px;'>分享</th>
                <th style='width:50px;'>是否添加群</th>
                {{--<th style='width:50px;'>备注</th>--}}
                <th style='width:50px;'>状态</th>
                <th style='width:50px;'>操作</th>
            </tr>
            </thead>
            <tbody id="v">
            @foreach($userInfo as $v)
                <tr>
                    <td>{{$v->id}}</td>
                    <td>{{$v->created_at}}</td>
                    <td>{{$v->user_define_nickname}}</td>
                    <td>{{$v->updated_at}}</td>
                    <td>{{$v->user_login_count}}</td>
                    <td>{{$v->following_count}}</td>
                    <td>{{$v->comment_count}}</td>
                    <td>{{$v->collection_count}}</td>
                    <td>{{$v->share_count}}</td>
                    <td>
                        @if($v->is_add_group == 1)
                            已添加
                        @elseif($v->is_add_group ==2)
                            未添加
                        @endif
                    </td>
                    {{--<td>--}}
                        {{--<input type="text" required lay-verify="required" autocomplete="off" placeholder="备注"--}}
                               {{--class="layui-input" style="width: 60px;">--}}
                    {{--</td>--}}
                    <td>
                        @if($v->user_status == 1)
                            正常
                        @elseif($v->user_status ==2)
                            屏蔽
                        @elseif($v->user_status ==3)
                            注销
                        @endif
                    </td>
                    <td>
                        <a href='/admin/frontDeskUser/update/{{$v->id}}'>修改</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(!empty($search['name']))
            {{$userInfo->appends(['name'=>"$search[name]"])->links()}}
        @else
            {{$userInfo->links()}}
        @endif
        <div id='test1'></div>
    </div>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
