<?php

class GenCard {
	private static string $extra;
	private static int $mm;
	private static int $yy;
	private static string $cvv;

	public static function Gen($extra, $mm = '', $yy = '', $cvv = '', $amo = 10) : array
	{
		self::$extra = $extra;
		self::$mm = intval($mm);
		self::$yy = intval(substr($yy, -2));
		self::$cvv = preg_replace('/\D/', '', $cvv);

		if (intval($amo) < 1 || intval($amo) > 100) $amo = 10;

		$cards = [];

		$gcards = [];

		for ($i = 1; $i <= intval($amo); $i++) {
			$card = self::GenCard();

			if (in_array($card[0], $gcards)) continue;

			$gcards[] = $card[0];

			$cards[] = implode('|', $card);
		}

		return $cards;
	}

	private static function GenCard() : array
	{
		return [self::GenCC(), self::GenMM(), self::GenYY(), self::GenCVV()];
	}

	private static function GenCC() : string
	{
		$num = 14;

		if (substr_compare(self::$extra, 37, 0, 2)) $num = 15;

		$ccbin = preg_replace("/[^0-9x]/", "", substr(self::$extra, 0, $num));

		for ($i = 0; $i < strlen($ccbin); $i++) {
			if ($ccbin[$i] == "x") {
				$ccbin[$i] = mt_rand(0, 9);
			}
		}

		$num++;

		return self::GenNum($ccbin, $num);
	}

	private static function GenNum($prefix, $length) : string
	{
		$ccnumber = $prefix;

		while (strlen($ccnumber) < ($length - 1)) {
			$ccnumber .= mt_rand(0, 9);
		}

		$sum = 0;
		$pos = 0;

		$reversedCCnumber = strrev($ccnumber);

		while ($pos < $length - 1) {
			$odd = $reversedCCnumber[$pos] * 2;

			if ($odd > 9) {
				$odd -= 9;
			}

			$sum += $odd;

			if ($pos != ($length - 2)) {
				$sum += $reversedCCnumber[$pos + 1];
			}

			$pos += 2;
		}

		$checkdigit = ((floor($sum / 10) + 1) * 10 - $sum) % 10;
		$ccnumber .= $checkdigit;

		return $ccnumber;
	}

	private static function GenMM() : string
	{
		return substr('0'.(empty(self::$mm) || self::$mm < 1 || self::$mm > 12 ? mt_rand(1, 12) : self::$mm), -2);
	}

	private static function GenYY() : string
	{
		$now_yy = date('y', time());
		$future_yy = date('y', time()) + 10;

		return substr('20'.(empty(self::$yy) || self::$yy < $now_yy || self::$yy > $future_yy ? mt_rand($now_yy, $future_yy) : self::$yy), -4);
	}

	private static function GenCVV() : string
	{
		return substr_compare(self::$extra, 37, 0, 2) ?
			substr(empty(self::$cvv) || strlen(self::$cvv) != 3 ? mt_rand(112, 998) : self::$cvv, -3) :
			substr(empty(self::$cvv) || strlen(self::$cvv) != 4 ? mt_rand(1102, 9998) : self::$cvv, -4);
	}
}