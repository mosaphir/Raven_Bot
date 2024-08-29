<?php 

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

/**
 * @package TelegramBot
 * @version 1.0
 * @author SHINCHAN
 * @copyright Copyright (c) 2022
 */

class TelegramBot {
	protected string $token;
	protected string $data;
	protected object $client;

	protected string $logs_channel;
	protected string $bot_group;

	protected string $db_server;
	protected string $db_name;
	protected string $db_user;
	protected string $db_pass;

	protected object $conn;

	protected object $curlx;
	protected object $response;
	protected object $tools;

	public function __construct(string $token, $logs_channel = null, $bot_group = null) {
		$this->token = $token;

		$this->client = new Client(["http_errors" => false, "base_uri" => "https://api.telegram.org/bot".$this->token."/"]);

		if (isset($logs_channel)) $this->logs_channel = $logs_channel;

		if (isset($bot_group)) $this->bot_group = $bot_group;
	}

	public function setData($data = '') {
		$this->data = $data;
	}

	public function getData() {
		return json_decode($this->data);
	}

	public function bot($method, $data) {
		$request = $this->client->postAsync($method, ['query' => $data])->wait();

		if ($request->getStatusCode() == 200) {
			return json_decode($request->getBody()->getContents())->result;
		} else {
			return $request->getBody()->getContents();
		}
	}

	public function dbInfo(string $server, string $name, string $user, string $pass) {
		$this->db_server = $server;
		$this->db_name = $name;
		$this->db_user = $user;
		$this->db_pass = $pass;
	}

	public function logSummary($logs) {
		if (!isset($this->logs_channel)) return;

		$this->sendMessage(array(
			'chat_id' => $this->logs_channel,
			'text' => $logs,
			'parse_mode' => 'HTML'
		));
	}

	public function dbConn() {
		try {
			$connect = new PDO("mysql:host=".$this->db_server."; dbname=".$this->db_name, $this->db_user, $this->db_pass);

			$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			return $connect;
		} catch (PDOException $e) {
			$this->logSummary("[☇] <b>Error</b> → <i>The Following Error Occurred During DB Connection:</i>\n<code>".$e."</code>");

			exit;
		}
	}

	public function cmd($message) {
		if (!preg_match('/^([^-\p{L}\x00-\x7F]+|[^A-Za-z ]+)[A-Za-z]+/', $message, $cmd)) return False;

		$data = trim(substr($message, strlen($cmd[0])));

		return (object) [
			'cmd' => strtolower(preg_replace('/([^-\p{L}\x00-\x7F]+|\W+)/', '', $cmd[0])),
			'data' => empty($data) ? null : $data
		];
	}

	public function getCards($string = null) {
		if (gettype($string) != 'string') return [];

		if (!preg_match_all('/\d+/', $string, $digits)) return [];

		$cards = [];

		$cc = false;

		$mm = false;

		$yy = false;

		$cvv = false;

		foreach ($digits[0] as $digit) {
			if (preg_match('/^(3\d{14}|[456]\d{15})$/', $digit)) {
				$cc = $digit;

				$mm = false;

				$yy = false;

				$cvv = false;
			} elseif ($cc) {
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
				}
			}

			if (!$cc || !$mm || !$yy || !$cvv) continue;

			$mm = substr("0{$mm}", -2);

			$yy = substr("20{$yy}", -4);

			if (strpos(json_encode($cards), $cc) === false) $cards[] = [$cc, $mm, $yy, $cvv];
		}

		return $cards;
	}

	public function luhnCheck($ccnum) {
		$checksum = 0;

		for ($i = 2 - (strlen($ccnum) % 2); $i <= strlen($ccnum); $i += 2) {
			$checksum += (int) $ccnum[$i - 1];
		}

		for ($i = (strlen($ccnum) % 2) + 1; $i < strlen($ccnum); $i += 2) {
			$digit = (int) $ccnum[$i - 1] * 2;

			if ($digit < 10) {
				$checksum += $digit;
			} else {
				$checksum += $digit - 9;
			}
		}
	
		if ($checksum % 10 == 0):
			return true;
		else:
			return false;
		endif;
	}

  public function proxy() {
      $proxy = [
        'rp.proxyscrape.com:6060:ma0mgm8mslr4mxb:zt0gjkb2u5dikhk',
      ];

      $result = [
          'proxy' => $proxy,
          'emoji' => '❌',
          'status' => 'DEAD',
          'ip' => '123.xxx.789'
      ];

      $ipify_query = $this->curlx->get('https://api.ipify.org/', null, null, $proxy);

      if (!$ipify_query->success || !filter_var($ipify_query->body, FILTER_VALIDATE_IP)) {
          return $result;
      }

      $result['ip'] = preg_replace('/\..*\./', '.xxx.', $ipify_query->body);
      $result['emoji'] = "✅";
      $result['status'] = "LIVE";

      return $result;
  }

	public function binlookUp($bin) {
		$bin = substr($bin, 0, 8);

		$bin_data = $this->curlx->Get("https://lookup.binlist.net/{$bin}", null, null, $this->proxy()['proxy']);

		$success = $bin_data->success && $bin_data->code == 200;

		$decode_api = $success ? json_decode($bin_data->body, true) : [];

		return (object) [
			"success" => $success,
			"scheme" => strtoupper($decode_api["scheme"] ?? "N/A"),
			"type" => strtoupper($decode_api["type"] ?? "N/A"),
			"brand" => strtoupper($decode_api["brand"] ?? "N/A"),
			"bank" => ($decode_api["bank"]["name"] ?? "N/A"),
			"country" => ($decode_api["country"]["name"] ?? "N/A"),
			"emoji" => ($decode_api["country"]["emoji"] ?? "🏳")
		];
	}
       public function isBannedBin($bin = null) {
		if (gettype($bin) != 'string') return false;

		$bin = substr($bin, 0, 6);

		$bins_file = file_get_contents('banned_bins.txt');

		$banned_bins = explode("\n", $bins_file);

		return in_array($bin, $banned_bins);

}
	public function banBin($bin = null) {
		$admin_id = $this->getData()->message->from->id;

		if (!$this->fetchUser($admin_id) || strtolower($this->fetchUser($admin_id)['range']) != 'owner') exit;

		if (!preg_match('/\d{6}/', $bin, $match)) {
			$this->sendMsg("<i>Enter Vaild Bin Number!</i>");

			exit;
		}

		$banned_bin = $match[0];

		$bins_file = file_get_contents('banned_bins.txt');

		$banned_bins = explode("\n", $bins_file);

		if (in_array($banned_bin, $banned_bins)) {
			$this->sendMsg("<i>Bin is Already Banned!</i>");

			exit;
		}

		file_put_contents('banned_bins.txt', $banned_bin . PHP_EOL, FILE_APPEND);

		$this->logSummary("<b>Another Bin Got Banned!</b>\n<b>Bin</b> → [$banned_bin]\n[☇] <b>Banned By</b> • [$admin_id]");

		$this->sendMsg("[✅] <b>Status</b> → <i>Bin (<code>{$banned_bin}</code>) Got Banned Successfully!</i>");
	}

	public function getstr($string, $start, $end) {
		$str = explode($start, $string);

		if (!isset($str[1])) return '';

		$str = explode($end, $str[1]);

		if (!isset($str[0])) return '';

		return $str[0];
	}

	public function uuid() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff ) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
	}

	public function sendMsg($text, $inline_keyboard = NULL) {
		$msg = array(
			'chat_id' => $this->getData()->message->chat->id ?? $this->getData()->message->peer_id->user_id,
			'text' => $text,
			'parse_mode' => 'HTML',
			'reply_to_message_id' => $this->getData()->message->message_id
		);

		if ($inline_keyboard) $msg['reply_markup'] = json_encode(['inline_keyboard' => $inline_keyboard]);

		return (object) $this->sendMessage($msg);
	}

	public function editMsg($text, $msg_id, $inline_keyboard = NULL) {
		$msg = array(
			'chat_id' => $this->getData()->message->chat->id,
			'message_id' => $msg_id,
			'text' => $text,
			'parse_mode' => 'HTML',
			'reply_to_message_id' => $this->getData()->message->message_id
		);

		if ($inline_keyboard) $msg['reply_markup'] = json_encode(['inline_keyboard' => $inline_keyboard]);

		return (object) $this->editMessageText($msg);
	}

	public function fetchUser($user_id) {
		$sql = $this->dbConn()->prepare('SELECT * FROM users WHERE id = :id');

		$sql->execute(array('id' => $user_id));

		if ($sql->rowCount() < 1) return False;

		$user_data = False;

		foreach ($sql->fetchAll() as $data) {
			$user_data = $data;
		}

		return $user_data;
	}

	public function register() {
		$user_id = $this->getData()->message->from->id;

		$sql = $this->dbConn()->prepare('SELECT * FROM `users` WHERE `id` = :id');

		$sql->execute(array('id' => $user_id));

		if ($this->fetchUser($user_id)) {
			$this->sendMsg("<b>User Already Registered!</b>");

			exit;
		}

		$sql = $this->dbConn()->prepare("INSERT INTO `users` (`id`, `range`, `credits`, `antispam`, `status`, `warns`, `plan`, `expiry`) VALUES (:id, 'USER', 0, 0, 'PENDING', 0, 'Free', '0')");

		$sql->execute(array('id' => $user_id));

		$this->logSummary("[☇] <b>New User Registered!</b> → [$user_id]");

		$this->sendMsg("<b>Registration successful! You can use this bot now.</b>");
	}

	public function fetchSpam($user_id) {
		$user_data = $this->fetchUser($user_id);

		if (!$user_data) {
			$this->sendMsg("<i>User isn't in My DB!</i> ❌");

			exit;
		}

		$spam = time() - $user_data['antispam'];

		switch (strtolower($user_data['plan'])) {
			case 'vip': $antispam = 20; break;
			case 'premium': $antispam = 20; break;
			default: $antispam = 40;
		}

		if ($spam < $antispam) return (object) ['status' => True, 'wait' => $antispam - $spam]; 

		$sql = $this->dbConn()->prepare("UPDATE `users` SET `antispam` = :antispam WHERE `id` = :id");

		$sql->execute(array('antispam' => time(), 'id' => $user_id));

		return (object) ['status' => False];
	}

	public function taken($first, $precision = 2) {
		$decimal = microtime(true) - $first;

		$sign = $decimal > 0 ? 1 : -1;

		$base = pow(10, $precision);

		return floor(abs($decimal) * $base) / $base * $sign;
	}

	public function randomString($length = 4) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$charactersLength = strlen($characters);
		$randomString = '';

		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	public function warnUser($user_id) {
		if (!$this->fetchUser($user_id)) {
			$this->sendMsg("<i>User isn't in My DB!</i>");

			exit;
		}

		$sql = $this->dbConn()->prepare('UPDATE `users` SET `warns` = warns + 1 WHERE `id` = :id');

		$sql->execute(array('id' => $user_id));

		$sql = $this->dbConn()->prepare("INSERT INTO `users` (`id`, `range`, `credits`, `antispam`, `status`, `warns`, `plan`, `expiry`) VALUES (:id, 'USER', 10, 0, 'ACTIVE', 0, 'Free', '0')");

		$sql->execute(array('id' => $user_id));

		$this->logSummary("[☇] <b>Another User Got Warned!</b>\n<b>User</b> → [$user_id]\n<b>Warned By</b> → [".$this->getData()->message->from->id."]");

		$this->sendMsg("<i>User Got Warned Successfully!</i>");
	}

	public function authUser($user_id = null) {
		$admin_id = $this->getData()->message->from->id;

		if (!$this->fetchUser($admin_id) || $this->fetchUser($admin_id)['range'] != 'owner') exit;

		if (!$user_id || empty($user_id)) {
			$this->sendMsg("Put Chat ID for Authorization!");

			exit;
		}

		$user_info = $this->fetchUser($user_id);

		if (!$user_info) {
			$sql = $this->dbConn()->prepare("INSERT INTO `users` (`id`, `range`, `credits`, `antispam`, `status`, `warns`, `plan`, `expiry`) VALUES (:id, 'USER', 0, 0, 'ACTIVE', 0, 'Free', '0')");

			$sql->execute(array('id' => $user_id));
		} elseif (strtolower($user_info['status']) == 'active') {
			$this->sendMsg("Chat has Already Being Authorized!");

			exit;
		} else {
			$sql = $this->dbConn()->prepare("UPDATE `users` SET `status` = 'ACTIVE' WHERE `id` = :id");

			$sql->execute(array('id' => $user_id));
		}

		$this->logSummary("[☇] <b>New Chat Got Authorized!</b>\n[☇] <b>User</b> → [$user_id]\n[☇] <b>Authorized By</b> → [$admin_id]");

		$this->sendMsg("<b>Chat Got Authorized Successfully!</b>");
	}

	public function unAuthUser($user_id = null) {
		$admin_id = $this->getData()->message->from->id;

		$admin_info = $this->fetchUser($admin_id);

		if (!$admin_info || strtolower($admin_info['range']) != 'owner') exit;

		if (!$user_id || empty($user_id)) {
			$this->sendMsg("Put Chat ID for Authorization!");

			exit;
		}

		if (!$this->fetchUser($user_id)) {
			$this->sendMsg("<i>User isn't in My DB!</i>");

			exit;
		}

		$sql = $this->dbConn()->prepare("UPDATE `users` SET `status` = 'PENDING' WHERE `id` = :id");

		$sql->execute(array('id' => $user_id));

		$this->logSummary("[☇] <b>Another User Got Unauthorized!</b>\n[☇] <b>User</b> → [$user_id]\n[☇] <b>UnAuthorized By</b> → [{$admin_id}]");

		$this->sendMsg("<i>User Got Unauthorized Successfully!</i>");
	}

	public function promUser($user_id) {
		$admin_id = $this->getData()->message->from->id;

		if (!$this->fetchUser($admin_id) || strtolower($this->fetchUser($admin_id)['range']) != 'owner') exit;

		if (!$this->fetchUser($user_id)) {
			$sql = $this->dbConn()->prepare("INSERT INTO `users` (`id`, `range`, `credits`, `antispam`, `status`, `warns`, `plan`, `expiry`) VALUES (:id, 'ADMIN', 0, 0, 'ACTIVE', 0, 'Free', '0')");

			$sql->execute(array('id' => $user_id));
		} else {
			$sql = $this->dbConn()->prepare("UPDATE `users` SET `range` = 'ADMIN', `status` = 'ACTIVE' WHERE `id` = :id");

			$sql->execute(array('id' => $user_id));
		}

		$this->logSummary("[☇] <b>New User Got Promoted!</b>\n[☇] <b>User</b> → [$user_id]\n[☇] <b>Promoted By</b> → [$admin_id]");

		$this->sendMsg("<i>User Got Promoted Successfully!</i>");
	}

	public function banUser($user_id) {
		$admin_id = $this->getData()->message->from->id;

		if (!$this->fetchUser($admin_id) || strtolower($this->fetchUser($admin_id)['range']) != 'owner') exit;

		if (!$this->fetchUser($user_id)) {
			$this->sendMsg("<i>User isn't in My DB!</i>");

			exit;
		}

		$sql = $this->dbConn()->prepare("UPDATE `users` SET `status` = 'BANNED' WHERE `id` = :id");

		$sql->execute(array('id' => $user_id));

		$this->logSummary("[☇] <b>New User Got Banned!</b>\n[☇] <b>User</b> → [$user_id]\n[☇] <b>Banned By</b> → [$admin_id]");

		$this->sendMsg("<i>User ({$user_id}) Got Banned Successfully!</i>");
	}

	public function cutCredit($user_id, $credit = 1) {
		if (!$this->fetchUser($user_id)) {
			$this->sendMsg("<i>User isn't in My DB!</i>");

			exit;
		}

		$sql = $this->dbConn()->prepare("UPDATE `users` SET `credits` = :credits WHERE `id` = :id");

		$sql->execute(array('credits' => $this->fetchUser($user_id)['credits'] - $credit, 'id' => $user_id));
	}

	public function createKey($key_info) {
		$user_id = $this->getData()->message->from->id;

		$range = strtolower($this->fetchUser($user_id)['range']);

		if ($range != 'owner' && $range != 'admin') exit;

		$split = explode("|", $key_info);

		$plan = $split[0] ?? '';

		$plan = strtolower($plan) == 'v' ? 'VIP' : 'Premium';

		$expiry = $split[1] ?? 1;
		$expiry = 0 > $expiry ? 30 : $expiry;
		$expiry = intval($expiry) * 86400;
		$key_expiry = time() + $expiry;

		$credits = $split[2] ?? 0;

		$credits = 0 > $credits ? 0 : $credits;

		$key = 'Brav-'.$this->randomString().'-'.$this->randomString().'-'.$this->randomString(8).'';

		$sql = $this->dbConn()->prepare("INSERT INTO `keys` (`key`, `status`, `plan`, `expiry`, `credits`) VALUES (:key, 'ACTIVE', :plan, :expiry, :credits)");

		$sql->execute(array('key' => $key, 'plan' => $plan, 'expiry' => $expiry, 'credits' => $credits));

		$expiry_date = date('Y-m-d h:i:s A', $key_expiry);

		$details = "[☇] <b>Key</b> → <code>{$key}</code>\n" .
			"[☇] <b>Plan Type</b> → <i>{$plan}</i>\n" .
			"[☇] <b>Expiration Date</b> → <i>{$expiry_date}</i>\n";

		if ($credits > 0) $details .= "[☇] <b>Credits</b> → <code>{$credits}</code>\n";

		$this->logSummary("[☇] <b>New Key Created!</b>\n{$details}\n[☇] <b>Created By</b> → ".'<a href="tg://user?id='.$user_id.'"><i>'.strtoupper($range).'</i></a>');

		$msg = "b>Key Generated Successfully!</b>\n\n" .
			$details .
			"<b>How To use</b> → <b>Send <code>/claim {$key}</code> in Bot to Claim It!</b>";

		$this->sendMsg($msg);
	}

	public function fetchKey($key) {
		$sql = $this->dbConn()->prepare('SELECT * FROM `keys` WHERE `key` = :key');

		$sql->execute(array('key' => $key));

		if ($sql->rowCount() < 1) return False;

		$key_data = False;

		foreach ($sql->fetchAll() as $data) {
			$key_data = $data;
		}

		return $key_data;
	}

	public function claimKey($key, $chat_id = null) {
		$user_id = $chat_id ?? $this->getData()->message->from->id;

		$user_info = $this->fetchUser($user_id);

		$key_data = $this->fetchKey($key); 

		$inline_keyboard = [
			[
				[
					"text" => "Owner",
					"url" => "https://t.me/".BOT_OWNER_USERNAME
				],
			]
		];

		if (!$key_data) {
			$status = "[❌] <b>Error</b> → Key not Found in My DB!";
		} elseif ($key_data['status'] == 'ACTIVE') {
			$sql = $this->dbConn()->prepare("UPDATE `keys` SET `status` = 'USED' WHERE `key` = :key");

			$sql->execute(array('key' => $key));

			$sql = $this->dbConn()->prepare("UPDATE `users` SET `plan` = :plan, `expiry` = :expiry, `credits` = :credits WHERE `id` = :id");

			$user_expiry = $user_info['expiry'] < 1 ? time() : $user_info['expiry'];

			$new_plan = strtolower($user_info['plan']) == 'vip' ? 'vip' : $key_data['plan'];

			$sql->execute(array(
				'plan' => $new_plan,
				'expiry' => $user_expiry + $key_data['expiry'],
				'credits' => $key_data['credits'] + $user_info['credits'],
				'id' => $user_id
			));

			$this->logSummary("[☇] <b>Key Claimed Successfully!</b>\n[☇] <b>Key</b> → <code>{$key}</code>\n[☇] <b>Claimed By</b> → [$user_id]");

			$status = "[✅] <b>Key Claimed Successfully!</b>\n\n" .
				"<b>Key</b> → <code>{$key}</code>\n" .
				"<b>Plan Type</b> → <i>".$key_data['plan']."</i>\n" .
				"<b>Expiration Date</b> → <i>".date('Y-m-d h:i:s A', ($user_expiry + $key_data['expiry']))."</i>\n" .
				"<b>Credits</b> → <code>".$key_data['credits']."</code>\n\n";

			if (isset($this->bot_group) && !$chat_id) {
				$this->unbanChatMember(array(
					'chat_id' => $this->bot_group,
					'user_id' => $user_id,
					'only_if_banned' => True
				));

				$create_link = $this->createChatInviteLink(array(
					'chat_id' => $this->bot_group,
					'member_limit' => 1
				));

				if (isset($create_link->invite_link)) $inline_keyboard[0][0] = ["text" => "Army Group", "url" => $create_link->invite_link];
			}
		} else {
			$status = "[❌] <b>Error</b> → Key is Expired/Used!\n";
		}

		$this->sendMsg($status, $inline_keyboard);
	}

	public function fetchGate($cmd) {
		$sql = $this->dbConn()->prepare('SELECT * FROM `gates` WHERE `cmd` = :cmd');

		$sql->execute(array('cmd' => $cmd));

		if ($sql->rowCount() < 1) return False;

		$gate_data = False;

		foreach ($sql->fetchAll() as $data) {
			$gate_data = $data;
		}

		return $gate_data;
	}

	public function addGate($gate_info) {
		$user_id = $this->getData()->message->from->id;

		if (strtolower($this->fetchUser($user_id)['range']) != 'owner') {
			exit;
		}

		if (!$gate_info) {
			$this->sendMsg("[❌] <b>Error</b> → Gate Details Can't Be Empty!");

			exit;
		}

		$split = explode("|", $gate_info);

		if (sizeof($split) < 5 || empty($split[0]) || empty($split[1]) || empty($split[2]) || empty($split[3]) || empty($split[4])) {
			$this->sendMsg("[❌] <b>Error</b> → Gate Details are Too Low!");

			exit;
		}

		$name = strtoupper($split[0]);

		switch (strtolower($split[1])) {
			case 'c': $type = 'credits'; break;
			case 'v': $type = 'vip'; break;
			case 'f': $type = 'free'; break;
			default: $type = 'premium';
		}

		$info = strtoupper($split[2]);
		$cmd = strtolower($split[3]);

		if ($this->fetchGate($cmd)) {
			$this->sendMsg("[❌] <b>Error</b> → There's Another Gate with Same Command!");

			exit;
		}

		$file = str_replace('.php', '', $split[4]);
		$extra = $split[5] ?? '';
		$comm = $split[6] ?? 'No Comments Added!';
		$format = $split[7] ?? '';

		$menu = in_array(strtolower($split[8] ?? ''), ['auth', 'ccn', 'charge', 'mass']) ? strtolower($split[8] ?? '') : 'charge';

		$creation = date('Y-m-d h:i:s A', time());

		$sql = $this->dbConn()->prepare("INSERT INTO `gates` (`menu`, `name`, `type`, `info`, `cmd`, `file`, `comm`, `format`, `creation`, `status`, `extra`) VALUES (:menu, :name, :type, :info, :cmd, :file, :comm, :format, :creation, '✅', :extra)");

		$sql->execute(array(
			'menu' => $menu,
			'name' => $name,
			'type' => $type,
			'info' => $info,
			'cmd' => $cmd,
			'file' => $file,
			'comm' => $comm,
			'format' => $format,
			'creation' => $creation,
			'extra' => $extra
		));

		$status_msg = "[☇] <b>Name</b> → <i>{$name}</i>\n" .
			"[☇] <b>Menu</b> → <i>{$menu}</i>\n" .
			"[☇] <b>Type</b> → <i>{$type}</i>\n" .
			(empty($format) ? "" : "[☇] <b>Format</b> → <i>{$format}</i>\n") .
			"[☇] <b>Info</b> → <i>{$info}</i>\n" .
			"[☇] <b>Command</b> → <code>/{$cmd}</code>\n" .
			"[☇] <b>Comment</b> → <i>{$comm}</i>\n" .
			"[☇] <b>File</b> → <i>{$file}</i>\n" .
			(empty($extra) ? "" : "[☇] <b>Extra</b> → <code>{$extra}</code>\n") .
			"[☇] <b>Created At</b> → <i>{$creation}</i>\n\n" .
			"[☇] <b>Added By</b> → [$user_id]";

		$this->logSummary("[☇] <b>New Gate Added!</b>\n\n{$status_msg}");

		$this->sendMsg("[✅] <b>Gate Added Successfully!</b>\n\n{$status_msg}");
	}

	public function fetchGates() {
		$sql = $this->dbConn()->prepare('SELECT * FROM `gates`');

		$sql->execute();

		if ($sql->rowCount() < 1) return False;

		$gates_data = [];

		foreach ($sql->fetchAll() as $gate_data) {
			$gates_data[] = $gate_data;
		}

		return $gates_data;
	}

	public function updateGate($update_data) {
		$user_id = $this->getData()->message->from->id;

		if (strtolower($this->fetchUser($user_id)['range']) != 'owner') {
			exit;
		}

		if (!$update_data) {
			$this->sendMsg("[❌] <b>Error</b> → Update Data Details Can't Be Empty!");

			exit;
		}

		$split = explode("|", $update_data);

		$gate_data = $this->fetchGate($split[0] ?? '');

		if (!$gate_data) {
			$this->sendMsg("[❌] <b>Error</b> → Gate Requested wasn't Found!");

			exit;
		}

		$keys = ["id","menu","name","type","info","cmd","status","comm","creation","file","extra","format"];

		$new_data = [];

		foreach ($gate_data as $key => $value) {
			if (in_array($key, $keys)) $new_data[$key] = $value;
		}

		$basic = json_decode($split[1] ?? '');

		if ($basic) {
			foreach ($basic as $key => $value) {
				if (isset($new_data[$key])) $new_data[$key] = $value;
			}
		}

		$extra = json_decode($split[2] ?? '');

		if ($extra) {
			$gate_extra = json_decode($new_data['extra']) ?? [];

			foreach ($extra as $key => $value) {
				$gate_extra[$key] = $value;
			}

			$new_data['extra'] = json_encode($gate_extra);
		}

		$new_data['creation'] = date('Y-m-d h:i:s A', time());

		$sql = $this->dbConn()->prepare("UPDATE `gates` SET `menu` = :menu, `name` = :name, `type` = :type, `info` = :info, `cmd` = :cmd, `status` = :status, `file` = :file, `comm` = :comm, `format` = :format, `creation` = :creation, `extra` = :extra WHERE `id` = :id");

		$sql->execute($new_data);

		$status_msg = "[☇] <b>Name</b> → <i>".$new_data['name']."</i>\n" .
			"[☇] <b>Menu</b> → <i>".$new_data['menu']."</i>\n" .
			"[☇] <b>Type</b> → <i>".$new_data['type']."</i>\n" .
			(empty($new_data['format']) ? "" : "[☇] <b>Format</b> → <i>".$new_data['format']."</i>\n") .
			"[☇] <b>Info</b> → <i>".$new_data['info']."</i>\n" .
			"[☇] <b>Command</b> → <code>/".$new_data['cmd']."</code>\n" .
			"[☇] <b>Status</b> → <i>".$new_data['status']."</i>\n" .
			"[☇] <b>Comment</b> → <i>".$new_data['comm']."</i>\n" .
			"[☇] <b>File</b> → <i>".$new_data['file']."</i>\n" .
			(empty($new_data['extra']) ? "" : "[☇] <b>Extra</b> → <code>".$new_data['extra']."</code>\n") .
			"[☇] <b>Updated At</b> → <i>".$new_data['creation']."</i>\n\n" .
			"[☇] <b>Updated By</b> → [$user_id]";

		$this->logSummary("[✅] <b>Gate has Been Updated!</b>\n\n{$status_msg}");

		$this->sendMsg("[✅] <b>Gate has Been Updated!</b>\n\n{$status_msg}");
	}

	public function setChkAPI($curlx, $response, $tools) {
		$this->curlx = $curlx;
		$this->response = $response;
		$this->tools = $tools;
	}

	public function chkAPI($api, $lista, $extra) {
		list($cc, $mm, $yyyy, $cvv) = $lista;

		$m = intval($mm);
		$yy = substr($yyyy, -2);
		$last4 = substr($cc, -4);

		require "gates/{$api}.php";

		$status = (object) $status;

		$status->retry = $retry;

		$status->proxy = $server;

		$status->msg = preg_replace('/[\.\!]+/', '', $status->msg);

		if (!in_array(substr($status->msg, -1), ['!', '.', ')'])) $status->msg .= '!';

		return $status;
	}

	public function fetchTool($cmd) {
		$sql = $this->dbConn()->prepare('SELECT * FROM `tools` WHERE `cmd` = :cmd');

		$sql->execute(array('cmd' => $cmd));

		if ($sql->rowCount() < 1) return False;

		$tool_data = False;

		foreach ($sql->fetchAll() as $data) {
			$tool_data = $data;
		}

		return $tool_data;
	}

	public function addTool($tool_info) {
		$user_id = $this->getData()->message->from->id;

		if (strtolower($this->fetchUser($user_id)['range']) != 'owner') {
			exit;
		}

		if (!$tool_info) {
			$this->sendMsg("[❌] <b>Error</b> → Tool Details Can't Be Empty!");

			exit;
		}

		$split = explode("|", $tool_info);

		if (sizeof($split) < 4 || empty($split[0]) || empty($split[1]) || empty($split[2]) || empty($split[3])) {
			$this->sendMsg("[❌] <b>Error</b> → Tool Details are Too Low!");

			exit;
		}

		$name = strtoupper($split[0]);

		switch (strtolower($split[1])) {
			case 'c': $type = 'credits'; break;
			case 'v': $type = 'vip'; break;
			case 'f': $type = 'free'; break;
			default: $type = 'premium';
		}

		$info = strtoupper($split[2]);
		$cmd = strtolower($split[3]);
		$format = $split[4] ?? '';
		$file = isset($split[5]) && !empty($split[5]) ? str_replace('.php', '', $split[5]) : '';
		$comm = $split[6] ?? 'No Comments Added!';

		$creation = date('Y-m-d h:i:s A', time());

		$sql = $this->dbConn()->prepare("INSERT INTO `tools` (`name`, `type`, `info`, `cmd`, `format`, `file`, `comm`, `creation`, `status`) VALUES (:name, :type, :info, :cmd, :format, :file, :comm, :creation, '✅')");

		$sql->execute(array(
			'name' => $name,
			'type' => $type,
			'info' => $info,
			'cmd' => $cmd,
			'format' => $format,
			'file' => $file,
			'comm' => $comm,
			'creation' => $creation
		));

		$status_msg = "[☇] <b>Name</b> → <i>{$name}</i>\n" .
			"[☇] <b>Type</b> → <i>{$type}</i>\n" .
			"[☇] <b>Info</b> → <i>{$info}</i>\n" .
			"[☇] <b>Command</b> → <code>/{$cmd}</code>\n" .
			(empty($format) ? "" : "[☇] <b>Format</b> → <i>{$format}</i>\n") .
			"[☇] <b>Comment</b> → <i>{$comm}</i>\n" .
			(empty($file) ? "" : "[☇] <b>File</b> → <i>{$file}</i>\n") .
			"[☇] <b>Created At</b> → <i>{$creation}</i>\n\n" .
			"[☇] <b>Added By</b> → [$user_id]";

		$this->logSummary("[☇] <b>New Tool Added!</b>\n\n{$status_msg}");

		$this->sendMsg("[✅] <b>Tool Added Successfully!</b>\n\n{$status_msg}");
	}

	public function fetchTools() {
		$sql = $this->dbConn()->prepare('SELECT * FROM `tools`');

		$sql->execute();

		if ($sql->rowCount() < 1) return False;

		$tools_data = [];

		foreach ($sql->fetchAll() as $tool_data) {
			$tools_data[] = $tool_data;
		}

		return $tools_data;
	}

	public function updateTool($update_data) {
		$user_id = $this->getData()->message->from->id;

		if (strtolower($this->fetchUser($user_id)['range']) != 'owner') {
			exit;
		}

		if (!$update_data) {
			$this->sendMsg("[❌] <b>Error</b> → Update Data Details Can't Be Empty!");

			exit;
		}

		$split = explode("|", $update_data);

		$tool_data = $this->fetchTool($split[0] ?? '');

		if (!$tool_data) {
			$this->sendMsg("[❌] <b>Error</b> → Tool Requested wasn't Found!");

			exit;
		}

		$keys = ["id","name","type","info","cmd","status","comm","creation","file","format"];

		$new_data = [];

		foreach ($tool_data as $key => $value) {
			if (in_array($key, $keys)) $new_data[$key] = $value;
		}

		$basic = json_decode($split[1] ?? '');

		if ($basic) {
			foreach ($basic as $key => $value) {
				if (isset($new_data[$key])) $new_data[$key] = $value;
			}
		}

		$new_data['creation'] = date('Y-m-d h:i:s A', time());

		$sql = $this->dbConn()->prepare("UPDATE `tools` SET `name` = :name, `type` = :type, `info` = :info, `cmd` = :cmd, `status` = :status, `file` = :file, `comm` = :comm, `format` = :format, `creation` = :creation WHERE `id` = :id");

		$sql->execute($new_data);

		$status_msg = "[☇] <b>Name</b> → <i>".$new_data['name']."</i>\n" .
			"[☇] <b>Type</b> → <i>".$new_data['type']."</i>\n" .
			(empty($new_data['format']) ? "" : "[☇] <b>Format</b> → <i>".$new_data['format']."</i>\n") .
			"[☇] <b>Info</b> → <i>".$new_data['info']."</i>\n" .
			"[☇] <b>Command</b> → <code>/".$new_data['cmd']."</code>\n" .
			"[☇] <b>Status</b> → <i>".$new_data['status']."</i>\n" .
			"[☇] <b>Comment</b> → <i>".$new_data['comm']."</i>\n" .
			"[☇] <b>File</b> → <i>".$new_data['file']."</i>\n" .
			"[☇] <b>Updated At</b> → <i>".$new_data['creation']."</i>\n\n" .
			"[☇] <b>Updated By</b> → [$user_id]";

		$this->logSummary("[✅] <b>Tool has Been Updated!</b>\n\n{$status_msg}");

		$this->sendMsg("[✅] <b>Tool has Been Updated!</b>\n\n{$status_msg}");
	}

	public function addShopify($name, $type = 'p', $info = '', $str, $extra) {
		$extra = json_decode($extra) ?? (object) ['prod_url' => $extra];

		require "gates/add_shopify.php";
	}

	public function addASP($name, $type = 'p', $str, $prod_url) {
		$extra = (object) ['prod_url' => $prod_url];

		require "gates/add_asp.php";
	}

	public function __call($method, $data) {
		$request = $this->client->postAsync($method, ["form_params" => $data[0]])->wait();
		$json = json_decode($request->getBody()->getContents());
		if($json->ok === false){
			return $json;
		}else{
			return $json->result;
		}
	}
}

?>