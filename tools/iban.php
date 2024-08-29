<?php

$data = $cmd->data ?? (isset($reply_txt) ? $reply_txt : '');

if (empty($data)) {
	$bot->sendMsg($tool_info);

	exit;
}

if (!preg_match("/([A-Z]{2}[ '+'\\'+'-]?[0-9]{2})(?=(?:[ '+'\\'+'-]?[A-Z0-9]){9,30}$)((?:[ '+'\\'+'-]?[A-Z0-9]{3,5}){2,7})([ '+'\\'+'-]?[A-Z0-9]{1,3})?/", $data, $iban)) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Enter Vaild IBAN</i>!");

	exit;
}

$api_query = $curlx->Get('https://openiban.com/validate/'.$iban[0].'?getBIC=true&validateBankCode=true', null, null, $bot->proxy()['proxy']);

if (!$api_query->success) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>General Server Error</i>!");

	exit;
}

$api_json = json_decode($api_query->body) ?? ['valid' => false];

if (!$api_json->valid) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>".(isset($api_json->messages) ? implode(', ', $api_json->messages) : "This IBAN isn't Vaild")."</i>!");

	exit;
}

$bank = $api_json->bankData;

$msg = "[📟] <b>IBAN</b> ↯ <code>".$iban[0]."</code>\n\n" .
	"[📜️] <b>Messages</b> ↯ <i>".implode(', ', $api_json->messages)."</i>!\n\n".
	"[🏦] <b>Bank (Name - Code - BIC)</b> ↯ <i>".($bank->name ?? 'N/A')."</i> - <i>".($bank->bankCode ?? 'N/A')."</i> - <i>".($bank->bic ?? 'N/A')."</i>";