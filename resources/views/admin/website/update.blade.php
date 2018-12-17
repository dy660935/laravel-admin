@extends("admin.layout.main")
@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <!-- /.box-header -->
            <fieldset class="layui-elem-field layui-field-title">
                <legend style="font-size:16px;">修改网站</legend>
            </fieldset>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" action="/admin/websites/{{$website->id}}/updatedo" method="POST" class="layui-form" >
                <input type="hidden" name='id' value="{{$website->id}}" >
                <input type="hidden" name='website_abbreviation' value="{{$website->website_abbreviation}}" >
                {{csrf_field()}}
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站名称</label>
                    <div class="layui-input-block" style='width:200px;height:40px;'>
                        <input type="text" name="website_name" value="{{$website->website_name}}" required  lay-verify="required" placeholder="请输入网站名称"  autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站简称</label>
                    <div class="layui-input-block" style='width:200px;height:40px;'>
                        <input type="text" name="website_abbreviation" value="{{$website->website_abbreviation}}" disabled required  lay-verify="required" placeholder="请输入网站简称" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站主页</label>
                    <div class="layui-input-block" style='width:200px;'>
                        <input type="text" name="website_url" value="{{$website->website_url}}"  required  lay-verify="required" placeholder="请输入主页URL" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div clas="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">缩略图</label>
                    <div class="layui-upload-drag" id="test">
                        <img width='80px' height='80px' src="{{$website->website_thumbnail}}">
                    </div>
                    <input type="hidden" name='website_thumbnail' value="{{$website->website_thumbnail}}" id='imgurl' required lay-verify="required">
                    <br>
                </div>
                <div class="layui-form-item" style="display:inline-block;margin-top: 20px;">
                    <label class="layui-form-label" style="width: inherit;">国家/地区</label>
                    <div class="layui-input-block">
                        <select name="website_country" lay-search>
                            @foreach($country as $countrys)
                                @if($website->website_country ==$countrys->id)
                                    <option selected value={{$countrys->id}}  >{{$countrys->country}}</option>
                                @else
                                    <option value={{$countrys->id}}>{{$countrys->country}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                {{--<div class="layui-form-item" style="display:inline-block">--}}
                    {{--<label class="layui-form-label">城市</label>--}}
                    {{--<div class="layui-input-block">--}}
                        {{--<select name="website_city" lay-search>--}}
                            {{--@foreach($city as $citys)--}}
                                {{--@if($website->website_city ==$citys->id)--}}
                                    {{--<option selected value={{$citys->id}} >{{$citys->country_status}}</option>--}}
                                {{--@else--}}
                                    {{--<option value={{$citys->id}}>{{$citys->country_status}}</option>--}}
                                {{--@endif--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="layui-form-item" style="display:inline-block">
                    <label class="layui-form-label" style="width: inherit">货币单位</label>
                    <div class="layui-input-block">
                        <select name="website_currency" lay-search>
                            @foreach($currency as $currencys)
                                @if($website->website_currency ==$currencys->id)
                                    <option selected value={{$currencys->id}} >{{$currencys->currency_name}}</option>
                                @else
                                    <option value={{$currencys->id}}>{{$currencys->currency_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">购买方式</label>
                    <div class="layui-input-block" style='width:150px;'>
                        <select name="pay_way">
                            @if($website->pay_way ==1)
                                <option value="1" selected >国内直邮</option>
                            @else
                                <option value="1">国内直邮</option>
                            @endif
                            @if($website->pay_way ==2)
                                <option value="2" selected >海外直邮</option>
                            @else
                                <option value="2">海外直邮</option>
                            @endif
                            @if($website->pay_way ==3)
                                <option value="3" selected >海淘直邮</option>
                            @else
                                <option value="3">海淘直邮</option>
                            @endif
                            @if($website->pay_way ==4)
                                <option value="4" selected >海淘转运</option>
                            @else
                                <option value="4">海淘转运</option>
                            @endif
                            @if($website->pay_way ==5)
                                <option value="5" selected >免税店</option>
                            @else
                                <option value="5">免税店</option>
                            @endif
                            @if($website->pay_way ==0)
                                <option value="0" selected >其他</option>
                            @else
                                <option value="0">其他</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">网站状态</label>
                    <div class="layui-input-block">
                        @if($website['website_status']==1)
                        <input type="radio" name="website_status" value="1"  checked="上架">上架
                        <input type="radio" name="website_status" value="2">下架
                        @else
                        <input type="radio" name="website_status" value="1">上架
                        <input type="radio" name="website_status" value="2"  checked="下架">下架
                        @endif
                    </div>
                </div><div class="layui-form-item">
                    <label class="layui-form-label" style="width: inherit;">更新频率</label>
                    <div class="layui-input-block">
                        @if($website['update_type']==1)
                            <input type="radio" name="update_type" value="1"  checked=>每周
                            <input type="radio" name="update_type" value="2">每天
                        @else
                            <input type="radio" name="update_type" value="1">每周
                            <input type="radio" name="update_type" value="2"  checked=>每天
                        @endif
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                </div>
            </form>
            @include('admin.layout.error')
    </section>
@endsection

<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
{{--<script src="/js/brand/brand.js"></script>--}}
<script src="/js/common/common.js"></script>