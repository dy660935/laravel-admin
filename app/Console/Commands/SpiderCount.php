<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Illuminate\Support\Facades\Redis;

class SpiderCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SpiderCount  {--filter=}';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [];
        $filter = ($this->option('filter'));
        if($filter) {
            $redisKey = 'SpiderCountFilter';
        } else {
            $redisKey = 'SpiderCount';
        }
        $data['filter'] = $filter;
        $collectionAry = ['american','asos','farfetch','feelunique','haitao_amazon','harrods','jd','jd_global','jxk','kaola','lookfantastic','macys','net_a_porter','optimism_mail','selfridges','sephora','shopbop','skinstore','tmall','tmallhk','vip','vip_global'];
        $strlen = 50;
        $allCount = $count = 0;
        foreach($collectionAry as $k => $v) {
            if(!$filter) {
                $count = DB::connection('mongodb') -> collection($v)->count();
            } else {
                if($v == 'jxk') {
                    $count = DB::connection('mongodb') -> collection($v)->whereIn('brandID',[74,49,71,66,39,29,69,31,1,46,15,7,38,93,48,80,67,42,58,10,13,76,5,35,41,54,59,61,70,77,78,85,9,11,14,19,33,55,62,65,73,91,97,64,72])->whereNotIn('country',['墨西哥','巴西','摩洛哥','智利','秘鲁','芬兰','克罗地亚','爱尔兰','波兰','丹麦','瑞典','卢森堡','斯洛伐克','奥地利','葡萄牙','特立尼达和多巴哥共和国','摩纳哥','挪威','印度','菲律宾','印度尼西亚','捷克','匈牙利'])->where('is_onsell',1)->count();
                } else {
                    $count = DB::connection('mongodb') -> collection($v)->whereIn('brandID',[74,49,71,66,39,29,69,31,1,46,15,7,38,93,48,80,67,42,58,10,13,76,5,35,41,54,59,61,70,77,78,85,9,11,14,19,33,55,62,65,73,91,97,64,72])->where('is_onsell',1)->count();
                }
            }
            $data['data'][$v] = $count;
            echo $v.str_repeat(' ',$strlen - strlen($v)).$count."\n";
            $allCount += $count;
        }
        $data['count'] = $allCount;
        $data['update'] = date('Y-m-d H:i:s');
        //Redis::setex($redisKey,60,json_encode($data));
        Redis::set($redisKey,json_encode($data));
        echo 'count:'.$allCount;
    }
}
