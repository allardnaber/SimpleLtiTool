<?php

namespace SimpleLti\Util;

use JsonSerializable;
use Packback\Lti1p3\JwksEndpoint;
use RuntimeException;

class Jwks implements JsonSerializable {

	private string $key;
	private array $jwks = [];

	public function __construct(private readonly string $keyId, private readonly string $filePath) {
		$this->initPrivateKey();
		$this->generateJwksDetails();
	}

	/**
	 * Gets the public key in an LTI 1.3 compliant JWKS array
	 * @return array{keys: array{kty: string, alg:string, use: string, kid: string, n: string, e: string}}
	 */
	public function getJwksDetails(): array {
		return $this->jwks;
	}

	private function initPrivateKey(): void {
		if (!is_readable($this->filePath)) {
			if (!file_exists($this->filePath)) {
				$this->key = $this->generatePrivateKey();
				$this->savePrivateKeyToFile();
			} else {
				throw new RuntimeException(sprintf(
					'Private key file %s is not readable, please fix the file permissions.',
					$this->filePath));
			}
		} else {
			$this->loadPrivateKeyFromFile();
		}
	}

	private function generateJwksDetails(): void {
		$endpoint = new JwksEndpoint([ $this->keyId => $this->key ]);
		$this->jwks = $endpoint->getPublicJwks();
		/*JwksEndpoint::new(['slti'=>file_get_contents($this->config->privateKeyFile)])->getPublicJwks();
		$details = openssl_pkey_get_details($this->key);
		$this->jwks = [
			'keys' => [
				[
					'kty' => 'RSA',
					'alg' => 'RS256',
					'use' => 'sig',
					'kid' => self::KEY_ID,
					'n' => $this->base64UrlEncode($details['rsa']['n']),
					'e' => $this->base64UrlEncode($details['rsa']['e']),
				]
			]
		];*/
	}

	private function generatePrivateKey(): string {
		$key = @openssl_pkey_new([
			'digest_alg' => 'sha256',
			'private_key_bits' => 2048,
			'private_key_type' => OPENSSL_KEYTYPE_RSA
		]);
		if ($key === false) {
			throw new \RuntimeException(
				'Unable to generate private key: ' .
				(error_get_last()['message'] ?? '(unknown error)'));
		}
		$output = null;
		$result = @openssl_pkey_export($key, $output);
		if ($result === false) {
			throw new \RuntimeException(
				'Unable to convert private key: ' .
				(error_get_last()['message'] ?? '(unknown error)'));
		}
		return $result;
	}

	private function savePrivateKeyToFile(): void {
		file_put_contents($this->filePath, $this->key);
	}

	private function loadPrivateKeyFromFile(): void {
		$key = file_get_contents($this->filePath);
		if ($key === false) {
			throw new \RuntimeException(
				'Unable to read private key: ' .
				(error_get_last()['message'] ?? '(unknown error)'));
		}
		$this->key = $key;
	}

	private function base64UrlEncode(string $data): string {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	public function __debugInfo(): array {
		return [ 'jwks' => $this->jwks ];
	}

	public function jsonSerialize(): array {
		return $this->jwks;
	}

}
