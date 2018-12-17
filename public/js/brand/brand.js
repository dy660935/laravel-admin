$(function(){
    $('#brand_category').click(function(){
        $('#input_category').after(
            "&nbsp&nbsp"+'<input type="text" class="layui-input box layui-this" name="category[]"   placeholder="分类/标签" autocomplete="off" style="width:100px;display:inline-block" >'
        );
    });
    $('#brand_name').click(function(){
        $('#name').after(
            "&nbsp&nbsp"+'<input type="text" class="layui-input box layui-this" name="brand_english_name"   placeholder="请输入英文名称" autocomplete="off" style="width:200px;display:inline-block" >'
        );
    });

    $(document).on('keyup',".remarkWords",function(){
        var id= $(this).attr("num");
        var brand_remark = $(this).val();
        var curWwwPath = window.document.location.href;

        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type:'post',
            url:"/admin/brand/remark",
            data:{id:id,brand_remark:brand_remark,url:curWwwPath},
            dataType:'json',
            success:function(result){
                // console.log(result);
                window.location.href=result.data;
            }

        });
    });
});

layui.use('form', function(){
    var form = layui.form
        ,layer = layui.layer;
});
