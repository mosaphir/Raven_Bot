<?php

$msg = "[☇] <b>Name</b> → {$first_n}\n";

if (!empty($usern_n)) $msg .= "[☇] <b>Username</b> → <i>@{$usern_n}</i>\n";

$msg .= "[☇] <b>ID</b> → [<code>{$user_id}</code>]\n" .
	"[☇] <b>Plan</b> → <i>".strtoupper($user_info['plan'])."</i>\n";

if (strtolower($user_info['plan']) != "free") $msg .= "[☇] <b>Expiration</b> → <i>".date('Y-m-d h:i:s A', $user_info['expiry'])."</i>\n";

$msg .= "[☇] <b>Anti Spam</b> → [<code>{$antispam}</code>]";

if ($user_info['credits'] > 0) $msg .= "\n[☇] <b>Credits</b> → [<code>".$user_info['credits']."</code>]\n[☇] <b>Mass Limit</b> → [<code>{$mass_limit}</code>]";

?>