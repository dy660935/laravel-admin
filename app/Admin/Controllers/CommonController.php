<?php

namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CommonController extends Controller
{
    /*
    * 图片上传
    */
    public function imageUpload( Request $request )
    {
        $onlineEnvironment = \config( 'admin.onlineEnvironment' );

        $domain = $_SERVER[ 'HTTP_ORIGIN' ];
        if( in_array( $domain , $onlineEnvironment ) ) {

            $res = $this->imgUpload();
            return $res;

        }
        else {

            $this->imageCompatible( '/upload' );
            $file = $request->file( 'file' );
            if( $file->isValid() ) {
                $path = $file->store( date( 'ymd' ) , 'upload' );
                return [ 'data' => [ 'src' => asset( 'uploadImages/' . $path ) ] , 'code' => 0 , 'msg' => '上传成功' ];

            }
            else {

                return [ "code" => 0 , "msg" => "上传失败" , "data" => [] ];
            }
        }
    }

    /**
     * 线上上传图片
     * @return array
     */
    public function imgUpload()
    {
        //线上上传图片
        $file = $_FILES;

        $str = substr( str_shuffle( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890" ) , rand( 0 , 20 ) , 40 );

        $extension = substr( $file[ 'file' ][ 'type' ] , strpos( $file[ 'file' ][ 'type' ] , '/' ) + 1 );

        $path = '/var/image/uploadImages/' . date( 'ymd' );

        if( !is_dir( $path ) ) {
            mkdir( $path , 0777 , true );
        }

        $new_path = $path . '/' . $str . "." . $extension;

        $newFileName = "http://six.qqzdj.com.cn" . '/uploadImages/' . date( 'ymd' ) . '/' . $str . '.' . $extension;

        $res = move_uploaded_file( $file[ 'file' ][ 'tmp_name' ] , $new_path );

        if( $res ) {

            return [ 'data' => [ 'src' => $newFileName , 'code' => 0 , 'msg' => '上传成功' ] ];

        }
        else {

            return [ "code" => 0 , "msg" => "上传失败" , "data" => [] ];

        }
    }

    /**
     * 图片兼容
     *
     * @param string $imageUrl
     *
     * @return string
     */
    public function imageCompatible( $imageUrl )
    {
        if( empty( $imageUrl ) ) {

            return $imageUrl;

        }
        else {

            $nums = substr( $imageUrl , 0,1 );



            $num = substr_count( $imageUrl , 'http' );

            if ( $num ) {

                $newImageUrl = $imageUrl;

            } else {

                $domain = config( 'admin.imgDomain' );

                if($nums=='/'){

                    //                $domain = config( 'app.image_domain' );
                    $newImageUrl = $domain . $imageUrl;

                }else{

                    $newImageUrl = $domain .'/'.$imageUrl;
                }



            }
            return $newImageUrl;
        }
    }

    public function getCategoryInfo()
    {
        $data = \request()->all();
        $category_info = Category::where( "parent_id" , '=' , $data[ 'parent_id' ] )->get();
        $category_info = $category_info->toArray();
        return json_encode( $category_info );
    }

}
