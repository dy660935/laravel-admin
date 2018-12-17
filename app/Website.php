<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use \App\Model;
use \App\Country;
use \App\Currency;

class Website extends Model
{
    use Notifiable;

    protected $table = "fb_website";

    public $websiteHash;

    public function getAllWebsite()
    {
        return Website::get()->toArray();
    }

    public function getWebsiteHashInstance() {
        if(!$this->websiteHash) {
            $this->websiteHash = $this->getWebsiteAry([ 'id' , 'website_abbreviation' , 'website_name', 'pay_way' ]);
        }
        return $this->websiteHash;
    }

    // 加上对应的字段
    protected $fillable = [ 'website_name' , 'website_abbreviation' , 'website_url' , 'website_thumbnail' , 'website_country' , 'website_city' , 'website_currency' , 'pay_way' ];

    public function getWebsiteHash()
    {
        $websiteList = Website::get( [ 'id' , 'website_abbreviation' ] )->toArray();;
        $websiteHash = [];
        if( $websiteList ) {
            foreach( $websiteList as $k => $v ) {
                $websiteHash[ $v[ 'website_abbreviation' ] ] = $v[ 'id' ];
            }
        }
        return $websiteHash;
    }

    public function getWebsiteAry($field = [ 'id' , 'website_abbreviation' , 'website_name' ])
    {
        $websiteList = Website::get( $field )->toArray();;
        $websiteHash = [];
        if( $websiteList ) {
            foreach( $websiteList as $k => $v ) {
                $websiteHash[ $v[ 'id' ] ] = $v;
            }
        }
        return $websiteHash;
    }

    //查询地区
    public function country()
    {
        return $this->belongsTo( Country::class , 'website_country' , 'id' );
    }


    //查询货币
    public function currency()
    {
        return $this->belongsTo( Currency::class , 'website_currency' , 'id' );
    }

}
