<?php

$msg = "";

if (!empty($chat_tt)) $msg .= "[☇] <b>Title</b> → {$chat_tt}\n";

if (!empty($chat_un)) $msg .= "[☇] <b>Username</b> → <i>@{$chat_un}</i>\n";

$msg .= "[☇] <b>ID</b> → [<code>{$chat_id}</code>]" .
	($chat_info ? "\n[☇] <b>Plan</b> → <i>".strtoupper($chat_info['plan'])."</i>" : "");

if ($chat_info && strtolower($chat_info['plan']) != "free") $msg .= "\n[☇] <b>Expiration</b> → <i>".date('Y-m-d h:i:s A', $chat_info['expiry'])."</i>";

if ($chat_info && $chat_info['credits'] > 0) $msg .= "\n[☇] <b>Credits</b> → [<code>".$chat_info['credits']."</code>]";

?>