@extends("admin.layout.main")
@section("content")
    {{--<fieldset class="layui-elem-field layui-field-title">--}}
        {{--<legend style="font-size:16px;" >修改品牌</legend>--}}
    {{--</fieldset>--}}
    <div>
        <!-- 内容主体区域 -->
        <div>
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                <legend>修改用户信息</legend>
            </fieldset>

            <form class="layui-form" method="post" action="/admin/frontDeskUser/save">

                {{csrf_field()}}
                <input type="hidden" name="id" id="b_id" value="{{$frontDeskUser['id']}}">
                <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                    <div class="layui-input-label" style='width:120px;height:40px;'>
                        <span style="margin-left: 15px;">用户状态</span>
                    </div>
                </div>
                <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
                    <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                        <select name="user_status" id="" lay-filter="type">
                            <option value="">请选择</option>
                            <option value="1">正常</option>
                            <option value="2">屏蔽</option>
                            <option value="3">注销</option>
                        </select>
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
        </div>
    </div>
@endsection



<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
