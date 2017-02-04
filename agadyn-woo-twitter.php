<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://stephenafamo.com
 * @since             1.0.0
 * @package           Agadyn_Woo_Twitter
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Twitter
 * Plugin URI:        github.com/stephenafamo/agadyn-woo-twitter
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Stephen Afam-Osemene
 * Author URI:        https://stephenafamo.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       agadyn-woo-twitter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-agadyn-woo-twitter-activator.php
 */
function activate_agadyn_woo_twitter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-agadyn-woo-twitter-activator.php';
	Agadyn_Woo_Twitter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-agadyn-woo-twitter-deactivator.php
 */
function deactivate_agadyn_woo_twitter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-agadyn-woo-twitter-deactivator.php';
	Agadyn_Woo_Twitter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_agadyn_woo_twitter' );
register_deactivation_hook( __FILE__, 'deactivate_agadyn_woo_twitter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-agadyn-woo-twitter.php';
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_agadyn_woo_twitter() {

	$plugin = new Agadyn_Woo_Twitter();
	$plugin->run();

}
run_agadyn_woo_twitter();
