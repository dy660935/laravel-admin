<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AmqpPublisher;
use App\AmqpConsumer;
use Illuminate\Support\Facades\Redis;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test {--action=}';

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

    public function consumer() {
        $consumerObj = new AmqpConsumer();
        $consumerObj->init();
        $message = $consumerObj->get();
        if($message->body) {
            echo $message->body;
            $consumerObj->ack($message);
        }
    }

    public function publisher() {
        $publisherObj = new AmqpPublisher();
        $publisherObj->init();
        $message = 'rabbitmq';
        $publisherObj->send($message);
    }

    public function redis() {
        $name = Redis::get('zhangbowen');
        if($name) {
            echo $name."\n";
        } else {
            Redis::set('zhangbowen','today is hot');
        }
    }

    public function __call($name,$arguments) {
        echo $name.' action not exists';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->option('action');
        $this->$action();
    }
}
