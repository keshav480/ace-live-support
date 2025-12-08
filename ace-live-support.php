<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://acewebx.com
 * @since             1.0.0
 * @package           Ace_Live_Support
 *
 * @wordpress-plugin
 * Plugin Name:       Ace live support
 * Plugin URI:        https://ace-live-support
 * Description:       Ace Live Support is a lightweight and powerful real-time chat system for WordPress. It allows site administrators to instantly communicate with users, answer questions, and provide live support — all from the WordPress dashboard.
 * Version:           1.0.0
 * Author:            AceWebx Team
 * Author URI:        https://acewebx.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ace-live-support
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ACE_LIVE_SUPPORT_VERSION', '1.0.0' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ace-live-support-activator.php
 */
function activate_ace_live_support() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace-live-support-activator.php';
	Ace_Live_Support_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ace-live-support-deactivator.php
 */
function deactivate_ace_live_support() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ace-live-support-deactivator.php';
	Ace_Live_Support_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ace_live_support' );
register_deactivation_hook( __FILE__, 'deactivate_ace_live_support' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ace-live-support.php';
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ace_live_support() {

	$plugin = new Ace_Live_Support();
	$plugin->run();

}
run_ace_live_support();
