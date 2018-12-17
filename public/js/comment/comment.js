layui.use('layer', function(){
    var layer = layui.layer;

    $(document).on('click',"button[name=shield]",function(){
        var id= $(this).attr("num");
        var par= $(this).attr("par");
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type:'post',
            url:"/admin/comment/update",
            data:{comment_status:3,id:id},
            dataType:'json',
            success:function(result){
                var url="/admin/comment/list/"+par;
                if(result.code == 1){
                    layer.msg(result.font,{icon:result.code,time:1000},function(){

                        window.location.href=url;
                    });
                }else{
                    layer.msg(result.font,{icon:result.code});
                }
            },

        });
    });
    $(document).on('click',"button[name=pass]",function(){
        var id= $(this).attr("num");
        var par= $(this).attr("par");
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type:'post',
            url:"/admin/comment/update",
            data:{comment_status:1,id:id},
            dataType:'json',
            success:function(result){
                var url="/admin/comment/list/"+par;
                if(result.code == 1){
                    layer.msg(result.font,{icon:result.code,time:1000},function(){
                        window.location.href=url;
                    });
                }else{
                    layer.msg(result.font,{icon:result.code});
                }
            },

        });
    });
    $(document).on('click',"button[name=recovery]",function(){
        var id= $(this).attr("num");
        var par= $(this).attr("par");
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type:'post',
            url:"/admin/comment/update",
            data:{comment_status:2,id:id},
            dataType:'json',
            success:function(result){
                var url="/admin/comment/list/"+par;
                if(result.code == 1){
                    layer.msg(result.font,{icon:result.code,time:1000},function(){
                        window.location.href=url;
                    });
                }else{
                    layer.msg(result.font,{icon:result.code});
                }
            },

        });
    });
});