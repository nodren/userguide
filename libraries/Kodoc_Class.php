<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc_Class extends Kodoc {

	/**
	 * @var  ReflectionClass The ReflectionClass for this class
	 */
	public $class;

	/**
	 * @var  string  modifiers like abstract, final
	 */
	public $modifiers;

	/**
	 * @var  string  description of the class from the comment
	 */
	public $description;

	/**
	 * @var  array  array of tags, retreived from the comment
	 */
	public $tags = array();

	/**
	 * @var  array  array of this classes constants
	 */
	public $constants = array();

	public function __construct($class)
	{
		$this->class = $parent = new ReflectionClass($class);

		if ($modifiers = $this->class->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		if ($constants = $this->class->getConstants())
		{
			foreach ($constants as $name => $value)
			{
				$this->constants[$name] = Kohana::debug($value);
			}
		}

		do
		{
			if ($comment = $parent->getDocComment())
			{
				// Found a description for this class
				break;
			}
		}
		while ($parent = $parent->getParentClass());

		list($this->description, $this->tags) = Kodoc::parse($comment);
	}

	public function properties()
	{
		$props = $this->class->getProperties();

		sort($props);

		foreach ($props as $key => $property)
		{
			// Only show public properties, because Reflection can't get the private ones
			if ($property->isPublic())
			{
				$props[$key] = new Kodoc_Property($this->class->name, $property->name);
			}
			else
			{
				unset($props[$key]);
			}
		}

		return $props;
	}

	public function methods()
	{
		$all_methods = $this->class->getMethods();

		$methods = array();

		foreach ($all_methods as $key => $method)
		{
			// Only show methods declared in this class
			$declaring_class = str_replace('_Core', '', $method->getDeclaringClass()->name);
			if ($declaring_class === $this->class->name)
			{
				$methods[$key] = new Kodoc_Method($this->class->name, $method->name);
			}
		}
		return $methods;
	}

} // End Kodac_Class
