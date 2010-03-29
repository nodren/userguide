<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc {
	protected static $packages = array();

	public static function factory($class)
	{
		return new Kodoc_Class($class);
	}

	protected static function packages()
	{
		// If we already found the packages just return them
		if ( ! empty(Kodoc::$packages))
			return Kodoc::$packages;

		$cache = Cache::instance();

		// Do we have anything cached?
		if (Kohana::config('userguide.cache') AND $packages = $cache->get('kodoc_packages'))
			return Kodoc::$packages = $packages;

		$files = Kodoc::classes();

		$packages = array();

		foreach ($files as $group => $classes)
		{
			// We have to parse config files differently
			if ($group === 'config')
			{
				foreach ($classes as $config)
				{
					$config = Kodoc::parse_config($config);

					if (isset($config->tags['package']))
					{
						foreach ($config->tags['package'] as $package)
						{
							$packages[strtolower($package)]['configs'][$config->name] = $config->name;
						}
					}
					else
					{
						$packages['unknown']['configs'][$config->name] = $config->name;
					}
				}
			}
			else
			{
				foreach ($classes as $class)
				{
					$class = Kodoc::factory($class);

					if (isset($class->tags['package']))
					{
						foreach ($class->tags['package'] as $package)
						{
							$packages[strtolower($package)][$group][$class->class->name] = $class->class->name;
						}
					}
					else
					{
						$packages['unknown'][$group][$class->class->name] = $class->class->name;
					}
				}
			}
		}

		// Sort the groups in each package
		foreach($packages as &$package)
		{
			foreach($package as &$group)
			{
				ksort($group);
			}
			ksort($package);
		}

		// Cache the results
		if (Kohana::config('userguide.cache'))
		{
			$cache->set('kodoc_packages', $packages);
		}

		return Kodoc::$packages = $packages;
	}

	public static function parse_config($file)
	{
		$config = new stdClass;
		$config->name = $file;
		$config->description = '';
		$config->options = array();

		if ($filename = Kohana::find_file('config', $file))
		{
			$config->source = file_get_contents($filename[0]);


			$start_offset = 0;

			// Find the config file comment first
			if (preg_match('~(/\*.*?\*/)~s', $config->source, $config_comment))
			{
				$comment = Kodoc::parse($config_comment[0]);
				$config->description = $comment[0];
				$config->tags = $comment[1];
				$start_offset = strlen($config_comment[0]);
			}

			preg_match_all('~(/\*.*?\*/)?\s*(\$config\[([^\]]+)]\s*=\s*([^;]*?);)~s', $config->source, $matches, PREG_SET_ORDER, $start_offset);

			foreach ($matches as $item)
			{
				$comment = Kodoc::parse($item[1]);
				$default = isset($comment[1]['default'][0]) ? $comment[1]['default'][0] : NULL;

				// Remove the @default tag
				unset($comment[1]['default']);

				$config->options[] = (object) array
									(
										'description' => $comment[0],
										'source'      => $item[2],
										'name'        => trim($item[3], '\'"'),
										'value'       => $item[4],
										'default'     => $default,
										'tags'        => (object) $comment[1],
									);
			}
		}

		return $config;
	}

	/**
	 * Creates an html list of all classes sorted by category (or package if no category)
	 *
	 * @return   string   the html for the menu
	 */
	public static function menu()
	{
		return Kodoc::packages();
	}

	/**
	 * Returns an array of all the classes available, built by listing all files in the classes folder and then trying to create that class.
	 *
	 * This means any empty class files (as in complety empty) will cause an exception
	 *
	 * @param   array   array of files, obtained using Kohana::list_files
	 * @return   array   an array of all the class names
	 */
	public static function classes($type = NULL)
	{
		$classes = array();

		if ($type === NULL)
		{
			foreach(array('libraries', 'helpers', 'models', 'controllers', 'core', 'config') AS $type)
			{
				$classes[$type] = Kodoc::classes($type);
			}
		}
		else
		{
			$files = Kohana::list_files($type);

			foreach ($files as $class)
			{
				// Remove directory and the extension
				$class = basename($class, EXT);

				// Add Controller prefix
				if ($type === 'controllers')
				{
					$class .= '_Controller';
				}
				else if ($type === 'models')
				{
					$class .= '_Model';
				}

				$classes[$class] = $class;
			}
		}

		return $classes;
	}

	/**
	 * Parse a comment to extract the description and the tags
	 *
	 * @param   string  the comment retreived using ReflectionClass->getDocComment()
	 * @return  array   array(string $description, array $tags)
	 */
	public static function parse($comment)
	{
		// Normalize all new lines to \n
		$comment = str_replace(array("\r\n", "\n"), "\n", $comment);

		// Remove the phpdoc open/close tags and split
		$comment = array_slice(explode("\n", $comment), 1, -1);

		// Tag content
		$tags = array();

		foreach ($comment as $i => $line)
		{
			// Remove all leading whitespace
			$line = preg_replace('/^\s*\* ?/m', '', $line);

			// Search this line for a tag
			if (preg_match('/^@(\S+)(?:\s*(.+))?$/', $line, $matches))
			{
				// This is a tag line
				unset($comment[$i]);

				$name = $matches[1];
				$text = isset($matches[2]) ? $matches[2] : '';

				switch ($name)
				{
					case 'license':
						if (strpos($text, '://') !== FALSE)
						{
							// Convert the lincense into a link
							$text = html::anchor($text);
						}
					break;
					case 'copyright':
						if (strpos($text, '(c)') !== FALSE)
						{
							// Convert the copyright sign
							$text = str_replace('(c)', '&copy;', $text);
						}
					break;
					case 'throws':
						if (preg_match('/^(\w+)\W(.*)$/',$text,$matches))
						{
							$text = html::anchor('docs/api/kohana/'.$matches[1], $matches[1]).' '.$matches[2];
						}
						else
						{
							$text = html::anchor('docs/api/kohana/'.$text, $text);
						}
					break;
					case 'uses':
						if (preg_match('/^([a-z_]+)::([a-z_]+)$/i', $text, $matches))
						{
							// Make a class#method API link
							$text = html::anchor('docs/api/'.$matches[1].'#'.$matches[2], $text);
						}
					break;
					// don't show @access lines, cause they are redundant
					case 'access':
					continue 2;
				}

				// Add the tag
				$tags[$name][] = $text;
			}
			else
			{
				// Overwrite the comment line
				$comment[$i] = (string) $line;
			}
		}

		// Concat the comment lines back to a block of text
		if ($comment = trim(implode("\n", $comment)))
		{
			// Parse the comment with Markdown
			$comment = Markdown($comment);
		}

		return array($comment, $tags);
	}

	/**
	 * Get the source of a function
	 *
	 * @param  string   the filename
	 * @param  int      start line?
	 * @param  int      end line?
	 */
	public static function source($file, $start, $end)
	{
		if ( ! $file)
		{
			return FALSE;
		}

		$file = file($file, FILE_IGNORE_NEW_LINES);

		$file = array_slice($file, $start - 1, $end - $start + 1);

		if (preg_match('/^(\s+)/', $file[0], $matches))
		{
			$padding = strlen($matches[1]);

			foreach ($file as & $line)
			{
				$line = substr($line, $padding);
			}
		}

		return implode("\n", $file);
	}

	/**
	 * Test whether a class should be shown, based on the api_packages config option
	 *
	 * @param  Kodoc_Class  the class to test
	 * @return  bool  whether this class should be shown
	 */
	public static function show_class(Kodoc_Class $class)
	{
		$api_packages = Kohana::config('userguide.api_packages');

		// If api_packages is TRUE, all packages should be shown
		if ($api_packages === TRUE)
			return TRUE;

		// Get the package tags for this class (as an array)
		$packages = arr::get($class->tags,'package',Array('None'));

		$show_this = FALSE;

		// Loop through each package tag
		foreach ($packages as $package)
		{
			// If this package is in the allowed packages, set show this to TRUE
			if (in_array($package,explode(',',$api_packages)))
				$show_this = TRUE;
		}

		return $show_this;
	}


} // End Kodoc
