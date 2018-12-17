@extends("admin.layout.main")
@section("content")
    {{--<div class="layui-body">--}}
        <!-- 内容主体区域 -->
        <div>
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>修改攻略作者</legend>
            </fieldset>

            <form class="layui-form"  action="/admin/author/save/{{$author['id']}}" method="post">

                {{csrf_field()}}
                <input type="hidden" name="id" value="{{$author['id']}}">
                <input type="hidden" name="returnUrl" value="{{$returnUrl}}">
                <div class="layui-form-item">
                    <label class="layui-form-label "  lay-allowclose="true" style="width: 90px;">作者昵称</label>
                    <div class="layui-input-block" style='display:flex'>
                        <input type="text"  id="name" name="author_name" required value="{{$author->author_name}}"
                               lay-verify="required"
                               placeholder="请输入作者昵称" autocomplete="off" class="layui-input" style="width:150px;
                       display:inline-block">
                    </div>
                </div>
                <div clas="layui-form-item"  >
                    <label class="layui-form-label" style="width: 90px;">作者头像</label>
                    <div class="layui-upload-drag" id="test10" style="margin-left: 20px;">
                        <img src="{{$author->author_head_portrait}}" alt="" style="width: 120px;height: 120px;">
                    </div>
                    <input type="hidden" name='author_head_portrait' id='imgurl10' required lay-verify="required">
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: 100px;">是否启用</label>
                    <div class="layui-input-block">
                        @if ($author['author_status'] == 1)
                            <input type="checkbox" name="author_status" checked lay-skin="switch"
                                   lay-text="ON|OFF" value="1">
                        @else
                            <input type="checkbox" name="author_status" lay-skin="switch" lay-text="ON|OFF"
                                   value="1">
                        @endif
                    </div>
                </div>

                <div class="layui-form-item" style="width: 500px;">
                    <label class="layui-form-label"></label>

                    <div class="layui-input-block" id="hhh">
                        @include('admin.layout.error')

                    </div>
                </div>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" type="submit">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    {{--</div>--}}
@endsection

<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
