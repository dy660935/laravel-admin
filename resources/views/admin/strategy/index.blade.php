@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;">新增攻略</legend>
    </fieldset>
    <form class="layui-form" action="/admin/strategy/create" method="post">
        {{csrf_field()}}
        <div class="layui-form-item">
            <label class="layui-form-label" lay-allowclose="true" style='width:100px;'>攻略标题</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="text" name="strategy_title" required lay-verify="required" placeholder="请输入攻略标题"
                       autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>作者</label>
            <div class="layui-input-block" style='width:200px;'>
                {{--<input type="text" name="name" required lay-verify="required" placeholder="请输入作者名称" autocomplete="off"--}}
                {{--class="layui-input">--}}
                <select name="author_id" lay-verify="">
                    <option value="">请选择作者</option>
                    @foreach($author_information as $k=>$v)
                        <option value="{{$v['id']}}">{{$v['author_name']}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>分类</label>
            <div class="layui-input-block" style='width:200px;'>
                {{--<input type="text" name="name" required lay-verify="required" placeholder="请输入作者名称" autocomplete="off"--}}
                {{--class="layui-input">--}}
                <select name="category_id" lay-verify="">
                    <option value="">请选择分类</option>
                    @foreach($category_information as $k=>$v)
                        <option value="{{$v->id}}">{{$v->category_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>标签</label>
            <div class="layui-input-block" style='width:200px; display:flex'>
                <input type="text" class="layui-input box" id="input_strategy" name="strategy_label[]" required
                       lay-verify="required" placeholder="攻略标签" autocomplete="off"
                       style="width:100px;display:inline-block">
                <a href="javascript:;" class="layui-btn layui-btn-sm " id="strategy_category"
                   style="display:inline-block;margin-left:30px;margin-top:3px;">
                    <i class="layui-icon">&#xe654;</i>
                </a>
            </div>
        </div>

        <div clas="layui-form-item">
            <label class="layui-form-label" style='width:100px;height: 100px;'>方形小图</label>
            <div class="layui-upload-drag" id="test9" style="margin-left: 11px;">
                <p>点击上传，或将文件拖拽到此处</p>
            </div>
            <input type="hidden" name='strategy_image' id='imgurl9' required lay-verify="required">
        </div>
        <br>

        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>攻略摘要</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="text" name="strategy_abstract" required lay-verify="required" placeholder="请输入攻略摘要"
                       autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div clas="layui-form-item">
            <label class="layui-form-label" style='width:100px;height: 100px;'>封面大图</label>
            <div class="layui-upload-drag" id="test10" style="margin-left: 11px;">
                <p>点击上传，或将文件拖拽到此处</p>
            </div>
            <input type="hidden" name='strategy_slider_image' id='imgurl10' required lay-verify="required">
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>攻略权重</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="number" name="strategy_weight" required lay-verify="required" max="99999999"
                       placeholder="请输入攻略权重值"
                       autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>攻略内容</label>
            <div class="layui-form-item" style="margin-left:110px;width:800px;">
                <textarea class="layui-textarea" id="LAY_demo1" style="display: none;" name="strategy_describe">请输入正文</textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>是否热门</label>
            <div class="layui-input-block">
                <input type="checkbox" checked lay-skin="switch" lay-text="ON|OFF" name="is_hot" value="1">
            </div>
        </div>
        @include("layouts.error")
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" type='submit' lay-submit lay-filter="formDemo">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>
    </form>
@endsection
{{--js--}}


<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/strategy/strategy_index.js"></script>
<script src="/js/common/common.js"></script>
