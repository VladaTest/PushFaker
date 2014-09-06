<?php

$config['debug'] = false;

$config['cache_dir']     = '/var/log/databox';
$config['max_clients']   = 2;
$config['from_date_str'] = '-30 days';
$config['to_date_str']   = 'now';
$config['granularity']   = 43200;

$config['provider'] = 'dump';

$config['db'] = [
	'ip'       => '{DB_IP}',
	'username' => '{DB_USERNAME}',
	'password' => '{DB_PASSWORD}'
];

$config['operations'] = [
    'push'    => 40,
    'select1' => 30,
    'select2' => 20,
    'select3' => 10
];