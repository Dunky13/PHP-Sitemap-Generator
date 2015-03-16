<?php

class Glob{
	public static function startsWith($haystack, $needle){
		return substr($haystack, 0, strlen($needle)) === $needle;
	}
	public static function endsWith($haystack, $needle){
		return substr($haystack, -strlen($needle)) === $needle;
	}
}

?>