<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Class method documentation generator.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc_Method extends Kodoc {

	public $name;
	public $modifiers = '';
	public $params = array();
	public $return = array();
	public $source = '';

	public function __construct($class, $method)
	{
		$method = new ReflectionMethod($class, $method);

		$this->name = $method->name;

		$class = $parent = $method->getDeclaringClass();

		if ($modifiers = $method->getModifiers())
		{
			$this->modifiers = '<small>'.implode(' ', Reflection::getModifierNames($modifiers)).'</small> ';
		}

		$comment = '';

		do
		{
			if ($parent->hasMethod($this->name))
			{
				$comment = $parent->getMethod($this->name)->getDocComment();
				// Found a description for this method
				break;
			}
		}
		while ($parent = $parent->getParentClass());

		list($this->description, $tags) = Kodoc::parse($comment);

		if ($file = $class->getFileName())
		{
			$this->source = Kodoc::source($file, $method->getStartLine(), $method->getEndLine());
		}

		if (isset($tags['param']))
		{
			$params = array();

			foreach ($method->getParameters() as $i => $param)
			{
				$param = new Kodoc_Method_Param(array($method->class, $method->name), $i);

				if (isset($tags['param'][$i]))
				{
					if (preg_match('/^(\S*)\s*(\$\w+)?(?:\s*(.+?))?$/', $tags['param'][$i], $matches))
					{
						$param->type = $matches[1];

						$param->description = arr::get($matches, 3);
					}
				}
				$params[] = $param;
			}

			$this->params = $params;

			unset($tags['param']);
		}

		if (isset($tags['return']))
		{
			foreach ($tags['return'] as $return)
			{
				if (preg_match('/^(\S*)(?:\s*(.+?))?$/', $return, $matches))
				{
					$this->return[] = array($matches[1], isset($matches[2]) ? $matches[2] : '');
				}
			}

			unset($tags['return']);
		}

		$this->tags = $tags;
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
		return array('name', 'params', 'return', 'tags', 'source', 'modifiers', 'description');
	}

} // End Kodoc_Method