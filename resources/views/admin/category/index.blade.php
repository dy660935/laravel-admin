@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;">新增分类</legend>
    </fieldset>
    <form class="layui-form" action="/admin/category/create" method="get">
        {{csrf_field()}}
        <input type="hidden" name="category_level" value="1">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:120px;height:40px;'>
                <span style="margin-left: 15px;">请选择分类层级</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                <select name="parent" id="" lay-filter="type">
                    <option value="">请选择</option>
                    <option value="1">一级分类</option>
                    <option value="2">二级分类</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:120px;height:40px;'>
                <span style="margin-left: 15px;">请选择上级分类</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:200px;height:40px;'>
                <select name="parent_id" id="category" lay-filter="level">
                    <option value="0">请选择</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                <input id="name" type="text" name="category_name" placeholder="请输入分类名称" autocomplete="off"
                       class="layui-input" required lay-verify="required">
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
                <button class="layui-btn" lay-submit="" lay-filter="sub">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection



<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/category/category.js"></script>
