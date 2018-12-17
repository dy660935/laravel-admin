<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    //首页
    public function index()
    {
        if(\Auth::user()->can('system')) {
            $spiderCount = json_decode(Redis::get('SpiderCount'),true);
            $spiderCountFilter = json_decode(Redis::get('SpiderCountFilter'),true);
            $importCount = json_decode(Redis::get('ImportCount_'.env('APP_ENV')),true);
        } else {
            $spiderCount = '';
            $spiderCountFilter = '';
        }
        return view( '/admin/home/index', compact('spiderCount','spiderCountFilter','importCount'));
    }

    public function test()
    {

        $res = DB::connection( 'mongodb' )->collection( 'test' )->get()->toArray();
        var_dump( $res );
        exit;
        return view( '/admin/home/index' );
    }
}
