<?php

namespace App;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpConnection extends Model
{
	public $amqpHost;
	public $amqpQueue;
	public $conn;
	public $channel;
	public $exchange;
	public $queue;
    //
    public function __construct($hostName = '') {
    	if(!$hostName) {
    		$this->getDefaultHost();
    	} else {
    		$this->amqpHost = config($hostName);	
    	}
    	if(!$this->amqpHost) {
    		$this->getDefaultHost();
    	}
    	if(!self::connection()) {
    		return false;
    	}
    }

    public function init() {
    	$this->declareChannel();
    	$this->declareExchange();
    	$this->declareQueue();
    	$this->bindQueue();
    }

    public function getDefaultHost() {
    	$this->amqpHost = config('amqp.price_host');
    }

    public function getDefaultQueue() {
    	$this->amqpQueue = config('amqp.amqp_price_push');
    }

    public function connection() {
    	try{
            $this->conn = new AMQPStreamConnection($this->amqpHost['host'], $this->amqpHost['port'], $this->amqpHost['user'], $this->amqpHost['pass'], $this->amqpHost['vhost']); // 创建连接
            return $this->conn;
        }catch(Exception $e){
            return false;
        }
    }

    public function declareExchange() {
    	if(!$this->amqpQueue) {
    		$this->getDefaultQueue();
    	}
    	try{
        	$this->channel->exchange_declare($this->amqpQueue['exchange'], $this->amqpQueue['exchange_type'], false, $this->amqpQueue['durable'], false);
    	}catch(Exception $e) {
    		return false;
    	}
    }

    public function declareChannel() {
    	try{
    		$this->channel = $this->conn->channel();
    	}catch(Exception $e) {
    		return false;
    	}
    }

    public function declareQueue() {
    	if(!$this->amqpQueue) {
    		$this->getDefaultQueue();
    	}
    	try{
    		$this->channel->queue_declare($this->amqpQueue['queue_name'], false, $this->amqpQueue['durable'], false, false);
    	}catch(Exception $e) {
    		return false;
    	}
    }

    public function bindQueue() {

    	if(!$this->amqpQueue) {
    		$this->getDefaultQueue();
    	}
    	try{
    		$this->channel->queue_bind($this->amqpQueue['queue_name'], $this->amqpQueue['exchange']); // 队列和交换器绑定
    	}catch(Exception $e) {
    		return false;
    	}
    }

   	public function __destruct() {
   		if($this->channel) {
   			$this->channel->close();
   		}
   		if($this->conn) {
   			$this->conn->close();
   		}
   	}
}
