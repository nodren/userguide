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
	public $properties = array();
	public $methods = array();

	public function __construct($class_name)
	{
		$class = $parent = new ReflectionClass($class_name);

		$this->name = $class->name;

		$this->parents = array();

		if ($modifiers = $class->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		if ($constants = $class->getConstants())
		{
			foreach ($constants as $name => $value)
			{
				$this->constants[$name] = Kohana::debug($value);
			}
		}

		if ($props = $class->getProperties())
		{
			foreach ($props as $key => $property)
			{
				// Only show public properties, because Reflection can't get the private ones
				if ($property->isPublic())
				{
					$this->properties[$key] = new Kodoc_Property($class->name, $property->name);
				}
			}
		}

		if ($methods = $class->getMethods())
		{
			foreach ($methods as $key => $method)
			{
				// Only show methods declared in this class
				$declaring_class = str_replace('_Core', '', $method->getDeclaringClass()->name);
				if ($declaring_class === $class->name)
				{
					$this->methods[$key] = new Kodoc_Method($class->name, $method->name);
				}
			}
		}

		do
		{
			// Skip the comments in the bootstrap file
			if ($comment = $parent->getDocComment() AND basename($parent->getFileName()) !== 'Bootstrap.php')
			{
				// Found a description for this class
				break;
			}
		}
		while ($parent = $parent->getParentClass());

		list($this->description, $this->tags) = Kodoc::parse($comment);
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
		return array('name', 'modifiers', 'constants', 'description', 'tags', 'properties', 'methods', 'parents');
	}

} // End Kodac_Class
