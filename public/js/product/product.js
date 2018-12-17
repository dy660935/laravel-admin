

$(function(){

    //忽略删除入库数据功能
    layui.use('form', function(){
        var form = layui.form;
        $(document).on('click',"a[name=hulue]",function(){

            var che=$(this).parents().siblings("div[name=sousuo]").find("input[type=checkbox]").prop("checked");

            if(che == true){

                $(this).parents().siblings("div[name=sousuo]").find("input[type=checkbox]").attr("checked",false);

                var original_goods_id = $(this).parents().siblings("div[name=sousuo]").find("input[type=checkbox]").attr("original_goods_id");

                $.ajax({
                    url:"/admin/goods/pass",
                    type:"post",
                    data:{original_goods_id:original_goods_id},
                }).success(function (result) {

                });
                form.render();
            }
        });
    });
    //导入数据
    layui.use(['layedit','form','layer'], function(){
        var form = layui.form,
            layedit = layui.layedit,
            layer = layui.layer;

        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

        layedit.set({
            uploadImage: {
                // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                url: "/admin/commons/img/upload",  //接口url
                type: 'post' ,//默认post
                success: function (res) {

                }
            }
        });
        var index = layedit.build('LAY_demo1');

        //监听提交
        form.on('submit(formDemo)', function(data){
            $.ajax({
                type:'post',
                url:"/admin/products/import",
                data:data.field,
                dataType:'json',
                success:function(result){
                    if(result.code == 1){
                        $("#dato1").html("<button class=\"layui-btn layui-btn-disabled\"" +
                            "   disabled=\"disabled\"" +
                            " >导入</button>")
                        // $('#dato1').addClass('layui-btn-disabled').attr('disabled',"true");
                        //品牌名称
                        $('#brand_name').val(result.data.brand_name);
                        //品牌id
                        $('[name=brand_id]').val(result.data.brandID);
                        //商品名称
                        $('[name=pro_name]').val(result.data.title);
                        //商品标签
                        $('.pro_tab').val(result.data.root_category_name);

                        if(result.data.category_name){
                            $('.pro_tab').last().after(
                                '<input type="text" value="'+result.data.category_name+'" class="layui-input box pro_tab" name="pro_tab[]"  placeholder="商品标签" autocomplete="off" style="width:100px;display:inline-block;margin-left: 15px;" >'
                            );
                        }
                        if(result.data.sub_category_name){
                            $('.pro_tab').last().after(
                                '<input type="text" value="'+result.data.sub_category_name+'" class="layui-input box pro_tab" name="pro_tab[]"  placeholder="商品标签" autocomplete="off" style="width:100px;display:inline-block;margin-left: 15px;" >'
                            );
                        }
                        //产品序列号
                        $('[name=product_sn]').val(result.data.good_id);
                        //货币类型
                        $('[name=currency_genre]').val(result.data.currency_genre);

                        //商品规格
                        var good_specs_num = result.data.good_specs.length;

                        if(good_specs_num>=1){

                            for (var i=0;i<good_specs_num;i++){

                                $('[name=attribute_value]').val(result.data.good_specs[i]);
                            }
                        }
                        //商品的售价
                        if(result.data.shop_price){
                            $('[name=shop_price]').val(result.data.shop_price);
                        }
                        //商品的原价
                        if(result.data.market_price){
                            $('[name=market_price]').val(result.data.market_price);
                        }
                        //原始商品id
                        $('[name=original_product_id]').val(result.data.good_id);

                        //产品状态
                        if(result.data.is_onsell==1){
                            var   str=" <input type=\"radio\" name=\"product_status\" value='1' title=\"上架\" checked>\n" +
                                " <input type=\"radio\" name=\"product_status\" value=\"2\" title=\"下架\" >";
                            $('#aaa').html(str);
                            form.render("radio")
                        }else{
                            var  str=" <input type=\"radio\" name=\"product_status\" value='1' title=\"上架\" >\n" +
                                " <input type=\"radio\" name=\"product_status\" value=\"2\" title=\"下架\" checked>";
                            $('#aaa').html(str);
                            form.render("radio");
                        }
                        //产品的图片
                        var tupian="<img width='80px' height='80px' src='"+result.data.images+"'>";
                        document.getElementById('test1').innerHTML=tupian;
                        $('[name=img1]').val(result.data.images);

                        //产品描述
                        layedit.setContent(index,result.data.title);

                    }else{
                        layer.msg(result.font,{icon:result.code});
                    }
                }
            });
            return false;
        });
        //商品提交基本信息
        form.on('submit(formDemo3)', function(data){
            // console.log(data.field);
            $.ajax({
                type:'post',
                url:"/admin/products/add",
                data:data.field,
                dataType:'json',
                success:function(result){
                    if(result.code == 1){
                        // console.log(result.data.product_id);
                        layer.msg(result.font,{icon:result.code,time:1000},function(){

                            if(result.data.attribute_name_id && result.data.attribute_value_id){

                                window.location.href="/admin/products/match/"+result.data.product_id+"/"+result.data.attribute_name_id+"/"+result.data.attribute_value_id;
                            }else{
                                window.location.href="/admin/products/match/"+result.data.product_id;
                            }

                        });
                    }else{
                        layer.msg(result.font,{icon:result.code});
                    }
                },error:function (msg) {
                    var json=JSON.parse(msg.responseText);
                        $.each(json.errors, function(idx, obj) {
                            alert(obj[0]);
                            return false;
                        });
                }
            });
            return false;
        });
        //关键字修改展示商品
        form.on('submit(saveWord)', function(data){
            $.ajax({
                type:'post',
                url:"/admin/products/match",
                data:data.field,
                dataType:'json',
                success:function(result){
                    if(result.code == 1){
                        $('#good_one').html(result.data.good_data_one);
                        $('#good_two').html(result.data.good_data_two);
                        form.render();
                    }else{
                        layer.msg(result.font,{icon:result.code});
                    }
                }
            });
            return false;
        });




        //关键字修改功能
        $(document).on('click',"#saveWords",function () {
            $(".search_a").removeAttr("disabled");
            //todo 1234567890
            var str1="<a href=\"javascript:;\" class=\"layui-btn layui-btn-sm \"  id=\"search_zhuijia\" style=\"display:inline-block;margin-left:30px;margin-bottom: 21px;\"><i class=\"layui-icon\">&#xe654;</i></a>";
            $(".search_a").parent().last().after(str1)
        })




        // //添加商品部分信息
        // form.on('submit(formDemoAdd)', function(data){
        //     console.log(data.elem); //得到radio原始DOM对象
        //     console.log(data.value); //被点击的radio的value值
        //     // console.log(data.field);
        //         $.ajax({
        //             type:'post',
        //             url:"/admin/goods/add",
        //             data:data.field,
        //             dataType:'json',
        //             success:function(result){
        //                 if(result.code == 1){
        //
        //                 }else{
        //
        //                 }
        //             }
        //         });
        //         return false;
        //     });
        //单选框选择入库功能
        form.on('checkbox(choose)', function(data){
            var original_goods_id=$(this).attr('original_goods_id');

            if(data.elem.checked == true){
                var product_id=$("input[name=product_id]").val();
                var attribute_name_id=$("input[name=attribute_name_id]").val();
                var attribute_value_id=$("input[name=attribute_value_id]").val();
                var original_goods_url=$(this).attr('original_goods_url');
                var goods_name=$(this).attr('goods_name');
                var market_price=$(this).attr('market_price');

                var shop_price=$(this).attr('shop_price');
                var stock_number=$(this).attr('stock_number');
                var goods_status=$(this).attr('goods_status');
                var orignal_website=$(this).attr('orignal_website');

                // console.log(website_name,original_goods_url,goods_name,market_price,original_goods_id,shop_price,stock_number,goods_status,orignal_website);
                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
                $.ajax({
                    type:'post',
                    url:"/admin/goods/add",

                    data:{product_id:product_id,original_goods_url:original_goods_url,goods_name:goods_name,market_price:market_price,original_goods_id:original_goods_id,shop_price:shop_price,stock_number:stock_number,goods_status:goods_status,orignal_website:orignal_website,attribute_value_id:attribute_value_id,attribute_name_id:attribute_name_id},
                    dataType:'json',
                    success:function(result){}
                });
            }else{
                $.ajax({
                    url:"/admin/goods/pass",
                    type:"post",
                    data:{original_goods_id:original_goods_id},
                }).success(function (result) {});
            }
            return false;
        });

        form.on('submit(formDemoId)', function(data){
            $.ajax({
                type:'post',
                url:"/admin/products/selectId",
                data:data.field,
                dataType:'json',
                success:function(result){
                    $('#good_two').html(result.data);
                    form.render();
                }
            });
            return false;
        });
        form.on('submit(formDemoIdUp)', function(data){
            $.ajax({
                type:'post',
                url:"/admin/products/selectIdUp",
                data:data.field,
                dataType:'json',
                success:function(result){
                    $('#good_two').html(result.data);
                    form.render();
                }
            });
            return false;
        });
    
    //监听select
    //     form.on('select(website_name)',function (data) {
    //         var product_id=$("input[name=product_id]").val();
    //         $.ajax({
    //             type:'post',
    //             url:"/admin/products/keyWordSearch",
    //             data:{product_id:product_id},
    //             dataType:'json',
    //             success:function(result){
    //                 if(result.code == 1){
    //                     $('#website_a').html(result.data);
    //                     form.render('select');
    //                 }
    //             }
    //         });
    //     })
    });

    // layui.use('upload', function () {
    //     var $ = layui.jquery,
    //         upload = layui.upload;
    //     //拖拽上传
    //     $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    //     upload.render({
    //         elem: '#test1',
    //         url: '/admin/commons/img/upload',
    //         done: function (res) {
    //             $('#imgurl').val(res.data.src);
    //             var str = "<img width='80px' height='80px' src='" + res.data.src + "'>";
    //             document.getElementById('test1').innerHTML = str;
    //         }
    //     });
    // });
    // layui.use('upload', function () {
    //     var $ = layui.jquery,
    //         upload = layui.upload;
    //     //拖拽上传
    //     $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    //     upload.render({
    //         elem: '#test2',
    //         url: '/admin/commons/img/upload',
    //         done: function (res) {
    //             $('#imgurl2').val(res.data.src);
    //             var str = "<img width='80px' height='80px' src='" + res.data.src + "'>";
    //             document.getElementById('test2').innerHTML = str;
    //         }
    //     });
    // });

//     $('#guiGe').click(function(){
//         $('.tab1').after(
//         "<div>"+
//             "<table class='table' >"+
//             "<tr>"+
//             "<td>商品规格</td>"+
//             "<td rowspan='2'>"+
//             "<input type=\"checkbox\" lay-ignore=\"\" style=\"display: inline-block;\">"+
//         "</td>"+
//         "<td >规格名</td>"+
//         "<td>参数类名</td>"+
//         "<td>对应价格</td>"+
//         "</tr>"+
//         "<tr>"+
//             "<td> <button type='button' class=\"layui-btn\" name='delete'>删除</button></td>"+
//         "<td><input type='text' placeholder='比如：颜色' class='layui-input'></td>"+
//             "<td><input type='text' placeholder='比如：红色' class='layui-input'></td>"+
//             "<td><input type='text' placeholder='价格' class='layui-input'></td>"+
//             "</tr>"+
//             "</table>"+
//             "</div>"
//         );
//     });
//
    $("#tab_zhuijia").click(function () {
        var str1= "<input type=\"text\" class=\"layui-input box pro_tab\" id=\"tab_input\"  name=\"pro_tab[]\"" +
            " placeholder=\"商品标签\" autocomplete=\"off\" style=\"width:100px;display:inline-block;margin-left: 15px;\" >"+
            "<button type='button' class=\"layui-btn pro_tab\" name='delete_tab'>X</button>"
        $('.pro_tab').last().after(
            str1
        )
    })

    //关键词追加+
    $(document).on('click','#search_zhuijia',function () {

        var num =$('.search_a').length;
        num = Math.random();
        var str1= "<input type=\"text\" class=\"layui-input box search_input search_a\"  name=\"search["+num+"]\"" +
            " placeholder=\"请填入关键字\" autocomplete=\"off\" style=\"width:100px;display:inline-block;margin-left: 15px;\" >"+
            "<button type='button' class=\"layui-btn search_input search_zhuijia\" name='delete_search'>X</button>"
        $('.search_input').last().after(str1)
    })
    

    $(document).on('click',"button[name=delete]",function(){
        $(this).parent().parent().parent().remove();
    });

    $(document).on('click',"button[name=delete_tab]",function(){
        $(this).prev().remove();
        $(this).remove();
    });
    $(document).on('click',"button[name=delete_search]",function(){
        $(this).prev().remove();
        $(this).remove();
    });

    $(document).on('click',"#formDemo",function(){
        window.location.href='/admin/products';
    });

    //三级联动
    layui.use('form', function () {
        var form = layui.form,
            layer = layui.layer;

        form.on("select(level_one)", function (data) {
            var parent_id = data.value;
            // console.log(category_type)
            // var category_level= parseInt(category_type) + 1;
            // $("[name=category_level]").val(category_level);
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                type: 'post',
                url: "/admin/common/getCategoryInfo",
                data: {parent_id: parent_id},
                dataType: 'json',
                success: function (result) {
                    // console.log($("[name=parent_id]"));
                    var str = "";
                    var str1 = "<option value='0'>请选择</option>";

                    if (result.length != 0) {
                        $.each(result, function (k, v) {
                            str += "<option value='" + v['id'] + "'>" + v['category_name'] + "</option>";
                        });
                        $("#level_two").html(str1 + str);
                        form.render('select');
                    } else {
                        $("#level_two").html(str1);
                        $("#level_three").html(str1);
                        form.render('select');
                    }
                }
            });

        })
        form.on("select(level_two)", function (data) {
            var parent_id = data.value;
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                type: 'post',
                url: "/admin/common/getCategoryInfo",
                data: {parent_id: parent_id},
                dataType: 'json',
                success: function (result) {
                    // console.log($("[name=parent_id]"));
                    var str = "";
                    var str1 = "<option value='0'>请选择</option>";

                    if (result.length != 0) {
                        $.each(result, function (k, v) {
                            str += "<option value='" + v['id'] + "'>" + v['category_name'] + "</option>";
                        });
                        $("#level_three").html(str1 + str);
                        form.render('select');
                    } else {
                        $("#level_three").html(str1);
                        form.render('select');
                    }
                }
            });

        })
    });

});