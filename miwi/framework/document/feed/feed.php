<?php
/**
 * @package     Moomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MPATH_PLATFORM') or die;

/**
 * DocumentFeed class, provides an easy interface to parse and display any feed document
 *
 * @package     Moomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class MDocumentFeed extends MDocument
{
	/**
	 * Syndication URL feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $syndicationURL = "";

	/**
	 * Image feed element
	 *
	 * optional
	 *
	 * @var    obMect
	 * @since  11.1
	 */
	public $image = null;

	/**
	 * Copyright feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $copyright = "";

	/**
	 * Published date feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $pubDate = "";

	/**
	 * Lastbuild date feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $lastBuildDate = "";

	/**
	 * Editor feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $editor = "";

	/**
	 * Docs feed element
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $docs = "";

	/**
	 * Editor email feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $editorEmail = "";

	/**
	 * Webmaster email feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $webmaster = "";

	/**
	 * Category feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $category = "";

	/**
	 * TTL feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $ttl = "";

	/**
	 * Rating feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $rating = "";

	/**
	 * Skiphours feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $skipHours = "";

	/**
	 * Skipdays feed element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $skipDays = "";

	/**
	 * The feed items collection
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $items = array();

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @since  11.1
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		// Set document type
		$this->_type = 'feed';
	}

	/**
	 * Render the document
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 *
	 * @since  11.1
	 * @throws Exception
	 * @todo   Make this cacheable
	 */
	public function render($cache = false, $params = array())
	{
		// Get the feed type
		$type = MFactory::getApplication()->input->get('type', 'rss');

		// Instantiate feed renderer and set the mime encoding
		$renderer = $this->loadRenderer(($type) ? $type : 'rss');
		if (!is_a($renderer, 'MDocumentRenderer'))
		{
			throw new Exception(MText::_('MGLOBAL_RESOURCE_NOT_FOUND'), 404);
		}
		$this->setMimeEncoding($renderer->getContentType());

		// Output
		// Generate prolog
		$data = "<?xml version=\"1.0\" encoding=\"" . $this->_charset . "\"?>\n";
		$data .= "<!-- generator=\"" . $this->getGenerator() . "\" -->\n";

		// Generate stylesheet links
		foreach ($this->_styleSheets as $src => $attr)
		{
			$data .= "<?xml-stylesheet href=\"$src\" type=\"" . $attr['mime'] . "\"?>\n";
		}

		// Render the feed
		$data .= $renderer->render();

		parent::render();
		return $data;
	}

	/**
	 * Adds an MFeedItem to the feed.
	 *
	 * @param   MFeedItem  $item  The feeditem to add to the feed.
	 *
	 * @return  MDocumentFeed  instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function addItem(MFeedItem $item)
	{
		$item->source = $this->link;
		$this->items[] = $item;

		return $this;
	}
}

/**
 * MFeedItem is an internal class that stores feed item information
 *
 * @package     Moomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class MFeedItem
{
	/**
	 * Title item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $title;

	/**
	 * Link item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $link;

	/**
	 * Description item element
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $description;

	/**
	 * Author item element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $author;

	/**
	 * Author email element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $authorEmail;

	/**
	 * Category element
	 *
	 * optional
	 *
	 * @var    array or string
	 * @since  11.1
	 */
	public $category;

	/**
	 * Comments element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $comments;

	/**
	 * Enclosure element
	 *
	 * @var    obMect
	 * @since  11.1
	 */
	public $enclosure = null;

	/**
	 * Guid element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $guid;

	/**
	 * Published date
	 *
	 * optional
	 *
	 * May be in one of the following formats:
	 *
	 * RFC 822:
	 * "Mon, 20 Man 03 18:05:41 +0400"
	 * "20 Man 03 18:05:41 +0000"
	 *
	 * ISO 8601:
	 * "2003-01-20T18:05:41+04:00"
	 *
	 * Unix:
	 * 1043082341
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $date;

	/**
	 * Source element
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $source;

	/**
	 * Set the MFeedEnclosure for this item
	 *
	 * @param   MFeedEnclosure  $enclosure  The MFeedEnclosure to add to the feed.
	 *
	 * @return  MFeedItem instance of $this to allow chaining
	 *
	 * @since   11.1
	 */
	public function setEnclosure(MFeedEnclosure $enclosure)
	{
		$this->enclosure = $enclosure;

		return $this;
	}
}

/**
 * MFeedEnclosure is an internal class that stores feed enclosure information
 *
 * @package     Moomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class MFeedEnclosure
{
	/**
	 * URL enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $url = "";

	/**
	 * Length enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $length = "";

	/**
	 * Type enclosure element
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = "";
}

/**
 * MFeedImage is an internal class that stores feed image information
 *
 * @package     Moomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class MFeedImage
{
	/**
	 * Title image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $title = "";

	/**
	 * URL image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $url = "";

	/**
	 * Link image attribute
	 *
	 * required
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $link = "";

	/**
	 * Width image attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $width;

	/**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $height;

	/**
	 * Title feed attribute
	 *
	 * optional
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $description;
}
