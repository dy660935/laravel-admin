$(function(){
    $(document).on('keyup',".remarkWords",function(){
        var id= $(this).attr("num");
        var brand_remark = $(this).val();
        var curWwwPath = window.document.location.href;

        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
        $.ajax({
            type:'post',
            url:"/admin/websites/remark",
            data:{id:id,website_remarks:brand_remark,url:curWwwPath},
            dataType:'json',
            success:function(result){
                // console.log(result);
                window.location.href=result.data;
            }

        });
    });
});
