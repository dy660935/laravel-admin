@extends("admin.layout.main")
@section("content")
    <fieldset class="layui-elem-field layui-field-title">
        <legend style="font-size:16px;">商品匹配</legend>
    </fieldset>
    <div class="layui-form" id="matchForm">
        {{csrf_field()}}
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">分类</label>
            <div class="layui-input-inline">
                <select name="level1" id="level1" lay-filter="level1" lay-search="">
                    <option value="0">请选择</option>
                    <?php foreach($category as $k => $v):?>
                    <option value="{{$v->id}}">{{$v->category_name}}</option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="level2" id="level2" lay-filter="level2" lay-search="">
                    <option value="0">请选择</option>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="level3" id="level3" lay-filter="level3" lay-search="">
                    <option value="0">请选择</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">搜索模式</label>
            <div class="layui-input-inline" style='width:700px;height:20px;'>
                <input type="radio" name="mode" value="0" checked/>a&b <input
                        type="radio" name="mode" value="1"/>a|b
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">
                <input type="checkbox" name="allwebsite" value="1"/>网站
            </label>
            <div class="layui-input-inline" style='width:1000px;line-height:28px;'>
                <?php $i = 0; foreach($websiteHash as $k => $v):?>
                <input type="checkbox" class="website" name="website[]"
                       value="<?=$v[ 'id' ]?>"/><?=$v[ 'website_name' ]?>
                <?php $i++;?>
                <?php endforeach;?>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
        </div>
    </div>

    <div id="show">

    </div>

    <div class="layui-form-item" id="is_show" style="display: none">
        <div class="layui-input-block">
            <button type="submit1" class="btn btn-primary">完成</button>
        </div>
    </div>
    <script type="text/javascript">

        $('#fenci').click(function () {
            if ($('#fenci_table').css('display') == 'none') {
                $('#fenci_table').show();
            } else {
                $('#fenci_table').hide();
            }
        });

        function websiteInit() {
            websiteCheckboxLen = $('input[name="website[]"]').length;
            websiteCheckboxLenTmp = 0;
            $('input[name="website[]"]').each(function (v) {
                _obj = $(this)
                if (_obj.prop("checked")) {
                    websiteCheckboxLenTmp++
                }
            })
            console.log(websiteCheckboxLen + ':' + websiteCheckboxLenTmp);
            if (websiteCheckboxLen == websiteCheckboxLenTmp) {
                $('input[name="allwebsite"]').prop("checked", true);
            } else {
                $('input[name="allwebsite"]').prop("checked", false);
            }
            var form = layui.form;
            form ? form.render("checkbox") : null;
        }

        websiteInit();

        $('input[name="allwebsite"]').click(function () {

            if ($('input[name="allwebsite"]').prop("checked")) {

                $('input[name="allwebsite"]').prop("checked", false)
                $('input[name="website[]"]').prop("checked", false);

            } else {

                $('input[name="allwebsite"]').prop("checked", true)
                $('input[name="website[]"]').prop("checked", true);

            }

            var form = layui.form;
            form ? form.render("checkbox") : null;

        });
        /*$('body').on('click','input[name="website[]"]',function(){
        	alert(789);
        	websiteInit();
        })*/
        /*$.each($('input[name="website[]"]'),function(k,v){
        	var _obje = v
        	$(_obje).click(function(k){
        		alert(123);
        	})
        })*/

        // $(function () {
        //
        //     $('input[name="website[]"]').each(function (k, v) {
        //
        //         $('input[name="website[]"]').eq(k).on('click', function () {
        //             alert(89);
        //         })
        //     })
        // })

        /*$('input[name="website[]"]').on('click',function(){
        	alert(789);
        	websiteInit();
        })*/
    </script>
    @include('admin.layout.error')
@endsection



<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/category/category.js"></script>
<script>
    layui.use('form', function () {
        var form = layui.form
            , layer = layui.layer;
        $('button[type=submit]').click(function () {

            var category_name = $("select[name=level3]").val();

            if (category_name == 0) {
                alert('分类必须选择三级分类');
                return false;
            }

            var mode_all = $("input[name=mode]");
            for (var i in mode_all) {
                if (mode_all[i].checked) {
                    var mode = mode_all[i].value;
                }
            }

            var website_all = $(".website");
            var website_id = "";
            for (var i in website_all) {
                if (website_all[i].checked) {
                    website_id += website_all[i].value + ',';
                }
            }

            website_id = website_id.substr(0, website_id.length - 1);

            if (website_id == "") {
                alert('网站必选');
                return false;
            }

            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                type: 'post',
                url: "/admin/category/matchingDo",
                data: {category_name: category_name, mode: mode, website_id: website_id},
                dataType: 'json',
                success: function (result) {
                    // console.log(result);
                    if (result != "") {
                        $("#show").html(result);
                        // $("#is_show").css("display",'block');
                        $("#is_show").show();
                        form.render("checkbox");
                    }
                }
            })
        })


        $('button[type=submit1]').click(function () {
            var checkbox_all = $(".tiJiao");
            var checkbox_id = "";
            for (var i in website_all) {
                if (website_all[i].checked) {
                    website_id += website_all[i].value + ',';
                }
            }

            website_id = website_id.substr(0, website_id.length - 1);
        })

    })


</script>