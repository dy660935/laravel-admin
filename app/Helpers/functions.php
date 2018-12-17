<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/23
 * Time: 11:19
 */
//多维数组的差集
function array_diff_assoc_recursive($array1,$array2){
    $diffarray=array();
    foreach ($array1 as $key=>$value){
        //判断数组每个元素是否是数组
        if(is_array($value)){
            //判断第二个数组是否存在key
            if(!isset($array2[$key])){
                $diffarray[$key]=$value;
                //判断第二个数组key是否是一个数组
            }elseif(!is_array($array2[$key])){
                $diffarray[$key]=$value;
            }else{
                $diff=array_diff_assoc_recursive($value, $array2[$key]);
                if($diff!=false){
                    $diffarray[$key]=$diff;
                }
            }
        }elseif(!array_key_exists($key, $array2) || $value!==$array2[$key]){
            $diffarray[$key]=$value;
        }
    }
    return $diffarray;
}