<?php

namespace App\Console\Commands;

use App\Brand;
use App\Libs\analysis\PhpAnalysis;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class KeyWord extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'KeyWord {--website=} {--startpage=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getArrayByObject( $object )
    {

        if( !$object || !is_array( $object ) ) return [];

        foreach( $object as $k => $v ) {

            $data[ $k ] = (array) $v;
        }

        return $data;
    }

    public function getBrandName(){

        $data=DB::table('fb_brand')->where('is_deteled',1)->get(['brand_chinese_name','brand_english_name'])->toArray();

        $data=$this->getArrayByObject($data);

        $new_data=[];

        foreach ($data as $k=>$v){

            $new_data[]=$v['brand_chinese_name'];

            $new_data[]=$v['brand_english_name'];
        }

        $new_data=array_unique($new_data);

        return $new_data;
    }

    public function getCategory(){

        $data=DB::table('fb_category')->where(['category_level'=>3,'is_deleted'=>1])->get(['category_name'])->toArray();

        $data=$this->getArrayByObject($data);

        $new_data=[];

        foreach ($data as $k=>$v){

            $new_data[]=$v['category_name'];
        }

        $new_data=array_unique($new_data);

        return $new_data;
    }

    public function getSpec(){

        $data=DB::table('fb_specs')->where('status',1)->get(['spec_name'])->toArray();

        $data=$this->getArrayByObject($data);

        $new_data=[];

        foreach ($data as $k=>$v){

            $new_data[]=$v['spec_name'];
        }

        $new_data=array_unique($new_data);

        return $new_data;
    }

    private function _getRejectRang() {

        $categoryList = ['ESTĒE LAUDER','ESTĒE LAUDER','毫升','克',1,2,3,4,5,6,7,8,9,0,'.','*','ETĒE E','E','/瓶','瓶装','-','T','()','（）','g','G','+','#','/支','Ē','/','LANCÔME','LANCÔME','Ô'];

        return $categoryList;
    }


    public function getRejectWords(){

        $brand_data=$this->getBrandName();

        $category_data=$this->getCategory();

        $spec_data=$this->getSpec();

        $rang_data=$this->_getRejectRang();

        $new_data=array_merge($brand_data,$category_data,$spec_data,$rang_data);

        return $new_data;

    }

    public function insertProduct($k_k,$key_word_id,$website){

        $keyWordProductInfo['goods_id']=$k_k;

        $keyWordProductInfo['keyword_id']=$key_word_id;

        $keyWordProductInfo['website_id']=intval($website);

        $keyWordProductInfo['created_at']=date('Y-m-d H:i:s',time());

//        DB::connection()->enableQueryLog();  // 开启QueryLog

        $keyWordProductId=DB::table('fb_keyword_goods')->insertGetId($keyWordProductInfo);

//        dd(DB::getQueryLog());
        return $keyWordProductId;

    }

    private function _getExcuteTime($start, $end) {

        return round($end - $start,3);

    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $website = ($this->option('website'));
        if(!$website) {
            exit('websiteId empty');
        }
        $startPage = $this->option('startpage');
        if(!$startPage) {
            $startPage = 0;
        }

        $this->match($website,$startPage);
        exit;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function match($website,$startPage)
    {

        $startMicro = explode(' ',microtime());

        $start = $startMicro[1] + $startMicro[0];

        $reject_add_data=$this->_getRejectRang();

        $count = DB::table('fb_goods')->where(['is_deleted'=>1,'orignal_website_id'=>$website])->count();

        $key_word_mun= $key_word_product_mun=0;

        $pageSize = 1000;

        $page = intval(ceil($count/$pageSize));

        $where=" AND p.orignal_website_id = ".$website;

        $analysis_model = new PhpAnalysis();

        $new_data=[];

        for($i = $startPage; $i < $page; $i++) {

            $offset = $i * $pageSize;

            $sql="SELECT
g.id AS goods_id,
 b.brand_chinese_name,
 b.brand_english_name,
 g.goods_original_name,
 s.spec_name,
 g.original_category_name
FROM
	fb_goods g
LEFT JOIN fb_product p ON g.original_goods_id = p.original_product_id
LEFT JOIN fb_brand b ON b.id = p.brand_id
LEFT JOIN fb_goods_specs gs ON gs.goods_id = g.id
LEFT JOIN fb_specs s ON gs.spec_id = s.id
WHERE
	g.is_deleted = 1
{$where} limit {$offset},{$pageSize}";

            $goods_data = DB::select($sql);

            $goods_data=$this->getArrayByObject($goods_data);

            foreach($goods_data as $k => $v) {

                $category=explode('/',$v['original_category_name']);

                $reject_data[]=$v['spec_name'];

                $reject_data[]=$v['brand_chinese_name'];

                $reject_data[]=$v['brand_english_name'];

                $new_reject_data=array_merge($category,$reject_data,$reject_add_data);

                $goods_data[$k]['goods_original_name'] = $v['goods_original_name'];

                $goods_data[$k]['goods_name']=trim(str_ireplace($new_reject_data,'',$v['goods_original_name']));

                #调用分词接口
                $analysis_model->differFreq = false;

                $analysis_model->SetSource($goods_data[$k]['goods_name'],'utf8','utf8');

                $analysis_model->StartAnalysis();

                $goods_data[$k]['key_word'] = $analysis_model->GetFinallyKeywords(2);

                $key_word[$goods_data[$k]['goods_id']]=explode(',',$goods_data[$k]['key_word']);

            }


            $midMicro = explode(' ',microtime());
            $mid = $midMicro[1] + $midMicro[0];
            echo '查询'.($pageSize * $i).' '.$mid.' '.$this->_getExcuteTime($start,$mid)."s \n";

            foreach ($key_word as $k_k=>$k_v){

                foreach ($k_v as $kk_k=>$kk_v){

                    if(empty($k_v[$kk_k])){

                        continue;
                    }

                    $keywordInfo['keyword_name']=$k_v[$kk_k];

                    $keywordInfo['created_at']=date('Y-m-d H:i:s',time());

                    $key_id=isset($new_data[$keywordInfo['keyword_name']]) ? $new_data[$keywordInfo['keyword_name']] : 0;

                    if ($key_id){

                        $keyWordProductId=$this->insertProduct($k_k,$key_id,$website);

                        if($keyWordProductId){

                            $key_word_product_mun++;
                        }

                    }else{

                        $key_data=\App\Keyword::where('keyword_name',$k_v[$kk_k])->first(['id']);

                        if ($key_data){

                            $key_word_id=$key_data->id;

                        }else{

                            $key_word_id=DB::table('fb_keyword')->insertGetId($keywordInfo);

                        }

                        $new_data[$keywordInfo['keyword_name']]=$key_word_id;

                        $key_word_mun++;

                        $keyWordProductId=$this->insertProduct($k_k,$key_word_id,$website);

                        if($keyWordProductId){

                            $key_word_product_mun++;
                        }

                    }

                }

                unset($key_word);

            }
            $midMicro = explode(' ',microtime());
            $mid = $midMicro[1] + $midMicro[0];
            echo $offset.' '.$mid.' '.$this->_getExcuteTime($start,$mid)."s \n";

            #并且存入分词和产品的派生表中

        }

        $endMicro = explode(' ',microtime());
        $end = $endMicro[1] + $endMicro[0];
        echo 'keyword import '.$key_word_mun.' ; keyword_product import '.$key_word_product_mun.' .';
        echo 'execute '.number_format($end - $start,2).' s';

    }


}
