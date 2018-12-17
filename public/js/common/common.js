// layui.use('form', function () {
//     var form = layui.form
//         , layer = layui.layer;
//     $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
// });
layui.use(['upload','form'], function () {
    var $ = layui.jquery,
        form = layui.form,
        upload = layui.upload,
        layer = layui.layer;
    //拖拽上传
    // $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    upload.render({
        elem: '#test',
        url: '/admin/commons/img/upload',
        method:"post",
        size:"2048",
        accept:"file",
        auto:false,
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        exts: 'jpg|png|jpeg|gif',
        choose: function(obj){  //上传前选择回调方法
            var flag = true;
            obj.preview(function(index, file, result){
                // console.log(file);            //file表示文件信息，result表示文件src地址
                var img = new Image();
                img.src = result;
                img.onload = function () { //初始化夹在完成后获取上传图片宽高，判断限制上传图片的大小。
                    if(img.width ==120 && img.height ==120){
                        obj.upload(index, file); //满足条件调用上传方法
                    }else{
                        flag = false;
                        layer.msg("您上传的小图大小必须是120*120尺寸!");
                        return false;
                    }
                }
                return flag;
            });
        },
        done: function (res) {
            layer.closeAll('loading');
            var url =urlReplace(res.data.src);
            $('#imgurl').val(url);
            var str = "<img width='120px' height='120px' src='" + res.data.src + "'>";
            document.getElementById('test').innerHTML = str;
            // $("#cover_img_big").val(res.data.cover_img)
            // $("#avatar").val(res.data.cover_img)
            // $("#showImg").show()
        }
    });
});
/*
富文本图片上传
 */
layui.use(['layedit', 'form'], function () {
    var form = layui.form,
        layedit = layui.layedit;

    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

    layedit.set({
        uploadImage: {
            // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            url: "/admin/commons/img/upload" //接口url
            , type: 'post' //默认post
            , success: function (res) {

            }
        }
    });
    var index = layedit.build('LAY_demo1');
});

//方形小图上传
layui.use('upload', function(){
    var upload = layui.upload;
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    //执行实例
    upload.render({
        elem: '#test9' //绑定元素
        ,url: '/admin/commons/img/upload' //上传接口
        ,done: function(res){
            var url =urlReplace(res.data.src);
            // alert(url);
            //上传完毕回调
            $('#imgurl9').val(url);
            var str = "<img width='120px' height='120px' src='" + res.data.src + "'>";
            document.getElementById('test9').innerHTML = str;
        }
        ,error: function(){
            //请求异常回调
        }
    });
});

//封面大图上传
layui.use('upload', function(){
    var upload = layui.upload;
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    //执行实例
     upload.render({
        elem: '#test10' //绑定元素
        ,url: '/admin/commons/img/upload' //上传接口
        ,done: function(res){
             var url =urlReplace(res.data.src);
            //上传完毕回调
             $('#imgurl10').val(url);
             var str = "<img width='120px' height='120px' src='" + res.data.src + "'>";
             document.getElementById('test10').innerHTML = str;
        }
        ,error: function(){
            //请求异常回调
        }
    });
});


function urlReplace(url){
    var domain = url.split('/');
    var domain1 = url.split('//'); //以“/”进行分割

    if( domain[2] ) {
        var new_domain = domain1[1].replace(domain[2],'');
    } else {
        var new_domain = ''; //如果url不正确就取空
    }
    return new_domain;
}
