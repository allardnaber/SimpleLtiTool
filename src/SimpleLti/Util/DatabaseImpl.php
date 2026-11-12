<?php

namespace SimpleLti\Util;

use Packback\Lti1p3\Interfaces\IDatabase;
use Packback\Lti1p3\LtiDeployment;
use Packback\Lti1p3\LtiRegistration;
use SimpleLti\LtiConfig;

class DatabaseImpl implements IDatabase {

	public function __construct(private readonly LtiConfig $config) {}

	public function findRegistrationByIssuer(string $iss, ?string $clientId = null): ?LtiRegistration {
		return new LtiRegistration([
			'issuer' => $this->config->issuer,
			'clientId' => $this->config->clientId,
			'keySetUrl' => $this->config->jwksUrl,
			'authLoginUrl' => $this->config->oidcAuthUrl,
			'authTokenUrl' => $this->config->tokenUrl,
			//'toolPrivateKey' => '',
			'kid' => $this->config->kid
		]);
	}

	public function findDeployment(string $iss, string $deploymentId, ?string $clientId = null): ?LtiDeployment {
		return new LtiDeployment($this->config->deploymentId);
	}
}
