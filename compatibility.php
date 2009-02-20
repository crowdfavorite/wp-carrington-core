<?php
// This file is part of the Carrington Theme Framework for WordPress
// http://carringtontheme.com
//
// Copyright (c) 2008-2009 Crowd Favorite, Ltd. All rights reserved.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************
//
// Functions here without the cfct_ prefix are taken from WordPress 2.7 under the GPL

/**
 * Displays classes for post div
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 */

if (!function_exists('post_class')) {
	function post_class( $class = '', $post_id = null ) {
		// Separates classes with a single space, collates classes for post DIV
		echo 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
	}
}

/**
 * Retrieve the classes for the post div as an array.
 *
 * The class names are add are many. If the post is a sticky, then the 'sticky'
 * class name. The class 'hentry' is always added to each post. For each
 * category, the class will be added with 'category-' with category slug is
 * added. The tags are the same way as the categories with 'tag-' before the tag
 * slug. All classes are passed through the filter, 'post_class' with the list
 * of classes, followed by $class parameter value, with the post ID as the last
 * parameter.
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int $post_id An optional post ID.
 * @return array Array of classes.
 */
if (!function_exists('get_post_class')) {
	function get_post_class( $class = '', $post_id = null ) {
		$post = get_post($post_id);

		$classes = array();

		$classes[] = $post->post_type;

		// sticky for Sticky Posts
		if ( is_sticky($post->ID) && is_home())
			$classes[] = 'sticky';

		// hentry for hAtom compliace
		$classes[] = 'hentry';

		// Categories
		foreach ( (array) get_the_category($post->ID) as $cat ) {
			if ( empty($cat->slug ) )
				continue;
			$classes[] = 'category-' . $cat->slug;
		}

		// Tags
		foreach ( (array) get_the_tags($post->ID) as $tag ) {
			if ( empty($tag->slug ) )
				continue;
			$classes[] = 'tag-' . $tag->slug;
		}

		if ( !empty($class) ) {
			if ( !is_array( $class ) )
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		}

		return apply_filters('post_class', $classes, $class, $post_id);
	}
}

/**
 * Display "sticky" CSS class, if a post is sticky.
 *
 * @param int $post_id An optional post ID.
 */
if (!function_exists('sticky_class')) {
	function sticky_class( $post_id = null ) {
		if ( !is_sticky($post_id) )
			return;

		echo " sticky";
	}
}

/**
 * Generates semantic classes for each comment element
 *
 * @param string|array $class One or more classes to add to the class list
 * @param int $comment_id An optional comment ID
 * @param int $post_id An optional post ID
 * @param bool $echo Whether comment_class should echo or return
 */
if (!function_exists('comment_class')) {
	function comment_class( $class = '', $comment_id = null, $post_id = null, $echo = true ) {
		// Separates classes with a single space, collates classes for comment DIV
		$class = 'class="' . join( ' ', get_comment_class( $class, $comment_id, $post_id ) ) . '"';
		if ( $echo)
			echo $class;
		else
			return $class;
	}
}

/**
 * Returns the classes for the comment div as an array
 *
 * @param string|array $class One or more classes to add to the class list
 * @param int $comment_id An optional comment ID
 * @param int $post_id An optional post ID
 * @return array Array of classes
 */
if (!function_exists('get_comment_class')) {
	function get_comment_class( $class = '', $comment_id = null, $post_id = null ) {
		global $comment_alt, $comment_depth, $comment_thread_alt;

		$comment = get_comment($comment_id);

		$classes = array();

		// Get the comment type (comment, trackback),
		$classes[] = ( empty( $comment->comment_type ) ) ? 'comment' : $comment->comment_type;

		// If the comment author has an id (registered), then print the log in name
		if ( $comment->user_id > 0 && $user = get_userdata($comment->user_id) ) {
			// For all registered users, 'byuser'
			$classes[] = 'byuser comment-author-' . $user->user_nicename;
			// For comment authors who are the author of the post
			if ( $post = get_post($post_id) ) {
				if ( $comment->user_id === $post->post_author )
					$classes[] = 'bypostauthor';
			}
		}

		if ( empty($comment_alt) )
			$comment_alt = 0;
		if ( empty($comment_depth) )
			$comment_depth = 1;
		if ( empty($comment_thread_alt) )
			$comment_thread_alt = 0;

		if ( $comment_alt % 2 ) {
			$classes[] = 'odd';
			$classes[] = 'alt';
		} else {
			$classes[] = 'even';
		}

		$comment_alt++;

		// Alt for top-level comments
		if ( 1 == $comment_depth ) {
			if ( $comment_thread_alt % 2 ) {
				$classes[] = 'thread-odd';
				$classes[] = 'thread-alt';
			} else {
				$classes[] = 'thread-even';
			}
			$comment_thread_alt++;
		}

		$classes[] = "depth-$comment_depth";

		if ( !empty($class) ) {
			if ( !is_array( $class ) )
				$class = preg_split('#\s+#', $class);
			$classes = array_merge($classes, $class);
		}

		return apply_filters('comment_class', $classes, $class, $comment_id, $post_id);
	}
}

/**
 * Outputs hidden fields for comment form with unique IDs, based on post ID, making it safe for AJAX pull.
 */
function cfct_comment_id_fields() {
	global $id;

	$replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;
	echo "<input type='hidden' name='comment_post_ID' value='$id' id='comment_post_ID_p$id' />\n";
	echo "<input type='hidden' name='comment_parent' id='comment_parent_p$id' value='$replytoid' />\n";
}


/**
 * Displays form token for unfiltered comments.
 *
 * Will only display nonce token if the current user has permissions for
 * unfiltered html. Won't display the token for other users.
 *
 * @uses $post Gets the ID of the current post for the token
 */
function cfct_comment_form_unfiltered_html_nonce() {
	global $post;

	$post_id = 0;
	if ( !empty($post) )
		$post_id = $post->ID;

	if ( current_user_can('unfiltered_html') )
		cfct_nonce_field('unfiltered-html-comment_' . $post_id, '_wp_unfiltered_html_comment', '_wp_unfiltered_html_comment_p' . $post_id, false);
}
/**
 * Retrieve or display nonce hidden field for forms.
 *
 * The nonce field is used to validate that the contents of the form came from
 * the location on the current site and not somewhere else. The nonce does not
 * offer absolute protection, but should protect against most cases. It is very
 * important to use nonce field in forms.
 *
 * If you set $echo to true and set $referer to true, then you will need to
 * retrieve the {@link wp_referer_field() wp referer field}. If you have the
 * $referer set to true and are echoing the nonce field, it will also echo the
 * referer field.
 *
 * The $action and $name are optional, but if you want to have better security,
 * it is strongly suggested to set those two parameters. It is easier to just
 * call the function without any parameters, because validation of the nonce
 * doesn't require any parameters, but since crackers know what the default is
 * it won't be difficult for them to find a way around your nonce and cause
 * damage.
 *
 * The input name will be whatever $name value you gave. The input value will be
 * the nonce creation value.
 *
 * @param string $action Optional. Action name.
 * @param string $name Optional. Nonce name.
 * @param bool $referer Optional, default true. Whether to set the referer field for validation.
 * @param bool $echo Optional, default true. Whether to display or return hidden form field.
 * @return string Nonce field.
 */
function cfct_nonce_field( $action = -1, $name = "_wpnonce", $id = "_wpnonce", $referer = true , $echo = true ) {
	$name = attribute_escape( $name );
	$nonce_field = '<input type="hidden" id="' . $id . '" name="' . $name . '" value="' . wp_create_nonce( $action ) . '" />';
	if ( $echo )
		echo $nonce_field;

	if ( $referer )
		wp_referer_field( $echo, 'previous' );

	return $nonce_field;
}
// Remove the default nonce from the comment form and add our custom one defined above.
remove_action('comment_form', 'wp_comment_form_unfiltered_html_nonce');
add_action('comment_form', 'cfct_comment_form_unfiltered_html_nonce');


/**
 * Filter the comment reply link to add a unique unique ID, based on post ID, making it safe for AJAX pull.
 */
function cfct_get_cancel_comment_reply_link($reply_link, $link, $text) {
	global $post;
	
	$style = '';
	if (!isset($_GET['replytocom'])) {
		$style = ' style="display:none;"';
	}
	
	$reply_link = '<a rel="nofollow" id="cancel-comment-reply-link-p' . $post->ID . '" href="' . $link . '-p' . $post->ID . '"' . $style . '>' . $text . '</a>';
	return $reply_link;
}
add_filter('cancel_comment_reply_link', 'cfct_get_cancel_comment_reply_link', 10, 3);

?>