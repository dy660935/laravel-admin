@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;">修改攻略</legend>
    </fieldset>
    <form class="layui-form"  role="form" action="/admin/strategy/save/{{$strategy['id']}}" method="POST">
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$strategy['id']}}">
        <input type="hidden" name="returnUrl" value="{{$returnUrl}}">
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>攻略标题</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="text" name="strategy_title" required lay-verify="required" placeholder="请输入攻略标题"
                       autocomplete="off"
                       class="layui-input" value="{{$strategy['strategy_title']}}">
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
                        @if($v['id'] == $strategy['author_id'])
                            <option value="{{$v['id']}}" selected>{{$v['author_name']}}</option>
                        @else
                            <option value="{{$v['id']}}">{{$v['author_name']}}</option>
                        @endif
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
                        @if($v->id == $strategy['category_id'])
                            <option value="{{$v->id}}" selected>{{$v->category_name}}</option>
                        @else
                            <option value="{{$v->id}}">{{$v->category_name}}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style='width:100px;'>分类/标签</label>
            <div class="layui-input-block" style='width:200px; display:flex'>
                @foreach($strategy['strategy_label'] as $k=>$v)
                    <input type="text" class="layui-input box" id='input_strategy' value="{{$v}}"
                           name="strategy_label[]" required
                           lay-verify="required" placeholder="分类/标签" autocomplete="off"
                           style="width:100px;display:inline-block">
                @endforeach
                    <a href="javascript:;" class="layui-btn layui-btn-sm " id="strategy_category"
                       style="display:inline-block;margin-left:30px;margin-top:3px;">
                        <i class="layui-icon">&#xe654;</i>
                    </a>
            </div>
        </div>
        <div clas="layui-form-item" style="margin-bottom: 15px;">
            <label class="layui-form-label" style='width:100px;height: 100px;'>方形小图</label>
            <div class="layui-upload-drag" id="test9" style="margin-left: 11px;">
                <img width='80px' height='80px' src="{{$strategy['strategy_image']}}">
            </div>
            <input type="hidden" name='strategy_image' id='imgurl9'>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style='width:90px;'>攻略摘要</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="text" name="strategy_abstract"  value="{{$strategy['strategy_abstract']}}"  autocomplete="off" class="layui-input" >
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label" style='width:90px;'>攻略权重</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="number" name="strategy_weight"  value="{{$strategy['strategy_weight']}}"  autocomplete="off"
                       class="layui-input" required lay-verify="required" max="99999999" >
            </div>
        </div>
        <div clas="layui-form-item">
            <label class="layui-form-label" style='width:100px;height: 100px;'>封面大图</label>
            <div class="layui-upload-drag" id="test10" style="margin-left: 11px;">
                <img width='80px' height='80px' src="{{$strategy['strategy_slider_image']}}">
            </div>
            <input type="hidden" name='strategy_slider_image' id='imgurl10' required lay-verify="required">
        </div>
        @if($strategy['is_weChat_add'] == 2)
        <div class="layui-form-item">
            <label class="layui-form-label" >攻略微信url</label>
            <div class="layui-input-block" style='width:500px;height:40px;'>
                <input type="text" name="strategy_wechat_url"  value="{{$strategy['strategy_wechat_url']}}"
                       autocomplete="off"
                       class="layui-input" >
            </div>
        </div>
        @endif
        <div class="layui-form-item">

            <label class="layui-form-label" style="width: 100px;">是否热门</label>

            <div class="layui-input-inline" style="margin-left: 11px;">
                @if ($strategy['is_hot'] == 1)
                    <input type="checkbox" name="is_hot" checked lay-skin="switch" lay-text="ON|OFF" value="1">
                @else
                    <input type="checkbox" name="is_hot" lay-skin="switch" value="1" lay-text="ON|OFF">
                @endif
            </div>

        </div>
        <div class="layui-form-item">

            <label class="layui-form-label" style="width: 110px;text-align: center">状态</label>

            <div class="layui-input-inline">
                @if ($strategy['strategy_status'] == 1)
                    <input type="checkbox" name="strategy_status" checked lay-skin="switch"
                           lay-text="上线|下线"
                           value="1">
                @else
                    <input type="checkbox" name="strategy_status" lay-skin="switch"
                           lay-text="上线|下线" value="1">
                @endif
            </div>

        </div>
        @if($strategy['is_weChat_add'] == 1)
            <div class="layui-form-item">
                <label class="layui-form-label" style='width:100px;'>攻略内容</label>
                <div class="layui-form-item" style="margin-left:110px;width:800px;">
                    <textarea class="layui-textarea" id="LAY_demo1" style="display: none;" name="strategy_describe">{{$strategy['strategy_describe']}}</textarea>
                </div>
            </div>
        </div>
        @endif
        @include("layouts.error")
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn" type='submit'>立即提交</button>
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

