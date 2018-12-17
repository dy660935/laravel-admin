<style type="text/css">
    *{font-size:12px;}
    .layui-table td{font-size: 12px;}
    .layui-table td, .layui-table th{padding:2px;}
</style>
<input type="hidden" id="match_product_id" value="<?=$product->id?>">
<form class="layui-form" role="form" id="matchForm" action="/admin/products/productsMatchDo" method="POST" >
    {{csrf_field()}}
    <table  class="layui-table" lay-skin="nob">
        <tr>
            <td colspan="9">匹配状态：<?=$matchMode[$product->is_import]?> <?php if(!$product->is_master && !$product->is_import):?> <a href="javascript:;">退出匹配</a><?php endif;?></td>
        </tr>
        <tr>
            <td colspan="9">匹配主体：<?php if($product->is_master):?>本商品<?php else:?><?php if($product->is_import):?>暂无<?php else:?><?=$productMaster->product_name?> <a href="/admin/products/update/<?=$productMaster->product_id?>">查看</a><?php endif;?><?php endif;?></td>
        </tr>
        <?php if($product->is_master):?>
        <tr>
            <td>匹配标签</td>
            <td><input type="text" readonly="" value="<?=isset($brandHash[$product->brand_id]) ? $brandHash[$product->brand_id] : '未设置'?>" style="width:60px;" /></td>
            <td><select id="matchcate" lay-search>
                <option value="0">请选择</option>
                <?php foreach($categoryAry['category3Hash'] as $k => $v):?>
                    <option value="<?=$k?>" <?php if($product->three_category_id == $k):?> selected <?php endif;?>><?=$v?></option>
                <?php endforeach;?>
            </select>
            </td>
            <td><input type="text" name="title" class="layui-input" style="width:300px; height: 30px; display: inline; line-height: 30px;" value="<?=$title?>" /> <button type="button" name="searchTitle" class="btn btn-primary" style="height: 30px; display: inline; line-height: 30px;padding: 0px 5px;">搜索</button><button type="button" name="emptyTitle" class="btn btn-primary" style="height: 30px; display: inline; line-height: 30px;padding: 0px 5px;margin-left:10px;">清空</button>
            <td><input type="text" id="keywords" value="<?php if($product->product_keywords):?><?=$product->product_keywords?><?php endif;?>" style="width:60px;" /></td>
            <td><input type="text" id="specs" value="<?php if($product->good_specs):?><?=implode(',',$product->good_specs)?><?php endif;?>" style="width:60px;" /></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>共<?=$goodsCount?>条记录</td>
        </tr>
        <?php endif;?>
        <tr>
            <td><?php if($product->is_master):?><input type="checkbox" name="matchIdAll" lay-filter="matchIdAll" value="1" /><?php endif;?></td>
            <td width="6%">品牌</td>
            <td>三级分类</td>
            <td>商品名称<span style="color:#f00;">(点击商品名称可到商品详情页)</span></td>
            <?php if($product->is_master):?>
            <td>关键词</td>
            <td>规格参数</td>
            <?php endif;?>
            <td>价格</td>
            <?php if($product->is_master):?>
            <td>最低价</td>
            <?php endif;?>
            <td>来源网站</td>
            <td>封面图片</td>
            <td>匹配状态</td>
        </tr>
        <?php $rowCount = count($goodsList); foreach($goodsList as $k => $v):?>
        <tr>
            <td><?php if(!$v->is_master && $rowCount > 1):?><input type="checkbox" name="matchId[]" lay-filter="matchId[]" value="<?=$v->id?>" /><?php endif;?></td>
            <td><?=isset($brandHash[$v->brand_id]) ? $brandHash[$v->brand_id] : '未设置'?></td>
            <td><?=isset($categoryAry['category3Hash'][$v->category_id]) ? $categoryAry['category3Hash'][$v->category_id] : '未设置' ?></td>
            <td><a href="/admin/products/update/<?=$v->pid?>?tab=match" target="_blank"><?=$v->goods_name?></a></td>
            <?php if($product->is_master):?>
            <td><?=$v->product_keywords?></td>
            <td><?php
                if($v->good_specs)
                    echo  implode(',',json_decode($v->good_specs,true));
                ?>
            </td>
            <?php endif;?>
            <td><?=$v->shop_price?></td>
            <?php if($product->is_master):?>
            <td style="color:#f00;"><?=$v->price_flag?></td>
            <?php endif;?>
            <td><?=$websiteHash[$v->orignal_website_id]['website_name']?> <a target="_blank" href="<?=$v->original_goods_url?>">URL</a></td>
            <td><a href="javascript:;" data-image="<?php if(preg_match('/^http/',$v->product_image)):?><?=$v->product_image?><?php else:?><?=$imageCdnUrl.$v->product_image?><?php endif;?>">图片URL</a></td>
            <td><?=$matchMode[$v->is_import]?><?php if($v->is_master):?>-主体<?php else:?><?php if(!$v->is_import):?> <a href="javascript:;" data-id="<?=$v->id?>" class="delMatch">退出匹配</a><?php endif;?><?php endif;?></td>
        </tr>
        <?php endforeach;?>
        <?php if($product->is_master):?>
        <tr>
            <td><?php if($rowCount > 1):?><button type="submit" class="btn btn-primary">合并</button><?php endif;?></td>
            <td></td>
            <td><button type="button" name="setCate" class="btn btn-primary">设置分类</button></td>
            <td></td>
            <td><button type="button" name="setKeyword" class="btn btn-primary">设置关键词</button></td>
            <td><button type="button" name="setSpecs" class="btn btn-primary">设置规格</button></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php endif;?>
    </table>
</form>
<script type="text/javascript">
    layui.use('form', function () {
        var form = layui.form,
        layer = layui.layer;
        $ = layui.jquery;
        form.on('checkbox(matchId[])',function(data){
            matchIdAllInit();
        })
        $('button[name="searchTitle"]').click(function(){
            if($.trim($('input[name="title"]').val())) {
                window.location = '/admin/products/update/<?=$product->id?>?tab=match&title='+encodeURIComponent($.trim($('input[name="title"]').val()))
            }
        })
        $('button[name="emptyTitle"]').click(function(){
            window.location = '/admin/products/update/<?=$product->id?>?tab=match'
        })
        function renderForm(){
            layui.use('form', function(){
                var form = layui.form;//高版本建议把括号去掉，有的低版本，需要加()
                form.render();
            });
        }
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
        function layerMsg(msg) {
            layer.msg(msg, {offset: (window.parent.parent.innerHeight / 2) + 'px'});
        }
        form.on('checkbox(matchIdAll)', function(data){
            var id = data.value;
            $('input[name="matchId[]"]').each(function(index, item){
               item.checked = data.elem.checked;
            });
            renderForm();
        })
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
        $('button[name="setCate"]').click(function(){
            if(!$('#matchcate').val() || $('#matchcate').val() == 0) {
                layerMsg('请选择一个要设置的分类');
                return false;
            }
            cateName = $('#matchcate option[value="'+$('#matchcate').val()+'"]').text();
            layer.confirm('确认将此商品设置为"'+cateName+'"分类？', {
                btn: ['确定','取消'], //按钮
                offset: (window.parent.parent.innerHeight / 4) + 'px',
            }, function(){
                $.ajax({
                    type:'post',
                    url:"/admin/products/batchSetCateByProductId",
                    data:{product_id:$('#match_product_id').val(),category_id:$('#matchcate').val()},
                    dataType:'json',
                    success:function(result){
                        if(result.code == 1){
                            layerMsg(result.msg)
                            window.location = '/admin/products/update/<?=$product->id?>?tab=match'
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
        $('button[name="setKeyword"]').click(function(){
            if(!$('#keywords').val() || $('#keywords').val() == 0) {
                layerMsg('请选择一个要设置的分类');
                return false;
            }
            keywordName = $('#keywords').val();
            layer.confirm('确认将此商品的关键词设置为"'+keywordName+'"？', {
                btn: ['确定','取消'], //按钮
                offset: (window.parent.parent.innerHeight / 4) + 'px',
            }, function(){
                $.ajax({
                    type:'post',
                    url:"/admin/products/batchSetKeywordByProductId",
                    data:{product_id:$('#match_product_id').val(),keyword:$('#keywords').val()},
                    dataType:'json',
                    success:function(result){
                        if(result.code == 1){
                            layerMsg(result.msg)
                            window.location = '/admin/products/update/<?=$product->id?>?tab=match'
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
        $('button[name="setSpecs"]').click(function(){
            if(!$('#specs').val() || $('#specs').val() == 0) {
                layerMsg('请选择一个要设置的分类');
                return false;
            }
            specsName = $('#specs').val();
            layer.confirm('确认将此商品的规格设置为"'+specsName+'"？', {
                btn: ['确定','取消'], //按钮
                offset: (window.parent.parent.innerHeight / 4) + 'px',
            }, function(){
                $.ajax({
                    type:'post',
                    url:"/admin/products/batchSetSpecsByProductId",
                    data:{product_id:$('#match_product_id').val(),specs:$('#specs').val()},
                    dataType:'json',
                    success:function(result){
                        if(result.code == 1){
                            layerMsg(result.msg)
                            window.location = '/admin/products/update/<?=$product->id?>?tab=match'
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
    $('.delMatch').click(function(){
        $.ajax({
            type:'post',
            url:"/admin/products/delGoodsMatch",
            data:{goods_id:$(this).attr('data-id')},
            dataType:'json',
            success:function(result){
                if(result.code == 1){
                    window.location = '/admin/products/update/<?=$product->id?>/?tab=match';
                    return true;
                }else{
                    layer.msg(result.msg);
                    return false;
                }
            }
        });
    })
</script>
