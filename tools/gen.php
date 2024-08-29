<?php

$data = $cmd->data ?? (isset($reply_txt) ? $reply_txt : '');

if (empty($data)) {
	$bot->sendMsg($tool_info);

	exit;
}

$data = trim(preg_replace('/[^\dx ]/', '\n', strtolower($data)));

$data = trim(preg_replace('/\n+/', '\n', strtolower($data)));

$split = explode(' ', $data);

if (!preg_match_all('/[\dx]+/', $split[0], $digits) && !preg_match_all('/[\dx]+/', $data, $digits)) {
	$bot->sendMsg($tool_info);

	exit;
}

$extra = false;

$mm = null;

$yy = null;

$cvv = null;

$amo = null;

foreach ($digits[0] as $digit) {
	if (preg_match('/^[3456]\d{4}[\dx]+/', $digit)) {
		$extra = substr($digit, 0, 16);

		$mm = null;

		$yy = null;

		$cvv = null;
	} elseif ($extra) {
		if (!$mm && preg_match('/^([1-9]|0[1-9]|1[012])$/', $digit)) {
			$mm = $digit;
		} elseif (!$yy && preg_match('/^(20(2[2-9]|3[0-5])|(2[2-9]|3[0-5]))$/', $digit)) {
			$yy = $digit;
		} elseif (!$mm && !$yy && preg_match('/^([1-9]|0[1-9]|1[012])(20(2[2-9]|3[0-5])|(2[2-9]|3[0-5]))$/', $digit)) {
			preg_match('/(20(2[2-9]|3[0-5])|(2[2-9]|3[0-5]))$/', $digit, $match);

			$yy = $match[0];

			preg_match('/^(1[012]|[1-9]|0[1-9])/', preg_replace("/{$yy}$/", "", $digit), $match);

			$mm = $match[0];
		} elseif (!$mm && !$yy && preg_match('/^(20(2[2-9]|3[0-5])|(2[2-9]|3[0-5]))([1-9]|0[1-9]|1[012])$/', $digit)) {
			preg_match('/^(20(2[2-9]|3[0-5])|(2[2-9]|3[0-5]))/', $digit, $match);

			$yy = $match[0];

			preg_match('/(1[012]|[1-9]|0[1-9])$/', preg_replace("/^{$yy}/", "", $digit), $match);

			$mm = $match[0];
		} elseif (!$cvv && preg_match('/^\d{3,4}$/', $digit)) {
			$cvv = $digit;
		} else $amo = $digit;
	}

	if (!$extra || !$mm || !$yy || !$cvv) continue;

	$mm = substr("0{$mm}", -2);

	$yy = substr("20{$yy}", -4);
}

if (strlen($extra) < 6) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Enter At Least Sex Digits Bin or Extra Number</i>!");

	exit;
}

$len = substr_compare($extra, 37, 0, 2) ? 16 : 15;

if (is_numeric($extra) && strlen($extra) == $len) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Same Card Number, Try Another Bin or Extra</i>!");

	exit;
}

$mm ??= 'rnd';
$yy ??= 'rnd';
$cvv ??= 'rnd';

$amo = intval($amo ?? $split[1] ?? 10);

if (empty($amo)) $amo = 10;

$cards = $generator->Gen($extra, $mm, $yy, $cvv, $amo);

$msg = "[⚙] <b>Format</b> → <code>$extra|$mm|$yy|$cvv $amo</code>\n" .
	"[🎰] <b>Amount</b> → <code>".sizeof($cards)."</code>\n\n";

foreach ($cards as $card) {
	$msg .= "<code>$card</code>\n";
}

$bin = substr($extra, 0, 6);

$bin_info = $bot->binlookUp($bin);

$banned = $bot->isBannedBin($bin) ? 'True ❌' : 'False ✅';

$msg .= "\n[📟] <b>Bin</b> ↯ (<code>{$bin}</code>) <code>".$bin_info->scheme."</code> - <code>".$bin_info->type."</code> - <code>".$bin_info->brand."</code>\n" .
	"[🏦] <b>Bank</b> ↯ <i>".$bin_info->bank."</i>\n" .
	"[🗺] <b>Country</b> ↯ <i>".$bin_info->country."</i> [<code>".$bin_info->emoji."</code>]\n" .
	"[⛔] <b>Banned</b> ↯ <i>{$banned}</i>";