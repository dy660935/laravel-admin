@extends("admin.layout.main")
@section("content")
    <section class="content">
    {{--<fieldset class="layui-elem-field layui-field-title">--}}
        {{--<legend style="font-size:16px;" >修改品牌</legend>--}}
    {{--</fieldset>--}}
        <!-- 内容主体区域 -->
        <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
            <legend>品牌修改</legend>
        </fieldset>
        <form class="layui-form" role="form" method="post" action="/admin/brand/save">
                {{csrf_field()}}
                <input type="hidden" name="id" id="b_id" value="{{$brand['id']}}">
                <input type="hidden" name='returnUrl' value="{{$returnUrl}}" >
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:120px;">品牌名称</label>
                    <div class="layui-input-block">
                        <input type="text"  id="name" value="{{$brand->brand_chinese_name}}" name="brand_chinese_name" required  lay-verify="required" placeholder="请输入品牌名称" autocomplete="off" class="layui-input" style="width:150px;display:inline-block">
                        @if($brand->brand_english_name != "")
                            <input type="text"  value="{{$brand->brand_english_name}}" class="layui-input box layui-this" name="brand_english_name"   placeholder="请输入英文名称" autocomplete="off" style="width:200px;display:inline-block" >
                        @else
                            <a href="javascript:;" class="layui-btn layui-btn-sm " id="brand_name" style="display:inline-block;margin-left:20px;margin-top: 4px;">
                                <i class="layui-icon">&#xe654;</i>
                            </a>
                        @endif
                    </div>
                </div>
                <div clas="layui-form-item">
                    <label class="layui-form-label" style="width: 120px;">品牌图片</label>
                    <div class="layui-upload-drag" id="test">
                        <img width='80px' height='80px' src="{{$brand->orginal_brand_logo}}">
                    </div>
                    <input type="hidden" name='orginal_brand_logo' value="{{$brand->orginal_brand_logo}}" id='imgurl' required lay-verify="required">
                    <br>
                </div>
                <div class="layui-form-item" style="margin-top: 16px;margin-left: 20px;">
                    <label class="layui-form-label" style=" width:100px;">国家/地区</label>
                    <div class="layui-input-block" style='width:100px;'>
                        <select name="brand_country" lay-verify="required" lay-search>
                            @foreach($country as $countrys)
                                @if($brand->brand_country ==$countrys->id)
                                    <option selected value={{$countrys->id}}  >{{$countrys->country}}</option>
                                @else
                                    <option value={{$countrys->id}}>{{$countrys->country}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-left: 30px;">
                    <label class="layui-form-label" style="width: inherit;">权重</label>
                    <div class="layui-input-block">
                        <input type="number"  max="99999999" class="layui-input box" required value="@if(isset($brand->brand_weight)){{$brand->brand_weight}}@endif"  id="tab_input" name="brand_weight"  autocomplete="off" style="width:60px;display:inline-block;border:0px;" >
                    </div>
                </div>
                <div class="layui-form-item" style="margin-left: 20px;">
                    <label class="layui-form-label" style=" width:90px;">适合性别</label>
                    <div class="layui-input-block" style='width:150px;'>
                        <select name="brand_suitable_genter">
                            @if($brand->brand_suitable_genter==1)
                                <option value="1" selected>女</option>
                                <option value="2">男</option>
                                <option value="3">无差别</option>
                            @elseif($brand->brand_suitable_genter==2)
                                <option value="1">女</option>
                                <option value="2" selected>男</option>
                                <option value="3">无差别</option>
                            @else
                                <option value="1">女</option>
                                <option value="2" selected>男</option>
                                <option value="3">无差别</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-left: 20px;">
                    <label class="layui-form-label" style=" width:90px;">消费水平</label>
                    <div class="layui-input-block" style='width:150px;'>
                        <select name="brand_consumption_level" lay-verify="required">
                            @if($brand->brand_consumption_level==1)
                                <option value="1" selected>顶级/高级</option>
                                <option value="2">轻奢</option>
                                <option value="3">高端</option>
                                <option value="4">大众</option>
                                <option value="5">平价</option>
                                <option value="0">廉价</option>
                            @elseif($brand->brand_consumption_level==2)
                                <option value="1">顶级/高级</option>
                                <option value="2" selected>轻奢</option>
                                <option value="3">高端</option>
                                <option value="4">大众</option>
                                <option value="5">平价</option>
                                <option value="0">廉价</option>
                            @elseif($brand->brand_consumption_level==3)
                                <option value="1">顶级/高级</option>
                                <option value="2" >轻奢</option>
                                <option value="3" selected>高端</option>
                                <option value="4">大众</option>
                                <option value="5">平价</option>
                                <option value="0">廉价</option>
                            @elseif($brand->brand_consumption_level==4)
                                <option value="1">顶级/高级</option>
                                <option value="2" >轻奢</option>
                                <option value="3" >高端</option>
                                <option value="4" selected>大众</option>
                                <option value="5">平价</option>
                                <option value="0">廉价</option>
                            @elseif($brand->brand_consumption_level==5)
                                <option value="1">顶级/高级</option>
                                <option value="2" >轻奢</option>
                                <option value="3" >高端</option>
                                <option value="4" >大众</option>
                                <option value="5" selected>平价</option>
                                <option value="0">廉价</option>
                            @else
                                <option value="1">顶级/高级</option>
                                <option value="2" >轻奢</option>
                                <option value="3" >高端</option>
                                <option value="4">大众</option>
                                <option value="5">平价</option>
                                <option value="0" selected >廉价</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="layui-form-item" style="margin-left: 20px;">
                    <label class="layui-form-label" style=" width:90px;">是否热门</label>
                    <div class="layui-input-block" style='width:150px;'>
                        <select name="is_hot" lay-verify="required">
                            @if($brand->is_hot==1)
                                <option value="1" selected>热门</option>
                                <option value="2">普通</option>
                            @else
                                <option value="1">热门</option>
                                <option value="2" selected>普通</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div>
                    <div class="layui-form-item" style="margin-left: 20px;">
                        <label class="layui-form-label" style="width: 90px;">是否上架</label>
                        <div class="layui-input-block" style='width:150px;'>
                            <select name="brand_status" lay-verify="required">
                                @if($brand->brand_status==1)
                                    <option value="1" selected>上架</option>
                                    <option value="2">下架</option>
                                @else
                                    <option value="1">上架</option>
                                    <option value="2" selected>下架</option>
                                @endif
                            </select>
                        </div>
                    </div>
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
<script src="/js/brand/brand.js"></script>


