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
	
	// Add the Kohana docs to the menu
	'userguide' => array(

		// This should be the path to your userguide pages
		'kohana' => array(
		
			// The name that should show up on the userguide index page
			'name'  => 'Kohana 3',
			
			// A short description of this module
			'desc'  => 'Documentation for Kohana 3.',
			
			// Where is the menu page for this page? WITHOUT 'guide/' at the beggining or '.md' at the end
			'menu'  => 'kohana/menu',
		)   
	)
);

