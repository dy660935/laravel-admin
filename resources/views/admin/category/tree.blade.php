@extends("admin.layout.main")
@section("content")
<ul id="demo"></ul>
<script type="text/javascript">
    layui.use(['tree','layer'],function(){
        layui.tree({
          	elem: '#demo',
          	nodes:<?=json_encode($categoryFirst)?>,
          	click: function(node){
          		console.log(node);
          		layer.confirm('确认将分类"'+node.name+'"设置为隐藏？', {
					btn: ['确定','取消'], //按钮
					offset: (window.parent.parent.innerHeight / 2) + 'px',
				}, function(){

					$.ajax({
		                type:'post',
		                url:"/admin/category/setDisplay",
		                data:{id:node.id},
		                dataType:'json',
		                success:function(result){
		                    if(result.code == 1){
		                    	layer.msg(result.msg)
		                    	window.location.reload();
		                    	return true;
		                    }else{
		                        layer.msg(result.msg)
		                        return false;
		                    }
		                }
		            });
				}, function(){
					layer.close();
				});
			    //console.log(node) //node即为当前点击的节点数据
			}
        });    
    })
    
</script>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/common/common.js"></script>