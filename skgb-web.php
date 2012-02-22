<?php
/*
 * $Id$
 * Copyright (c) 2009 Segel- und Kanugemeinschaft Brucher Talsperre e. V. SKGB
 * Proprietary/Confidential. All Rights Reserved.
 * UTF-8
 */

/*
Plugin Name: SKGB-Web Plugin
Description: Dieses Plugin implementiert verschiedene Details des SKGB-Web.
Author: Arne Johannessen, SKGB
Version: 0.3
P_lugin URI: http://www.skgb.de/
A_uthor URI: http://www.skgb.de/
*/

// made for Wordpress 2.8.4

// try to fix catgeory links
function SB_wp_remove_category_from_category_link ($catlink, $category_id) {
	$XML = array('/category/allgemein', '/category/');
	$HTML = array('/', '/');
	return str_replace($XML, $HTML, $catlink);
}
add_filter('category_link', 'SB_wp_remove_category_from_category_link', 10, 2);

// Transforming XHTML into HTML
// <http://www.robertnyman.com/2006/09/20/how-to-deliver-html-instead-of-xhtml-with-wordpress/>
function SB_wp_xml2html ($buffer) {
	$XML = array(' />');
	$HTML = array('>');
	return str_replace($XML, $HTML, $buffer);
/*
	$patterns = array('/ \/>/');
	$replacements = array('>');
	
	// let's also do some search term highlighting while we're at it
	$pregResult = NULL;
	$searchterm = preg_match('/\/\/[^\/]*\/\?(?:.*&)?s=([^&]+)/', $_SERVER["HTTP_REFERER"], $pregResult) ? $pregResult[1] : FALSE;
	if ($searchterm) {
		$patterns[] = '/>([^<>]*)(' . preg_quote($searchterm) . ')([^<>]*)</i';
		$replacements[] = '>$1<SPAN CLASS="searchterm">$2</SPAN>$3<';
	}
	
	return preg_replace($patterns, $replacements, $buffer);
*/
}
function SB_wp_xml2html_ob_start () {
	ob_start('SB_wp_xml2html');
}
add_action('get_header', 'SB_wp_xml2html_ob_start');

function SB_highlight_searchterms ($the_content) {
	$searchterm = FALSE;
	if (array_key_exists('s', $_GET)) {
		$searchterm = $_GET['s'];
	}
	elseif (array_key_exists('HTTP_REFERER', $_SERVER)) {
		$pregResult = NULL;
		$searchterm = preg_match('/\/\/[^\/]*\/\?(?:.*&)?s=([^&]+)/', $_SERVER["HTTP_REFERER"], $pregResult) ? $pregResult[1] : FALSE;
	}
	if (! $searchterm) {
		// not a search result page, move along
		return $the_content;
	}
	
	$pattern = '/>([^<>]*)(' . preg_quote($searchterm) . ')([^<>]*)</i';
	$replacement = '>$1<SPAN CLASS="searchterm">$2</SPAN>$3<';
	return preg_replace($pattern, $replacement, $the_content);
}
add_filter('the_content', 'SB_highlight_searchterms');
add_filter('the_excerpt', 'SB_highlight_searchterms');

// used Dashboard code example 'dashboard-google-pagerank' by Weston Deboer
function SB_wp_dashboard_test() {
	echo '<A HREF="//intern.skgb.de/digest/">SKGB-intern Hauptmenü</A>';
//	echo "<PRE>\n\n";
//	print_r(wp_upload_dir());
//	echo "</PRE>";
}
function SB_wp_dashboard_setup () {
	wp_add_dashboard_widget('SB_wp_dashboard_test', 'SKGB-intern', 'SB_wp_dashboard_test');
}
add_action('wp_dashboard_setup', 'SB_wp_dashboard_setup');



function SB_wp_disable_rich_editor_option () {
	if (IS_PROFILE_PAGE) {
		echo '<script type="text/javascript">if (document.addEventListener) { document.addEventListener("DOMContentLoaded", function () { document.getElementById("rich_editing").disabled = true; }, false); }</script>';
	}
}
add_action('admin_head', 'SB_wp_disable_rich_editor_option');

function SB_wp_disable_rich_editor ( $user_id ) {
	$_POST['rich_editing'] = 'false';
}
add_action('personal_options_update', 'SB_wp_disable_rich_editor');
add_action('edit_user_profile_update', 'SB_wp_disable_rich_editor');



/*
# work in progress; see /wp-includes/query.php
function SB_the_post () {
	global $wp_query;
	$wp_query->the_post();
	#if (is_front_page() && get_post_meta(get_the_ID(), 'teflon_post', TRUE) == '1') { continue; }
}
add_action('the_post', 'SB_the_post');
*/


/*
// taken from 'search-meter' (C) by Bennett McElwee, GPL
// :TODO: is the GPL acceptable for us? if not, maybe this qualifies as minor / free use
// <http://codex.wordpress.org/Adding_Administration_Menus>
function SB_add_admin_pages() {
	function SB_summary_page() {
?>
<div class="wrap">
	<h2>SKGB Dashbord</h2>
</div>
<?php
	}
	function SB_options_page() {
?>
<div class="wrap">
	<h2>SKGB Options</h2>
</div>
<?php
	}
	function SB_plugin_action_links ($action_links) {
		// taken from 'custom-post-limits' (C) Scott Reilly, MIT
		$settings_link = '<a href="options-general.php?page=' . plugin_basename(__FILE__) . '">' . __('Settings') . '</a>';
		array_unshift($action_links, $settings_link);
		return $action_links;
	}
	add_submenu_page('index.php', 'SKGB Dashbord', 'SKGB', 'read', __FILE__, 'SB_summary_page');  // 'index.php' is what makes this go into the Dashbord
	add_options_page('SKGB Options', 'SKGB', 'manage_options', __FILE__, 'SB_options_page');
	if (current_user_can('manage_options')) {
		global $wp_version;
		if (version_compare($wp_version, '2.6.999', '>')) {
			add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'SB_plugin_action_links');
		}
	}
}
//add_action('admin_head', 'SB_stats_css');
add_action('admin_menu', 'SB_add_admin_pages');
*/

?>
