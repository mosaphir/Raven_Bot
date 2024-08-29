<?php

$data = $cmd->data ?? (isset($reply_txt) ? $reply_txt : '');

if (empty($data)) {
	$bot->sendMsg($tool_info);

	exit;
}

$bin = substr(preg_replace('/\D/', '', $data), 0, 6);

if (strlen($bin) < 6) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Enter Sex Digits Bin Number</i>!");

	exit;
}

$bin_info = $bot->binlookUp($bin);

if (!$bin_info->success){
	$bot->sendMsg("[❌] <b>Error</b> → <i>Bin wasn't Found</i>!");

	exit;
}

$banned = $bot->isBannedBin($bin) ? 'True ❌' : 'False ✅';

$msg = "[📟] <b>Bin</b> ↯ (<code>{$bin}</code>) <code>".$bin_info->scheme."</code> - <code>".$bin_info->type."</code> - <code>".$bin_info->brand."</code>\n" .
	"[🏦] <b>Bank</b> ↯ <i>".$bin_info->bank."</i>\n" .
	"[🗺] <b>Country</b> ↯ <i>".$bin_info->country."</i> [<code>".$bin_info->emoji."</code>]\n" .
	"[⛔] <b>Banned</b> ↯ <i>{$banned}</i>";