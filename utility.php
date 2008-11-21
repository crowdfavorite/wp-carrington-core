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

function cfct_die($str = '') {
	if (!empty($str)) {
		include(CFCT_PATH.'error/exit.php');
		die();
	}
}

function cfct_banner($str = '') {
	if (!empty($str)) {
		include(CFCT_PATH.'misc/banner.php');
	}
}

function cfct_get_option($name) {
	$defaults = array(
		'cfct_list_limit_1' => CFCT_HOME_LIST_LENGTH
		, 'cfct_latest_limit_1' => CFCT_HOME_LATEST_LENGTH
		, 'cfct_list_limit_2' => CFCT_HOME_LIST_LENGTH
		, 'cfct_latest_limit_2' => CFCT_HOME_LATEST_LENGTH
		, 'cfct_list_limit_3' => CFCT_HOME_LIST_LENGTH
		, 'cfct_latest_limit_3' => CFCT_HOME_LATEST_LENGTH
		, 'cfct_ajax_load' => 'yes'
		, 'cfct_credit' => 'yes'
	);
	$value = get_option($name);
	if ($value == '' && isset($defaults[$name])) {
		$value = $defaults[$name];
	}
	return $value;
}

function cfct_load_plugins() {
	$files = cfct_files(CFCT_PATH.'plugins');
	foreach ($files as $file) {
		include(CFCT_PATH.'plugins/'.$file);
	}
}

function cfct_default_file($dir) {
	$fancy = $dir.'-default.php';
	file_exists(CFCT_PATH.$dir.'/'.$fancy) ? $default = $fancy : $default = 'default.php';
	return $default;
}

function cfct_context() {
	$context = 'home';
	if (is_page()) {
		$context = 'page';
	}
	else if (is_single()) {
		$context = 'single';
	}
	else if (is_category()) {
		$context = 'category';
	}
	else if (is_tag()) {
		$context = 'tag';
	}
	else if (is_author()) {
		$context = 'author';
	}
	else if (is_archive()) {
// possible future abstraction for:
// 	is_month()
// 	is_year()
// 	is_day()
		$context = 'archive';
	}
	else if (is_search()) {
		$context = 'search';
	}
	else if (is_home()) {
		$context = 'home';
		// TODO - check page #
	}
	else if (is_404()) {
		$context = '404';
	}
	return apply_filters('cfct_context', $context);
}

/**
 * @param $template = folder name of file
 * @param $type = file name of file
 * @param $keys = keys that could be used for additional filename params
 * returns false if file does not exist
 *
 */
function cfct_filename($dir, $type = 'default', $keys = array()) {
	switch ($type) {
		case 'author':
			if (count($keys)) {
				$file = 'author-'.$keys[0];
			}
			else {
				$file = 'author';
			}
			break;
		case 'category':
			if (count($keys)) {
				$file = 'cat-'.$keys[0];
			}
			else {
				$file = 'category';
			}
			break;
		case 'tag':
			if (count($keys)) {
				$file = 'tag-'.$keys[0];
			}
			else {
				$file = 'tag';
			}
			break;
		case 'meta':
			if (count($keys)) {
				foreach ($keys as $k => $v) {
					if (!empty($v)) {
						$file = 'meta-'.$k.'-'.$v;
					}
					else {
						$file = 'meta-'.$k;
					}
					break;
				}
			}
			break;
		case 'user':
			if (count($keys)) {
				$file = 'user-'.$keys[0];
			}
			break;
		case 'role':
			if (count($keys)) {
				$file = 'role-'.$keys[0];
			}
			break;
		case 'parent':
			if (count($keys)) {
				$file = 'parent-'.$keys[0];
			}
			break;
		default:
		// handles single, etc.
			$file = $type;
	}
	// fallback for category, author, tag, etc.
	$path = CFCT_PATH.$dir.'/'.$file.'.php';
	if (!file_exists($path)) {
		switch ($type) {
			case 'author':
			case 'category':
			case 'tag':
				$archive_file = CFCT_PATH.$dir.'/archive.php';
				if (file_exists($archive_file)) {
					$path = $archive_file;
				}
		}
	}
	$default = CFCT_PATH.$dir.'/'.cfct_default_file($dir);
	if (file_exists($path)) {
		$path = $path;
	}
	else if (file_exists($default)) {
		$path = $default;
	}
	else {
		$path = false;
	}
	return apply_filters('cfct_filename', $path);
}

function cfct_template($dir, $keys = array()) {
	$context = cfct_context();
	$file = cfct_filename($dir, $context, $keys);
	if ($file) {
		include($file);
	}
	else {
		cfct_die('Error loading '.$dir.' '.__LINE__);
	}
}

function cfct_template_file($dir, $file) {
	$path = '';
	if (!empty($file)) {
		$file = basename($file, '.php');
		$path = CFCT_PATH.$dir.'/'.$file.'.php';
	}
	if (file_exists($path)) {
		include($path);
	}
	else {
		cfct_die('Error loading '.$file.' '.__LINE__);
	}
}

function cfct_choose_general_template($dir) {
	$exec_order = array(
		'author'
		, 'role'
		, 'category'
		, 'tag'
		, 'default'
	);
	$new_exec_order = apply_filters('cfct_general_match_order', $exec_order);
	$files = cfct_files(CFCT_PATH.$dir);
	foreach ($new_exec_order as $func) {
		$func_name = 'cfct_choose_general_template_'.$func;
		if (function_exists($func_name) && in_array($func, $exec_order)) {
			$filename = $func_name($dir, $files);
			if ($filename != false) {
				break;
			}
		}
	}
	return apply_filters('cfct_choose_general_template', $filename);
}

function cfct_choose_general_template_author($dir, $files) {
	$files = cfct_author_templates($dir, $files);
	if (count($files)) {
		$username = get_query_var('author_name');
		$filename = 'author-'.$username.'.php';
		if (in_array($filename, $files)) {
			$keys = array($username);
			return cfct_filename($dir, 'author', $keys);
		}
 	}
	return false;
}

function cfct_choose_general_template_category($dir, $files) {
	$files = cfct_cat_templates($dir, $files);
	if (count($files)) {
		global $cat;
		$slug = cfct_cat_id_to_slug($cat);
		if (in_array('cat-'.$slug.'.php', $files)) {
			$keys = array($slug);
			return cfct_filename($dir, 'category', $keys);
		}
 	}
	return false;
}

function cfct_choose_general_template_tag($dir, $files) {
	$files = cfct_tag_templates($dir, $files);
	if (count($files)) {
		$tag = get_query_var('tag');
		if (in_array('tag-'.$tag.'.php', $files)) {
			$keys = array($tag);
			return cfct_filename($dir, 'tag', $keys);
		}
 	}
	return false;
}

function cfct_choose_general_template_role($dir, $files) {
	$files = cfct_role_templates($dir, $files);
	if (count($files)) {
		$username = get_query_var('author_name');
		$user = new WP_User(cfct_username_to_id($username));
		if (!empty($user->user_login)) {
			if (count($user->roles)) {
				foreach ($user->roles as $role) {
					$role_file = 'role-'.$role.'.php';
					if (in_array($role_file, $files)) {
						return $role_file;
					}
				}
			}
		}
 	}
	return false;
}

function cfct_choose_general_template_default($dir, $files) {
	$context = cfct_context();
	return cfct_filename($dir, $context);
}



function cfct_choose_content_template($type = 'content') {
	$exec_order = array(
		'author'
		, 'meta'
		, 'category'
		, 'role'
		, 'tag'
		, 'parent'
		, 'default'
	);
	$new_exec_order = apply_filters('cfct_content_match_order', $exec_order);
	$files = cfct_files(CFCT_PATH.$type);
	foreach ($new_exec_order as $func) {
		$func_name = 'cfct_choose_content_template_'.$func;
		if (function_exists($func_name) && in_array($func, $exec_order)) {
			$filename = $func_name($type, $files);
			if ($filename != false) {
				break;
			}
		}
	}
	return apply_filters('cfct_choose_content_template', $filename);
}

function cfct_choose_content_template_author($type = 'content', $files = null) {
	$files = cfct_author_templates($type, $files);
	if (count($files)) {
		$author = get_the_author_login();
		$file = 'author-'.$author.'.php';
		if (in_array($file, $files)) {
			$keys = array($author);
			return cfct_filename($type, 'author', $keys);
		}
	}
	return false;
}

function cfct_choose_content_template_meta($type = 'content', $files = null) {
	global $post;
	$files = cfct_meta_templates($type, $files);
	if (count($files)) {
		$meta = get_post_custom($post->ID);
		if (count($meta)) {
// check key, value matches first
			foreach ($meta as $k => $v) {
				$val = $v[0];
				$file = 'meta-'.$k.'-'.$val.'.php';
				if (in_array($file, $files)) {
					$keys = array($k => $val);
					return cfct_filename($type, 'meta', $keys);
				}
			}
// check key matches only
			foreach ($meta as $k => $v) {
				$file = 'meta-'.$k.'.php';
				if (in_array($file, $files)) {
					$keys = array($k => '');
					return cfct_filename($type, 'meta', $keys);
				}
			}
		}
	}
	return false;
}

function cfct_choose_content_template_category($type = 'content', $files = null) {
	$files = cfct_cat_templates($type, $files);
	if (count($files)) {
		foreach ($files as $file) {
			$cat_id = cfct_cat_filename_to_id($file);
			if (in_category($cat_id)) {
				$keys = array(cfct_cat_filename_to_slug($file));
				return cfct_filename($type, 'category', $keys);
			}
		}
	}
	return false;
}

function cfct_choose_content_template_role($type = 'content', $files = null) {
	$files = cfct_role_templates($type, $files);
	if (count($files)) {
		$user = new WP_User(get_the_author_ID());
		if (count($user->roles)) {
			foreach ($files as $file) {
				foreach ($user->roles as $role) {
					if (cfct_role_filename_to_name($file) == $role) {
						$keys = array($role);
						return cfct_filename($type, 'role', $keys);
					}
				}
			}
		}
	}
	return false;
}

function cfct_choose_content_template_tag($type = 'content', $files = null) {
	global $post;
	$files = cfct_tag_templates($type, $files);
	if (count($files)) {
		$tags = get_the_tags($post->ID);
		if (count($tags)) {
			foreach ($files as $file) {
				foreach ($tags as $tag) {
					if ($tag->slug == cfct_tag_filename_to_name($file)) {
						$keys = array($tag->slug);
						return cfct_filename($type, 'tag', $keys);
					}
				}
			}
		}
	}
	return false;
}

function cfct_choose_content_template_parent($type = 'content', $files = null) {
	global $post;
	$files = cfct_parent_templates($type, $files);
	if (count($files) && $post->post_parent > 0) {
		$parent = cfct_post_id_to_slug($post->post_parent);
		$file = 'parent-'.$parent.'.php';
		if (in_array($file, $files)) {
			$keys = array($parent);
			return cfct_filename($type, 'parent', $keys);
		}
	}
	return false;
}

function cfct_choose_content_template_default($type = 'content') {
	$context = cfct_context();
	return cfct_filename($type, $context);
}

function cfct_choose_comment_template() {
	$exec_order = array(
		'ping'
		, 'author'
		, 'user'
		, 'role'
		, 'default'
	);
	$new_exec_order = apply_filters('cfct_comment_match_order', $exec_order);
	$files = cfct_files(CFCT_PATH.'comment');
	foreach ($new_exec_order as $func) {
		$func_name = 'cfct_choose_comment_template_'.$func;
		if (function_exists($func_name) && in_array($func, $exec_order)) {
			$filename = $func_name($files);
			if ($filename != false) {
				break;
			}
		}
	}
	return apply_filters('cfct_choose_comment_template', $filename);
}

function cfct_choose_comment_template_ping($files) {
	global $comment;
	switch ($comment->comment_type) {
		case 'pingback':
		case 'trackback':
			return 'ping';
			break;
	}
	return false;
}

function cfct_choose_comment_template_author($files) {
	global $post, $comment;
	if (!empty($comment->user_id) && $comment->user_id == $post->post_author && in_array('author.php', $files)) {
		return 'author';
 	}
	return false;
}

function cfct_choose_comment_template_user($files) {
	global $comment;
	$files = cfct_comment_templates('user', $files);
	if (count($files) && !empty($comment->user_id)) {
		$user = new WP_User($comment->user_id);
		if (!empty($user->user_login)) {
			$user_file = 'user-'.$user->user_login.'.php';
			if (in_array($user_file, $files)) {
				return $user_file;
			}
		}
 	}
	return false;
}

function cfct_choose_comment_template_role($files) {
	global $comment;
	$files = cfct_comment_templates('user', $files);
	if (count($files) && !empty($comment->user_id)) {
		$user = new WP_User($comment->user_id);
		if (!empty($user->user_login)) {
			if (count($user->roles)) {
				foreach ($user->roles as $role) {
					$role_file = 'role-'.$role.'.php';
					if (in_array($role_file, $files)) {
						return $role_file;
					}
				}
			}
		}
 	}
	return false;
}

function cfct_choose_comment_template_default($files) {
	return cfct_default_file('comment');
}

function cfct_files($path) {
	$files = wp_cache_get('cfct_files_'.$path, 'cfct');
	if ($files) {
		return $files;
	}
	$files = array();
	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			$path = trailingslashit($path);
			if (is_file($path.$file) && strtolower(substr($file, -4, 4)) == ".php") {
				$files[] = $file;
			}
		}
	}
	wp_cache_set('cfct_files_'.$path, $files, 'cfct', 3600);
	return $files;
}

function cfct_filter_files($files = array(), $prefix = '') {
	$matches = array();
	if (count($files)) {
		foreach ($files as $file) {
			if (substr($file, 0, strlen($prefix)) == $prefix) {
				$matches[] = $file;
			}
		}
	}
	return $matches;
}

function cfct_meta_templates($dir, $files = null) {
	if (is_null($files)) {
		$files = cfct_files(CFCT_PATH.$dir);
	}
	$matches = cfct_filter_files($files, 'meta-');
	return apply_filters('cfct_meta_templates', $matches);
}

function cfct_cat_templates($dir, $files = null) {
	if (is_null($files)) {
		$files = cfct_files(CFCT_PATH.$dir);
	}
	$matches = cfct_filter_files($files, 'cat-');
	return apply_filters('cfct_cat_templates', $matches);
}

function cfct_tag_templates($dir, $files = null) {
	if (is_null($files)) {
		$files = cfct_files(CFCT_PATH.$dir);
	}
	$matches = cfct_filter_files($files, 'tag-');
	return apply_filters('cfct_tag_templates', $matches);
}

function cfct_author_templates($dir, $files = null) {
	if (is_null($files)) {
		$files = cfct_files(CFCT_PATH.$dir);
	}
	$matches = cfct_filter_files($files, 'author-');
	return apply_filters('cfct_author_templates', $matches);
}

function cfct_role_templates($dir, $files = null) {
	if (is_null($files)) {
		$files = cfct_files(CFCT_PATH.$dir);
	}
	$matches = cfct_filter_files($files, 'role-');
	return apply_filters('cfct_role_templates', $matches);
}

function cfct_parent_templates($dir, $files = null) {
	if (is_null($files)) {
		$files = cfct_files(CFCT_PATH.$dir);
	}
	$matches = cfct_filter_files($files, 'parent-');
	return apply_filters('cfct_parent_templates', $matches);
}

function cfct_comment_templates($type, $files = false) {
	if (!$files) {
		$files = cfct_files(CFCT_PATH.'comment');
	}
	$matches = array();
	switch ($type) {
		case 'user':
			$matches = cfct_filter_files($files, 'user-');
			break;
		case 'role':
			$matches = cfct_filter_files($files, 'role-');
			break;
	}
	return apply_filters('cfct_comment_templates', $matches);
}

function cfct_cat_filename_to_id($file) {
	$cat = str_replace(array('cat-', '.php'), '', $file);
	$cat = get_category_by_slug($cat);
	return $cat->cat_ID;
}

function cfct_cat_filename_to_name($file) {
	$cat = str_replace(array('cat-', '.php'), '', $file);
	$cat = get_category_by_slug($cat);
	return $cat->name;
}

function cfct_cat_filename_to_slug($file) {
	return str_replace(array('cat-', '.php'), '', $file);
}

function cfct_cat_id_to_slug($id) {
	$cat = &get_category($id);
	return $cat->slug;
}

function cfct_username_to_id($username) {
	return get_profile('ID', $username);
}

function cfct_tag_filename_to_name($file) {
	return str_replace(array('tag-', '.php'), '', $file);
}

function cfct_author_filename_to_name($file) {
	return str_replace(array('author-', '.php'), '', $file);
}

function cfct_role_filename_to_name($file) {
	return str_replace(array('role-', '.php'), '', $file);
}

function cfct_hcard_comment_author_link($str) {
	return str_replace('<a href', "<a class='fn url' href", $str);
}
function cfct_hcard_ping_author_link($str) {
	return str_replace('<a href', "<a rel='bookmark' class='fn url' href", $str);
}

function cfct_post_id_to_slug($id) {
	$post = get_post($id);
	return $post->post_name;
}

?>