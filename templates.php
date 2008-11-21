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

function cfct_page($file = '') {
	if (empty($file)) {
		$file = cfct_default_file('pages');
	}
	cfct_template_file('pages', $file);
}

function cfct_header() {
	$file = cfct_choose_general_template('header');
	cfct_template_file('header', $file);
}

function cfct_footer() {
	$file = cfct_choose_general_template('footer');
	cfct_template_file('footer', $file);
}

function cfct_sidebar() {
	$file = cfct_choose_general_template('sidebar');
	cfct_template_file('sidebar', $file);
}

function cfct_posts() {
	$file = cfct_choose_general_template('posts');
	cfct_template_file('posts', $file);
}

function cfct_single() {
	$file = cfct_choose_general_template('single');
	cfct_template_file('single', $file);
}

function cfct_loop() {
	$file = cfct_choose_general_template('loop');
	cfct_template_file('loop', $file);
}

function cfct_content() {
	$file = cfct_choose_content_template();
	cfct_template_file('content', $file);
}

function cfct_excerpt() {
	$file = cfct_choose_content_template('excerpt');
	cfct_template_file('excerpt', $file);
}

function cfct_comments() {
	$file = cfct_choose_general_template('comments');
	cfct_template_file('comments', $file);
}

function cfct_comment() {
	$file = cfct_choose_comment_template();
	cfct_template_file('comment', $file);
}

function cfct_form($name = '') {
	cfct_template_file('forms', $name);
}

function cfct_misc($name = '') {
	cfct_template_file('misc', $name);
}

function cfct_error($name = '') {
	cfct_template_file('error', $name);
}


?>