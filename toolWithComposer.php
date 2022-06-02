<?php

require './vendor/autoload.php';

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
