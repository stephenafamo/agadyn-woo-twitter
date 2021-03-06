<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://stephenafamo.com
 * @since      1.0.0
 *
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/includes
 * @author     Stephen Afam-Osemene <stephenafamo@gmail.com>
 */
class Agadyn_Woo_Twitter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Agadyn_Woo_Twitter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'agadyn-woo-twitter';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Agadyn_Woo_Twitter_Loader. Orchestrates the hooks of the plugin.
	 * - Agadyn_Woo_Twitter_i18n. Defines internationalization functionality.
	 * - Agadyn_Woo_Twitter_Admin. Defines all hooks for the admin area.
	 * - Agadyn_Woo_Twitter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-agadyn-woo-twitter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-agadyn-woo-twitter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-agadyn-woo-twitter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-agadyn-woo-twitter-public.php';

		$this->loader = new Agadyn_Woo_Twitter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Agadyn_Woo_Twitter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Agadyn_Woo_Twitter_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Agadyn_Woo_Twitter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Add menu item
		if(is_multisite()) {
			$this->loader->add_action( 'network_admin_menu', $plugin_admin, 'add_plugin_network_menu' );
			$this->loader->add_action( 'admin_post_agadyn_woo_twitter_network_settings', $plugin_admin, 'site_options_update' );

			// action to delete the id and access token when a sub-site is deleted
			$this->loader->add_action( 'delete_blog', $plugin_admin, 'delete_twitter', 10, 1 );
		}

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
		// Add Settings link to the plugin
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );
		// Save/Update our plugin options
		$this->loader->add_action( 'admin_init', $plugin_admin, 'options_update');

        //handle twitter authentication
		$this->loader->add_action( 'admin_post_agadyn_woo_twitter_authorize', $plugin_admin, 'authorize_twitter', 10, 1 );
		$this->loader->add_action( 'admin_post_agadyn_woo_twitter_delete', $plugin_admin, 'delete_twitter', 10, 1 );

		//authenticate twitter notifier
		$this->loader->add_action( 'admin_post_agadyn_woo_twitter_authorize_n', $plugin_admin, 'authorize_twitter_n', 10, 1 );
		$this->loader->add_action( 'admin_post_agadyn_woo_twitter_delete_n', $plugin_admin, 'delete_twitter_n', 10, 1 );

		//add tweet metabox
		$this->loader->add_filter( 'add_meta_boxes', $plugin_admin, 'add_custom_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_custom_meta_box', 10, 2);

		//send new order notification
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_admin, 'send_new_order_tweet_dm', 10, 1);

		//add weekly cron schedule
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'add_weekly_schedule', 10, 1);

		//handle tweet scheduling
		$this->loader->add_action( 'admin_post_agadyn_woo_twitter_schedule', $plugin_admin, 'twitter_schedule', 10, 1 );

		//tweet
		$this->loader->add_action( 'send_product_tweet', $plugin_admin, 'send_product_tweet', 10, 1 );

		//handle front end redirects
		$this->loader->add_action( 'admin_post_redirect', $plugin_admin, 'redirect', 10, 1 );
		$this->loader->add_action( 'admin_post_no_priv_redirect', $plugin_admin, 'redirect', 10, 1 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Agadyn_Woo_Twitter_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Agadyn_Woo_Twitter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
