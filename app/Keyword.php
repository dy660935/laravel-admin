<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $table = "fb_keyword";

    public function getKeywordId($keyword) {

    	if(!$keyword) {
    		return false;
    	}
    	if($keywordInfo = Keyword::where(['keyword_name'=>$keyword])->first()) {
    		$keywordId = $keywordInfo->id;
    	} else {
    		$keywordId = Keyword::insertGetId(['keyword_name'=>$keyword]);
    	}
    	return $keywordId;
    }
}
