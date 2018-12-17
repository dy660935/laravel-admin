@extends("admin.layout.main")
@section("content")

    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;" >新增品牌</legend>
    </fieldset>
    <form class="layui-form" action="/admin/brand/create" method="post">
        {{csrf_field()}}
        <div class="layui-form-item">
            <label class="layui-form-label "  lay-allowclose="true" style=" width:90px;">品牌名称</label>
            <div class="layui-input-block" style='display:flex'>
                <input type="text"  id="name" name="brand_chinese_name" required  lay-verify="required" placeholder="请输入品牌名称" autocomplete="off" class="layui-input" style="width:150px;display:inline-block">
                <a href="javascript:;" class="layui-btn layui-btn-sm " id="brand_name" style="display:inline-block;margin-left:20px;margin-top: 4px;">
                    <i class="layui-icon">&#xe654;</i>
                </a>
            </div>
        </div>
        {{--<div class="layui-form-item" >--}}
            {{--<label class="layui-form-label">分类/标签</label>--}}
            {{--<div class="layui-input-block" style='display:flex'>--}}
                {{--<input type="text" class="layui-input box" id="input_category" name="category[]" required  lay-verify="required" placeholder="分类/标签" autocomplete="off" style="width:150px;display:inline-block" >--}}
                {{--<a href="javascript:;" class="layui-btn layui-btn-sm " id="brand_category" style="display:inline-block;margin-left:20px;margin-top: 4px;">--}}
                    {{--<i class="layui-icon">&#xe654;</i>--}}
                {{--</a>--}}
            {{--</div>--}}
        {{--</div>--}}
        <div clas="layui-form-item"  >
            <label class="layui-form-label">缩略图</label>
            <div class="layui-upload-drag" id="test">
                <p>点击上传，或将文件拖拽到此处</p>
            </div>
            <p style="display: inline">* 图片尺寸最佳为120*120像素</p>
            <input type="hidden" name='orginal_brand_logo' id='imgurl' required lay-verify="required">

        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style=" width:90px;">品牌介绍</label>
            <textarea id='demo' name='brand_describe' class='layui-textarea' lay-verify='content' style="width:30%;margin-top:20px;">
        </textarea >
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style=" width:90px;">国家/地区</label>
            <div class="layui-input-block" style='width:100px;'>
                <select name="brand_country" lay-verify="required" lay-search>
                    @foreach ($country_data as $k => $v)
                    <option value="{{$v->id}}">{{$v->country}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style=" width:90px;">适合性别</label>
            <div class="layui-input-block" style='width:150px;'>
                <select name="brand_suitable_genter" lay-verify="required">
                    <option value="1">女</option>
                    <option value="2">男</option>
                    <option value="3">无差别</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style=" width:90px;">消费水平</label>
            <div class="layui-input-block" style='width:150px;'>
                <select name="brand_consumption_level" lay-verify="required">
                    <option value="1">顶级/高级</option>
                    <option value="2">轻奢</option>
                    <option value="3">高端</option>
                    <option value="4">大众</option>
                    <option value="5">平价</option>
                    <option value="0">廉价</option>
                </select>
            </div>
        </div>
        @include("layouts.error")
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" type='submit'>立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection



<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/brand/brand.js"></script>
<script src="/js/common/common.js"></script>
