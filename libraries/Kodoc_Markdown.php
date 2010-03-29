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

	/**
	 * Currently defined heading ids.
	 * Used to prevent creating multiple headings with same id.
	 * @var array
	 */
	protected $_heading_ids = array();

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

		// Parse Kohana view inclusions at the very end
		$this->document_gamut['doIncludeViews'] = 100;

		// PHP4 makes me sad.
		parent::MarkdownExtra_Parser();
	}

	/**
	 * Callback for the heading setext style
	 *
	 * Heading 1
	 * =========
	 *
	 * @param  array    Matches from regex call
	 * @return string   Generated html
	 */
	function _doHeaders_callback_setext($matches)
	{
		if ($matches[3] == '-' && preg_match('{^- }', $matches[1]))
			return $matches[0];
		$level = $matches[3]{0} == '=' ? 1 : 2;
		$attr  = $this->_doHeaders_attr($id =& $matches[2]);

		// Only auto-generate id if one doesn't exist
		if(empty($attr))
			$attr = ' id="'.$this->make_heading_id($matches[1]).'"';

		$block = "<h$level$attr>".$this->runSpanGamut($matches[1])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

	/**
	 * Callback for the heading atx style
	 *
	 * # Heading 1
	 *
	 * @param  array    Matches from regex call
	 * @return string   Generated html
	 */
	function _doHeaders_callback_atx($matches)
	{
		$level = strlen($matches[1]);
		$attr  = $this->_doHeaders_attr($id =& $matches[3]);

		// Only auto-generate id if one doesn't exist
		if(empty($attr))
			$attr = ' id="'.$this->make_heading_id($matches[2]).'"';

		$block = "<h$level$attr>".$this->runSpanGamut($matches[2])."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}


	/**
	 * Makes a heading id from the heading text
	 * If any heading share the same name then subsequent headings will have an integer appended
	 *
	 * @param  string The heading text
	 * @return string ID for the heading
	 */
	function make_heading_id($heading)
	{
		$id = url::title($heading, '-', TRUE);

		if(isset($this->_heading_ids[$id]))
		{
			$id .= '-';

			$count = 0;

			while (isset($this->_heading_ids[$id]) AND ++$count)
			{
				$id .= $count;
			}
		}

		return $id;
	}

	public function doIncludeViews($text)
	{
		if (preg_match_all('/{{(\S+?)}}/m', $text, $matches, PREG_SET_ORDER))
		{
			$replace = array();

			foreach ($matches as $set)
			{
				list($search, $view) = $set;

				try
				{
					$replace[$search] = View::factory($view)->render();
				}
				catch (Exception $e)
				{
					ob_start();

					// Capture the exception handler output and insert it instead
					Kohana::exception_handler($e);

					$replace[$search] = ob_get_clean();
				}
			}

			$text = strtr($text, $replace);
		}

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
