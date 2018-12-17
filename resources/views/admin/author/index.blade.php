@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;">新增攻略作者</legend>
    </fieldset>
    <form class="layui-form" action="/admin/author/create" method="post">
        {{csrf_field()}}
        <div class="layui-form-item">
            <label class="layui-form-label "  lay-allowclose="true" style="width: 90px;">作者昵称</label>
            <div class="layui-input-block" style='display:flex'>
                <input type="text"  id="name" name="author_name" required  lay-verify="required"
                       placeholder="请输入作者昵称" autocomplete="off" class="layui-input" style="width:150px;
                       display:inline-block">
            </div>
        </div>
        <div clas="layui-form-item"  >
            <label class="layui-form-label" style="width: 90px;">作者头像</label>
            <div class="layui-upload-drag" id="test10" style="margin-left: 20px;">
                <p>点击上传，或将文件拖拽到此处</p>
            </div>
            <input type="hidden" name='author_head_portrait' id='imgurl10' required lay-verify="required">
        </div>


        <div class="layui-form-item" style="width: 500px;">
            <label class="layui-form-label"></label>

            <div class="layui-input-block" id="hhh">
                @include('admin.layout.error')

            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit="" lay-filter="sub">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection



<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
