<?php

// Default errors
error_reporting(E_ALL);

// Default timezone of server
date_default_timezone_set('UTC');

require('vendor/autoload.php');
require('config.php');

// create cache_dir
if (!file_exists($config['cache_dir'])) {
    mkdir($config['cache_dir']);
}

// Get clients
$clients = array_diff(scandir($config['cache_dir']), ['.', '..']);

$client     = null;
$clientData = null;
$operation  = null;

if (count($clients) < $config['max_clients']) {
    // Create new client
    $client     = bin2hex(openssl_random_pseudo_bytes(16));
    $clientData = [
        'id'          => $client,
        'created'     => date('Y-m-d H:i:s'),
        'from'        => date('Y-m-d H:i:s', strtotime($config['from_date_str'])),
        'granularity' => $config['granularity']
    ];

    $operation = 'push';

} else {
    // Select random client
    $client     = $clients[array_rand($clients)];
    $clientData = json_decode(file_get_contents($config['cache_dir'] . '/' . $client), true);
}

// Get operation based on distribution
if (null === $operation) {
    $rand = mt_rand(1, (int) array_sum($config['operations']));

    foreach ($config['operations'] as $key => $value) {
        $rand -= $value;
        if ($rand <= 0) {
            $operation = $key;
            break;
        }
    }
}

$provider = Provider::factory($config['provider'], $clientData);

PHP_Timer::start();
$provider->run($operation);
$time = PHP_Timer::stop();

// Save current status
$clientData       = $provider->getData();
$clientData['to'] = date('Y-m-d H:i:s', time());
file_put_contents($config['cache_dir'] . '/' . $client, json_encode($clientData));

// Push time + request
//
