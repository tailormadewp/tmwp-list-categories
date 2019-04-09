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
		$defaults =	array(
			'show_option_all'    => '',
			'orderby'            => 'name',
			'order'              => 'ASC',
			'style'              => 'list',
			'show_count'         => 0,
			'hide_empty'         => 1,
			'use_desc_for_title' => 1,
			'child_of'           => 0,
			'feed'               => '',
			'feed_type'          => '',
			'feed_image'         => '',
			'exclude'            => '',
			'exclude_tree'       => '',
			'include'            => '',
			'hierarchical'       => 1,
			'title_li'           => __( 'Categories' ),
			'show_option_none'   => __( 'No categories' ),
			'number'             => null,
			'echo'               => 1,
			'depth'              => 0,
			'current_category'   => 0,
			'pad_counts'         => 0,
			'taxonomy'           => 'category',
			'walker'             => null,
			'prefix'             => '',
			'postfix'            => '',
		);

		$r = wp_parse_args( $atts, $defaults );
		if ( ! isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
			$r['pad_counts'] = true;
		}

		// Descendants of exclusions should be excluded too.
		if ( true === $r['hierarchical'] ) {
			$exclude_tree = array();

			if ( $r['exclude_tree'] ) {
				$exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $r['exclude_tree'] ) );
			}

			if ( $r['exclude'] ) {
				$exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $r['exclude'] ) );
			}

			$r['exclude_tree'] = $exclude_tree;
			$r['exclude']      = '';
		}

		if ( ! isset( $r['class'] ) ) {
			$r['class'] = ( 'category' === $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];
		}

		if ( ! taxonomy_exists( $r['taxonomy'] ) ) {
			return false;
		}

		$show_option_all  = $r['show_option_all'];
		$show_option_none = $r['show_option_none'];

		$categories = get_categories( $r );

		$output = '';
		if ( $r['title_li'] && 'list' === $r['style'] && ( ! empty( $categories ) || ! $r['hide_title_if_empty'] ) ) {
			$output = '<li class="' . esc_attr( $r['class'] ) . '">' . $r['title_li'] . '<ul>';
		}
		if ( empty( $categories ) ) {
			if ( ! empty( $show_option_none ) ) {
				if ( 'list' === $r['style'] ) {
					$output .= '<li class="cat-item-none">' . $show_option_none . '</li>';
				} else {
					$output .= $show_option_none;
				}
			}
		} else {
			if ( ! empty( $show_option_all ) ) {

				$posts_page = '';

				// For taxonomies that belong only to custom post types, point to a valid archive.
				$taxonomy_object = get_taxonomy( $r['taxonomy'] );
				if ( ! in_array( 'post', $taxonomy_object->object_type, true ) && ! in_array( 'page', $taxonomy_object->object_type, true ) ) {
					foreach ( $taxonomy_object->object_type as $object_type ) {
						$_object_type = get_post_type_object( $object_type );

						// Grab the first one.
						if ( ! empty( $_object_type->has_archive ) ) {
							$posts_page = get_post_type_archive_link( $object_type );
							break;
						}
					}
				}

				// Fallback for the 'All' link is the posts page.
				if ( ! $posts_page ) {
					if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) {
						$posts_page = get_permalink( get_option( 'page_for_posts' ) );
					} else {
						$posts_page = home_url( '/' );
					}
				}

				$posts_page = esc_url( $posts_page );
				if ( 'list' === $r['style'] ) {
					$output .= "<li class='cat-item-all'><a href='$posts_page'>$show_option_all</a></li>";
				} else {
					$output .= "<a href='$posts_page'>$show_option_all</a>";
				}
			}

			if ( empty( $r['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
				$current_term_object = get_queried_object();
				if ( $current_term_object && $r['taxonomy'] === $current_term_object->taxonomy ) {
					$r['current_category'] = get_queried_object_id();
				}
			}

			if ( $r['hierarchical'] ) {
				$depth = $r['depth'];
			} else {
				$depth = -1; // Flat.
			}
			$output .= walk_category_tree( $categories, $depth, $r );
		}

		if ( $r['title_li'] && 'list' === $r['style'] && ( ! empty( $categories ) || ! $r['hide_title_if_empty'] ) ) {
			$output .= '</ul></li>';
		}

		/**
		 * Filters the HTML output of a taxonomy list.
		 *
		 * @since 2.1.0
		 *
		 * @param string $output HTML output.
		 * @param array  $atts   An array of taxonomy-listing arguments.
		 */
		$html = apply_filters( 'wp_list_categories', $output, $atts );

		if ( $r['echo'] ) {
			echo $html;
		} else {
			return $html;
		}
	}
}

if ( class_exists( 'TMWP_ShowCategories' ) ) {
	$tmwp_showcategories = new TMWP_ShowCategories();
}
