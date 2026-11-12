<?php

use SimpleLti\SimpleLtiTool;

require_once '../vendor/autoload.php';
require_once './config.php';

$tool = new SimpleLtiTool($GLOBALS['simpleLtiConfig'] ?? null);

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		if (isset($_GET['jwks'])) {
			$tool->outputJwks();
		} else {
			$tool->oidcLogin();
		}
		break;

	case 'POST':
		break;

	default:
		error('Method not allowed');
}

function error(string $msg, int $httpStatus = 500): never {
	if (!headers_sent()) {
		http_response_code($httpStatus);
	}
	die($msg);
}
