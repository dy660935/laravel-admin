<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Libs\sphinx\SphinxClient;
use App\Website;

class CategoryBatchSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CategoryBatchSet';

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

    private function _getWebsiteHash() {
        $websiteObj = new Website();
        $websiteList = $websiteObj->getAllWebsite();
        $websiteHash = [];
        if($websiteList) {
            foreach($websiteList as $k => $v) {
                $websiteHash[$v['id']] = $v['website_abbreviation'];
            }
        }
        return $websiteHash;
    }

    private function _getCategoryRang() {
        $categoryList = [
//390 => '沐浴露',
391 => '牙膏',
496 => '吸尘器',
497 => '加湿器',
506 => '电吹风',
507 => '卷/直发器',
547 => '洁面',
548 => '卸妆',
549 => '面部磨砂/去角质',
560 => '化妆水/爽肤水',
562 => '乳液/凝胶',
563 => '面部精华',
564 => '面膜/面膜粉',
//565 => '面部按摩霜',
//566 => '面部护肤套装',
567 => '眼霜',
568 => '眼部精华',
569 => '眼膜',
571 => '眼部护理套装',
573 => '润唇膏',
576 => '其它唇部护理',
626 => '婴幼儿牛奶粉',
632 => '米粉/米糊/汤粥',
//698 => '宝宝防晒乳/露',
//700 => '宝宝洗手液',
714 => '其他婴幼儿洗浴护肤品',
726 => '妆前/隔离',
727 => 'BB霜/CC霜',
728 => '粉底液/膏',
729 => '遮瑕',
730 => '粉饼',
731 => '蜜粉/散粉',
732 => '腮红/胭脂',
//733 => '修颜/高光/阴影粉',
736 => '唇膏/口红',
737 => '唇彩/唇蜜',
752 => '化妆刷/套装',
754 => '化妆棉',
755 => '粉扑',
757 => '修眉刀',
764 => '彩妆套装',
922 => '眼线',
923 => '眼影',
924 => '眉部造型',
925 => '睫毛膏/睫毛增长液',
932 => '洗发水',
933 => '护发素',
934 => '发膜',
935 => '洗护发套装',
937 => '染发剂/膏',
952 => '空气净化/氧吧',
955 => '电风扇',
957 => '扫地机器人',
961 => '其他生活电器',
987 => '笔记本电脑',
988 => '平板电脑',
989 => '电脑配件',
991 => '手机',
994 => '智能手表',
1031 => '男士单肩斜挎包',
1037 => '女士双肩包',
1072 => '围巾',
1073 => '腰带/皮带',
1084 => '女士拖鞋',
1087 => '男士休闲鞋',
1128 => '卫生巾',
1129 => '护垫',
1132 => '其他女性护理',
1160 => '彩漂',
1163 => '抽纸',
1165 => '多用途清洁剂',
1499 => '纸尿裤',
1500 => '拉拉裤',
1725 => '女士帆布鞋/运动鞋',
1790 => '母乳储存保鲜',
1794 => '吸奶器',
1795 => '防溢乳垫',
1797 => '乳头保护罩',
1798 => '产妇卫生巾/护垫',
1800 => '其他产前产后用品',
1803 => '孕产妇牙膏',
1805 => '棉棒/棉签',
1806 => '孕产妇漱口水',
1810 => '宝宝剪刀/指甲钳',
1824 => '孕产妇乳房护理',
1831 => '孕产妇防晒/隔离',
1853 => '孕产妇DHA',
1857 => '宝宝洗衣皂',
//1861 => '宝宝洗衣液',
1872 => '孕吐缓解',
1977 => '防腹泻/抗过敏奶粉',
2091 => '面部清洁套装',
2112 => '防晒',
2116 => '防晒套装',
2225 => '女士单肩手提包',
2226 => '女士手提斜挎包',
2227 => '女士单肩/单肩斜挎包',
2265 => '干洗喷雾',
2278 => '暖风机/取暖器',
2292 => '鼠标',
2293 => '键盘',
2308 => '耳机/耳麦',
2434 => '其他家居清洁工具',
2436 => '洁厕剂',
2439 => '洗衣机槽清洁剂',
2457 => '洗衣粉',
//2458 => '洗衣液',
2461 => '衣物柔顺剂',
5118 => '宝宝洗发/护发',
//5119 => '宝宝沐浴乳/沐浴露',
5125 => '爽身粉 /痱子粉',
5127 => '宝宝润肤乳/按摩油',
5275 => '奶嘴',
5283 => '奶瓶',
5285 => '哺乳文胸',
5292 => '宝宝水杯',
5305 => '孕妇内裤',
5354 => '儿童勺子',
5357 => '催奶发奶产品',
5373 => '孕产妇奶粉',
5413 => '奶瓶果蔬清洗液',
5422 => '孕妇多元营养',
5432 => '儿童驱蚊贴/驱蚊手环',
5437 => '乳牙刷',
5441 => '儿童牙膏',
5443 => '面霜',
5444 => '喷雾',
5465 => '睫毛夹',
5473 => '脱毛工具',
5492 => '颈霜',
5494 => '身体乳/霜',
5495 => '身体护理套装',
5499 => '止汗/去异味',
5500 => '其他身体护理',
5503 => '护手霜',
5509 => '胸部乳霜',
//5510 => '胸部精华/精油',
5522 => '女士香水',
5523 => '男士香水',
5525 => '香水套装',
5531 => '精油',
//5561 => '其他男士护肤',
5608 => '男士手提单肩包',
5688 => '女式板鞋/休闲鞋',
5703 => '时尚耳饰',
5705 => '时尚项链',
5707 => '时尚项坠/吊坠',
5709 => '时尚手镯/手链',
5713 => '时尚戒指/指环',
6246 => '其他饰品',
6259 => '钱包/卡包',
6948 => '时尚串饰',
7672 => '运动背包',
7677 => '运动长裤',
7678 => '运动中短裤',
7679 => '运动POLO衫',
7682 => '运动内衣',
7684 => '运动T恤',
7686 => '运动衬衫/长袖',
7687 => '运动夹克/卫衣',
7991 => '休闲鞋',
7992 => '跑步鞋',
7993 => '篮球鞋',
7994 => '板鞋',
8085 => '运动拖鞋',
9178 => '宝宝湿巾',
11826 => '瑞士腕表',
12558 => '美容仪',
12559 => '塑身仪',
//12729 => '男士洁面',
//12731 => '男士面部清洁套装',
//12732 => '男士爽肤水',
//12733 => '男士面部精华',
//12734 => '男士面部乳霜',
//18021 => '宝宝洗发沐浴二合一',
18258 => '眼罩',
18383 => '太阳眼镜',
18385 => '光学镜架/镜片'];
return array_keys($categoryList);

    }

    private function _matchAsc() {
        $sql = 'select * from fb_category where id in (select distinct(category_id) from fb_product) and category_level = 3';
        //$sql = 'select * from fb_category where category_level = 3';
        $kaolaCategoryList = DB::select($sql);
        //print_r($kaolaCategoryList);
        $websiteHash = $this->_getWebsiteHash();
        unset($websiteHash[7]);
        $websiteAry = array_keys($websiteHash);
        $searchAry = [];
        foreach($kaolaCategoryList as $k => $v) {
            $searchAry[mb_strlen($v->category_name)][] = $v->category_name;
        }
        krsort($searchAry);
        $cl = new SphinxClient ();
        $mode = SPH_MATCH_ALL;
        $cl->SetServer ( env('SPHINX_HOST'), 9312);
        $cl->SetConnectTimeout ( 3 );
        $cl->SetArrayResult ( true );
        $cl->SetFilter('orignal_website_id',$websiteAry);
        $cl->SetMatchMode ( $mode);
        //$cl->SetSortMode(SPH_SORT_EXTENDED,' is_import asc, @id desc ');
        $cl->SetRankingMode ( SPH_RANK_PROXIMITY );
        $pagesize = 20;
        $cl->SetLimits(0,$pagesize);
        $all = 0;
        foreach($searchAry as $k => $v) {
            foreach($v as $m => $n) {
                $res = $cl->Query ( $n, env('SPHINX_INDEX_GOODS') );
                echo $n.':'.($res['total_found'])."\n";
                $all += $res['total_found'];
            }
            //break;
        }
        echo $all;
    }

    private function _matchDesc() {
        $sql = 'select * from fb_product limit 50';
        //$sql = 'select * from fb_category where category_level = 3';
        $productList = DB::select($sql);
        //print_r($kaolaCategoryList);
        /*$websiteHash = $this->_getWebsiteHash();
        unset($websiteHash[7]);
        $websiteAry = array_keys($websiteHash);
        $searchAry = [];
        foreach($kaolaCategoryList as $k => $v) {
            $searchAry[mb_strlen($v->category_name)][] = $v->category_name;
        }
        krsort($searchAry);*/
        $idAry = $this->_getCategoryRang();
        $cl = new SphinxClient ();
        $mode = 1;
        $cl->SetServer ( env('SPHINX_HOST'), 9312);
        $cl->SetConnectTimeout ( 3 );
        $cl->SetArrayResult ( true );
        $cl->SetFilter('cid',$idAry);
        $cl->SetMatchMode ( $mode);
        //$cl->SetSortMode(SPH_SORT_EXTENDED,' is_import asc, @id desc ');
        //$cl->SetRankingMode ( SPH_RANK_PROXIMITY );
        $pagesize = 20;
        $cl->SetLimits(0,$pagesize);
        $all = 0;
        foreach($productList as $k => $v) {
                $product_name = $v->product_name;
                //$product_name = '雅诗兰黛 肌初赋活原生液';
                //$product_name = '眼部精华';
                $res = $cl->Query ( $product_name, env('SPHINX_INDEX_CATEGORY') );
                //$cl->ResetFilters();
                //echo $product_name.':'.($res['total_found'])."\n";
                $cur = 0;
                $curCate = '';
                if(isset($res['matches']) && $res['matches']) {
                    foreach($res['matches'] as $m => $n) {
                        //similar_text($product_name,$n['attrs']['category_name'],$person);
                        $person = similar_text($product_name,$n['attrs']['category_name']);
                        //echo $n['attrs']['category_name'].'----'.$person."\n";
                        if($person > $cur) {
                            $cur = $person;
                            $curCate = $n['attrs']['category_name'];
                        }
                    }
                    echo $product_name.':'.($curCate)."\n";
                    $all += $res['total_found'];    
                } else {
                    echo $product_name."\n";
                }
                //echo $curCate;
                //print_r($res);
            //break;
        }
        echo $all;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->_matchAsc();
    }
}
