<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Check' ,
        'App\Console\Commands\ProductImport' ,
        'App\Console\Commands\KeyWord' ,
        'App\Console\Commands\priceUpdateFromMongodb' ,
        'App\Console\Commands\priceUpdateToMongodb' ,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule( Schedule $schedule )
    {
        // $schedule->command('inspire')
        //          ->hourly();

//        $schedule->command( 'priceUpdateFromMongodb' )
//            ->everyMinute();
//
//        $schedule->command( 'priceUpdateToMongodb' )
//            ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load( __DIR__ . '/Commands' );

        require base_path( 'routes/console.php' );
    }
}
