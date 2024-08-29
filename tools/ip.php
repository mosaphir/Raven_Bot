<?php

$data = $cmd->data ?? (isset($reply_txt) ? $reply_txt : '');

if (empty($data)) {
	$bot->sendMsg($tool_info);

	exit;
}

if (!preg_match("/((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/", $data, $ip)) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Enter Vaild IP Address</i>!");

	exit;
}

$api_query = $curlx->Get('https://slostapis.000webhostapp.com/Scamalytics/?ip='.$ip[0], null, null, $bot->proxy()['proxy']);

if (!$api_query->success){
	$bot->sendMsg("[❌] <b>Error</b> → <i>General Server Error</i>!");

	exit;
}

$api_json = json_decode(str_replace('n\/a', 'N\/A', $api_query->body)) ?? ['ok' => false];

if (!$api_json->ok) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>IP Data wasn't Found</i>!");

	exit;
}

$country = $api_json->country;

$msg = "[📟] <b>IP</b> ↯ <code>".$ip[0]."</code>\n\n" .
	"[🧮️] <b>ASN</b> ↯ <code>".$api_json->asn."</code>\n".
	"[🖥] <b>ISP</b> ↯ <a href='".$api_json->hostname."'>".$api_json->isp."</a>\n\n" .
	"[⛔] <b>Scam (Risk - Score)</b> ↯ <i>".ucfirst($api_json->risk)."</i> - <code>".$api_json->score."</code>\n\n" .
	"[🏙] <b>City</b> ↯ <i>".$country->city."</i>\n" .
	"[🗽] <b>State</b> ↯ <i>".$country->region."</i>\n" .
	"[📟] <b>Postal Code</b> ↯ <code>".$country->zip."</code>\n" .
	"[🗺] <b>Country</b> ↯ <i>".$country->name."</i> [<code>".$tools->GetFlag($country->code)."</code>]";