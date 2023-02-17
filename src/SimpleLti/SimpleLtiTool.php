<?php

namespace SimpleLti;

use IMSGlobal\LTI\ToolProvider\DataConnector\DataConnector;
use IMSGlobal\LTI\ToolProvider\ToolProvider;

/**
 * This tool provides an easy method to verify an LTI request and retrieve
 * data from that request.
 * 
 * It is currently based on LTI 1.1, which is officially deprecated, but will be
 * around for a few years. In the mean time we will try to work on an LTI 1.3
 * version with the same signature.
 * 
 * In case the tool exits with error <strong>Array and string offset access
 * syntax with curly braces is deprecated</strong>, edit
 * <code>imsglobal/lti/src/OAuth/OAuthSignatureMethod.php</code>, line 59. Replace
 * the curly braces <code>{ }</code> with brackets <code>[ ]</code>.
 */
class SimpleLtiTool {

	private $toolProvider;

	public function __construct(string $key, string $secret) {
		$dataConnector = $this->getDataConnector($key, $secret);
		$this->toolProvider = $this->getToolProvider($dataConnector);
		$this->toolProvider->handleRequest();
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

	/**
	 * Build the data connector used for the key/secret validation.
	 * @param string $key The LTI key to use
	 * @param string $secret The LTI secret to use.
	 * @return DataConnector The LTI signature validator.
	 */
	private function getDataConnector(string $key, string $secret): DataConnector {
		return new class($key, $secret) extends DataConnector {

			private $key, $secret;

			public function __construct($key, $secret) {
				parent::__construct(null, '');
				$this->key = $key;
				$this->secret = $secret;
			}

			public function loadToolConsumer($consumer): bool {
				parent::loadToolConsumer($consumer);
				$consumer->setKey($this->key);
				$consumer->secret = $this->secret;
				return true;
			}
		};
	}

	/**
	 * Build the tool provider that takes care of processing the LTI message.
	 * @param DataConnector $connector The LTI signature validator
	 * @return ToolProvider The LTI tool provider with some extra methods.
	 */
	private function getToolProvider(DataConnector $connector): ToolProvider {
		return new class($connector) extends ToolProvider {

			public $postVars = [];

			public function onLaunch(): bool {
				$this->postVars = $_POST;
				return true;
			}

			public function onError() {
				throw new \Exception($this->message . ' ' . $this->reason);
			}

			/**
			 * Get a variable that was sent along with the POST request.
			 * @param string $key The name of the parameter.
			 * @return string|null The value of the parameter, or null if undefined.
			 */
			public function getPostVar(string $key): ?string {
				return $this->postVars[$key] ?? null;
			}

		};
	}

}
