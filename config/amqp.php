<?php

return [

	'price_host' => [
	    'host' => env('AMQP_PRICE_HOST'),
	    'port' => env('AMQP_PRICE_PORT'),
	    'user' => env('AMQP_PRICE_USER'),
	    'pass' => env('AMQP_PRICE_PASS'),
	    'vhost' => env('AMQP_PRICE_VHOST')
	],
    
    'amqp_price_push' => [
	    "exchange"      => env('AMQP_PRICE_EXCHANGE'),
	    "exchange_type" => "direct",
	    "durable"       => true,
	    "queue_name"    => env('AMQP_PRICE_QUEUE'),
	]
];
