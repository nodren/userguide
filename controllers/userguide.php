<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kohana user guide and api browser.
 *
 * @package    Userguide
 * @author     Kohana Team
 */
class Userguide_Controller extends Template_Controller {

	public $template = 'userguide/template';

	protected $cache = FALSE;

	public function __construct()
	{
		parent::__construct();

		// Create a custom 404 handler for this controller
		Event::clear('system.404', array('Kohana_404_Exception', 'trigger'));
		Event::add('system.404', array($this, 'error'));

		// Do we have anything cached?
		if (Kohana::config('userguide.cache'))
		{
			$this->cache = Cache::instance();
		}

		if (Router::$method === 'media')
		{
			// Do not template media files
			$this->auto_render = FALSE;
		}

		// Use customized Markdown parser
		define('MARKDOWN_PARSER_CLASS', 'Kodoc_Markdown');

		// Load Markdown support
		require Kohana::find_file('vendor', 'markdown', TRUE);

		// Set the base URL for links and images
		Kodoc_Markdown::$base_url  = url::site('userguide').'/';
		Kodoc_Markdown::$image_url = url::site('userguide/media').'/';

		// Disable eAccelerator, it messes with	the ReflectionClass->getDocComments() calls
		ini_set('eaccelerator.enable', 0);

		// Bind the breadcrumb
		$this->template->bind('breadcrumb', $this->breadcrumb);

		// Add the breadcrumb
		$this->breadcrumb = array();
		$this->breadcrumb['userguide'] = 'User Guide';
		if ($this->package = URI::instance()->segment(3))
		{
			$this->breadcrumb['userguide/guide/'.$this->package] = ucfirst($this->package);
		}
	}

	// List all modules that have userguides
	public function index()
	{
		$this->template->title = "Userguide";
		$this->template->content = View::factory('userguide/index', array('modules' => Kohana::config('userguide.guides')));
		$this->template->menu = View::factory('userguide/menu', array('modules' => Kohana::config('userguide.guides')));
	}

	public function error()
	{
		$this->auto_render = TRUE;
		$this->template->title = "Userguide - Error";
		$this->template->content = View::factory('userguide/error', array('message' => 'Page not found'));

		// If we are in a module and that module has a menu, show that, otherwise use the index page menu
		if ($package = URI::instance()->segment(3) AND Kohana::config("userguide.guides.$package"))
		{
			$this->template->menu = $this->markdown($package.'/menu', NULL);
			$this->breadcrumb[] = 'Error';
		}
		else
		{
			$this->template->menu = View::factory('userguide/menu', array('modules' => Kohana::config('userguide.userguide')));
		}

		header('HTTP/1.1 404 File Not Found');
		$this->_render();
		exit();
	}

	public function guide($module = NULL, $page = NULL)
	{
		$this->template->title = ucfirst($page);

		$file = implode('/', URI::instance()->segment_array(2));

		$this->template->content = $this->markdown($file);

		// Find this modules menu file and send it to the template
		$this->template->menu = $this->markdown($module.'/menu', NULL);
	}

	public function api($package = NULL, $class_name = NULL)
	{
		if ($class_name)
		{
			// Do we have anything cached?
			if ($this->cache AND ($class = $this->cache->get('kodoc_class_'.$class_name)) !== NULL)
			{
				// Nothing to do, it's cached.
			}
			else
			{
				try
				{
					$class = Kodoc_Class::factory($class_name);
				}
				catch (Exception $e)
				{
					Event::run('system.404');
				}

				if ($this->cache)
				{
					$this->cache->set('kodoc_class_'.$class_name, $class);
				}
			}

			$this->breadcrumb['userguide/api/kohana'] = 'API Reference';
			$this->template->title = $class_name;

			$this->template->content = View::factory('userguide/api/class', array('class' => $class));
			$this->template->menu = View::factory('userguide/api/menu', array('class' => $class));
		}
		else
		{
			$this->template->title = 'API Reference';

			$this->template->content = View::factory('userguide/api/toc', array('toc' => Kodoc::packages()));

			$this->template->menu = $this->markdown('kohana/menu');
		}

		$breadcrumb[] = $this->template->title;
	}

	public function config($package = NULL, $config_name = NULL)
	{
		if ($config_name)
		{
			// Do we have anything cached?
			if ($this->cache AND ($config = $this->cache->get('kodoc_config_'.$config_name)) !== NULL)
			{
				// Nothing to do, it's cached.
			}
			else
			{
				try
				{
					$config = new Kodoc_Config($config_name);
				}
				catch (Exception $e)
				{
					Event::run('system.404');
				}

				if ($this->cache)
				{
					$this->cache->set('kodoc_config_'.$config_name, $config);
				}
			}
		}

		$this->template->title = $config_name;
		$this->template->content = View::factory('userguide/api/config', array('config' => $config));
		$this->template->menu = View::factory('userguide/api/config_menu', array('config' => $config));

		$this->breadcrumb['userguide/api/kohana'] = 'API Reference';
		$this->breadcrumb[] = $config_name.' config';
	}

	public function media($type = NULL, $file = NULL)
	{
		// Get the file path from the request
		$file = implode('/', URI::instance()->segment_array(2));

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
				'userguide/media/js/jquery.coda-slider-2.0.js',
				'userguide/media/js/jquery.easing.1.3.js',
				'userguide/media/js/kodoc.js',
				'userguide/media/js/shCore.js',
				'userguide/media/js/shBrushPhp.js',
			);
		}

		return parent::_render();
	}

	/**
	 * Render markdown page
	 * @param   string   Name of page
	 * @return  string   Rendered markdown content
	 */
	protected function markdown($page, $show_404 = TRUE)
	{
		if ( ! ($file = Kohana::find_file('guide', $page, FALSE, 'md')))
		{
			// If no file has been found, try to see if $page is a folder with an index file
			$file = Kohana::find_file('guide', $page.'/index', FALSE, 'md');
		}

		if ( ! $file AND $show_404 === TRUE)
		{
			Event::run('system.404');
		}

		if ( ! $file)
			return $show_404;
		else
			return Markdown(file_get_contents($file));
	}

} // End Userguide Controller