<?php

namespace App;

use App\AmqpConnection;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpPublisher extends AmqpConnection
{
	public function __construct($hostName = '') {
		parent::__construct($hostName);
	}
    
    public function init() {
    	$this->declareChannel();
    	$this->declareQueue();
    	$this->declareExchange();
    	$this->bindQueue();
    }

    public function send($message) {
    	if(is_array($message)) {
    		$message = json_encode($message);
    	} else {
    		$message = strval($message);
    	}
    	$msg = new AMQPMessage($message, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        return $this->channel->basic_publish($msg, $this->amqpQueue['exchange']); // 推送消息  
    }

    public function __destruct() {
    	parent::__destruct();
    }
}
