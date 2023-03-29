<?php

namespace App\Objects\Util;

class Util {

	public static function mb_ucfirst(string $string): string {
		if(mb_strlen($string) <= 0) {
		    return $string;
		}

		$char = mb_strtoupper(mb_substr($string, 0, 1));

		return $char.mb_substr($string, 1);
	}
}