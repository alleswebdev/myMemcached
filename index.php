<?php
require_once("memcached.php");

use \Alleswebdev\Tools\Memcached;

ob_implicit_flush();

$app = new Memcached();
$app->connect();
$app->set("www", "asdf") . PHP_EOL;
echo $app->get("www")['value'] . PHP_EOL;
$app->delete("www") . PHP_EOL;
echo $app->stat();
echo PHP_EOL;
$app->close();

echo $app::sendCommandEx("version");
