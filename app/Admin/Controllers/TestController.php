<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //未使用生成器，占用内存32mb
        /*echo memory_get_usage();
        function makeRange($length) {
            $dataSet = [];
            for ($i=0; $i<$length; $i++) {
                $dataSet[] = $i;
            }
            return $dataSet;
        }

        $customRange = makeRange(1000000);
        foreach ($customRange as $i) {
            echo $i . PHP_EOL;
        }
        echo memory_get_usage();*/
        //使用生成器，占用内存1kb
        /*function makeRange($length) {
            for ($i=0; $i<$length; $i++) {
                yield $i;
            }
        }

        foreach (makeRange(1000000) as $i) {
            echo $i . PHP_EOL;
        }
        echo memory_get_usage();*/
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
