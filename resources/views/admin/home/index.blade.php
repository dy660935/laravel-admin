@extends("admin.layout.main")
@section("content")
<style type="text/css">
	*{font-size:12px;}
	.layui-table td{font-size: 12px;}
	.layui-table td, .layui-table th{padding:2px;}
	.layui-form-radio{padding: 0;margin: 0;}
</style>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            欢迎
        </div>
    </div>
    <div class="row">
		<?php if($spiderCount):?>
        <table  class="layui-table" lay-skin="nob" style="margin: 0px 10px;">
			<thead>
			<tr>
				<td width="3%">网站</td>
				<td width="6%">全部数据<br/>更新时间<?=$spiderCount['update']?></td>
				<td width="6%">过滤后数据<br/>更新时间<?=$spiderCountFilter['update']?></td>
				<td width="6%">已导入数据<br/>更新时间<?=$importCount['update']?></td>
				<td width="6%">是否导入规格<br/>更新时间<?=$importCount['update']?></td>
				<td width="6%">是否导入关键词<br/>更新时间<?=$importCount['update']?></td>
			</tr>
			</thead>
		<?php foreach($spiderCount['data'] as $k => $v):?>
			<tr>
				<td><?=$k?></td>
				<td><?=$v?></td>
				<td><?=$spiderCountFilter['data'][$k]?></td>
				<td><?=(isset($importCount['goods'][$k]) ? $importCount['goods'][$k] : '-')?></td>
				<td><?=(isset($importCount['spec'][$k]) ? '已导入' : '-')?></td>
				<td><?=(isset($importCount['keyword'][$k]) ? '已导入' : '-')?></td>
			</tr>
			<?php endforeach;?>
			<tr>
				<td>总计</td>
				<td><?=$spiderCount['count']?></td>
				<td><?=$spiderCountFilter['count']?></td>
				<td><?=(isset($importCount['count']) ? $importCount['count'] : '-')?></td>
				<td>-</td>
				<td>-</td>
			</tr>
		</table>
		<?php endif;?>
    </div>
</section>
@endsection