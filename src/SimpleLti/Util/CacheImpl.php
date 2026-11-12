<?php

namespace SimpleLti\Util;

use Packback\Lti1p3\Interfaces\ICache;

class CacheImpl implements ICache {

	public function getLaunchData(string $key): ?array {
		$val = $this->getValue('lti-launch-data-' . $key);
		return $val === null ? null : json_decode($val, true);
	}

	public function cacheLaunchData(string $key, array $jwtBody): void {
		$this->setValue('lti-launch-data-' . $key, json_encode($jwtBody));
	}

	public function cacheNonce(string $nonce, string $state): void {
		$this->setValue('lti-nonce-' . $nonce, $state);
	}

	public function checkNonceIsValid(string $nonce, string $state): bool {
		$k = 'lti-nonce-' . $nonce;
		$cached = $this->getValue($k);
		if ($cached === null) {
			return false;
		}
		$this->clearValue($k);
		return $cached === $state;
	}

	public function cacheAccessToken(string $key, string $accessToken): void {
		 $this->setValue('lti-access-token-' . $key, $accessToken);
	}

	public function getAccessToken(string $key): ?string {
		return $this->getValue('lti-access-token-' . $key);
	}

	public function clearAccessToken(string $key): void {
		$this->clearValue('lti-access-token-' . $key);
	}

	private function getValue(string $key): ?string {
		return $_SESSION[$key] ?? null;
	}

	private function setValue(string $key, string $value): void {
		$_SESSION[$key] = $value;
	}

	private function clearValue(string $key): void {
		unset($_SESSION[$key]);
	}
}
