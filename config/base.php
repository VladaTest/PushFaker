<?php

$config['debug'] = false;

$config['cache_dir']     = '/var/log/databox';
$config['max_clients']   = 2;
$config['from_date_str'] = '-30 days';
$config['to_date_str']   = 'now';
$config['granularity']   = 43200;

$config['provider'] = getenv('PROVIDER');

$config['db'] = [
	'ip'       => getenv('DB_IP'),
	'username' => 'loadtester',
	'password' => 'testing4fun'
];

$config['operations'] = [
    'push'    => 40,
    'select1' => 30,
    'select2' => 20,
    'select3' => 10
];
