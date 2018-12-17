@extends("admin.layout.main")
@section("content")
    <section class="content">
            <!-- 内容主体区域 -->
            <div>
                <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
                    <legend>修改分类</legend>
                </fieldset>

                <form class="layui-form"  action="/admin/category/save/{{$category['id']}}" method="post">

                    {{csrf_field()}}
                    <input type="hidden" name="category_level" value="{{$category['category_level']}}">
                    <input type="hidden" name="id" value="{{$category['id']}}">
                    <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                        <div class="layui-input-label" style='width:80px;height:40px;'>
                            <span style="margin-left: 15px;">分类层级</span>
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
                        <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>

                                @if($category['category_level'] == 1)
                                    一级分类
                                @elseif($category['category_level'] ==2)
                                    二级分类
                                @elseif($category['category_level'] ==3)
                                    三级分类
                                @endif

                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;">
                        <div class="layui-input-label" style='width:80px;height:40px;'>
                            <span style="margin-left: 15px;">上级分类</span>
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                        <div class="layui-input-label" style='width:200px;height:40px;'>
                                {{$category_name['category_name']}}
                        </div>
                    </div>
                    <div class="layui-form-item" style="margin-left:5px;display:block;">
                        <label class="layui-form-label" style='width:100px;height:40px;'>分类名称</label>

                        <div class="layui-input-block">
                            <input type="text" id="name" value="{{$category['category_name']}}" name="category_name"
                                   required
                                   lay-verify="required" placeholder="请输入分类名称" autocomplete="off" class="layui-input"
                                   style="width:150px;display:inline-block">
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                        <div class="layui-input-label" style='width:120px;height:40px;'>
                            <span style="margin-left: 15px;">请选择分类层级</span>
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
                        <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                            <select name="" id="" lay-filter="type">
                                <option value="">请选择</option>
                                <option value="1">一级分类</option>
                                <option value="2">二级分类</option>
                                {{--<option value="3">三级分类</option>--}}
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                        <div class="layui-input-label" style='width:120px;height:40px;'>
                            <span style="margin-left: 15px;">请选择上级分类</span>
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                        <div class="layui-input-label" style='width:200px;height:40px;'>
                            <select name="parent_id" id="category" lay-verify="">
                                <option value="{{$category['parent_id']}}">请选择</option>
                                @foreach($category_data as $k=>$v)
                                    <option value="1">{{$v->category_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="layui-form-item">
                            <label class="layui-form-label" style="width: 120px;">是否启用</label>

                            <div class="layui-input-inline">
                                @if ($category['category_status'] == 1)
                                    <input type="checkbox" name="category_status" checked lay-skin="switch"
                                           lay-text="NO|OFF" value="1">
                                @else
                                    <input type="checkbox" name="category_status" lay-skin="switch" lay-text="NO|OFF"
                                           value="1">
                                @endif
                            </div>
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
                            <button class="layui-btn" type="submit">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </div>
                </form>
            </div>
    </section>
@endsection

<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/category/category.js"></script>
