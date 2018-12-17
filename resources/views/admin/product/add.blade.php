@extends("admin.layout.main")
@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <fieldset class="layui-elem-field layui-field-title"></fieldset>

        <form class="layui-form">
            <p style="font-size:14px; margin-bottom: 10px;color: darkgrey;">目前支持京东,网易考拉导入</p>
            {{csrf_field()}}
            <div class="layui-form-item">
                <div class="layui-input-inline" style='width:100px;'>
                    <select name="website_name">
                        <option value="jd">京东</option>
                        <option value="kaola">考拉</option>
                    </select>
                </div>
                <label class="layui-form-label" style="width:inherit;margin-left:50px;">商品id:</label>
                <div class="layui-input-inline">
                    <input type="text" name="product_id" required  lay-verify="required" placeholder="输入商品id" autocomplete="off" class="layui-input" style="width: 120px;">
                </div>
                <div class="layui-input-inline" id="dato1">
                    <a class="layui-btn"  type='button' lay-submit lay-filter="formDemo">导入</a>
                </div>
            </div>
        </form>
        <fieldset class="layui-elem-field layui-field-title"></fieldset>
        <form class="layui-form">
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">商品名称:</label>
                <div class="layui-input-inline">
                    <input type="text" name="pro_name"   placeholder="请输入商品名称" autocomplete="off" class="layui-input" style="width: 600px">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">商品品牌:</label>
                <div class="layui-input-inline">
                    <input type="text" id="brand_name" name="brand_name"  disabled required lay-verify="required"  placeholder="请输入商品品牌" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">商品分类:</label>
                <div class="layui-input-inline">
                    <select  id="level_one" lay-filter="level_one" name="category_id">
                        <option value="">请选择</option>
                        @foreach($category as $categorys)
                            <option value="{{$categorys->id}}">{{$categorys->category_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select  id="level_two" lay-filter="level_two">
                        <option value="">请选择</option>
                    </select>
                </div>
                <div class="layui-input-inline">
                    <select name="category_name" id="level_three" lay-filter="">
                        <option value="">请选择</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">商品标签:</label>
                <div class="layui-input-inline" style='display:flex' >
                    <input type="text" class="layui-input box pro_tab"  name="pro_tab[]"  placeholder="商品标签" autocomplete="off" style="width:100px;display:inline-block;" >
                    <a href="javascript:;" class="layui-btn layui-btn-sm " id="tab_zhuijia" style="display:inline-block;margin-left:30px">
                        <i class="layui-icon">&#xe654;</i>
                    </a>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">关键字:</label>
                <div class="layui-input-inline" style='display:flex'>
                    <input type="text" class="layui-input box search_input" required lay-verify="required" name="search[]"   placeholder="请填入关键字" autocomplete="off" style="width:100px;display:inline-block;" >
                    <a href="javascript:;" class="layui-btn layui-btn-sm " id="search_zhuijia" style="display:inline-block;margin-left:30px">
                        <i class="layui-icon">&#xe654;</i>
                    </a>
                </div>
            </div>
            <fieldset class="layui-elem-field layui-field-title"></fieldset>
            <p style="font-size:14px; margin-bottom: 10px;color: red;display:inline-block;">*官方标价/原价一致的,视为同一商品;如有不同价的规格，点此增加-></p>
            <a href="javascript:;" class="layui-btn layui-btn-sm " id="guiGe"  style="display:inline-block;
            background-color:
            lightgrey;">
                <i class="layui-icon" >&#xe654;</i>
            </a>
            <div class="tab1">
                <table class="table" >
                    <tr>
                        <td rowspan="2">商品规格</td>
                        <td rowspan="2">
                            <input type="checkbox"  lay-ignore>
                        </td>
                        <td >规格名</td>
                        <td>参数类名</td>
                        <td>对应价格</td>
                    </tr>
                    <tr>
                        <td><input type="text" name="attribute_name"  placeholder="比如：颜色" class="layui-input"></td>
                        <td><input type="text"  name="attribute_value"   placeholder="比如：红色" class="layui-input"></td>
                        <td><input type="text" name="shop_price"   placeholder="价格"  class="layui-input"></td>
                    </tr>
                </table>
            </div>
            <fieldset class="layui-elem-field layui-field-title"></fieldset>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">缩略图</label>
                <div class="layui-upload-drag" id="test9">
                    <i class="layui-icon"></i>
                    <p id="tupain">点击上传，或将文件拖拽到此处</p>
                </div>
                <input type="hidden" name='img1' id='imgurl9'>
                <br>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label" style="width: inherit;">轮播顶图</label>
                <div class="layui-upload-drag" id="test10">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
                <input type="hidden" name='img2' id='imgurl10'>
                <br>
            </div>
            <label class="layui-form-label"style="width: inherit;">商品详情</label>
            <div class="layui-form-item" style="width:800px; margin-left:50px;">
              <textarea class="layui-textarea" id="LAY_demo1" style="display: none;">请输入正文</textarea>
            </div>
            <div class="layui-form-item" >
                <div class="layui-input-block" id='aaa'>
                    <input type="radio" name="product_status" value='1' title="上架">
                    <input type="radio" name="product_status" value="2" title="下架" >
                </div>
            </div>
            {{csrf_field()}}
            <input type="hidden" name="product_sn"/>
            <input type="hidden" name="original_product_id"/>
            <input type="hidden" name="currency_genre"/>
            <input type="hidden" name="market_price"/>
            <input type="hidden" name="brand_id"/>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type='button' lay-submit lay-filter="formDemo3">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/product/product.js"></script>
<script src="/js/common/common.js"></script>

