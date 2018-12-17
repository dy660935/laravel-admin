<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Shop;
use App\Website;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input();
        $where = [];
        $english_where = [];
        if( isset( $search[ 'name' ] ) ) {
            $where[] = [ 'shop_name' , 'like' , '%' . $search[ 'name' ] . '%' ];
        }
        $shopList = Shop::where($where)->offset(0)->paginate( 20 );
        return view( '/admin/shop/index' , compact( 'shopList', 'search') );
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
        if(!$id) {
            return redirect('/admin/shop/');
        }
        $shopInfo = Shop::where(['id'=>$id])->first();
        if(!$shopInfo) {
            return redirect('/admin/shop/');   
        }
        $shopInfo->toArray();
        return view( '/admin/shop/update' , compact( 'shopInfo' ) );
    }

    public function updateDo( Request $request )
    {
        //验证
        $this->validate( $request , [ 'shop_name' => 'required|min:2' , 'shop_thumbnail' => 'required' ] );

        //逻辑
        $data = \request()->all();
        if(!isset($data['id']) || !$data['id']) {
            return redirect()->back();;
        }
        $shopInfo = Shop::where(['id'=>$data['id']])->first();
        if(!$shopInfo) {
            return redirect('/admin/shop/');   
        }
        $shopInfo->toArray();
        if( $data[ 'shop_thumbnail' ] == "" ) {
            unset( $data[ 'shop_thumbnail' ] );
        }

        unset( $data[ 'file' ] , $data[ '_token' ] , $data[ 's' ] , $data[ 'returnUrl' ] );

        $res = Shop::where( 'id' , '=' , $data[ 'id' ] )->update( $data );

        if( $res ) {
            $websiteInfo = Website::where(['id'=>$shopInfo['orignal_website_id']])->first()->toArray();
            if($websiteInfo['pay_way'] == 5) {
                $shopList = Shop::where(['orignal_website_id'=>$shopInfo['orignal_website_id']])->get();
                if($shopList) {
                    $shopTmp = [];
                    echo count($shopList)."\n";
                    $currentShopName = '';
                    foreach($shopList as $k => $v) {
                        $shopName = preg_replace('/(\(|\（)(.*)(\)|\）)/','',$v->shop_name)."\n";
                        $shopTmp[$shopName][] = $v->id;
                        if($v->id == $data['id']) {
                            $currentShopName = $shopName;
                        }
                    }
                    if(isset($shopTmp[$currentShopName]) && count($shopTmp[$currentShopName]) > 1 && $data[ 'shop_thumbnail' ]) {
                        Shop::whereIn('id',$shopTmp[$currentShopName])->update(['shop_thumbnail'=>$data['shop_thumbnail']]);
                    }
                }
            }
            return redirect( '/admin/shop/' );
        }
        else {
            callback();
        }
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
