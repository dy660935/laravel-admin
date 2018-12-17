<?php

namespace App;

use App\AmqpConnection;

class AmqpConsumer extends AmqpConnection
{
	public function __construct($hostName = '') {
		parent::__construct($hostName);
	}
    //
    public function init() {
    	$this->declareChannel();
    	$this->declareQueue();
    }

    public function get() {
    	return $this->channel->basic_get($this->amqpQueue['queue_name']);
    }

    public function ack($message) {
        $this->channel->basic_ack($message->delivery_info['delivery_tag']);
    }

    public function __destruct() {
    	parent::__destruct();
    }
}
