<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Category;

class CategoryBrandSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CategoryBrandSet {--root_category_id=}';

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
        $rootCategoryId = ($this->option('root_category_id'));
        $categoryObj = new Category;
        $categoryObj->rootCategoryBrandIdsSet($rootCategoryId);
    }
}
