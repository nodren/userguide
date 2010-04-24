# Upgrading

## Basic Instructions

> These instructions assume that you have not edited your system folder in any way. If you have then you will need to apply your changes again after upgrading if you use this method.

  - Delete the contents of your ''system'' folder.
  - Replace it with the ''system'' folder from the version you wish to upgrade to.
  - Follow the instructions from the relevant pages for your upgrade in the list above.

## Advanced Instructions

Some find it valuable to upgrade side-by-side instead of replacing the system directory completely.  This allows for easy downgrading should anything break due to the changes in the new version.  In order for this to work, it is suggested that you move your application directory.  Your directory structure might look something like this **(assume we are starting at your website's root directory, not the web root)**:

	* /
		* Kohana-old.version.number/
			* Default modules, system, and application directories
		* Kohana-new.version.number/
			* Same as above, only newer!
		* application/
			* A copy of the application directory from your original install that you actually use
		* modules/
			* Modules that you install from [[http://projects.kohanaphp.com|Kohana Projects]]
		* httpdocs/ (or www or htdocs or whatever it is you call your web root directory for your domain)
			* index.php
			* .htaccess
			* etc.

From this setup, you are able to simply extract the new version of Kohana in your domain's root directory and then make a change to your index.php to target the new version.  If anything goes wrong simply change the $kohana_system variable back to the old version.

	$kohana_application = '../application';
	$kohana_modules = '../modules';
	$kohana_system = '../Kohana-new.version.number/system';
