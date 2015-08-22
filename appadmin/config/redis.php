<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$cache_redis = array();
$cache_redis['host'] = '10.165.120.39';
$cache_redis['port'] = 8879;
$config['cache_redis'] = $cache_redis; 

$db_redis = array();
$db_redis['host'] = '10.165.120.39';
$db_redis['port'] = 8878;
$config['db_redis'] = $db_redis;
