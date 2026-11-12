<?php

namespace SimpleLti\Util;

use Packback\Lti1p3\Interfaces\ICookie;

class CookieImpl implements ICookie {

	public function getCookie(string $name): ?string {
		return $_COOKIE[$name] ?? null;
	}

	public function setCookie(string $name, string $value, int $exp = 3600, array $options = []): void {
		setcookie($name, $value, [
			'secure' => true,
			'httponly' => true,
			'samesite' => 'None',
			'expires' => $exp
		]);
	}
}
