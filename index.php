<?php
require_once("memcached.php");

use \Alleswebdev\Tools\Memcached;

//ob_implicit_flush();

$app = new Memcached();
$app->connect();
echo $app->getVersion() . "\r\n";
$app->close();
echo $app::sendCommandEx("version");
