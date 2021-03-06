<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class property documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc_Property extends Kodoc {

	/**
	 * @var  ReflectionProperty  The ReflectionProperty for this property
	 */
	public $property;

	/**
	 * @var  string   Modifiers, like public, private, static. Defaults to 'public'
	 */
	public $modifiers = 'public';

	/**
	 * @var  string  The variable type, retreived from the comment
	 */
	public $type;

	/**
	 * @var  string  The value of this property
	 */
	public $value;

	public function __construct($class, $property)
	{
		$property = new ReflectionProperty($class, $property);

		list($description, $tags) = Kodoc::parse($property->getDocComment());

		$this->description = $description;

		if ($modifiers = $property->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		if (isset($tags['var']))
		{
			if (preg_match('/^(\S*)(?:\s*(.+?))?$/', $tags['var'][0], $matches))
			{
				$this->type = $matches[1];

				if (isset($matches[2]))
				{
					$this->description = $matches[2];
				}
			}
		}

		$this->property = $property;

		if ($property->isStatic())
		{
			$this->value = Kohana::debug(! is_object($var = $property->getValue($class)) ? $var : get_class($var));
		}
	}

} // End Kodoc_Property
