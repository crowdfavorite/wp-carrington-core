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

// - add admin page for config settings

function cfct_admin_menu() {
	if (!current_user_can('manage_options')) {
		return;
	}
	add_submenu_page(
		'themes.php'
		, __('Carrington Settings', 'carrington')
		, __('Carrington', 'carrington')
		, 0
		, 'carrington-settings'
		, 'cfct_settings_form'
	);
}
add_action('admin_menu', 'cfct_admin_menu');

function cfct_admin_request_handler() {
	if (isset($_POST['cf_action'])) {
		switch ($_POST['cf_action']) {
			case 'cfct_update_settings':
				call_user_func($_POST['cf_action']);
				wp_redirect(trailingslashit(get_bloginfo('wpurl')).'wp-admin/themes.php?page=carrington-settings&updated=true');
		}
	}
}

function cfct_update_settings() {
	if (!current_user_can('manage_options')) {
		return;
	}
	global $cfct_options;
	foreach ($cfct_options as $option) {
		if (isset($_POST[$option])) {
			update_option($option, stripslashes($_POST[$option]));
		}
	}
	do_action('cfct_update_settings');
}

function cfct_settings_form() {
	if (isset($_GET['updated'])) {
		print('
<div id="message" class="updated fade">
	<p>'.__('Settings updated.', 'carrington').'</p>
</div>
		');
	}
	print('
<div class="wrap">
	<h2>'.__('Carrington Settings', 'carrington').'</h2>
	<form action="options.php" method="post">
		<table class="form-table">
			<tbody>'
//			.cfct_options_home_column('1')
//			.cfct_options_home_column('2')
//			.cfct_options_home_column('3')
			.cfct_options_misc()
			.'</tbody>
		</table>
	');
	do_action('cfct_settings_form');
	print('
		<p class="submit">
			<input type="hidden" name="cf_action" value="cfct_update_settings" />
			<input type="submit" name="submit_button" value="'.__('Save Changes', 'carrington').'" />
		</p>
	</form>
</div>
	');
	do_action('cfct_settings_form_after');
}

function cfct_options_home_column($key) {
	$categories = get_categories('hide_empty=0');
	$cat_options = '';
	foreach ($categories as $category) {
		if ($category->term_id == get_option('cfct_home_col_'.$key.'_cat')) {
			$selected = 'selected="selected"';
		}
		else {
			$selected = '';
		}
		$cat_options .= "\n\t<option value='$category->term_id' $selected>$category->name</option>";
	}
	$show_options = '';
	$show_option = cfct_get_option('cfct_home_column_'.$key.'_content');
	if ($show_option == 'latest') {
		$latest_selected = 'selected="selected"';
		$list_selected = '';
	}
	else {
		$latest_selected = '';
		$list_selected = 'selected="selected"';
	}
	$html = '
				<tr valign="top">
					<th scope="row">'.sprintf(__('Home Column %s', 'carrington'), $key).'</td>
					<td>
						<fieldset>
							<p>
								<label for="cfct_home_column_'.$key.'_cat">'.__('Category:', 'carrington').'</label>
								<select name="cfct_home_column_'.$key.'_cat" id="cfct_home_column_'.$key.'_cat">'.$cat_options.'</select>
							</p>
							<p>
								<label for="cfct_home_column_'.$key.'_content">'.__('Show:', 'carrington').'</label>
								<select name="cfct_home_column_'.$key.'_content" id="cfct_home_column_'.$key.'_content" class="home_column_select">
									<option value="latest" '.$latest_selected.'>'.__('Latest Post Preview', 'carrington').'</option>
									<option value="list" '.$list_selected.'>'.__('List of Recent Post Titles', 'carrington').'</option>
								</select>
							</p>
							<p id="cfct_latest_limit_'.$key.'_option" class="hidden">
								<label for="cfct_latest_limit_'.$key.'">'.__('Length of preview, in characters (250 recommended):', 'carrington').'</label>
								<input type="text" name="cfct_latest_limit_'.$key.'" id="cfct_latest_limit_'.$key.'" value="'.cfct_get_option('cfct_latest_limit_'.$key).'" />
							</p>
							<p id="cfct_list_limit_'.$key.'_option" class="hidden">
								<label for="cfct_list_limit_'.$key.'">'.__('Number of titles to show in list (5 recommended):', 'carrington').'</label>
								<input type="text" name="cfct_list_limit_'.$key.'" id="cfct_list_limit_'.$key.'" value="'.cfct_get_option('cfct_list_limit_'.$key).'" />
							</p>
						</fieldset>
					</td>
				</tr>
	';
	return $html;
}

function cfct_options_misc() {
	$options = array(
		'yes' => 'Yes'
		, 'no' => 'No'
	);
	$ajax_load_options = '';
	$credit_options = '';
	foreach ($options as $k => $v) {
		if ($k == get_option('cfct_ajax_load')) {
			$ajax_load_selected = 'selected="selected"';
		}
		else {
			$ajax_load_selected = '';
		}
		$ajax_load_options .= "\n\t<option value='$k' $ajax_load_selected>$v</option>";
		if ($k == get_option('cfct_credit')) {
			$credit_selected = 'selected="selected"';
		}
		else {
			$credit_selected = '';
		}
		$credit_options .= "\n\t<option value='$k' $credit_selected>$v</option>";
	}
	$cfct_posts_per_archive_page = get_option('cfct_posts_per_archive_page');
	if (intval($cfct_posts_per_archive_page) == 0) {
		$cfct_posts_per_archive_page = 25;
	}
	$html = '
				<tr valign="top">
					<th scope="row">'.sprintf(__('Misc.', 'carrington'), $key).'</td>
					<td>
						<fieldset>
							<p>
								<label for="cfct_about_text">'.__('About text (shown in sidebar):', 'carrington').'</label>
								<br />
								<textarea name="cfct_about_text" id="cfct_about_text" cols="40" rows="8">'.htmlspecialchars(get_option('cfct_about_text')).'</textarea>
							</p>
							<p>
								<label for="cfct_ajax_load">'.__('Load archives and comments with AJAX:', 'carrington').'</label>
								<select name="cfct_ajax_load" id="cfct_ajax_load">'.$ajax_load_options.'</select>
							</p>
							<p>
								<label for="cfct_posts_per_archive_page">'.__('Posts shown on archives pages:', 'carrington').'</label>
								<input type="text" name="cfct_posts_per_archive_page" id="cfct_posts_per_archive_page" value="'.$cfct_posts_per_archive_page.'" size="3" />
							</p>
							<p>
								<label for="cfct_credit">'.__('Give <a href="http://crowdfavorite.com">Crowd Favorite</a> credit in footer:', 'carrington').'</label>
								<select name="cfct_credit" id="cfct_credit">'.$credit_options.'</select>
							</p>
							<p>
								<label for="cfct_wp_footer">'.__('Footer code (for analytics, etc.):', 'carrington').'</label>
								<br />
								<textarea name="cfct_wp_footer" id="cfct_wp_footer" cols="40" rows="5">'.htmlspecialchars(get_option('cfct_wp_footer')).'</textarea>
							</p>
						</fieldset>
					</td>
				</tr>
	';
	return $html;
}

function cfct_admin_js() {
?>
<script type="text/javascript">
jQuery(function() {
	jQuery('select.home_column_select').each(function() {
		cfct_home_columns(jQuery(this), false);
	}).change(function() {
		cfct_home_columns(jQuery(this), true);
	});
});

function cfct_home_columns(elem, slide) {
	var id = elem.attr('id').replace('cfct_home_column_', '').replace('_content', '');
	var val = elem.val();
	var option_show = '#cfct_latest_limit_' + id + '_option';
	var option_hide = '#cfct_list_limit_' + id + '_option';
	if (val == 'list') {
		option_show = '#cfct_list_limit_' + id + '_option';
		option_hide = '#cfct_latest_limit_' + id + '_option';
	}
	if (slide) {
		jQuery(option_hide).slideUp(function() {
			jQuery(option_show).slideDown();
		});
	}
	else {
		jQuery(option_show).show();
		jQuery(option_hide).hide();
	}
}
</script>
<?php
}
add_action('admin_head', 'cfct_admin_js');

// TODO
// - color pickers for background/theme integration

?>