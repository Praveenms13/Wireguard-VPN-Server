<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/libs/Database.class.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/libs/IPNetwork.class.php";

echo "hey";
exit;
$ip = new IPNetwork("10.0.0.0/24", 'wg0');
$ip->syncNetworkFile();
