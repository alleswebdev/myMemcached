<?php
require_once("lib/Memcached.php");

use \Alleswebdev\Tools\Memcached;

ob_implicit_flush();

$app = new Memcached();
$app->connect();
$app->set("testkey", "testdata 222 111 32412");
echo $app->get("testkey")['value'] . PHP_EOL;
$app->delete("testkey") . PHP_EOL;
echo $app->stat();
echo PHP_EOL;
$app->close();

echo $app::sendCommandEx("version");
