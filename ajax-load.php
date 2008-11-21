<?php

// This file is part of the Carrington Theme for WordPress
// http://carringtontheme.com
//
// Copyright (c) 2008 Crowd Favorite, Ltd. All rights reserved.
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

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

function cfct_ajax_post_content($post_id) {
	global $posts, $post, $wp;
	$posts = get_posts('include='.$post_id);
	$post = $posts[0];
	if (is_null($post)) {
		$posts = get_pages('include='.$post_id);
		$post = $posts[0];
	}
	setup_postdata($post);
	remove_filter('the_content', 'st_add_widget');
	$wp->send_headers();
	cfct_content();
}

function cfct_ajax_post_comments($post_id) {
	global $post, $wp_query, $wp;
	$wp_query->is_single = true;
	$posts = get_posts('include='.$post_id);
	$post = $posts[0];
	if (is_null($post)) {
		$posts = get_pages('include='.$post_id);
		$post = $posts[0];
	}
	setup_postdata($post);
	$wp->send_headers();
	comments_template();
}

function cfct_ajax_load() {
	if (isset($_GET['cfct_action'])) {
		switch ($_GET['cfct_action']) {
			case 'post_content':
			case 'post_comments':
				if (isset($_GET['id'])) {
					$post_id = intval($_GET['id']);
				}
				else if (isset($_GET['url'])) {
					$post_id = url_to_post_id($_GET['url']);
				}
				if ($post_id) {
					call_user_func('cfct_ajax_'.$_GET['cfct_action'], $post_id);
					die();
				}
		}
	}
}

function cfct_ajax_comment_link() {
	global $post;
	echo ' rev="post-'.$post->ID.'" ';
}
add_filter('comments_popup_link_attributes', 'cfct_ajax_comment_link');

function cfct_posts_per_archive_page($query) {
	$count = get_option('cfct_posts_per_archive_page');
	intval($count) > 0 ? $count = $count : $count = 25;
	$query->set('posts_per_archive_page', $count);
	if (is_category()) {
		$query->set('posts_per_page', $count);
	}
	return $query;
}
add_filter('pre_get_posts', 'cfct_posts_per_archive_page');

?>