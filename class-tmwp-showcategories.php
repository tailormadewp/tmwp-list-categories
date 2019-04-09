<?php
/**
 * TMWP_showCategories Class File Doc Comment
 *
 * @package  TMWP_showCategories
 * @author   TailormadeWP <hello@tailormadewp.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.tailormadewp.com/
 */

/**
 * TMWP_showCategories Class Doc Comment
 *
 * @category Class
 * @package  TMWP_showCategories
 * @author   TailormadeWP <hello@tailormadewp.com>
 * @author   Marcel Reschke <hello@marcelreschke.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://www.tailormadewp.com/
 */
class TMWP_ShowCategories {

	/**
	 * Show Categories
	 *
	 * Summaries for methods should use 3rd person declarative rather
	 * than 2nd person imperative, beginning with a verb phrase.
	 *
	 * @param  array  $atts Attributes.
	 * @param  string $content Content.
	 * @return TMWP_ShowCategories
	 */
	public static function show_categories( $atts, $content = null ) {
		return wp_list_categories();
	}
}

if ( class_exists( 'TMWP_ShowCategories' ) ) {
	$tmwp_showcategories = new TMWP_ShowCategories();
}
