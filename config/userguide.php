<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	// Default the userguide language.
	'lang'		=> 'en-us',
	
	// Add this userguide entry to kodoc
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

