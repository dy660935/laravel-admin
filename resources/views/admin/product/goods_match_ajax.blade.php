<?php if(isset($goodsList) && $goodsList):?>
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
<?php endif;?>