@extends("admin.layout.main")
@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <!-- /.box-header -->
        <fieldset class="layui-elem-field layui-field-title">
            <legend style="font-size:16px;">增加网站</legend>
        </fieldset>
            <!-- form start -->
            <form role="form" action="/admin/websites" method="POST" class="layui-form">
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站名称</label>
                    <div class="layui-input-block" style='width:200px;height:40px;'>
                        <input type="text" name="website_name" required  lay-verify="required" placeholder="请输入网站名称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站简称</label>
                    <div class="layui-input-block" style='width:200px;height:40px;'>
                        <input type="text" name="website_abbreviation" required  lay-verify="required" placeholder="请输入网站简称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站主页</label>
                    <div class="layui-input-block" style='width:200px;'>
                        <input type="text" name="website_url" required  lay-verify="required" placeholder="请输入主页URL" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div clas="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">缩略图</label>
                    <div class="layui-upload-drag" id="test">
                        <i class="layui-icon"></i>
                        <p>点击上传，或将文件拖拽到此处</p>
                    </div>
                    <input type="hidden" name='website_thumbnail' id='imgurl' required lay-verify="required">
                    <br>
                </div>
                <div class="layui-form-item" style="display:inline-block;margin-top: 20px;">
                    <label class="layui-form-label" style="width: inherit;">国家/地区</label>
                    <div class="layui-input-block">
                        <select name="website_country">
                            <option value="">请选择</option>
                            @foreach($country as $countrys)
                                <option value={{$countrys->id}}>{{$countrys->country}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{--<div class="layui-form-item" style="display:inline-block">--}}
                    {{--<label class="layui-form-label">城市</label>--}}
                    {{--<div class="layui-input-block">--}}
                        {{--<select name="website_city">--}}
                            {{--<option value="">请选择</option>--}}
                            {{--@foreach($city as $citys)--}}
                                {{--<option value={{$citys->id}}>{{$citys->country_status}}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="layui-form-item" style="display:inline-block">
                    <label class="layui-form-label" style="width: inherit">货币单位</label>
                    <div class="layui-input-block">
                        <select name="website_currency" >
                            @foreach($currency as $currencys)
                                <option value={{$currencys->id}}>{{$currencys->currency_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">购买方式</label>
                    <div class="layui-input-block" style='width:150px;'>
                        <select name="pay_way" >
                            <option value="1">国内直邮</option>
                            <option value="2">海外直邮</option>
                            <option value="3">海淘直邮</option>
                            <option value="4">海淘转运</option>
                            <option value="5">免税店</option>
                            <option value="0">其他</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="btn btn-primary">提交</button>
                        <button type="reset" class="btn btn-primary">重置</button>
                    </div>
                </div>
            </form>

        @include('admin.layout.error')
    <section>
@endsection

<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>