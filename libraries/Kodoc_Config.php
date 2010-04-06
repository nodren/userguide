<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Config file documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc_Config {

	public $name;
	public $description = '';
	public $options = array();
	public $tags = array();

	public function __construct($file)
	{
		if ($filename = Kohana::find_file('config', $file))
		{
			$this->name = $file;

			$source = file_get_contents($filename[0]);

			$start_offset = 0;

			// Find the config file comment first
			if (preg_match('~(/\*.*?\*/)~s', $source, $config_comment))
			{
				$comment = Kodoc::parse($config_comment[0]);
				$this->description = $comment[0];
				$this->tags = $comment[1];
				$start_offset = strlen($config_comment[0]);
			}

			preg_match_all('~(/\*.*?\*/)?\s*(\$config\[([^\]]+)]\s*=\s*([^;]*?);)~s', $source, $matches, PREG_SET_ORDER, $start_offset);

			foreach ($matches as $item)
			{
				$comment = Kodoc::parse($item[1]);
				$default = isset($comment[1]['default'][0]) ? Kohana::debug($comment[1]['default'][0]) : NULL;

				// Remove the @default tag
				unset($comment[1]['default']);

				$this->options[] = (object) array
									(
										'description' => $comment[0],
										'source'      => $item[2],
										'name'        => trim($item[3], '\'"'),
										'default'     => $default,
										'tags'        => $comment[1],
									);
			}
		}
		else
		{
			throw new Kohana_Exception('Error reading config file');
		}
	}

	/**
	 * Allows serialization of only the object data. Reflection objects can't be
	 * serialized.
	 *
	 * @return  array
	 */
	public function __sleep()
	{
		// Store only information about the object
		return array('name', 'description', 'options', 'tags');
	}

} // End Kodoc_Config
