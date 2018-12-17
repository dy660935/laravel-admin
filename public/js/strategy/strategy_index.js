$(function () {
    $('#strategy_category').click(function () {
        $('#input_strategy').after(
            "&nbsp&nbsp" + '<input type="text" class="layui-input box" name="strategy_label[]" id="tab_div" required  lay-verify="required" placeholder="攻略标签" autocomplete="off" style="width:100px;display:inline-block" >'
        );
    });

});

layui.use(['layedit', 'form'], function () {
    var $ = layui.jquery,
         form = layui.form
        , layer = layui.layer
        , layedit = layui.layedit;
    var index = layedit.build('LAY_demo1');

    //导入js
    $('#import').click(function () {
        var importName = $("#name").val();
        if(importName == ''){
            alert("链接网址非空");
        }else{
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
            $.ajax({
                url:"/admin/strategy/getImport",
                type:"post",
                data:{importName:importName},
                dataType:"json"
            }).done(function (result) {
                console.log(result);
                $("input[name=strategy_title]").val(result.strategy_title);
                $("input[name=author_name]").val(result.author_name);
                $("input[name=strategy_wechat_url]").val(result.strategy_wechat_url);

                // layedit.setContent(index,result.strategy_describe);


            })
            //     .error(function (msg) {
            //     var json=JSON.parse(msg.responseText);
            //     $.each(json.errors, function(idx, obj) {
            //         alert(obj[0]);
            //         return false;
            //     });
            // })
        }
    });
});