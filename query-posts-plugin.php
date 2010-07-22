<?php
/**
 * Plugin Name: Query Posts
 * Plugin URI: http://justintadlock.com/archives/2009/03/15/query-posts-widget-wordpress-plugin
 * Description: A widget that allows you to show posts/pages in any way you'd like on your site.
 * Version: 0.1
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * This plugin was created to allow users to show posts anywhere on their
 * site.  Of course, the ability to show the widget anywhere rests on the idea
 * that the theme has plenty of widget-ready areas.  This can be used to for
 * simple lists in the sidebar, but it is so much more than that.  Essentially, one
 * could run a completely widgetized site with this plugin.
 *
 * @copyright 2009
 * @version 0.1
 * @author Justin Tadlock
 * @link http://justintadlock.com/archives/2009/03/15/query-posts-widget-wordpress-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package QueryPosts
 */

/*
* Yes, we're localizing the plugin.  This partly makes sure non-English
* users can use it too.  To translate into your language use the
* en_EN.po file as as guide.  Poedit is a good tool to for translating.
* @link http://poedit.net
*/
	load_plugin_textdomain( 'query-posts' );

/*
* Make sure we get the correct directory.
*/
	if ( !defined( 'WP_CONTENT_DIR' ) )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( !defined( 'WP_PLUGIN_DIR' ) )
		define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

/*
* Define constant paths to the plugin folder.
*/
	define( WIDGET_QUERY_POSTS, WP_PLUGIN_DIR . '/query-posts' );

/*
* Load widgets after WP functions have been loaded
*/
	add_action( 'plugins_loaded', 'query_posts_load_widgets' );

/**
* Loads all the widget files at appropriate time.
* Calls the register function for each widget.
*
* @since 0.1
*/
function query_posts_load_widgets() {

	require_once( WIDGET_QUERY_POSTS . '/query-posts.php' );

	widget_query_posts_register();
}

?>