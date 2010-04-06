<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Custom Markdown parser for Kohana documentation.
 *
 * @package    Userguide
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kodoc_Markdown extends MarkdownExtra_Parser {

	/**
	 * @var  string  base url for links
	 */
	public static $base_url = '';

	/**
	 * @var  string  base url for images
	 */
	public static $image_url = '';

	protected $header_ids = array();

	public function __construct()
	{
		// doImage is 10, add image url just before
		$this->span_gamut['doImageURL'] = 9;

		// doLink is 20, add base url just before
		$this->span_gamut['doBaseURL'] = 19;

		// Add API links
		$this->span_gamut['doAPI'] = 90;

		// Add note spans last
		$this->span_gamut['doNotes'] = 100;

		// Add toc
		$this->span_gamut['doTOC'] = 100;

		// PHP4 makes me sad.
		parent::MarkdownExtra_Parser();
	}

	public function _doHeaders_callback_setext($matches)
	{
		if (isset($matches[2]))
		{
			$this->header_ids[] = $matches[2];
		}
		return parent::_doHeaders_callback_setext($matches);
	}

	public function _doHeaders_callback_atx($matches)
	{
		if (isset($matches[3]))
		{
			$this->header_ids[] = $matches[3];
		}
		return parent::_doHeaders_callback_atx($matches);
	}

	public function doTOC($text)
	{
		/**if ( ! preg_match('/^{{toc = (.+)}}$/', $text, $match))
		{
			echo Kohana::debug($match);
			die;
			$text = strtr($text, array($match[0], Kohana::debug($this->header_ids)));
		}**/

		return $text;
	}

	/**
	 * Add the current base url to all links.
	 *
	 * @param   string  span text
	 * @return  string
	 */
	public function doBaseURL($text)
	{
		return preg_replace_callback('~(?!!)\[(.+?)\]\(([^#]\S*(?:\s*".+?")?)\)~', array($this, '_add_base_url'), $text);
	}

	public function _add_base_url($matches)
	{
		if ($matches[2] AND strpos($matches[2], '://') === FALSE)
		{
			// Add the base url to the link URL
			$matches[2] = Kodoc_Markdown::$base_url.$matches[2];
		}
		// Recreate the link
		return "[{$matches[1]}]({$matches[2]})";
	}

	/**
	 * Add the current base url to all images.
	 *
	 * @param   string  span text
	 * @return  string
	 */
	public function doImageURL($text)
	{
		return preg_replace_callback('#!\[(.+?)\]\((\S*(?:\s*".+?")?)\)#', array($this, '_add_image_url'), $text);
	}

	public function _add_image_url($matches)
	{
		if ($matches[2] AND strpos($matches[2], '://') === FALSE)
		{
			// Add the base url to the link URL
			$matches[2] = Kodoc_Markdown::$image_url.$matches[2];
		}

		// Recreate the link
		return "![{$matches[1]}]({$matches[2]})";
	}

	public function doAPI($text)
	{
		return preg_replace_callback('/\[([a-z_]+(?:::[a-z_]+)?)\]/i', array($this, '_convert_api_link'), $text);
	}

	public function _convert_api_link($matches)
	{
		$link = $matches[1];

		if (strpos($link, '::'))
		{
			// Split the class and method
			list($class, $method) = explode('::', $link, 2);

			// Add the id symbol to the method
			$method = '#'.$method;
		}
		else
		{
			// Class with no method
			$class  = $link;
			$method = NULL;
		}

		return html::anchor('userguide/api/'.$class.$method, $link);
	}

	public function doNotes($text)
	{
		if ( ! preg_match('/^\[!!\]\s*(.+)$/D', $text, $match))
		{
			return $text;
		}

		return $this->hashBlock('<p class="note">'.$match[1].'</p>');
	}

} // End Kodoc_Markdown
