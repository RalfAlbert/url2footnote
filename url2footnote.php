<?php
/**
 * WordPress-Plugin Url2Footnote
 * PHP version 5.2
 *
 * @category PHP
 * @package WordPress
 * @subpackage url2footnote
 * @author Ralf Albert <me@neun12.de>
 * @license GPLv3 http://www.gnu.org/licenses/gpl-3.0.txt
 * @version 1.1
 * @link http://wordpress.com
 */

/**
 * Plugin Name: Url2Footnote
 * Plugin URI: http://yoda.neun12.de
 * Text Domain:
 * Domain Path:
 * Description: Creates a link summary at the end of each post
 * Author: Ralf Albert
 * Author URI: http://yoda.neun12.de/
 * Version: 1.1
 * License: GPLv3
 */

class Url2Footnote
{

	/**
	 * Counter for links
	 * @var integer
	 */
	public $counter  = 0;

	/**
	 * List with urls
	 * @var array
	 */
	public $url_list = array();

	/**
	 * New content with footnotes
	 * @var string
	 */
	public $result   = '';

	/**
	 * The constructor runs the replacement
	 * @param string $content
	 */
	public function __construct( $content ) {

		$this->result = preg_replace_callback( '#(<a\s[^>]*href=[\'|\"](.+)[\'|"].*>.*</a>)#iUus', array( $this, 'replace_callback' ), $content );

	}

	/**
	 * Callback function for preg_match_callback() in constructor
	 * @param array $matches Array with matches
	 * @return string Link with footnote
	 */
	public function replace_callback( $matches ) {

		$this->url_list[] = $matches[2];

		$this->counter++;

		return $matches[0] . sprintf( '<a href="#url2footnote_%1$d"> <span class="url2footnote-footnote">[%1$d]</span></a>', $this->counter );

	}

	/**
	 * Returns the content with footnotes
	 * @return string Content
	 */
	public function get_content() {

		return $this->result;

	}

	/**
	 * Returns the list with urls
	 * @return string List with urls
	 */
	public function get_url_list() {

		$list = '';

		foreach ( $this->url_list as $index => $link ) {
			$list .= sprintf( '[%1$d] <a name="url2footnote_%1$d">%2$s</a><br>', $index + 1, $link );
		}

		return sprintf( '<div class="url2footnote">%s</div>', $list );

	}

}

/**
 * Callback for add_filter
 * @param string $content The (post) content
 * @return string         Content with url list
 */
function url2footnote( $content = NULL ) {

	if ( empty( $content ) || ! is_main_query() )
		return $content;

	$u2f = new Url2Footnote( $content );

	return $u2f->get_content() . $u2f->get_url_list();

}

add_filter( 'the_content', 'url2footnote', 10 );
