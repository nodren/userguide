<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class method parameter documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc_Method_Param extends Kodoc {

	public $name;
	public $type;
	public $default;
	public $description;
	public $byref = FALSE;
	public $optional = FALSE;

	public function __construct($method,$param)
	{
		$param = new ReflectionParameter($method, $param);
		$this->name = $param->name;

		if ($param->isDefaultValueAvailable())
		{
			$default = $param->getDefaultValue();

			if ($default === NULL)
			{
				$this->default .= 'NULL';
			}
			elseif (is_bool($default))
			{
				$this->default .= $default ? 'TRUE' : 'FALSE';
			}
			elseif (is_string($default))
			{
				$this->default .= "'".$default."'";
			}
			else
			{
				$this->default .= print_r($default, TRUE);
			}
		}

		if ($param->isPassedByReference())
		{
			$this->byref = TRUE;
		}

		if ($param->isOptional())
		{
			$this->optional = TRUE;
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
		return array('optional', 'byref', 'default', 'name', 'type', 'description');
	}

} // End Kodoc_Method_Param
