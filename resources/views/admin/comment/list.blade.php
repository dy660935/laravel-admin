@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>已审核/屏蔽评论</legend>
    </fieldset>
    <form class="layui-form" action="/admin/comment/list/@if($param==1) 1 @else 2 @endif" method="get">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:80px;height:40px;'>
                <span style="margin-left: 5px;">评论对象</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>

                <input id="name" type="text" name="name" value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" placeholder="请输入昵称" autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">确定</button>
        @if($param == 1)
            <a href="/admin/comment/list/{{2}}" class="layui-btn layui-btn-normal" style="float:right">返回</a>
        @else
            <a href="/admin/comment/list/{{1}}" class="layui-btn layui-btn-normal" style="float:right">其他评论</a>
        @endif
    </form>
    <div style='margin-left:30px;'>
        <table class="layui-table" lay-skin="nob" style="">

            <thead>
            <tr>
                <th style='width:10px;'>序号</th>
                <th style='width:10px;'>评论时间</th>
                <th style='width:60px;'>昵称</th>
                <th style='width:10px;'>评论对象（文章/商品）</th>
                <th style='width:90px;'>评论内容</th>
                <th style='width:50px;'>操作</th>
                <th style='width:50px;'>评论点赞数</th>
                <th style='width:50px;'>状态</th>
            </tr>
            </thead>
            <tbody id="v">
            @foreach($userInfo as $v)
                <tr>
                    <td class="id">{{$v->id}}</td>
                    <td>{{$v->created_at}}</td>
                    <td>{{$v->frontDeskUser->user_define_nickname}}</td>
                    <td>{{$v->comment_title}}</td>
                    <td>{{$v->comment_describle}}</td>
                    <td>
                        @if($v->comment_status == 1)

                            <button type="button" name="shield" class="layui-btn layui-btn-radius layui-btn-sm
                            layui-btn-danger" num="{{$v->id}}" par="{{$param}}">屏蔽</button>

                        @elseif($v->comment_status ==2)

                            <button type="button" name="shield" class="layui-btn layui-btn-radius layui-btn-sm
                            layui-btn-danger" num="{{$v->id}}" par="{{$param}}">屏蔽</button>

                            <button type="button" name="pass" class="layui-btn layui-btn-radius layui-btn-sm
                            layui-btn-warm" num="{{$v->id}}" par="{{$param}}">审核通过</button>

                        @elseif($v->comment_status ==3)

                            <button type="button" name="recovery" class="layui-btn layui-btn-radius layui-btn-sm
                            layui-btn-normal" num="{{$v->id}}" par="{{$param}}">恢复</button>

                        @endif
                    </td>
                    <td>{{$v->click_thums_number}}</td>
                    <td>
                        @if($v->comment_status == 1)
                            正常
                        @elseif($v->comment_status ==2)
                            待处理
                        @elseif($v->comment_status ==3)
                            已屏蔽
                        @endif
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
<script src="/js/comment/comment.js"></script>
