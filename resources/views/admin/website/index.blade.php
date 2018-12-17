@extends("admin.layout.main")
@section("content")
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <fieldset class="layui-elem-field layui-field-title">
            <legend>网站列表</legend>
        </fieldset>
        <form class="layui-form">
            <div class="layui-form-item" style="display:inline-block;margin-left:20px;">
                <div class="layui-input-label" style='width:80px;height:40px;'>
                    <span style="margin-left: 15px;">网站名称</span>
                </div>
            </div>
            <div class="layui-form-item" style="display:inline-block;margin-left: 30px;">
                <div class="layui-input-label" style='width:200px;height:40px;'>
                    <input id="name" type="text" name="name" placeholder=""
                           value="@if(!empty($search['name'])) {{$search['name']}} @else  @endif" autocomplete="off"
                           class="layui-input">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">确定</button>
            <a type="button" class="layui-btn layui-btn-normal" style="float:right"
               href="/admin/websites/create">增加网站</a>
        </form>
        <div style='margin-left:30px;'>
            <table class="layui-table" lay-skin="nob">
                <thead>
                <tr style="text-align: center">
                    <th>序号</th>
                    <th>网站名称</th>
                    <th>缩略图</th>
                    <th>地区</th>
                    <th>购买方式</th>
                    <th>货币单位</th>
                    <th>更新频率</th>
                    {{--<th>最低价数量</th>--}}
                    <th>备注</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($website as $websites)
                    <tr>
                        <td>{{$websites->id}}</td>
                        <td>{{$websites->website_name}}</td>
                        <td><img src="{{$websites->website_thumbnail}}" style='width:50px;'></td>
                        <td>
                            @if(!empty($websites->country->country))
                                {{$websites->country->country}}
                            @endif
                        </td>
                        <td>
                            @if($websites->pay_way==1)
                                国内直邮
                            @elseif($websites->pay_way==2)
                                海外直邮
                            @elseif($websites->pay_way==3)
                                海淘直邮
                            @elseif($websites->pay_way==4)
                                海淘转运
                            @elseif($websites->pay_way==5)
                                免税店
                            @else
                                其他
                            @endif
                        </td>
                        <td>{{$websites->currency->currency_name}}</td>
                        <td>
                            @if($websites->update_type == 1)
                                每周
                            @else
                                每天
                            @endif
                        </td>
                        <td>
                            <input type="text" required lay-verify="required" num="{{$websites->id}}" id=""
                                   autocomplete="off"
                                   placeholder="备注"
                                   class="layui-input remarkWords" style="width: 60px;"
                                   value="{{$websites->website_remarks}}">

                        </td>
                        <td>
                            @if($websites->website_status==1)
                                上架
                            @else
                                下架
                            @endif
                        </td>
                        <td>
                            <a class="btn" href="/admin/websites/update/{{$websites->id}}">编辑</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if(!empty($search['name']))
            {{$website->appends(['name'=>"$search[name]"])->links()}}
        @else
            {{$website->links()}}
        @endif
    </section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>
<script src="/js/website/website.js"></script>
