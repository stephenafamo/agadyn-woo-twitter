<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://stephenafamo.com
 * @since      1.0.0
 *
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/admin
 * @author     Stephen Afam-Osemene <stephenafamo@gmail.com>
 */
class Agadyn_Woo_Twitter_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Agadyn_Woo_Twitter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Agadyn_Woo_Twitter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/agadyn-woo-twitter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Agadyn_Woo_Twitter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Agadyn_Woo_Twitter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/agadyn-woo-twitter-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	 
	public function add_plugin_admin_menu() {
	    
	    add_options_page( 'WooCommerce Twitter', 'Woo Twitter', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
	    );
	}

	 
	public function add_plugin_network_menu() {
	    
	    add_submenu_page( 'settings.php', 'WooCommerce Twitter', 'Woo Twitter', 'manage_network', $this->plugin_name, array($this, 'display_plugin_setup_page')
	    );
	}

	 /**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	 
	public function add_action_links( $links ) {
	    /*
	    *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	    */
	   $settings_link = array(
	    '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	 
	public function display_plugin_setup_page() {
	    include_once( 'partials/agadyn-woo-twitter-admin-display.php' );
	}


	public function options_update() {
	   register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
	}
    
    public function site_options_update(){
        update_site_option($this->plugin_name, $_POST['agadyn-woo-twitter']);
        wp_redirect(network_admin_url('/settings.php?page='.$this->plugin_name));
    }

	public function validate($input) {
	    // All checkboxes inputs        
	    $valid = array();

	    //Cleanup
	    $valid['consumer_key'] = filter_var($input['consumer_key'], FILTER_SANITIZE_STRING);
	    $valid['consumer_secret'] = filter_var($input['consumer_secret'], FILTER_SANITIZE_STRING);
	    
	    return $valid;
	}

    public function add_custom_meta_box(){
        add_meta_box( 
            $this->plugin_name, 
            'Product Tweets', 
            array($this, 'custom_meta_box_markup'),
            ["product"],
            'normal',
            'high');

    }

    public function custom_meta_box_markup($object) {

        $agadyn_woo_tweets = get_post_meta($object->ID, "_agadyn_woo_tweets", true);

        if (!is_array($agadyn_woo_tweets)) $agadyn_woo_tweets = [];

        wp_nonce_field(basename(__FILE__), "meta-box-nonce");

        ?>

            <div id="agadyn_tweet_meta_box">

	            <h3> Enter tweets for this product </h3>

	            <?php 
	            $count = -1;
	            foreach ($agadyn_woo_tweets as $key => $value) { ?>

	            	<div id="agadyn_tweet_template<?= $key ?>">
		                <label for="agadyn_woo_tweets<?= $key ?>">Enter tweet</label>
		                <input name="agadyn_woo_tweets[]" id="agadyn_woo_tweets<?= $key ?>" type="text" value="<?= $value ?>" maxlength="113" > 
		                <input type="button"  onclick="remove_tweet_template(<?= $key ?>)" class="button" value="remove" />
		                <br/>
	                </div>

	            <?php $count++;
	            } ?>
	            <input type="button" id="add_tweet_template<?= $count ?>" onclick="add_tweet_template(<?= $count ?>)" class="button-primary" value="add tweet template" />

            </div>



            <script type="text/javascript">

            	function add_tweet_template(index){

					var parent = document.getElementById("agadyn_tweet_meta_box");
					var child = document.getElementById("add_tweet_template" + index);
					parent.removeChild(child);  

	            	index++;

	            	var div = document.createElement("div");
	            	div.id = "agadyn_tweet_template" + index ;

	            	var label = document.createElement("label");
	            	label.for = "agadyn_woo_tweets" + index;
	            	var labeltextnode = document.createTextNode("Enter tweet");
	            	label.appendChild(labeltextnode);

	            	var input = document.createElement("input");
	            	input.name = "agadyn_woo_tweets[]";
	            	input.id = "agadyn_woo_tweets" + index;
	            	input.type = "text";

	            	var input2 = document.createElement("input");
	            	input2.name = "agadyn_woo_tweets[]";
	            	input2.id = "agadyn_woo_tweets" + index;
	            	input2.type = "button";
	            	input2.className = "button";
	            	input2.value = "remove";
	            	input2.onclick = function() {
								        remove_tweet_template(index);
								    }

	            	var input3 = document.createElement("input");
	            	input3.id = "add_tweet_template" + index;
	            	input3.type = "button";
	            	input3.className = "button-primary";
	            	input3.value = "add tweet template";
	            	input3.onclick = function() {
								        add_tweet_template(index);
								    }

	            	var br = document.createElement("br");

	            	div.appendChild(label);
	            	div.appendChild(input);
	            	div.appendChild(input2);
	            	div.appendChild(br);
	            	div.appendChild(br);

					parent.appendChild(div);      
					parent.appendChild(input3);
	            }

	            function remove_tweet_template(index){	            		
					var parent = document.getElementById("agadyn_tweet_meta_box");
					var child = document.getElementById("agadyn_tweet_template" + index);
					parent.removeChild(child);
	            }
            </script>

        <?php
    }



    public function save_custom_meta_box($post_id, $post){

        if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
            return $post_id;

        if(!current_user_can("edit_post", $post_id))
            return $post_id;

        if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
            return $post_id;

        if(isset($_POST["agadyn_woo_tweets"])) {
        	foreach ($_POST["agadyn_woo_tweets"] as $key => $value) {
        		# code...
        		if (!empty($value))
		            $agadyn_woo_tweets[] = $value;
        	}
        }   
        update_post_meta($post_id, "_agadyn_woo_tweets", $agadyn_woo_tweets);
    }

	public function authorize_twitter(){
				
		if (is_multisite()){
		    //Grab all options
		    $options = get_site_option($this->plugin_name);

		} else {
		    //Grab all options
		    $options = get_option($this->plugin_name);
		}

		try {

			$consumer_key = $options['consumer_key'];
			$consumer_secret = $options['consumer_secret'];

			$oauth_callback = admin_url( 'options-general.php?page=' . $this->plugin_name );

			$connection = new Abraham\TwitterOAuth\TwitterOAuth($consumer_key, $consumer_secret);

			$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));

			$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

			$current_user_id = get_current_user_id();

			set_transient( $current_user_id."_request_token", $request_token, 3600 );

	        wp_redirect($url);
			
		} catch (Exception $e) {

			$url = wp_redirect(admin_url( 'options-general.php?page=' . $this->plugin_name ));
	        wp_redirect($url);
			
		}
	}

	public function authorize_twitter_n(){
				
		if (is_multisite()){
		    //Grab all options
		    $options = get_site_option($this->plugin_name);
			$oauth_callback = network_admin_url( 'settings.php?page=' . $this->plugin_name.'&notifier' );

		} else {
		    //Grab all options
		    $options = get_option($this->plugin_name);
			$oauth_callback = admin_url( 'options-general.php?page=' . $this->plugin_name.'&notifier' );
		}

		try {

			// Cleanup
			$consumer_key = $options['consumer_key'];
			$consumer_secret = $options['consumer_secret'];

			$connection = new Abraham\TwitterOAuth\TwitterOAuth($consumer_key, $consumer_secret);

			$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $oauth_callback));

			$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

			$current_user_id = get_current_user_id();

			set_transient( $current_user_id."_request_token_n", $request_token, 3600 );

	        wp_redirect($url);

		} catch (Exception $e) {
				
			if (is_multisite()){

				$url = wp_redirect(network_admin_url( 'settings.php?page=' . $this->plugin_name ));

			} else {

				$url = wp_redirect(admin_url( 'options-general.php?page=' . $this->plugin_name ));
			}

	        wp_redirect($url);
			
		}
	}

	public function delete_twitter( $current_blog = 0) {
		if ($current_blog === 0) $current_blog = get_current_blog_id();

            if(is_multisite()){	
				delete_site_option( $this->plugin_name.'-access_token-'.$current_blog );
				delete_site_option( $this->plugin_name.'-id-'.$current_blog );
            } else {                    
                delete_option( $this->plugin_name.'-access_token',  $access_token );
                delete_option( $this->plugin_name.'-id',  $access_token );
            }
            
		wp_redirect(admin_url( 'options-general.php?page=' . $this->plugin_name ));

	}

	public function delete_twitter_n() {
				
		if (is_multisite()){
		    //Grab all options
		    $options = delete_site_option($this->plugin_name."-notifier");
			wp_redirect(network_admin_url( 'settings.php?page=' . $this->plugin_name ));

		} else {
		    //Grab all options
		    $options = delete_option($this->plugin_name."-notifier");
			wp_redirect(admin_url( 'options-general.php?page=' . $this->plugin_name ));
		}

	}

	public function twitter_notifier($message, $blog_id = 0) {

				
		if (is_multisite()){
		    //Grab all options
		    $options = get_site_option($this->plugin_name);
		    $notifier = get_site_option($this->plugin_name."-notifier");
		
			if ($blog_id === 0) $blog_id = get_current_blog_id();
			$user_id = get_site_option( $this->plugin_name.'-id-'.$blog_id );

		} else {
		    //Grab all options
		    $options = get_option($this->plugin_name);
		    $notifier = get_option($this->plugin_name."-notifier");
            $user_id = get_option( $this->plugin_name.'-id');
		}

		try {
			$consumer_key = $options['consumer_key'];
			$consumer_secret = $options['consumer_secret'];

            $connection = new Abraham\TwitterOAuth\TwitterOAuth
                ($consumer_key, $consumer_secret, $notifier['oauth_token'], $notifier['oauth_token_secret']);

			$url = $connection->post('direct_messages/new', array('user_id' => $user_id, 'text' => $message) );

		} catch (Exception $e) {
			
		}

	}

	public function send_new_order_tweet_dm($order_id){
		$post_meta = get_post_meta($order_id);
		$order_total = number_format($post_meta["_order_total"][0]);
		$customer_name = $post_meta["_billing_first_name"][0]." ".$post_meta["_billing_last_name"][0];
		$customer_email = $post_meta["_billing_email"][0];
		$customer_phone = $post_meta["_billing_phone"][0];
		$order_link = get_edit_post_link($order_id, '');

		$text = "A new order worth ₦".$order_total." has been placed on your store."."\r\n"."\r\n";
		$text .= "The order was place by ".$customer_name.".\r\n";
		$text .= "Email: ".$customer_email."\r\n";
		$text .= "Phone: ".$customer_phone."\r\n"."\r\n";
		$text .= "Order Number: ".$order_id."\r\n"."\r\n";
		$text .= "View all order details here - ".$order_link;

		$this->twitter_notifier($text);
	}

	public function add_weekly_schedule($schedules) {
		// add a 'weekly' schedule to the existing set
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __('Once Weekly')
		);
		return $schedules;
	}

	public function twitter_schedule(){

		wp_clear_scheduled_hook( 'send_product_tweet' );

		date_default_timezone_set("UTC");

		$offset = (int) $_POST['offset'];
		$offset_secs = $offset * 60;

		$dw = date( "w", time());
		$dw_array = ["sun" => 0,
                        "mon" => 1,
                        "tue" => 2,
                        "wed" => 3,
                        "thu" => 4,
                        "fri" => 5,
                        "sat" => 6];

		foreach ($_POST['days'] as $day) {

			$dw_offset = ( 7 - ($dw - $dw_array[$day])) % 7;
			$dw_offset_time = $dw_offset * 86400;
			
			foreach ($_POST['time'][$day] as $key => $time) {

				if ($time != "null"){
					$time = strtotime($time);
					wp_schedule_event($time + $dw_offset_time + $offset_secs, 'weekly', 'send_product_tweet');
				}
			}

		}

	    update_option($this->plugin_name."-tweet-schedule", $_POST);
		wp_redirect(admin_url( 'options-general.php?page=' . $this->plugin_name ));
	}

	public function send_product_tweet(){

		if (is_multisite()){
		    //Grab all options
		    $site_id = int(get_site($this->blog_id));
		    $options = get_site_option($this->plugin_name);
		    $access_token = get_site_option( 'agadyn-woo-twitter'.'-access_token-'.$site_id);

		} else {
		    //Grab all options
		    $options = get_option($this->plugin_name);
			$access_token = get_option($this->plugin_name.'-access_token');

		}

		$consumer_key = $options['consumer_key'];
		$consumer_secret = $options['consumer_secret'];

        if (!empty($access_token)){ 
			$connection = new Abraham\TwitterOAuth\TwitterOAuth
                ($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        }
	         
	    $args = array(
	        'posts_per_page'   => 1,
	        'orderby'          => 'rand',
	        'post_type'        => 'product',
	    );
	    $posts_array = get_posts( $args );
	    $post_link = get_post_permalink($posts_array[0]->ID );

	    $price = get_post_meta( $posts_array[0]->ID , '_price', true);

	    $tweets = get_post_meta( $posts_array[0]->ID , '_agadyn_woo_tweets', true);    
	    $tweet = $tweets[mt_rand(0, count($tweets) - 1)];

	    $file = wp_upload_dir()['basedir'] . '/' . get_post_meta( get_post_thumbnail_id( $posts_array[0]->ID ), '_wp_attached_file', true);

	    $media1 = $connection->upload('media/upload', ['media' => $file]);
	    $parameters = [
	        'status' => $tweet.' '.$post_link,
	        'media_ids' => $media1->media_id_string
	    ];
	    $result = $connection->post('statuses/update', $parameters);

	}
    

}
