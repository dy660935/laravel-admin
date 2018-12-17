<?php

namespace App;

use App\Model;

class Currency extends Model
{
    protected $table = "fb_currency";

    public function getCurrencyHash() {
        $currencyList = Currency::where('currency_status',1)->get(['currency_unit','currency_rate'])->toArray();;
        $currencyHash = [];
        if($currencyList) {
            foreach($currencyList as $k => $v) {
                $currencyHash[$v['currency_unit']] = $v['currency_rate'];
            }
        }
        return $currencyHash;
    }

}
