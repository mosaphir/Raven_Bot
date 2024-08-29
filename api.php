<?php

ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

header('content-type: application/json');

if (!isset($_GET['cmd'], $_GET['card'])) exit;

require_once 'config.php';
require_once 'telegram.php';

$bot = new TelegramBot(BOT_TOKEN, BOT_LOGS, BOT_GROUP);

$bot->dbInfo(DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD);

require 'classes/CurlX.php';
require 'classes/Response.php';
require 'classes/Tools.php';
require 'classes/Generator.php';

$curlx = new CurlX;
$response = new Response;
$tools = new Tools;
$generator = new GenCard;

$bot->setChkAPI($curlx, $response, $tools);

$gate = $bot->fetchGate($_GET['cmd']);

if ($gate) {
	$lista = $bot->getCards($_GET['card'])[0];

	$extra = empty($gate['extra']) ? '' : json_decode(''.$gate['extra'].'');

	$result = $bot->chkAPI($gate['file'], $lista, $extra);

	echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

	die();
}

?>