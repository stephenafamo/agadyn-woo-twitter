<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://stephenafamo.com
 * @since      1.0.0
 *
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/includes
 * @author     Stephen Afam-Osemene <stephenafamo@gmail.com>
 */
class Agadyn_Woo_Twitter_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'agadyn-woo-twitter',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
