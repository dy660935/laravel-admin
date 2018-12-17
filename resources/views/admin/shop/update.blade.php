@extends("admin.layout.main")
@section("content")
    <section class="content">
    {{--<fieldset class="layui-elem-field layui-field-title">--}}
        {{--<legend style="font-size:16px;" >修改店铺</legend>--}}
    {{--</fieldset>--}}
        <!-- 内容主体区域 -->
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>店铺修改</legend>
        </fieldset>
        <form class="layui-form" role="form" method="post" action="/admin/shop/updatedo">
                {{csrf_field()}}
                <input type="hidden" name="id" id="b_id" value="{{$shopInfo->id}}">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:120px;">店铺名称</label>
                    <div class="layui-input-block">
                        <input type="text"  id="name" value="{{$shopInfo->shop_name}}" name="shop_name" required  lay-verify="required" placeholder="请输入店铺名称" autocomplete="off" class="layui-input" style="width:350px;display:inline-block">
                    </div>
                </div>
                <div clas="layui-form-item">
                    <label class="layui-form-label" style="width: 120px;">店铺图片</label>
                    <div class="layui-upload-drag" id="test">
                        <img width='80px' height='80px' src="{{$shopInfo->shop_thumbnail}}">
                    </div>
                    <input type="hidden" name='shop_thumbnail' value="{{$shopInfo->shop_thumbnail}}" id='imgurl' required lay-verify="required">
                    <br>
                </div>
                <div>

                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit="" lay-filter="sub">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
                @include('admin.layout.error')
            </form>
    </section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>


