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

function cfct_get_logout_url($url) {
	if (function_exists('wp_logout_url')) {
		return wp_logout_url($url);
	} 
	else {
		return get_bloginfo('wpurl') . '/wp-login.php?action=logout';
	}
}

function cfct_logout_url($url) {
	echo cfct_get_logout_url($url);
}

?>