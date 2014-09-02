<?php

require('vendor/autoload.php');

$args = getopt('', [
    'space_id:',
    'access_id:'
]);

if (!isset($args['space_id'])) {
    throw new InvalidArgumentException('Missing space_id');
}

if (!isset($args['access_id'])) {
    throw new InvalidArgumentException('Missing access_id');
}

$spaceID  = (int) $args['space_id'];
$accessId = (int) $args['access_id'];

