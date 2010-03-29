<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana user guide and api browser.
 *
 * @package    Userguide
 * @author     Kohana Team
 */
class Userguide_Controller extends Template_Controller {

	public $template = 'userguide/template';

	public function __construct()
	{
		parent::__construct();

		// Create a custom 404 handler for this controller
		Event::clear('system.404', array('Kohana_404_Exception', 'trigger'));
		Event::add('system.404', array($this, 'error'));

		if (URI::instance()->segment(2) === 'media')
		{
			// Do not template media files
			$this->auto_render = FALSE;
		}
		else
		{
			// Disable eAccelerator, it messes with	the ReflectionClass->getDocComments() calls
            ini_set('eaccelerator.enable', 0);

			// Grab the necessary routes
			$this->media = url::site();
			$this->guide = url::site();

			// Use customized Markdown parser
			define('MARKDOWN_PARSER_CLASS', 'Kodoc_Markdown');

			// Load Markdown support
			require Kohana::find_file('vendor', 'markdown', TRUE);

			// Set the base URL for links and images
			Kodoc_Markdown::$base_url  = preg_replace('#//#', '/', url::site().'/');
			Kodoc_Markdown::$image_url = preg_replace('#//#', '/', url::site().'/');
		}
	}

	// List all modules that have userguides
	public function index()
	{
		$this->template->title = "Userguide";
		$this->template->breadcrumb = array('User Guide');
		$this->template->content = View::factory('userguide/index', array('modules' => Kohana::config('userguide.guides')));
		$this->template->menu = View::factory('userguide/menu', array('modules' => Kohana::config('userguide.guides')));
	}

	public function error()
	{
		$this->template->title = "Userguide - Error";
		$this->template->content = View::factory('userguide/error', array('message' => 'Page not found'));

		// If we are in a module and that module has a menu, show that, otherwise use the index page menu
		if ($module = URI::instance()->segment(2) AND $config = Kohana::config("userguide.guides.$module"))
		{
			$menu = $this->file($config['menu']);
			$this->template->menu = Markdown(file_get_contents($menu));
			$this->template->breadcrumb = array
			(
				'userguide'          => 'User Guide',
				'userguide/'.key($module) => $module,
				'Error'
			);
		}
		else
		{
			$this->template->menu = View::factory('userguide/menu', array('modules' => Kohana::config('userguide.userguide')));
			$this->template->breadcrumb = array('userguide' => 'User Guide', 'Error');
		}
	}

	public function guide($module = NULL, $name = NULL)
	{
		// Trim trailing slashes, to ensure breadcrumbs work
		$page = trim($module.'/'.$name, '/');

		if ( ! ($file = $this->file($page)))
		{
			Event::run('system.404');
		}

		$this->template->title = $this->title($page);

		$this->template->content = Markdown(file_get_contents($file));

		// Find this modules menu file and send it to the template
		$menu = $this->file($module.'/menu');
		$this->template->menu = Markdown(file_get_contents($menu));

		// Bind the breadcrumb
		$this->template->bind('breadcrumb', $breadcrumb);

		// Begin building the breadcrumbs backwards
		$breadcrumb = array();

		// Add the page name
		$breadcrumb[] = $this->template->title;

		// Find all the parents
		$last = $page;
		$current = null;
		while ($last !== $current = preg_replace('~/[^/]+$~','',$last))
		{
			$breadcrumb[$this->guide->uri().'/'.$current] = $this->title($current);
			$last = $current;
		}

		// Add the userguide root link
		$breadcrumb['userguide'] = __('User Guide');

		// Now reverse the array
		$breadcrumb = array_reverse($breadcrumb);
	}

	public function api($module = NULL, $class = NULL)
	{
		if ($class)
		{
			try
			{
				$_class = Kodoc_Class::factory($class);

				if ( ! Kodoc::show_class($_class))
					throw new Exception("That class is hidden");
			}
			catch (Exception $e)
			{
				Event::run('system.404');
			}

			$this->template->title = $class;

			$this->template->content = View::factory('userguide/api/class')
				->set('doc', $_class)
				->set('route', 'test');

			$this->template->menu = Kodoc::menu().View::factory('userguide/api/menu',array('doc'=>$_class));
		}
		else
		{
			$this->template->title = __('Table of Contents');

			$this->template->content = Kodoc::menu();

			$this->template->menu = Kodoc::menu();
		}

		// Bind the breadcrumb
		$this->template->bind('breadcrumb', $breadcrumb);

		// Add the breadcrumb
		$breadcrumb = array();
		$breadcrumb['userguide'] = __('User Guide');
		$breadcrumb['userguide/api'] = 'API Reference';
		$breadcrumb[] = $this->template->title;
	}

	public function media($type = NULL, $file = NULL)
	{
		// Get the file path from the request
		$file = $type.'/'.$file;

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, - (strlen($ext) + 1));

		// Find the file
		if ( ! ($file = Kohana::find_file('media', $file, FALSE, $ext)) )
		{
			Event::run('system.404');
		}

		// Tell browsers to cache the file for an hour. Chrome especially seems to not want to cache things
		expires::check(3600);

		// Send the file content as the response, and send some basic headers
		download::send($file);
	}

	public function _render()
	{
		if ($this->auto_render)
		{
			// Add styles
			$this->template->styles = array
			(
				'userguide/media/css/print.css'  => 'print',
				'userguide/media/css/screen.css' => 'screen',
				'userguide/media/css/kodoc.css'  => 'screen',
				'userguide/media/css/topline.css' => 'screen',
				'userguide/media/css/shCore.css' => 'screen',
				'userguide/media/css/shThemeDefault.css' => 'screen',
			);

			// Add scripts
			$this->template->scripts = array
			(
				'userguide/media/js/jquery-1.3.2.min.js',
				'userguide/media/js/kodoc.js',
				'userguide/media/js/shCore.js',
				'userguide/media/js/shBrushPhp.js',
			);
		}

		return parent::_render();
	}

	/**
	 * Find a userguide page
	 * @param   string   the url of the page
	 * @return  string   the name of the file
	 */
	public function file($page)
	{
		if ( ! ($file = Kohana::find_file('guide', $page, FALSE, 'md')))
		{
			// If no file has been found, try to see if $page is a folder with an index file
			$file = Kohana::find_file('guide', $page.'/index', FALSE, 'md');
		}

		return $file;
	}

	/**
	 * Find the title of a page in the menu file by looking for the url. Assuming we are looking for "url" and the following is in the menu file: [Name](url) it will return "Name".
	 * @param  string   the url to find the title of
	 * @return string   the title of the page
	 */
	public function title($page)
	{
		return $page;
	}

} // End Userguide Controller