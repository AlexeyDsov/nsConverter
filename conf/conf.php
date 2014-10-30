<?php

use Zend\Config\Config;
use Zend\Config\Reader\Ini;

gc_enable();
set_time_limit(0);
ob_implicit_flush();
date_default_timezone_set('Europe/Moscow');

require_once dirname(__DIR__).'/vendor/autoload.php';

//$config = new Config((new Ini())->fromFile(__DIR__.'/base.ini'));
//if (file_exists(__DIR__.'/merge.ini')) {
//	$config->merge(new Config((new Ini())->fromFile(__DIR__.'/merge.ini')));
//}