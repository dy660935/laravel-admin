@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend>分类列表</legend>
    </fieldset>

    <form class="layui-form" action="/admin/category/list" method="get">
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:70px;height:40px;'>
                <span style="margin-left: 5px;">分类名称</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left: 10px;">
            <div class="layui-input-label" style='width:200px;height:40px;margin-top: 20px;'>
                <input id="name" type="text" name="name"
                       value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" placeholder="请输入分类名称"
                       autocomplete="off"
                       class="layui-input">
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
            <div class="layui-input-label" style='width:80px;height:40px;'>
                <span style="margin-left: 15px;">分类层级</span>
            </div>
        </div>
        <div class="layui-form-item" style="display:inline-block;">
            <div class="layui-input-label" style='width:100px;height:40px;'>
                <select name="category_level" id="" lay-verify="">
                    @if(!empty($search['category_level']) && $search['category_level'] == 1)
                        <option value="">请选择</option>
                        <option value="1" selected>一级分类</option>
                        <option value="2">二级分类</option>
                        <option value="3">三级分类</option>
                    @elseif(!empty($search['category_level']) && $search['category_level'] == 2)
                        <option value="">请选择</option>
                        <option value="1">一级分类</option>
                        <option value="2" selected>二级分类</option>
                        <option value="3">三级分类</option>
                    @elseif(!empty($search['category_level']) && $search['category_level'] == 3)
                        <option value="">请选择</option>
                        <option value="1">一级分类</option>
                        <option value="2">二级分类</option>
                        <option value="3" selected>三级分类</option>
                    @else
                        <option value="">请选择</option>
                        <option value="1">一级分类</option>
                        <option value="2">二级分类</option>
                        <option value="3">三级分类</option>
                    @endif
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">确定</button>
        <a href="/admin/category" class="layui-btn layui-btn-normal" style="float:right;margin-right: 15px;">新增</a>
        <!-- <a href="/admin/category/matching" class="layui-btn layui-btn-normal" style="float:right;margin-right:
        15px;">匹配商品</a>
        <a href="/admin/category/tree" class="layui-btn layui-btn-normal" style="float:right;margin-right:
        15px;">分类树</a> -->
    </form>
    <div style='margin-left:30px;'>
        <table class="layui-table" lay-skin="nob" style="">
            <colgroup>
                <col width="90">
                <col width="150">
                <col>
            </colgroup>
            <thead>
            <tr>
                <th style='width:10px;'>序号</th>
                <th style='width:50px;'>分类名称</th>
                <th style='width:120px;'>分类状态</th>
                <th style='width:120px;'>分类层级</th>
                <th style='width:50px;'>操作</th>
            </tr>
            </thead>
            <tbody id="v">
            @foreach($category as $v)
                <tr>
                    <td>{{$v->id}}</td>
                    <td>{{$v->category_name}}</td>
                    <td>
                        @if ($v->category_status == 1)
                            启用中
                        @elseif($v->category_status == 2)
                            未启用
                        @endif
                    </td>
                    <td>
                        @if($v->category_level == 1)
                            一级分类
                        @elseif($v->category_level==2)
                            二级分类
                        @elseif($v->category_level==3)
                            三级分类
                        @endif
                    </td>
                    <td>
                        <a href='/admin/category/update/{{$v->id}}'>修改</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if(!empty($search['name']))

            {{$category->appends(['name'=>"$search[name]"])->links()}}

        @elseif(!empty($search['category_level']))

            {{$category->appends(['category_level'=>"$search[category_level]"])->links()}}

        @elseif(!empty($search['name']) && !empty($search['category_level']))

            {{$category->appends(['category_level'=>"$search[category_level]",'name'=>"$search[name]"])->links()}}
        @else
            {{$category->links()}}
        @endif
        {{--<div id='test1'></div>--}}
    </div>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
