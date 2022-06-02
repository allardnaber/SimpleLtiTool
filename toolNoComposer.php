<?php

require './src/SimpleLti/SimpleLtiTool.php';

// Register autoloader to get all classes within IMSGlobal\LTI\
spl_autoload_register(function(string $fqcn) {
	$parts = explode('\\', $fqcn);
	
	if (array_shift($parts) === 'IMSGlobal' && array_shift($parts) === 'LTI') {
		$path = './imsglobal/lti/src/' . join('/', $parts) . '.php';
		require $path;
	}
});

try {
	$lti = new SimpleLti\SimpleLtiTool('lti_key', 'lti_secret');
	
	// LTI was verified and valid. You can use the the SimpleLtiTool methods
	// to use the LTI launch data.
	printf('Hi %s, welcome to course %s.',
			htmlentities($lti->getUsername()), htmlentities($lti->getCourseCode()));

}
catch (Exception $ex) {
	
	// There was something wrong with the LTI request. Abort.
	// If you want to use a custom Exception type, change SimpleLtiTool.php
	
	die(htmlentities($ex->getMessage()));
}
