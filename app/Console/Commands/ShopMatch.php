<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Shop;
use DB;

class ShopMatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ShopMatch';

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
        $sql = "select * from fb_shop where orignal_website_id = 25";
        $shopList = DB::select($sql);
        if($shopList) {
            $shopTmp = [];
            echo count($shopList)."\n";
            foreach($shopList as $k => $v) {
                $shopName = preg_replace('/(\(|\（)(.*)(\)|\）)/','',$v->shop_name)."\n";
                $shopTmp[$shopName][] = $v->id;
            }
            foreach($shopTmp as $k => $v) {
                echo trim($k).' => '.count($v)."\n";
            }
            echo count($shopTmp)."\n";
        }
    }
}
