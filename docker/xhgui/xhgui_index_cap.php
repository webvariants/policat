#!/usr/bin/env php
<?php

sleep(2); // wait some for mongodb startup

$max_size = getenv('XHGUI_INDEX_CAP');
if ($max_size === false || strlen($max_size) === 0) {
    $max_size = 50 * 1024 * 1024;  // default 50 MB
} else {
    $max_size = (int) $max_size;
}

echo 'Index cap: ' . $max_size . "\n";

define('XHGUI_CONFIG_DIR', '/xhgui/config/');
require_once '/xhgui/vendor/autoload.php';
require_once '/xhgui/vendor/perftools/xhgui-collector/src/Xhgui/Config.php';
Xhgui_Config::load(XHGUI_CONFIG_DIR . 'config.php');

require '/xhgui/src/bootstrap.php';

$config = Xhgui_Config::all();
$saver = Xhgui_Saver::factory($config);
$saver->save([]);

$mongo = new MongoClient($config['db.host'], $config['db.options']);

$result = $mongo->{$config['db.db']}->command([
    'convertToCapped' => 'results',
    'size' => $max_size
]);

if ($result == ['ok' => 1]) {
    echo 'success';
} else {
    echo 'fail';
    print_r($result);
}
echo "\n";
