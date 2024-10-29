<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://coderockz.com
 * @since             1.0.0
 * @package           Amazing_Search
 *
 * @wordpress-plugin
 * Plugin Name:       Amazing Search
 * Plugin URI:        https://coderockz.com
 * Description:       Amazing Search is a WordPress plugin that gives you the most advanced Amazon product searcher with your affilaite link. You can put the searchbar using a shortcode anywhere you want. Also it has a search widget which can be placed in your sidebar.
 * Version:           1.0.2
 * Author:            CodeRockz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amazing-search
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if(!defined("AMAZING_SEARCH_PLUGIN_DIR"))
    define("AMAZING_SEARCH_PLUGIN_DIR",plugin_dir_path(__FILE__));
if(!defined("AMAZING_SEARCH_PLUGIN_URL"))
    define("AMAZING_SEARCH_PLUGIN_URL",plugin_dir_url(__FILE__));
if(!defined("AMAZING_SEARCH_PLUGIN"))
    define("AMAZING_SEARCH_PLUGIN",plugin_basename(__FILE__));

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'AMAZING_SEARCH_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-amazing-search-activator.php
 */
function activate_amazing_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-amazing-search-activator.php';
	$activator = new Amazing_Search_Activator();
    $activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-amazing-search-deactivator.php
 */
function deactivate_amazing_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-amazing-search-deactivator.php';
	$deactivator = new Amazing_Search_Deactivator();
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_amazing_search' );
register_deactivation_hook( __FILE__, 'deactivate_amazing_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-amazing-search.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_amazing_search() {

	$plugin = new Amazing_Search();
	$plugin->run();

}
run_amazing_search();