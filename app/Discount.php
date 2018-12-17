<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = "fb_discount";
    //
    protected $schema = [
        "id" => 4440 ,
        "title" => '纸尿裤疯抢节满199再用100券' ,
        "intro" => '纸尿裤疯抢节满199再用100券' ,
        "exten" => '301疯抢节 满199用100劵' ,
        "start_time" => '2016-02-29 18:00:00' ,
        "end_time" => '2016-03-05 00:00:00' ,
        "full_money" => 0 ,
        "full_money_type" => 0 ,//是否循环
        "account_type" => 0 ,
        "discount" => 0.00 ,
        "reduce_money" => 0 ,
        "m_money" => 0 ,
        "n_item" => 0 ,
        "create_time" => '2016-01-11 18:09:17' ,
        "update_time" => '2016-02-03 17:13:28' ,
        "status" => 3 ,
        "admin_id" => 10060 ,
        "type" => 'full_reduce' ,
    ];

    public $discountList = array(
        //特价
        'bargain_price' => '特价' ,
        //立减
        'normal_reduce' => '立减' ,
        // 买赠
        'normal_gift' => 买赠 ,
        //折扣
        'normal_discount' => 折扣 ,
        //团购
        'groupon' => 团购 ,
        // 满减
        'full_reduce' => 满减 ,
        // 满赠
        'full_gift' => 满赠 ,
        // 满折
        'full_discount' => 满折 ,
        // 阶梯满减
        'ladder_full_reduce' => 阶梯满减 ,
        // 阶梯满N件M折
        'ladder_full_discount' => 阶梯满N件M折 ,
        // 阶梯满赠
        'ladder_full_gift' => 阶梯满赠 ,
        'm_n' => m件n元 ,
        'get_coupon' => 赠券 ,
        //买N免M
        'm_free_n' => m件减n ,
    );
}
