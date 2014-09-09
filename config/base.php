<?php

$config['debug'] = false;

$config['cache_dir']     = '/var/log/databox';
$config['max_clients']   = getenv('CLIENTS_COUNT') ?: 1;
$config['from_date_str'] = '-' . (getenv('LAST_X_DAYS') ?: 7) . ' days';
$config['to_date_str']   = 'now';
$config['granularity']   = getenv('GRANULARITY') ?: 43200;

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

$config['raw_key_values'] = [
    '$price', '$height', '$weight', '$age', '$power'
];

// Avg. 300 value keys per client
$config['raw_key_count'] = getenv('AVG_KEYS_COUNT') ?: 100;

// Avg. 10 attribute keys per client
$config['raw_attributes_count'] = getenv('AVG_ATTR_COUNT') ?: 10;
