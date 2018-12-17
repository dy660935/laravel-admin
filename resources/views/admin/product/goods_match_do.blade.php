@extends("admin.layout.main")
@section("content")
<style type="text/css">
	*{font-size:12px;}
	.layui-table td{font-size: 12px;}
	.layui-table td, .layui-table th{padding:2px;}
</style>
<section class="content">
        <!-- Small boxes (Stat box) -->
        <fieldset class="layui-elem-field layui-field-title">
            <legend>选择主体</legend>
        </fieldset>
	<form class="layui-form" action="/admin/products/goodsMatchSave" role="form" id="matchForm">
	<table  class="layui-table" lay-skin="nob">
	<tr>
			<td colspan="9">商品信息</td>
		</tr>
			<tr>
			<td width="6%">选择主体</td>
			<td width="6%">三级分类</td>
			<td width="6%">品牌</td>
			<td>商品名称</td>
			<td width="10%">规格参数</td>
			<td width="5%">价格</td>
			<td width="8%">来源网站</td>
			<td width="6%">封面图片</td>
			<td width="8%">匹配状态</td>
			<td width="4%">操作</td>
		</tr>
	<?php foreach($goodsList as $k => $v):?>
		<tr>
			<td>
				<input type="radio" name="productId" <?php if($v->master):?> checked <?php endif;?> value="<?=$v->product_id?>" />
				<input type="hidden" name="goodsId[]" value="<?=$v->id?>" />
			</td>
			<td><?=isset($categoryAry['category3Hash'][$v->category_id]) ? $categoryAry['category3Hash'][$v->category_id] : '未设置' ?></td>
			<td><?=isset($brandHash[$v->brand_id]) ? $brandHash[$v->brand_id] : '未设置'?></td>
			<td><a href="/admin/products/update/<?=$v->product_id?>?tab=match" target="_blank"><?=$v->goods_name?></a></td>
			<td><?php if($v->good_specs) {echo implode(',',json_decode($v->good_specs,true));}?></td>
			<td><?=$v->shop_price?></td>
			<td><?=$websiteHash[$v->orignal_website_id]['website_name']?> <a target="_blank" href="<?=$v->original_goods_url?>">URL</a></td>
			<td><a href="javascript:;" data-image="<?php if(preg_match('/^http/',$v->product_image)):?><?=$v->product_image?><?php else:?><?=$imageCdnUrl.$v->product_image?><?php endif;?>">图片URL</a></td>
			<td><?=$matchMode[$v->is_import]?><?php if($v->master):?>-主体<?php endif;?></td>
			<td><a href="javascript:;" data-master="<?=$v->master?>" class="delMatch">X</a></td>
		</tr>
		<?php endforeach;?>
	</table>
	<div class="layui-form-item">
        <div class="layui-input-block">
            <button type="button" class="btn btn-primary" lay-submit lay-filter="form_match">提交匹配</button>
        </div>
    </div>
	</form>
	<script type="text/javascript">
		layui.use(['form','layer'], function () {
			var form = layui.form,
			layer = layui.layer;
			$ = layui.jquery;
            function layerMsg(msg) {
            	layer.msg(msg, {offset: (window.parent.parent.innerHeight / 2) + 'px'});
            }
            $('.delMatch').click(function(){
            	if($('.delMatch').length < 3) {
            		layer.msg('至少保留两个合并项', function(){});
            		return false;
            	}
            	_obj = $(this)
            	if(_obj.attr('data-master') == '1') {
            		layerMsg('不能删除主体');
            		return false;
            	}
            	$(this).parents('tr').remove();
            })
            form.on('submit(form_match)',function(data){
            	if(!data.field.productId) {
            		layer.msg('请选择主体', function(){
						
					});
					return false;
            	}
            	$.ajax({
	                type:'post',
	                url:"/admin/products/goodsMatchSave",
	                data:data.field,
	                dataType:'json',
	                success:function(result){
	                    if(result.code == 1){
	                    	window.location = '/admin/products/update/'+result.productId;
	                    	return true;
	                    }else{
	                        layer.msg(result.msg);
	                        return false;
	                    }
	                }
	            });
	            return false;
            })
            /*$('#matchForm').submit(function(){
				checked = false;
				$('input[name="productId"]').each(function(){
					if($(this).prop('checked')) {
						checked = true;
					}
				})
				if(!checked) {
					layer.msg('请选择主体', function(){
						
					});
					return false;
				}
				return true;
			});*/
        })
		function renderForm(){
			layui.use('form', function(){
				var form = layui.form;//高版本建议把括号去掉，有的低版本，需要加()
				form.render();
			});
		}
		$('a[data-image]').mouseover(function(){
			layer.open({
				offset:  [(window.parent.parent.innerHeight / 4) + 'px',(window.parent.parent.innerWidth * 0.6) + 'px'],
				type: 3,
				shadeClose: true,
				skin: 'layui-layer-rim', //加上边框
				area: ['340px', '340px'], //宽高
				content: '<img src="'+$(this).attr('data-image')+'" width="340" height="340"/>'
			});
		})
	</script>
	@include('admin.layout.error')
</section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/product/product.js"></script>