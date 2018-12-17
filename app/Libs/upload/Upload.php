<?php
namespace App\Libs\upload;
	/**
	 * Class Upload
	 * 文件上传
	 */

	class Upload{

		public $max;      //文件大小
		public $arr=array("gif","png","jpg","bmp","jpeg","mp4","x-ms-wmv");   //文件后缀
		public $error;    //文件错误提示

	/**
	 *  File
	 *  $file | 文件名
	 *  return | 路径 / false
	 */

		function up($file){
		//验证错误类型 
			switch ($file['error']) {
				case '1': $this->error="上传文件超过php.ini中的设置";return false;
				case '2': $this->error="上传文件超过表单中的设置";return flase;
				case '3': $this->error="部分文件被上传";return false;
				case '4': $this->error="没有文件被上传";return false;
			}


		//文件大小
		$this->max=1024*1024*200;
			if($file['size']>$this->max){
				$this->error="上传图片超限";
				return false;
			}

		//文件类型 
		$style=substr($file['type'],strpos($file['type'],'/')+1);
			if(!in_array($style,$this->arr)){
				$this->error="上传图片类型错误";
				return false;
			}

			//定义要上传的路径
			$puth='Uploads'.'/'.date('Y').'/'.date('m').'/'.date('d').'/';
			//判断路径是否存在
			is_dir($puth) or mkdir($puth,0777,true);

			//定义新的文件名	(1)截取后缀名
			$name=substr($file['name'],strpos($file['name'],'.'));
			//(2)当前时间加随机数命名文件
			$new_name=time().rand(10000,99999).$name;

			//拼接要上传的路径
			$new_puth=$puth.$new_name;
			//移动文件到到指定文件夹
			if(!empty($new_puth)){
				//把临时路径放到指定路径
				move_uploaded_file($file['tmp_name'], $new_puth);
				return $new_puth;
			}
		

		}





	/**
	 *  File
	 *  return | 错误详情
	 */

		function getError(){
			return $this->error;
		}


	}



