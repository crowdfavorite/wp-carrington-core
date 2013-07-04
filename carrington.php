<?php

// This file is part of the Carrington Core Platform for WordPress
// http://crowdfavorite.com/wordpress/carrington-core/
//
// Copyright (c) 2008-2012 Crowd Favorite, Ltd. All rights reserved.
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

// 	ini_set('display_errors', '1');
// 	ini_set('error_reporting', E_ALL);

define('CFCT_CORE_VERSION', '3.4');

// Path to Carrington Core parent directory (usually the theme).
if (!defined('CFCT_PATH')) {
	define('CFCT_PATH', trailingslashit(TEMPLATEPATH));
}

load_theme_textdomain('carrington');

include_once(CFCT_PATH.'carrington-core/templates.php');
include_once(CFCT_PATH.'carrington-core/utility.php');
include_once(CFCT_PATH.'carrington-core/deprecated.php');

cfct_load_plugins();

function cfct_init() {
}
