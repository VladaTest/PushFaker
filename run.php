<?php

/**
 * ENVIRONMENT VARIABLE     Default
 * =================================
 *  PROVIDER                required
 *  DB_IP                   required
 *
 *  CLIENTS_COUNT           1
 *  LAST_X_DAYS             7
 *  GRANULARITY             43200
 *  AVG_KEYS_COUNT          100
 *  AVG_ATTR_COUNT          10
 */

// Default errors
error_reporting(E_ALL);

// Default timezone of server
date_default_timezone_set('UTC');

// Absolute path to the system folder
define('PATH', realpath(__DIR__) . '/');

require('vendor/autoload.php');
require('StatsD.php');

function config($file = 'config/base.php') {
    static $configs = [];

    if (empty($configs[$file])) {
        require(PATH . $file);

        $configs[$file] = (object) $config;
    }

    return $configs[$file];
}

// create cache_dir
if (!file_exists(config()->cache_dir)) {
    @mkdir(config()->cache_dir);
}

// Get clients
$clients = array_diff(scandir(config()->cache_dir), ['.', '..']);

$client     = null;
$clientData = null;
$operation  = null;

if (count($clients) < config()->max_clients) {
    // Create new client
    $client     = bin2hex(openssl_random_pseudo_bytes(16));
    $clientData = [
        'id'          => $client,
        'created'     => date('Y-m-d H:i:s'),
        'from'        => date('Y-m-d H:i:s', strtotime(config()->from_date_str)),
        'granularity' => config()->granularity
    ];

    $operation = 'push';

} else {
    // Select random client
    $client     = $clients[array_rand($clients)];
    $clientData = json_decode(file_get_contents(config()->cache_dir . '/' . $client), true);
}

// Get operation based on distribution
if (null === $operation) {
    $rand = mt_rand(1, (int) array_sum(config()->operations));

    foreach (config()->operations as $key => $value) {
        $rand -= $value;
        if ($rand <= 0) {
            $operation = $key;
            break;
        }
    }
}

$provider = Provider::factory(config()->provider, $clientData);

PHP_Timer::start();
$provider->run($operation);
$time = PHP_Timer::stop();

// Save current status
$clientData       = $provider->getData();
$clientData['to'] = date('Y-m-d H:i:s', time());
file_put_contents(config()->cache_dir . '/' . $client, json_encode($clientData));

// Push time + request
StatsD::increment($operation . ' - count');
StatsD::timing($operation . ' - response time', str_replace(' ms', '', PHP_Timer::secondsToTimeString($time)));
