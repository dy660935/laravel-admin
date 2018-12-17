@extends("admin.layout.main")
@section("content")
<link rel="stylesheet" type="text/css" href="/public/layui/css/modules/layer/default/layer.css">
<style type="text/css">
	*{font-size:12px;}
	.layui-table td{font-size: 12px;}
	.layui-table td, .layui-table th{padding:2px;}
	.layui-form-radio{padding: 0;margin: 0;}
</style>
<section class="content">
        <!-- Small boxes (Stat box) -->
        <fieldset class="layui-elem-field layui-field-title">
            <legend>商品匹配</legend>
        </fieldset>
	<form class="layui-form" role="form" id="searchForm">
		<div class="layui-form-item">
			<label class="layui-form-label" style="width: inherit;">待匹配</label>
			<?php foreach($goodsMatchCount as $k => $v):?>
            <label class="layui-form-label" style="width: inherit;<?php if($v->is_import == 1):?>color:#f00;<?php endif?>"><?=$matchMode[$v->is_import]?>:<?=$v->countNum?></label>
        <?php endforeach;?>
        </div>
		<div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">品牌</label>
            <div class="layui-input-inline">
                <select name="brand" id="brand" lay-search="">
				<option value="0">请选择</option>
				<?php foreach($brandHash as $k => $v):?>
				<option value="<?=$k?>" <?php if($k == $brandId):?> selected <?php endif;?>><?=$v?></option>
				<?php endforeach;?>
			</select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">分类</label>
            <div class="layui-input-inline">
                <select name="cate1" id="category1" lay-filter="cate1" lay-search="">
                	<option value="0">请选择</option>
                	<?php foreach($categoryAry['category1'] as $k => $v):?>
				<option value="<?=$v['id']?>" <?php if($cate1Id == $v['id']):?> selected <?php endif;?>><?=$v['category_name']?></option>
				<?php endforeach;?>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="cate2" id="category2" lay-filter="cate2" lay-search="">
                	<option value="0">请选择</option>
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="cate3" id="category3" lay-filter="cate3" lay-search="">
                	<option value="0">请选择</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">关键字</label>
            <div class="layui-input-inline" style='width:700px;height:20px;'>
                <input class="layui-input box" name="keyword" id="keyword" value="<?=$keyword?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">结果类型</label>
            <div class="layui-input-inline" style='width:700px;height:40px;line-height: 40px;'>
                <input type="radio" name="is_master" value="2"<?php if($isMaster==2):?> checked<?php endif;?>/>查看所有<input type="radio" name="is_master" value="1"<?php if($isMaster==1):?> checked<?php endif;?>/>只看主体<input type="radio" name="is_master" value="0"<?php if($isMaster==0):?> checked<?php endif;?>/>只看未匹配
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">搜索模式</label>
            <div class="layui-input-inline" style='width:300px;height:20px;'>
                <input type="radio" name="mode" value="0"<?php if($mode==0):?> checked<?php endif;?>/>a&b <input type="radio" name="mode" value="1"<?php if($mode==1):?> checked<?php endif;?>/>a|b
            </div>
            <label class="layui-form-label" style="width: inherit;">每页显示</label>
            <div class="layui-input-inline" style='width:300px;height:20px;'>
                <select name="pagesize" id="pagesize" lay-search="">
				<?php foreach($pageSizeAry as $k => $v):?>
				<option value="<?=$v?>" <?php if($v == $pagesize):?> selected <?php endif;?>><?=$v?></option>
				<?php endforeach;?>
			</select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;">覆盖字段</label>
            <div class="layui-input-inline" style='width:700px;height:20px;'>
                <input type="checkbox" name="" value="">商品名称<input type="checkbox" name="" value="">规格<input type="checkbox" name="" value="">分类<input type="checkbox" name="" value="">属性
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label" style="width: inherit;"><input type="checkbox" name="allwebsite"  value="1" />网站</label>
            <div class="layui-input-inline" style='width:1000px;line-height:28px;'>
                <?php $i=0; foreach($websiteHash as $k => $v):?>
				<input type="checkbox" lay-filter="website" name="website[]" value="<?=$v['id']?>"<?php if(in_array($v['id'],$websiteAry)):?> checked<?php endif;?>/><?=$v['website_name']?>
				<?php $i++;?>
			<?php endforeach;?>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block" style="text-align:center; margin: 0;">
                <button type="submit" class="btn btn-primary">搜索</button>
            </div>
        </div>
	</form>
	<?php if(isset($res['total_found']) && $res['total_found']):?>
	<table  class="layui-table" lay-skin="nob">
		<tr>
			<td>共查询到<?=$res['total_found']?>条记录</td>
		</tr>
	</table>
	<?php endif;?>
	<?php if(isset($res['words']) && $res['words']):?>
	<table  class="layui-table" lay-skin="nob">
	<tr>
			<td colspan="2">分词信息 <a href="javascript:" id="fenci">查看</a></td>
		</tr>
	</table>
	<table  class="layui-table" lay-skin="nob" style="display: none;" id="fenci_table">
			<tbody>
	<?php foreach($res['words'] as $k => $v):?>

		<tr>
			<td><?=$k?></td>
					<td>命中<?=$v['hits']?>次</td>
		</tr>
		<?php endforeach;?>
			</tbody>
	</table>
	<?php endif;?>
	<?php if(isset($goodsList) && $goodsList):?>
	<form class="layui-form" action="/admin/products/goodsMatchDo" role="form" id="matchForm">
	<table  class="layui-table" lay-skin="nob" id="goodsListTable">
	<tr>
			<td colspan="10">商品信息</td>
		</tr>
		<tr>
			<td colspan="10">
				<button type="button" name="setCate" class="btn btn-primary">批量设置分类</button>
				<div class="layui-input-inline">
	                <select name="matchcate" id="matchcate" lay-filter="matchcate" lay-search="">
	                	<option value="0">请选择</option>
	                	<?php foreach($categoryAry['category3'] as $k => $v):?>
					<option value="<?=$v['id']?>"><?=$v['category_name']?></option>
					<?php endforeach;?>
	                </select>
	            </div>
			</td>
		</tr>
			<tr>
			<td width="3%"><input type="checkbox" name="matchIdAll" lay-filter="matchIdAll" value="1" /></td>
			<td width="6%">三级分类</td>
			<td width="7%">原分类</td>
			<td width="8%">品牌</td>
			<td>商品名称</td>
			<td width="8%">规格参数</td>
			<td width="5%">价格</td>
			<td width="8%">来源网站</td>
			<td width="6%">封面图片</td>
			<td width="8%">匹配状态</td>
		</tr>
	<?php foreach($goodsList as $k => $v):?>
		<tr>
			<td><input type="checkbox" name="matchId[]" lay-filter="matchId[]" value="<?=$v->id?>" /></td>
			<td><?=isset($categoryAry['category3Hash'][$v->category_id]) ? $categoryAry['category3Hash'][$v->category_id] : '未设置' ?></td>
			<td><?=$v->original_category_name?></td>
			<td><?=isset($brandHash[$v->brand_id]) ? $brandHash[$v->brand_id] : '未设置'?></td>
			<td><a href="/admin/products/update/<?=$v->product_id?>?tab=match" target="_blank"><?=$v->goods_name?></a></td>
			<td><?php if($v->good_specs) {echo implode(',',json_decode($v->good_specs,true));}?></td>
			<td><?=$v->shop_price?></td>
			<td><?=$websiteHash[$v->orignal_website_id]['website_name']?> <a target="_blank" href="<?=$v->original_goods_url?>">URL</a></td>
			<td><a href="javascript;" data-image="<?php if(preg_match('/^http/',$v->product_image)):?><?=$v->product_image?><?php else:?><?=$imageCdnUrl.$v->product_image?><?php endif;?>">图片URL</a></td>
			<td><?=$matchMode[$v->is_import]?><?php if($v->master):?>-主体<?php endif;?></td>
		</tr>
		<?php endforeach;?>
	</table>
	<div class="layui-form-item">
        <div class="layui-input-block" style="text-align:center; margin: 0;">
            <button type="button" name="moreList" class="btn btn-primary">查看更多</button>
        </div>
    </div>
	<div class="layui-form-item">
        <div class="layui-input-block" style="text-align:center; margin: 0;">
            <button type="submit" class="btn btn-primary">合并</button>
        </div>
    </div>
	</form>
	<?php endif;?>
	<script type="text/javascript">
		category2 = <?php echo json_encode($categoryAry['category2'])?>;
		category3 = <?php echo json_encode($categoryAry['category3'])?>;
		curPage = 1;
		function renderForm(){
			layui.use('form', function(){
				var form = layui.form;//高版本建议把括号去掉，有的低版本，需要加()
				form.render();
			});
		}
		$('body').on('mouseover','a[data-image]',function(){
            $('a[data-image]').css('color','#000')
			$(this).css('color','#f00')
			layer.open({
				offset:  [(window.parent.parent.innerHeight / 4) + 'px',(window.parent.parent.innerWidth * 0.7) + 'px'],
				type: 3,
				shadeClose: true,
				skin: 'layui-layer-rim', //加上边框
				area: ['200px', '200px'], //宽高
				content: '<img src="'+$(this).attr('data-image')+'" width="200" height="200"/>'
			});
        })

		$('#fenci').click(function(){
			if($('#fenci_table').css('display') == 'none') {
				$('#fenci_table').show();
			} else {
				$('#fenci_table').hide();
			}
		});
		function getWebsiteCheckedLen() {
			websiteCheckboxLenTmp = 0;
			$('input[name="website[]"]').each(function(v){
				_obj = $(this)
				if(_obj.prop("checked")) {
					websiteCheckboxLenTmp++
				}
			})
			return websiteCheckboxLenTmp;
		}
		function websiteInit() {
			websiteCheckboxLen = $('input[name="website[]"]').length;
			websiteCheckboxLenTmp = getWebsiteCheckedLen();
			if(websiteCheckboxLen == websiteCheckboxLenTmp) {
				$('input[name="allwebsite"]').prop("checked",true);
			} else {
				$('input[name="allwebsite"]').prop("checked",false);
			}
			renderForm();
		}
		websiteInit();
		function getmatchIdCheckedLen() {
			matchIdCheckboxLenTmp = 0;
			$('input[name="matchId[]"]').each(function(v){
				_obj = $(this)
				if(_obj.prop("checked")) {
					matchIdCheckboxLenTmp++
				}
			})
			return matchIdCheckboxLenTmp;
		}
		function matchIdAllInit() {
			matchIdCheckboxLen = $('input[name="matchId[]"]').length;
			matchIdCheckboxLenTmp = getmatchIdCheckedLen();
			if(matchIdCheckboxLen == matchIdCheckboxLenTmp) {
				$('input[name="matchIdAll"]').prop("checked",true);
			} else {
				$('input[name="matchIdAll"]').prop("checked",false);
			}
			renderForm();
		}
		//matchIdAllInit();
		$('input[name="allwebsite"]').click(function(){
			if($('input[name="allwebsite"]').prop("checked")) {
				$('input[name="allwebsite"]').prop("checked",false)
				$('input[name="website[]"]').prop("checked",false);
			} else {
				$('input[name="allwebsite"]').prop("checked",true)
				$('input[name="website[]"]').prop("checked",true);
			}
			renderForm();
		});
		$('input[name="matchIdAll"]').click(function(){
			if($('input[name="matchIdAll"]').prop("checked")) {
				$('input[name="matchIdAll"]').prop("checked",false)
				$('input[name="matchId[]"]').prop("checked",false);
			} else {
				$('input[name="matchIdAll"]').prop("checked",true)
				$('input[name="matchId[]"]').prop("checked",true);
			}
			renderForm();
		});
		function categoryInit() {
			cate1 = '<?=$cate1Id?>';
			cate2 = '<?=$cate2Id?>';
			cate3 = '<?=$cate3Id?>';
			if(cate1 && cate2) {
				cate2Render(cate1,cate2);
			}
			if(cate2 && cate3) {
				cate3Render(cate2,cate3);
			}
		}
		categoryInit();
		function cate2Render(parent_id,current_id) {
			var str = "";
            var str1 = "<option value='0'>请选择</option>";
            var str2 = str1 + str;
    		$.each(category2, function (k, v) {
        		if(v.parent_id == parent_id) {
        			if(v.id == current_id) {
        				str1 += '<option value="'+v.id+'" selected>'+v.category_name+'</option>';
        			} else {
        				str1 += '<option value="'+v.id+'">'+v.category_name+'</option>';
        			}
        		}
        	})
        	$("#category2").html(str1 + str);
        	$("#category3").html(str2);
        	renderForm();
		}

		function cate3Render(parent_id,current_id) {
			var str = "";
            var str1 = "<option value='0'>请选择</option>";
    		$.each(category3, function (k, v) {
        		if(v.parent_id == parent_id) {
        			if(v.id == current_id) {
        				str1 += '<option value="'+v.id+'" selected>'+v.category_name+'</option>';
        			} else {
        				str1 += '<option value="'+v.id+'">'+v.category_name+'</option>';
        			}
        		}
        	})
        	$("#category3").html(str1 + str);
        	renderForm();
		}
		layui.use('form', function () {
			var form = layui.form,
			layer = layui.layer;
			$ = layui.jquery;
            form.on("select(cate1)", function (data) {
            	if(data.value) {
            		cate2Render(data.value,0);
            	}
            })
            form.on("select(cate2)", function (data) {
            	if(data.value) {
            		cate3Render(data.value,0);
            	}
            })
            form.on('checkbox(website)',function(data){
            	websiteInit();
            })
	        form.on('checkbox(matchIdAll)', function(data){
		        var id = data.value;
		        $('input[name="matchId[]"]').each(function(index, item){
		           item.checked = data.elem.checked;
		        });
		        renderForm();
	    	})
	    	form.on('checkbox(matchId[])',function(data){
            	matchIdAllInit();
            })
            function layerMsg(msg) {
            	layer.msg(msg, {offset: (window.parent.parent.innerHeight / 2) + 'px'});
            }
            $('#searchForm').submit(function(){
				len = getWebsiteCheckedLen();
				if(!len) {
					layerMsg('请至少选择一个网站');
					return false;
				}
				brandId = $.trim($('#brand').val());
				cate = $.trim($('#category3').val());
				keyword = $.trim($('#keyword').val());
				if(brandId == '0' && cate == '0' && keyword == '') {
					layerMsg('请至少设置品牌、分类、关键字中一个搜索条件');
					return false;	
				}
				return true;
			});
			$('#matchForm').submit(function(){
				matchIdLen = $('input[name="matchId[]"]').length;
				checkedLen = 0;
				$('input[name="matchId[]"]').each(function(){
					if($(this).prop('checked')) {
						checkedLen++;
					}
				})
				if(!matchIdLen || checkedLen < 2) {
					layerMsg('请至少选择两个合并项');
					return false;
				} else {
					return true;
				}
			});
			$('button[name="moreList"]').click(function(){
				curPage++;
				$(this).attr('disabled',true).text('加载中...');
				$moreList = $(this);
				$.ajax({
	                type:'post',
	                url:"/admin/products/goodsMatch",
	                data:{page:curPage},
	                dataType:'text',
	                success:function(result){
						$moreList.removeAttr('disabled').text('查看更多');
	                    if(result){
							$('#goodsListTable').append(result);
							renderForm();
							return true;
	                    }else{
							$moreList.attr('disabled',true).text('没有更多数据了');
	                        layerMsg('没有更多数据了')
	                        return false;
	                    }
	                }
	            });
			})
			$('button[name="setCate"]').click(function(){
				if($('#matchcate').val() == 0) {
					layerMsg('请选择一个要设置的分类');
					return false;
				}
				matchIdLen = $('input[name="matchId[]"]').length;
				checkedLen = 0;
				$('input[name="matchId[]"]').each(function(){
					if($(this).prop('checked')) {
						checkedLen++;
					}
				})
				if(!matchIdLen || !checkedLen) {
					layerMsg('请至少选择一个商品');
					return false;
				}
				cateName = $('#matchcate option[value="'+$('#matchcate').val()+'"]').text();
				layer.confirm('确认将已选商品设置为"'+cateName+'"分类？', {
					btn: ['确定','取消'], //按钮
					offset: (window.parent.parent.innerHeight / 4) + 'px',
				}, function(){
					$.ajax({
		                type:'post',
		                url:"/admin/products/batchSetCate",
		                data:$('#matchForm').serialize(),
		                dataType:'json',
		                success:function(result){
		                    if(result.code == 1){
		                    	layerMsg(result.msg)
		                    	return true;
		                    }else{
		                        layerMsg(result.msg)
		                        return false;
		                    }
		                }
		            });
				}, function(){
					layer.close();
				});
			})
        })
	</script>
	@include('admin.layout.error')
</section>
@endsection
<script src="/js/jquery-3.2.1.min.js"></script>
<script src="/layui/layui.js"></script>
<script src="/js/product/product.js"></script>