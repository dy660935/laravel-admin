<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\testUser;
class UserController extends Controller
{
    public function index()
    {
        testUser::test();
//        $res = testUser::all();
        $res = DB::connection('mongodb')->collection('jd')->get();
//
////        $m = new MongoClient("mongodb://${username}:${password}@localhost", array("db" => "myDatabase"));
////        DB::connection('mongodb')->collection('test')->insert(['name' => 'tom', 'age' => 18]);
////
////        $res = DB::connection('mongodb')->collection('users')->all();
        dd($res);
    }


    protected function test() {

    }

}
