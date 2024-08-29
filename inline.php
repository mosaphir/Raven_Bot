<?php

$call_query = $bot->getData()->callback_query;

$call_dt = $call_query->data;
$call_id = $call_query->id;
$call_mi = $call_query->message->message_id;
$call_ci = $call_query->message->chat->id;
$call_fr = $call_query->from->id;
$call_rl = $call_query->message->reply_to_message->from->id ?? '';
$call_us = $call_query->message->reply_to_message->from->username ?? '';
$callrst = $call_query->message->reply_to_message->from->first_name ?? '';

$user_info = $bot->fetchUser($call_rl);

if (!$user_info) {
	$bot->answerCallbackQuery(array(
		'callback_query_id' => $call_id,
		'text' => "Please register first to use me. Use the /register command.",
		'show_alert' => false
	));

	exit;
}

if ($call_fr != $call_rl) {
	$bot->answerCallbackQuery(array(
		'callback_query_id' => $call_id,
		'text' => "Access Denied!",
		'show_alert' => false
	));

	exit;
}

$person = empty($call_us) ? "<a href='tg://openmessage?user_id={$call_fr}'>{$call_fr}</a>" : "@$call_us";
if ($call_dt == "cmds") {
	$bot->editMessageText(array(
		'chat_id' => $call_ci,
		'text' => "Check Menu!!</b>.\n\n",
		'parse_mode' => 'HTML',
		'message_id' => $call_mi,
		'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Menu 🔎', 'callback_data' => 'gates']]]]),
		));
}

if ($call_dt == 'gates') {
	$gates = $bot->fetchGates();

	if (!$gates) {
		$bot->editMessageText(array(
			'chat_id' => $call_ci,
			'text' => "Not Available at The Moment!",
			'parse_mode' => 'HTML',
			'message_id' => $call_mi,
			'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Close', 'callback_data' => 'Close']]]]),
		));

		exit;
	}

	$menus = [
		'auth' => 0,
		'ccn' => 0,
		'charge' => 0,
		'mass' => 0
	];

	$types = [
		'free' => 0,
		'premium' => 0,
		'vip' => 0,
		'credits' => 0
	];

	$online = 0;

	$offline = 0;

	foreach ($gates as $gate) {
		$menus[$gate['menu']]++;

		$types[$gate['type']]++;

		if ($gate['status'] == '✅') $online++;
		else $offline++;
	}
	$gates_menu = "<b>Bot: Running ✅🌧️</b>\n
<b>• Welcome to my command panel,</b>
<b>here you can see my Gateways and Tools.</b>
<b>• Press the buttons to see my commands.</b>\n\n";

	$inline_keyboard = [[]];

	$inline_keyboard[] = [
		[
			'text' => 'Stripe',
			'callback_data' => 'menu stripe'
		],
				[
			'text' => 'Payeezy',
			'callback_data' => 'menu payeezy'
		],
	
	];

	$inline_keyboard[] = [
		[
			'text' => 'Shopify',
			'callback_data' => 'menu shopify'
		],
		[
			'text' => 'Adyen',
			'callback_data' => 'menu adyen'
		]
	];
	
$inline_keyboard[] = [
		[
			'text' => 'Braintree',
			'callback_data' => 'menu b3'
		],
		[
			'text' => 'vBv',
			'callback_data' => 'menu vbv'
		]
	];
	$inline_keyboard[] = [
		[
			'text' => 'Cybersource',
			'callback_data' => 'menu cy'
		],
		[
			'text' => 'PayPal',
			'callback_data' => 'menu paypal'
		]
	];
	$inline_keyboard[] = [
		[
			'text' => 'Square',
			'callback_data' => 'menu square'
		],
		[
			'text' => 'Authorize Net',
			'callback_data' => 'menu auth'
		]
	];
	$inline_keyboard[] = [
		[
			'text' => '↩️', 'callback_data' => 'cmds'
		],
	];

	$bot->editMessageText(array(
		'chat_id' => $call_ci,
		'text' => $gates_menu,
		'parse_mode' => 'HTML',
		'message_id' => $call_mi,
		'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard]),
	));
}

if (preg_match('/^menu/', $call_dt)) {
	$split = explode(' ', $bot->cmd("/$call_dt")->data ?? '');

	$menu = $split[0];

	$page = $split[1] ?? 0;

	$gates = $bot->fetchGates();

	if (!$gates) {
		$bot->editMessageText(array(
			'chat_id' => $call_ci,
			'text' => "<b>No Gates Available At This Time! </b>",
			'parse_mode' => 'HTML',
			'message_id' => $call_mi,
			'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Close', 'callback_data' => 'gates']]]]),
		));

		exit;
	}

	$gates_menu = [];

	foreach ($gates as $gate) {
		if ($gate['menu'] == $menu) $gates_menu[] = $gate;
	}

	$menu_msg = "<b>All Gates Are Made With Love And Hard Work :)</b>\n\n";

	$start = $page * 10;

	$last = $start + 10;

for ($i = $start; $i < $last; $i++) {
		if (isset($gates_menu[$i])) $menu_msg .= " -<b>Command</b> : <b>/".$gates_menu[$i]['cmd']."</b>\n" .
			"-<b>✅ Active </b> | <b>".$gates_menu[$i]['name']."</b>\n\n";

}

	$inline_keyboard = [[]];

	if ($page > 0) $inline_keyboard[0][] = ['text' => 'Previous', 'callback_data' => "menu $menu ".($page - 1).""];
	if (isset($gates_menu[$last++])) $inline_keyboard[0][] = ['text' => 'Next', 'callback_data' => "menu $menu ".($page + 1).""];

	$inline_keyboard[] = [
		[
			'text' => '↩️', 'callback_data' => 'gates'
		]
	];

	$bot->editMessageText(array(
		'chat_id' => $call_ci,
		'text' => $menu_msg,
		'parse_mode' => 'HTML',
		'message_id' => $call_mi,
		'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard]),
	));
}

if (preg_match('/^tools/', $call_dt)) {
	$page = intval(preg_replace('/\D/', '', $call_dt));

	$tools = $bot->fetchTools();

	if (!$tools) {
		$bot->editMessageText(array(
			'chat_id' => $call_ci,
			'text' => "<b>Not Available at The Moment! </b>",
			'parse_mode' => 'HTML',
			'message_id' => $call_mi,
			'reply_markup' => json_encode(['inline_keyboard' => [[['text' => 'Gates', 'callback_data' => 'gates']], [['text' => 'Home', 'callback_data' => 'cmds'], ['text' => 'Close', 'callback_data' => 'exit']]]]),
		));

		exit;
	}

	$tools_menu = "[❃] <b>".BOT_NAME."</b>  → <i>Tools</i> → [<code>".($page + 1)."</code>/<code>".ceil(sizeof($tools) / 5)."</code>]\n\n";

	$start = $page * 5;

	$last = $start + 5;

for ($i = $start; $i < $last; $i++) {
		if (isset($tools[$i])) $tools_menu .= "[".$tools[$i]['status']."] <b>".$tools[$i]['name']."</b>  - [<i>".strtoupper($tools[$i]['type'])."</i>]\n" .
			"<b>Info</b>  - <i>".$tools[$i]['info']."</i>\n" .
			"<b>Command</b>  - <code>/".$tools[$i]['cmd'].(empty($tools[$i]['format']) ? "" : " ".$tools[$i]['format'])."</code>\n\n";
	}

	$inline_keyboard = [[]];

	if ($page > 0) $inline_keyboard[0][] = ['text' => 'Previous', 'callback_data' => "tools".($page - 1).""];
	if (isset($tools[$last++])) $inline_keyboard[0][] = ['text' => 'Next', 'callback_data' => "tools".($page + 1).""];

	$inline_keyboard[] = [
		[
			'text' => 'Home', 'callback_data' => 'cmds'
		],
		[
			'text' => 'Close', 'callback_data' => 'exit'
		]
	];

	$bot->editMessageText(array(
		'chat_id' => $call_ci,
		'text' => $tools_menu,
		'parse_mode' => 'HTML',
		'message_id' => $call_mi,
		'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard]),
	));
}

if ($call_dt == "exit") {
	$bot->editMessageText(array(
		'chat_id' => $call_ci,
		'text' => "<b>Menu Closed</b> → <b>Good Bye <a href='tg://user?id=$call_rl'>$callrst</a></b>!",
		'parse_mode' => 'HTML',
		'message_id' => $call_mi,
	));
}

if (preg_match('/^cut_dead/', $call_dt)) {
	$org = $call_query->message->text;

	$cmd = explode('[', $bot->getstr($org, '↯', ']'))[1];

	$gate = $bot->fetchGate($cmd);

	if (!$gate) exit;

	$live = $bot->getstr($org, 'Lives', '═');

	preg_match_all("/Card ↯ (.*?)\n/", $live, $cards);

	preg_match_all("/Message ↯ \[(.*?)\]/", $live, $msgs);

	preg_match_all("/Taken ↯ (.*?)\|/", $live, $tkns);

	preg_match_all("/Retry ↯ (.*?)\n/", $live, $retrys);

	preg_match_all("/\[(.*?)\] Proxy ↯ \[(.*?)\]/", $live, $proxies);

	preg_match_all("/Credits ↯ (.*?)\n/", $live, $credits);

	$live_text = "";

	for ($i = 0; $i < sizeof($cards[1]); $i++) {
		$live_text .= "<b>● 𝗖𝗖 »</b><code>".trim($cards[1][$i])."</code>\n" .
			"<b>● 𝗥𝗲𝘀𝘂𝗹𝘁 »</b>[ <i>".trim($msgs[1][$i])."</i> ]\n" .
			"<b>● 𝗧𝗼𝗼𝗸</b>[ <code>".trim($tkns[1][$i])."</code> ]\n\n";
	}

	preg_match_all("/Checked » \[(.*?)\]/", $org, $checked);

	$lives = intval(explode('Lives »', $org)[1] ?? '');

	$deads = intval(explode('Deads »', $org)[1] ?? '');

	$msg = "<i>MASS</i> [<code>/mass</code>]\n" .
		"<b>● 𝗚𝗮𝘁𝗲𝘄𝗮𝘆 »</b><i>".$gate['name']."</i>\n" .
		"<b>Checked »</b> ↯ [".($checked[1][0] || ($lives + $deads))."]\n" .
		"<b>Lives✅</b> » <code>$lives</code>\n" .
		"$live_text\n" .
		"<b>Deads❌</b> » <code>$deads</code>\n\n" .
		"<b>Checked By</b> » $person [<code>".strtoupper($user_info['plan'])."</code>]\n" .
		"<b>Author</b> »    <i>".BOT_OWNER_NAME."</i>\n";

	$msg = preg_replace("/\n\n\n/", "\n\n", $msg);

	$bot->editMessageText(array(
		'chat_id' => $call_ci,
		'text' => $msg,
		'parse_mode' => 'HTML',
		'message_id' => $call_mi,
	));
}

if ($call_dt == "remove") {
	$bot->deleteMessage(array(
		'chat_id' => $call_ci,
		'message_id' => $call_mi,
	));
}

?>