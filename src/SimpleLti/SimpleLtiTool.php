<?php

namespace SimpleLti;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Packback\Lti1p3\Factories\MessageFactory;
use Packback\Lti1p3\LtiMessageLaunch;
use Packback\Lti1p3\LtiOidcLogin;
use Packback\Lti1p3\LtiServiceConnector;
use RuntimeException;
use SimpleLti\Util\CacheImpl;
use SimpleLti\Util\CookieImpl;
use SimpleLti\Util\DatabaseImpl;
use SimpleLti\Util\Jwks;

/**
 * This tool provides an easy method to verify an LTI request and retrieve
 * data from that request.
 * 
 * It is currently based on LTI 1.1, which is officially deprecated, but will be
 * around for a few years. In the meantime we will try to work on an LTI 1.3
 * version with the same signature.
 */
class SimpleLtiTool {

	private const DEFAULT_JWT_LEEWAY_SEC = 10;

	private Jwks $jwks;
	private $database, $cache, $cookie;

	/**
	 * Construct the tool launch verifier with specified secrets.
	 * @param ?LtiConfig $config Required config. Null can be passed for missing config detection
	 */
	public function __construct(private ?LtiConfig $config) {
		if ($this->config === null) {
			throw new RuntimeException('LTI Configuration is missing, please check the setup guide');
		}

		if (JWT::$leeway === 0) {
			JWT::$leeway = self::DEFAULT_JWT_LEEWAY_SEC;
		}
		$this->ensureSession();
		$this->jwks = new Jwks($this->config->kid, $this->config->privateKeyFile);
		$this->database = new DatabaseImpl($this->config);
		$this->cache = new CacheImpl();
		$this->cookie = new CookieImpl();
	}

	/**
	 * Gets the public key in an LTI 1.3 compliant JWKS array
	 * @return array{keys: array{kty: string, alg:string, use: string, kid: string, n: string, e: string}}
	 *     The JWKS array based on the current private key, {@see Jwks::getJwksDetails()}
	 */
	public function getJwks(): array {
		return $this->jwks->getJwksDetails();
	}

	public function outputJwks(): void {
		if (!headers_sent()) {
			header('Content-Type: application/json');
		}
		echo json_encode($this->jwks->getJwksDetails());
	}

	public function oidcLogin(): never {
		$login = new LtiOidcLogin($this->database, $this->cache, $this->cookie);
		$redirectUrl = $login->getRedirectUrl($_POST['target_link_uri'] ?? '', $_POST);
		if (!headers_sent()) {
			http_response_code(302);
			header('Location: ' . $redirectUrl);
		} else {
			printf('<a href="%s">Login to this service</a>', htmlentities($redirectUrl));
		}
		exit;
	}

	public function ltiLaunch(): mixed {
		$factory = new MessageFactory($this->database, $this->getServiceConnector(), $this->cache, $this->cookie);

		$launch = $factory->create($_POST);

		echo 'your unique user id is ' . $launch->getBody()['sub'];

		return '';
	}

	private function ensureSession(): void {
		$status = session_status();
		if ($status === PHP_SESSION_ACTIVE) {
			throw new RuntimeException('Sessions are disabled, but are required for this tool to work.');
		} elseif ($status === PHP_SESSION_NONE) {
			session_start();
		}
	}

	private function getServiceConnector(): LtiServiceConnector {
		return new LtiServiceConnector($this->cache, new Client());
	}


	/**
	 * Gets the course code from the LTI launch. It is not included in the
	 * Context returned by the IMS library.
	 * 
	 * @return string|null The Course Code if present, null otherwise.
	 */
	public function getCourseCode(): ?string {
		return $this->toolProvider->getPostVar('context_label');
	}
	
	/**
	 * Gets the LTI context (= course) from the LTI request.
	 * @return \IMSGlobal\LTI\ToolProvider\Context The Context.
	 */
	public function getContext(): \IMSGlobal\LTI\ToolProvider\Context {
		return $this->toolProvider->context;
	}
	
	/**
	 * Gets the Resource Links from the LTI request. The Resource Link is the
	 * link the user clicked to initiate the LTI message. This might be useful
	 * to link items within the tool to specific items in the LMS.
	 * @return \IMSGlobal\LTI\ToolProvider\ResourceLink The LTI Resource Link
	 *                                                  that initiated this request.
	 */
	public function getResourceLink(): \IMSGlobal\LTI\ToolProvider\ResourceLink {
		return $this->toolProvider->resourceLink;
	}

	/**
	 * Gets the LTI user from the LTI request. The user id will be anonymized,
	 * use getUserName to get the actual username from the LMS.
	 * @see SimpleLtiTool::getUserName() The method to get the actual username.
	 * @return \IMSGlobal\LTI\ToolProvider\User The LTI User that performed this request.
	 */
	public function getUser(): \IMSGlobal\LTI\ToolProvider\User {
		return $this->toolProvider->user;
	}

	/**
	 * Gets the username for the authenticated user. By default, LTI does not
	 * contain this information. We try the most common fields for LMSs.
	 * Source: https://developers.exlibrisgroup.com/leganto/integrations/lti/troubleshooting/user-problems/lms-username-parameter/
	 * @return string|null The Brightspace username if defined, null otherwise.
	 */
	public function getUsername(): ?string {
		return
			$this->toolProvider->getPostVar('custom_lis_user_username') ?? // Canvas / Blackboard
			$this->toolProvider->getPostVar('ext_d2l_username') ??         // Brightspace
			$this->toolProvider->getPostVar('ext_user_username') ??        // Moodle
			$this->toolProvider->getPostVar('ext_sakai_eid') ??            // Sakai
			$this->toolProvider->getPostVar('lis_person_sourcedid');       // Sakai
	}

	/**
	 * Gets the current locale for the user in http://www.rfc-editor.org/rfc/bcp/bcp47.txt format.
	 * @return string|null The locale identifier if present, null otherwise.
	 */
	public function getLocale(): ?string {
		return $this->toolProvider->getPostVar('launch_presentation_locale');
	}

}
