<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Specs extends Model
{
    protected $table = "fb_specs";

    public function getSpecsId($specs) {
    	if(!$specs) {
    		return false;
    	}
    	if($specsInfo = Specs::where(['spec_name'=>$specs])->first()) {
    		$specsId = $specsInfo->id;
    	} else {
    		$specsId = Specs::insertGetId(['spec_name'=>$specs]);
    	}
    	return $specsId;
    }
}
