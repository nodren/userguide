### Removing index.php From URLs (When Using Lighttpd)

Removing the index.php from your website URLs look better, and can help with SEO.

Note: This tutorial only focuses on Lighttd, and has been adapted from the guide for Apache.

#### Method 1: Redirect All Requests to index.php

##### Requirements:

You *must* enable the fastcgi configuration directive "allow-x-send-file" in order to be able to send the file

Simply add a line in your lighttpd.conf fastcgi.server directive below "bin-path":

~~~~
"allow-x-send-file" => "enable",
~~~~

##### The Code
Add the following rewrite rule to your lighttpd config file: (Replace /path/ with the path to your Kohana application)

~~~~
url.rewrite-once = ("^/path/(.*)$" => "path/index.php/$1")
~~~~

Now, all requests to /path/* will be forwarded to Kohana.  So we need to hande the case when static files are added.

So we add the following to the top of our index.php file:

~~~~
if (is_file($_SERVER['PATH_TRANSLATED']) && $_SERVER['PATH_TRANSLATED'] != __FILE__) {
	// We need to handle the case when a php file was called
	if (substr($_SERVER['PATH_TRANSLATED'], -4) == '.php') {
		include $_SERVER['PATH_TRANSLATED'];
		exit();
	} else {
		header("X-Sendfile: ".$_SERVER['PATH_TRANSLATED']);
		exit();
	}
}
~~~~

Now, if a file is requested that doesn't exist (and isn't the currently executing file), the program will either
include the file (if it's a php file), or instruct lighttpd to send the static file.


##### The Advantages
*  Very short configuration change
*  All requests go through index.php, so it's easy to do complicated access control inside your application

##### The Disadvantages
*  All requests go through index.php, so there's a HUGE performance hit
*  Mime types won't be handled correctly (All types will be sent with text/html)
*  PHP files may not execute correctly if they rely on path information in $_SERVER
*  Significant DOS (Denial of Service) risk in that all requests generate a PHP process.


Note: This method was adapted from http://feedsandwich.com/2009/01/06/kohana-lighttpd-a-quick-way-of-doing-pretty-urls.html


#### Method 2: Redirect Only Unknown File Types to index.php

##### The Code
Add the following rewrite rule to your lighttpd config file: (Replace /path/ with the path to your Kohana application)

~~~~
url.rewrite-once = (
	"^/path/((.*)\.(php|css|js|gif|png|swf|jpg|jpeg))$" => "path/$1"
	"^/path/(.*)$" => "path/index.php/$1"
)
~~~~

Now, see that the second rule is identical to the rule in method one.  The difference is what happens when Lighttpd encounters
the first rule.  Since the rules are of type "rewrite-once", it will stop looking at rules once it finds one that matches the
current request.  The first rule basically matches all "known" static types, and rewrites the url to the exact same url.

There is a caviat to this.  You cannot have a dynaic url with the same extension as a static one.  One way around that is to
use a convention.  You can say that all .htm files are static, whereas all .html are dynamic.  Therefore, you'd add "|htm"
after jpeg in the first rule.  (You can add any other static file extensions that you may use to that list as well)

Note the presence of php to that list.  If we visit a file ending with .php, we can safely assume that it's a php file, and
let Lighttpd take care of executing it.

##### The Advantages
*  Reasonably short configuration change
*  Only requests with an unkown (or no) extension will go through Kohana (Better for performance)
*  No changes required to index.php
*  LEAST DOS risk (since all "static file types" avoid PHP processing reguardless of error code)

##### The Disadvantages
*  You must update the list of known extensions every time you add a new static file type
*  Access control for files falls to the domain of Lighttpd (Simple for Basic or Digest authentication)

#### Method 3: Adding a 404 handler

##### The Code
Add the following to your lighttpd config file (Replace /path/ with the path to your Kohana application)

~~~
server.error-handler-404 = "/path/index.php"
~~~

The way this works is a little bit different from how the prior 2 methods worked.  Lighttpd goes through its normal workings
and will only forward to index.php if the url would generate a 404.

Since it's not using a redirect, the proper $_SERVER values aren't available to PHP.  So we need to edit our index.php to
fake the ORIG_PATH_INFO value. I chose this value, since it is what lighttpd would set if we did actually perform a rewrite.

~~~~
list($_SERVER['ORIG_PATH_INFO']) = explode('?',$_SERVER['REQUEST_URI'], 2);
~~~~

Note the explode.  That's because REQUEST_URI can contain the query string (if present).  So now we're all set to run!

##### The Advantages
*  Very short configuration change
*  Allows PHP to handle ALL 404 errors.  This lets you assign urls by mime type (captcha.png) and still server it dynamically
*  Also allows for the possibility of creating virtual directories for access control of static files

##### The Disadvantages
*  Requires change to PHP
*  DOS risk since all 404 errors are handled by PHP
*  Requires changes to $_SERVER variable (which is normally frowned upon)