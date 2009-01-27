<?php defined('SYSPATH') or die('No direct script access.');

$config['default'] = array
(
    'benchmark'     => TRUE,
    'persistent'    => FALSE,
    'connection'    => array
    (
        'type'     => 'pgsql',
        'user'     => '*user*',
        'pass'     => '*password*',
        'host'     => '*host*',
        'port'     => *port*,
        'socket'   => FALSE,
        'database' => '*name*'
    ),
    'character_set' => 'utf8',
    'table_prefix'  => '*prefix*',
    'schema'        => '*schema*',
    'object'        => TRUE,
    'cache'         => FALSE,
    'escape'        => TRUE
);