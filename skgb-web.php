<?php
/*
 * Copyright (c) 2009 Segel- und Kanugemeinschaft Brucher Talsperre e. V. SKGB
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. See LICENSE for details.
 */

/*
Plugin Name: SKGB-Web Plugin
Description: Dieses Plugin implementiert verschiedene Details des SKGB-Web.
Author: Arne Johannessen, SKGB
Version: 0.5
Plugin URI: https://github.com/skgb/wordpress-plugin
Author URI: https://github.com/johannessen
*/

// automatic updating isn't possible -- wp-includes/update.php#wp_update_plugins hard-codes the plugin update service to api.wordpress.org

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
	echo '<P>Die Textbearbeitung erfolgt in <A HREF="http://de.wikipedia.org/wiki/Markdown#Auszeichnungsbeispiele">Markdown</A>-Syntax (<A HREF="http://daringfireball.net/projects/markdown/syntax" HREFLANG="en">Referenz</A>).';
	echo '<P>→ <A HREF="//intern.skgb.de/">SKGB-intern Hauptmenü</A>';
	echo '<P>→ <A HREF="/wp-admin/tools.php?page=skgb_server_conf">Server-Konfiguration</A>';
//	echo "<PRE>\n\n";
//	print_r(wp_upload_dir());
//	echo "</PRE>";
}
function SB_wp_dashboard_setup () {
	wp_add_dashboard_widget('SB_wp_dashboard_test', 'SKGB', 'SB_wp_dashboard_test');
}
add_action('wp_dashboard_setup', 'SB_wp_dashboard_setup');



function SB_wp_disable_rich_editor_option () {
	if (defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE) {
		echo '<script type="text/javascript">if (document.addEventListener) { document.addEventListener("DOMContentLoaded", function () { document.getElementById("rich_editing").disabled = true; }, false); }</script>';
	}
}
add_action('admin_head', 'SB_wp_disable_rich_editor_option');

function SB_wp_disable_rich_editor ( $user_id ) {
	$_POST['rich_editing'] = 'false';
}
add_action('personal_options_update', 'SB_wp_disable_rich_editor');
add_action('edit_user_profile_update', 'SB_wp_disable_rich_editor');

// the option is only disabled in the GUI if the user views her own profile, not if she views other user's profiles; however, even if the option is enabled, changing it won't have any effect



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


// debugging aid: stacktrace for deprecated functions
if (defined('WP_DEBUG') && WP_DEBUG) {
	function SB_stacktrace_on_deprecated_function ($trigger_error) {
		if ($trigger_error) {
			// print a stacktrace
			// (it's impossible to find the culprit without one; that WP doesn't handle this by itself is disgraceful I think)
			
			// prepare stacktrace (the PHP default is hardly usable; another disgrace)
			$clipBacktrace = debug_backtrace();
			$clipBacktrace[] = array('function' => '&lt;init>');
			array_unshift($clipBacktrace, array('function' => __FUNCTION__, 'file' => __FILE__, 'line' => __LINE__));
			
			// if we know this WP version's call stack structure, we shave off parts we don't need
			global $wp_version;
			$clipTraceIndexBegin = ('3.4' == preg_replace('/^([0-9]+\.[0-9]+).*/', '$1', $wp_version)) ? 5 : 1;
			
			// convert stacktrace to HTML for output
			$clipTraceHtml = '<ol class=debug_stacktrace>';
			for ($clipTraceIndex = $clipTraceIndexBegin; $clipTraceIndex < count($clipBacktrace); $clipTraceIndex++) {
				$clipTraceHtml .= "\n\t" . '<li>' . @$clipBacktrace[$clipTraceIndex]['class'];
				$clipTraceHtml .= @$clipBacktrace[$clipTraceIndex]['type'];
				$clipTraceHtml .= @$clipBacktrace[$clipTraceIndex]['method'] ? $clipBacktrace['method'] : $clipBacktrace[$clipTraceIndex]['function'];
				if (array_key_exists('file', $clipBacktrace[$clipTraceIndex - 1]) || array_key_exists('line', $clipBacktrace[$clipTraceIndex - 1])) {
					$clipTraceHtml .= ' (' . $clipBacktrace[$clipTraceIndex - 1]['file'];
					$clipTraceHtml .= ':' . $clipBacktrace[$clipTraceIndex - 1]['line'] . ')';
				}
				$clipTraceHtml .= '</li>';
			}
			$clipTraceHtml .= "\n" . '</ol>';
			echo $clipTraceHtml;
		}
		return $trigger_error;
	}
	
	// high order for this filter so that earlier-called plug-ins may disable the error message by passing FALSE as $trigger_error
	add_filter('deprecated_function_trigger_error', 'SB_stacktrace_on_deprecated_function', 20);
}


function SB_secure_http_links( $content ) {
	// avoid protocol-specific links to own site in database
	// (URLs, denen ein "<" vorangestellt ist, könnten Teil eines Markdown-Autolinks sein und dürfen nicht verändert werden.)
	// (URLs, denen ein ":" oder "=" vorangestellt ist, könnten Teil eines Query Parameters sein und dürfen nicht verändert werden.)
	$content = preg_replace('{(^|[^<=:])https?://www\.skgb\.de/}', '$1/', $content);
	// avoid frequent mis-spellings of the ligature letter "IJ"
	$content = preg_replace('{(\W)Ij(ssel|lst)}', '$1IJ$2', $content);
	// auto-highlight names of club boats (untested)
//	$content = preg_replace('{([^>])(Vayu|Papillon)}', '$1<i class=bootsname>$2</i>', $content);
	return $content;
}
add_filter('content_save_pre', 'SB_secure_http_links');


# offer config files to user
function SB_server_conf_menu_setup () {
	add_management_page( 'SKGB: Server-Konfiguration', 'Server-Konfig', 'manage_options', 'skgb_server_conf', 'SB_server_conf_menu' );
}
function SB_server_conf_menu () {
	$settings = wp_enqueue_code_editor( array(
		'type' => 'text/nginx',
		'codemirror' => array('readOnly'=>'nocursor') )
	);
	if ( FALSE !== $settings ) {
		$settings = wp_json_encode( $settings );
		wp_add_inline_script( 'code-editor', sprintf('wp.codeEditor.initialize( "skgb-aliases", %s );', $settings) );
		wp_add_inline_script( 'code-editor', sprintf('wp.codeEditor.initialize( "skgb-siteconf", %s );', $settings) );
		wp_add_inline_script( 'code-editor', sprintf('wp.codeEditor.initialize( "skgb-siteinclude", %s );', $settings) );
		wp_add_inline_script( 'code-editor', sprintf('wp.codeEditor.initialize( "skgb-htaccess", %s );', $settings) );
	}
	?>
	<h2>SKGB: Server-Konfiguration</h2>
	<p>Im Folgenden werden die Inhalte einiger wichtiger Konfigurationsdateien für den SKGB-Server <code><?php echo php_uname('n'); ?></code> gezeigt. Bei Änderungswünschen bitte Kontakt mit dem IT-Ausschuss aufnehmen.
	<p title='/etc/postfix/virtual'>Aliase im E-Mail–Server:
	<p><textarea id=skgb-aliases rows=15 cols=30><?php
	echo htmlspecialchars(file_get_contents('/etc/postfix/virtual'));
	?></textarea>
	<p title='/etc/apache2/sites-available/www.conf'>Apache VirtualHost <code>www.conf</code>:
	<p><textarea id=skgb-siteconf rows=15 cols=30><?php
	echo htmlspecialchars(file_get_contents('/etc/apache2/sites-available/www.conf'));
	?></textarea>
	<p title='/etc/apache2/sites-available/www.include'>Apache VirtualHost <code>www.include</code>:
	<p><textarea id=skgb-siteinclude rows=15 cols=30><?php
	echo htmlspecialchars(file_get_contents('/etc/apache2/sites-available/www.include'));
	?></textarea>
	<p title='<?php echo $_SERVER['DOCUMENT_ROOT'] . '/.htaccess'; ?>'>Apache directory <code>.htaccess</code>:
	<p><textarea id=skgb-htaccess rows=15 cols=30><?php
	echo htmlspecialchars(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/.htaccess'));
	?></textarea>
	<?php
}
add_action('admin_menu', 'SB_server_conf_menu_setup');


?>
