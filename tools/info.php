<?php

$data = $reply_id ?? $cmd->data ?? $chat_id;

$data = preg_replace('/[^\d-]/', '', $data);

$tg_info = $bot->getChat(array('chat_id' => $data));

if (isset($tg_info->ok) && !$tg_info->ok) {
	$bot->sendMsg("[❌] Error → ".$tg_info->description.'! '.$tg_info->error_code."");

	exit;
}

$db_info = $bot->fetchUser($data);

$msg = "";

if ($tg_info->type == 'private') {
	$msg .= "[☇] <b>Name</b> → ".$tg_info->first_name."\n" .
	(isset($tg_info->username) ? "[☇] <b>Username</b> → @".$tg_info->username."\n" : "");
} else {
	$msg .= "[☇] <b>Title</b> → ".$tg_info->title."\n" .
	(isset($tg_info->username) ? "[☇] <b>Username</b> → @".$tg_info->username."\n" : "");
}

$msg .= "[☇] <b>ID</b> → [<code>".$tg_info->id."</code>]";

if ($db_info) $msg .= "\n[☇] <b>Plan</b> → <i>".strtoupper($db_info['plan'])."</i>\n" .
	(strtolower($db_info['plan']) != "free" ? "[☇] <b>Expiration</b> → <i>".date('Y-m-d h:i:s A', $db_info['expiry'])."</i>\n" : "") .
	"[☇] <b>Anti Spam</b> → [<code>{$antispam}</code>]" .
	($db_info['credits'] > 0 ? "\n[☇] <b>Credits</b> → [<code>".$db_info['credits']."</code>]\n[☇] <b>Mass Limit</b> → [<code>{$mass_limit}</code>]" : "");

?>