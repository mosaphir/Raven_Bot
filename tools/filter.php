<?php

$data = isset($file_data) ? $file_data : (isset($reply_txt) ? $reply_txt : '');

if (sizeof($cards = $bot->getCards($cmd->data ?? '')) < 1) $cards = $bot->getCards($data);

if (sizeof($cards) < 1) {
	$bot->sendMsg($tool_info);

	exit;
}

$filtered = [];

$bins = [];

$last = [];

foreach ($cards as $card) {
	if (sizeof($filtered) > 99) break;

	$bin = substr($card[0], 0, 6);

	if (strpos($cmd->data, 'flt_gen') !== false && isset($bins[$bin]) && (in_array($bin, array_slice($last, -5, 5, true)) || $bins[$bin]++ >= 5)) continue;

	if (!isset($bins[$bin])) $bins[$bin] = 0;

	$last[] = $bin;

	$filtered[] = implode('|', $card);
}

$msg = "[📑] <b>Total</b> ↯ [<code>".sizeof($filtered)."</code>/<code>".sizeof($cards)."</code>]\n" .
	"<code>" . implode("</code>\n<code>", $filtered) . "</code>";