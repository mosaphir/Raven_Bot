<?php

$nat = $cmd->data ?? (isset($reply_txt) ? $reply_txt : '');

if (empty($nat)) {
	$bot->sendMsg($tool_info);

	exit;
}

$fake_api = $curlx->Get("https://randomuser.me/api/1.2/?nat={$nat}", null, null, $bot->proxy()['proxy']);

if (!$fake_api->success){
	$bot->sendMsg("[❌] <b>Error</b> → <i>Country wasn't Found</i>!");

	exit;
}

$fake = json_decode($fake_api->body)->results[0];

if (empty($country = Locale::getDisplayRegion("-".$fake->nat, 'en'))) {
	$bot->sendMsg("[❌] <b>Error</b> → <i>Enter Vaild Country Code</i>!");

	exit;
}

$name = $fake->name;

$loc = $fake->location;

$msg = "[👤] <b>Name</b> ↯ <code>".ucfirst($name->title)."</code>. <code>".ucfirst($name->first)."</code> <code>".ucfirst($name->last)."</code>\n\n" .
	"[📧] <b>Email</b> ↯ <code>".$fake->email. "</code>\n" .
	"[☎️] <b>Phone</b> ↯ <code>".$fake->phone. "</code>\n\n".
	"[🛣] <b>Street</b> ↯ <code>".$loc->street."</code>\n" .
	"[🏙] <b>City</b> ↯ <code>".ucfirst($loc->city)."</code>\n" .
	"[🗽] <b>State</b> ↯ <code>".ucfirst($loc->state)."</code>\n" .
	"[📟] <b>Postal Code</b> ↯ <code>".$loc->postcode."</code>\n" .
	"[🗺] <b>Country</b> ↯ <code>{$country}</code> [<code>".$tools->GetFlag($fake->nat)."</code>]";