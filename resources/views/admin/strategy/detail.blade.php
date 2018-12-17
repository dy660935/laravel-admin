@extends("admin.layout.main")
@section("content")
<div class="layui-form">
    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;" >攻略详情</legend>
    </fieldset>
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 90px;">攻略标题</label>
        <div class="layui-input-block" style='width:500px;height:40px;'>
            <input type="text" name="title"  value="{{$data['strategy_title']}}" disabled autocomplete="off" class="layui-input" style="border-width: 0px;">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">作者</label>
        <div class="layui-input-block" style='width:200px;'>
            <input type="text" name="name"  value="{{$data['author_name']}}" disabled  autocomplete="off"
                   class="layui-input" style="border-width: 0px;">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">分类</label>
        <div class="layui-input-block" style='width:200px;'>
            <input type="text" name="name"  value="{{$data['category_name']}}" disabled  autocomplete="off"
                   class="layui-input" style="border-width: 0px;">
        </div>
    </div>
    <div class="layui-form-item" >
        <label class="layui-form-label">标签</label>
        <div class="layui-input-block" style='width:200px; display:flex' >
            @foreach($data['strategy_label'] as $k=>$v)
                <input type="text" class="layui-input box" id='input_strategy_{{$k}}' value="{{$v}}" name="strategy_label[]" required
                       lay-verify="required" placeholder="标签" autocomplete="off"
                       style="width:100px;display:inline-block">
            @endforeach
            <a href="javascript:;" class="layui-btn layui-btn-sm " id="strategy_category"
               style="display:inline-block;margin-left:30px;margin-top:3px;">
                <i class="layui-icon">&#xe654;</i>
            </a>
        </div>
    </div>
    <div clas="layui-form-item">
        <label class="layui-form-label" style='width:100px;height: 100px;'>方形小图</label>
        <div class="layui-input-block" id="test9">
            <img width='80px' height='80px' src="<?= $data['strategy_image'] ?>">
        </div>
        {{--<input type="hidden" name='img' id='imgurl' >--}}
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style='width:90px;'>攻略摘要</label>
        <div class="layui-input-block" style='width:500px;height:40px;'>
            <input type="text" name="title"  value="{{$data['strategy_abstract']}}" disabled autocomplete="off" class="layui-input" style="">
        </div>
    </div>
    <div clas="layui-form-item">
        <label class="layui-form-label" style='width:100px;height: 100px;'>封面大图</label>
        <div class="layui-upload-drag" id="test10">
            <img width='80px' height='80px' src="{{$data['strategy_slider_image']}}">
        </div>
        {{--<input type="hidden" name='strategy_slider_image' id='imgurl10' required lay-verify="required">--}}
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style='width:90px;'>攻略权重</label>
        <div class="layui-input-block" style='width:500px;height:40px;'>
            <input type="text" name="title"  value="{{$data['strategy_weight']}}" disabled autocomplete="off"
                   class="layui-input" style="border-width: 0px;">
        </div>
    </div>
    <div class="layui-form-item">

        <label class="layui-form-label" style="width: 90px;">是否热门</label>

        <div class="layui-input-inline">
            @if ($data['is_hot'] == 1)
                <input type="checkbox" name="is_hot" checked lay-skin="switch" lay-text="ON|OFF" value="1">
            @else
                <input type="checkbox" name="is_hot" lay-skin="switch" lay-text="ON|OFF">
            @endif
        </div>

    </div>
    <div class="layui-form-item">

        <label class="layui-form-label" style="width: 90px;">状态</label>

        <div class="layui-input-inline">
            @if ($data['strategy_status'] == 1)
                <input type="checkbox" name="strategy_status" checked lay-skin="switch"
                       lay-text="上线|下线"
                       value="1">
            @else
                <input type="checkbox" name="strategy_status" lay-skin="switch"
                       lay-text="上线|下线" value="1">
            @endif
        </div>

    </div>
    <div class="layui-form-item" style="width:800px; margin-left: 50px;">
        <fieldset class="layui-elem-field layui-field-title">
            <legend style="font-size:16px;" ></legend>
        </fieldset>
        <textarea id='LAY_demo1' name='brand_info' class='layui-textarea'  lay-verify='content' style="width:30%;border-width: 0px;">
        	  <?php echo $data['strategy_describe'] ?>
        </textarea >
    </div>


        <div class="layui-form-item">
        <div class="layui-input-block">
            <a href="/admin/strategy/update/{{$data['id']}}" class="layui-btn layui-btn-radius" style="font-color:"red" ">进入修改模式</a>
        </div>
    </div>
</div>

@endsection

<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
