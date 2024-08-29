<?php

$data = $cmd->data ?? (isset($reply_txt) ? $reply_txt : '');

if (empty($data)) {
	$bot->sendMsg($tool_info);

	exit;
}

if (!preg_match("/sk_(test|live)_[A-Za-z0-9]+/", $data, $sk)) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Enter Vaild SK Key</i>!");

	exit;
}

$headers = [
	'authorization: Basic '.base64_encode($sk[0])
];

$api_query = $curlx->Get('https://api.stripe.com/v1/balance', $headers, null, $bot->proxy()['proxy']);

if (!$api_query->success) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>General Server Error</i>!");

	exit;
}

$api_json = (object) json_decode($api_query->body);

if (isset($api_json->error)) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>".($api_json->error->message ?? "This SK Key isn't Vaild")."</i>!");

	exit;
}

$sk = substr($sk[0], 0, 12).'_SWDQYL_'.substr($sk[0], -4);

$msg = "[🔑] <b>SK Key</b> ↯ <code>$sk</code>\n\n" .
	"[📜️] <b>Currency</b> ↯ <i>".strtoupper($api_json->available[0]->currency)."</i>\n".
	"[🏦] <b>Balance (Available - Pending)</b> ↯ <code>".$api_json->available[0]->amount."</code> - <code>".$api_json->pending[0]->amount."</code>";