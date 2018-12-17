<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class KeywordGoods extends Model
{
    protected $table = "fb_keyword_goods";

    public function getKeywordGoodsListByGoodsId($goodsIdAry) {
    	if(!$goodsIdAry || !is_array($goodsIdAry)) return [];
 		$goodsIdStr = implode(',',$goodsIdAry);
    	$sql = "select goods_id,keyword_name,is_custom from fb_keyword_goods kg left join fb_keyword k on kg.keyword_id = k.id where goods_id in ({$goodsIdStr})";
    	return DB::select($sql);
    }
}
