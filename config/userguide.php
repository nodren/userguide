<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	// Default the userguide language.
	'lang' => 'en-us',
	
	// Enable the API browser.  TRUE or FALSE
	'api_browser' => TRUE,
	
	// Enable these packages in the API browser.  TRUE for all packages, or a string of comma seperated packages. You can use "None" for classes with no @package tag.
	// Example: 'api_packages' => 'Kohana,Database,ORM,None',
	'api_packages' => TRUE,
	
	// Create an empty module menu
	'userguide' => array(),
);

