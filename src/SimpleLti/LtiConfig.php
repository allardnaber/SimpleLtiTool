<?php

namespace SimpleLti;

class LtiConfig {

	public function __construct(
		public string $issuer,
		public string $clientId,
		public string $deploymentId,
		public string $oidcAuthUrl,
		public string $tokenUrl,
		public string $jwksUrl,
		public ?string $privateKeyFile = '../.private.key',
		public ?string $kid = 'slti'
	) {}
}
