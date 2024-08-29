<?php

class Response {
	static public function ErrorHandler($error, $msg = '', $cvc_check = '') {
		$code = $error->code ?? $error->type ?? 'unknown_error';
		$decline_code = $error->decline_code ?? $code;
		$message = $error->message ?? 'Unknown Error!';

		$codex = $decline_code;

		if (empty($msg)) {
			$err_msg = "$message -> $codex";

			if (!empty($cvc_check)) $err .= " -> CVC ".ucfirst($cvc_check)."!";
		} else $err_msg = $msg;

		$cvv = [
			'incorrect_zip',
			'insufficient_funds',
			'transaction_not_allowed',
			'pickup_card'
		];

		$ccn = [
			'incorrect_cvc',
			'lost_card',
			'stolen_card'
		];

		if ($cvc_check == 'pass' || in_array($code, $cvv) || in_array($decline_code, $cvv)) {
			$status = 'CVV CARD';
		} elseif (in_array($code, $ccn) || in_array($decline_code, $ccn)) {
			$status = 'CCN CARD';
		}

		$result = isset($status) ? 'Approved!' : 'Decline';

		$emoji = isset($status) ? '✅' : '❌';

		$status = $status ?? 'DEAD';

		return (object) ['emoji' => $emoji, 'status' => $result, 'msg' => "$status - $err_msg"];
	}

	/* A function that is used to check the response from the server and return the status of the
	card. */
	static public function Stripe($cURL = "", $err = "", $type = "charge") {
		# ---------------[RESPONSES]--------------- #
		$resp_arr = [
			['result":"success', "HIT", "Result -> Success!"],
			['success":true', "HIT", "Success -> True!"],
			['success": true', "HIT", "Success -> True!"],
			['redirectUrl": "/thank-you', "HIT", "Thank You."],
			['thank you for your gift', "HIT", "thank you for your gift."],
			['Purchase Successful!', "HIT", "Purchase Successful!"],
			['"status": "succeeded"', "HIT", "Status -> Succeeded!"],
			['Payment complete', "HIT", "Payment complete."],
			['status":true', "HIT", "Status -> True!"],
			["Your order has been received", "HIT", "Your Order has Been Received."],
			["Thank you for completing your order", "HIT", "Thank you for completing your order."],
			['Thank You for your donation', "HIT", "Thank You for your donation."],
			["Thank you. We couldn’t do it without you.", "HIT", "Thank you. We couldn’t do it without you."],
			['Thank you for supporting', "HIT", "Thank you for supporting"],
			["Thank you for your membership", "HIT", "Thank You for Your Membership."],
			["Welcome to HOAleader.com membership", "HIT", "Welcome to XXXxxxxxx.com membership!"],
			["You have successfully created your new account", "HIT", "You have successfully created your new account."],
			["Thank you for joining", "HIT", "Thank you for joining."],
			["thank you for your payment", "HIT", "thank you for your payment."],
			["Thanks for purchasing a gift", "HIT", "Thanks for purchasing a gift."],
			["Thank you for your purchase", "HIT", "Thank you for your purchase."],
			["Thank you for your Fable gift card purchase", "HIT", "Thank you for your XXXXX gift card purchase."],
			["Membership confirmation", "HIT", "Membership confirmation."],
			["The payment has been received", "HIT", "The payment has been received."],
			["Thank You.", "HIT", "Thank You."],
			['cvc_check":"pass', "CVV CARD", "CVC Check: Pass."],
			['cvc_check": "pass', "CVV CARD", "CVC Check: Pass."],
			["The zip code you supplied failed validation.", "CVV CARD", "Zip Code Failed Validation."],
			["incorrect_zip", "CVV CARD", "Incorrect ZIP."],
			["insufficient funds.", "CVV CARD", "Insufficient Funds."],
			['3D Secure', "CVV CARD", "3D Secure Card."],
			['Your bank requires further authentication', "CVV CARD", "3D Secure Card."],
			['Authentication Required', "CVV CARD", "3D Secure Card."],
			['three_d_secure_redirect', "CVV CARD", "3D Secure Card."],
			['stripe_3ds2_fingerprint', "CVV CARD", "3D Secure Card."],
			["card does not support this type of purchase", "CVV CARD", "Transaction not Allowed."],
			["security code is incorrect", "CCN CARD", "Incorrect Security Code."],
			["incorrect_cvc", "CCN CARD", "Incorrect CVC."],
			["CVC error", "CCN CARD", "CVC Error."]
		];

		$response = empty($err) ? "Unknown Error!" : $err;

		foreach ($resp_arr as $key => $value) {
			if (strpos($cURL, $value[0]) !== false) {
				$status = $value[1];
				$response = $value[2];
				break;
			}
		}

		$result = empty($status) ? "Decline" : "Approved!";
		$emoji = empty($status) ? "❌" : "✅";

		$status = $status ?? "DEAD";

		if ($status == "HIT") {
			if ($type == "auth") {
				$status = "CVV CARD";
			} else {
				$status = $type == "ccn" ? "CCN CHARGED" : "CHARGED";
			}
		}

		return ['emoji' => $emoji, 'status' => $result, 'msg' => "$status - $response"];
	}

	/* Checking the response from the server and returning the status of the card. */
	static public function Shopify($cURL1, $err = '') {
		if (strpos($cURL1, '3d_secure_2') !== false) return ['status' => 'Approved!', 'emoji' => '✅', "msg" => "CVV CARD - 3D Secure Required!"];

		$err_code = intval(preg_replace("/[^0-9]/", "", $err));

		$codes_arr = [
			[2059, "CVV CARD", "Zip code failed validation"],
			[2060, "CVV CARD", "Zip code failed validation"],
			[2001, "CVV CARD", "Insufficient Funds"],
			[2010, "CCN CARD", "Card Issuer Declined CVV"],
			[99048, "CCN CARD", "CVD ERROR"],
		];

		foreach ($codes_arr as $code) {
			if ($err_code == $code[0]) return ['status' => 'Approved!', 'emoji' => '✅', "msg" => "".$code[1]." - ".$code[2]."! (".$code[0].")" ];
		}

		$resp_arr = [
			["ZIP code does not match billing address", "CVV CARD", "AVS FAILED"],
			["Not Funds", "CVV CARD", "Insufficient Funds"],
			["Not enough balance", "CVV CARD", "Insufficient Funds"],
			["Insufficient Funds", "CVV CARD", "Insufficient Funds"],
			["The card has reached the credit limit", "CVV CARD", "Insufficient Funds"],
			["Address not Verified - Approved", "CVV CARD", "AVS FAILED"],
			["AVS", "CVV CARD", "AVS FAILED"],
			["card has insufficient funds", "CVV CARD", "Insufficient Funds"],
			["Over the limit", "CVV CARD", "Insufficient Funds"],
			["Exc W/D Freq Lmt", "CVV CARD", "Insufficient Funds"],
			["Insuff Funds", "CVV CARD", "Insufficient Funds"],
			["NSF", "CVV CARD", "Insufficient Funds"],
			["Street address and postal code do not match", "CVV CARD", "AVS FAILED"],
			["Security code was not matched by the processor", "CCN CARD"],
			["Card Issuer Declined CVV", "CCN CARD"],
			["CVV2 Mismatch", "CCN CARD"],
			["CVC Declined", "CCN CARD"],
			["CVD ERROR", "CCN CARD"],
			["No Match", "CCN CARD"],
			["Security codes does not match correct format (3-4 digits)", "CCN CARD"],
			["Invalid card verification number", "CCN CARD"]
		
		];

		foreach ($resp_arr as $resp) {
			if (strpos($err, $resp[0]) !== false) return ['status' => 'Approved!', 'emoji' => '✅', "msg" => "".$resp[1]." - ".(isset($resp[2]) ? $resp[2]." -> $err" : $err).""];
		}

		$hit_arr = [
			"Thank You For Your Order",
			"Your order is confirmed"
		];

		foreach ($hit_arr as $hit) {
			if (strpos($cURL1, $hit) !== false) return ['status' => 'Approved!', 'emoji' => '✅', "msg" => "CHARGED - {$hit}!" ];
		}

		return ['status' => 'Declined', 'emoji' => '❌', 'msg' => 'DEAD - '.(empty($err) ? 'Unknown Error' : $err).'!'];
	}


	static public function AuthNet($cURL1, $cvv2_reponse, $response_msg, $avs_response, $monto) {

		# -----------------------[RESPONSES]----------------------- #
		if (strpos($cURL1, "Transaction Approved")) {
			$status = "LIVE CVV";
			$response = "CHARGED: $".$monto." 『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";
		} elseif ($cvv2_reponse == "M") {
			$status = "LIVE CVV";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} elseif ($cvv2_reponse == "N") {
			$status = "DECLINED CVV2";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} elseif ($cvv2_reponse == "P") {
			$status = "DEAD CARD";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} elseif ($cvv2_reponse == "U") {
			$status = "Issuer no certified";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} elseif ($cvv2_reponse == "S") {
			$status = "Invalid CVV";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} elseif (strpos($response_msg, "DENIED")) {
			$status = "INvalid card gei";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} elseif (strpos($response_msg, "DO NOT HONOR")) {
			$status = "Do not honor";
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";

		} else {
			$status = $response_msg;
			$response = "『AVS: ".$avs_response." «» CVV: ".$cvv2_reponse."』";
		}

		$finalData = array(

			'code' => true,
			'status' => $status,
			'msg' => $response

		);

		return $finalData;

	}

	static public function AuthxWoo($cURL1) {

			# ---------------[RESPONSES]--------------- #
		#=====[CVV]=====#
		if (strpos($cURL1, 'The provided address does not match the billing address for cardholder. Please verify the address and try again')) {
			$status = "CVV CARD";
			$response = "The provided address does not match the billing address for cardholder. Please verify the address and try again.";
		} elseif (strpos($cURL1, 'insufficient funds')) {
			$status = "CVV CARD";
			$response = "Insufficient Funds.";
		}
		#=====[CCN]=====#
		elseif (strpos($cURL1, "The card verification number does not match")) {
			$status = "CCN CARD";
			$response = "The card verification number does not match.";
		}
		#=====[DEAD]=====#
		elseif (strpos($cURL1, "The Provided Card Was Declined, Please Use An Alternate Card Or Other Form Of Payment.")) {
			$status = "DEAD";
			$response = "The Provided Card Was Declined, Please Use An Alternate Card Or Other Form Of Payment..";
		}
		#======[ERROR]=====#
		else{
			$status = "ERROR";
			$err = trim(strip_tags(getStr($cURL1,'woocommerce-error" role="alert">
		<li>','<')));
			$response = !empty($err) ? $err : "Unknown Error!";
		}

		$finalData = array(

			'code' => true,
			'status' => $status,
			'msg' => $response,

		);

		return $finalData;

	}
}