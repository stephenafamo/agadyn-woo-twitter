<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://stephenafamo.com
 * @since      1.0.0
 *
 * @package    Agadyn_Woo_Twitter
 * @subpackage Agadyn_Woo_Twitter/admin/partials
 */

if (is_multisite()){
    //Grab all options
    $options = get_site_option($this->plugin_name);

} else {
    //Grab all options
    $options = get_option($this->plugin_name);

}
$consumer_key = $options['consumer_key'];
$consumer_secret = $options['consumer_secret'];

$current_user_id = get_current_user_id();
$blog_id = get_current_blog_id();
$this_page = get_current_screen();

if (isset($_GET['oauth_token'])) {

    if (isset($_GET['notifier'])){
        $request_token = get_transient( $current_user_id."_request_token_n");
    } else {
        $request_token = get_transient( $current_user_id."_request_token");
    }


    if ($request_token['oauth_token'] === $_REQUEST['oauth_token']) {
        $connection = new Abraham\TwitterOAuth\TwitterOAuth
            ($consumer_key, $consumer_secret, $request_token['oauth_token'], $request_token['oauth_token_secret']);

        try {
            $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);


            if (isset($_GET['notifier'])){

                if(is_multisite()){
                    update_site_option( $this->plugin_name.'-notifier',  $access_token );
                } else {                    
                    update_option( $this->plugin_name.'-notifier',  $access_token );
                }

            } else {

                if(is_multisite()){
                    update_site_option( $this->plugin_name.'-access_token-'.$blog_id,  $access_token );
                } else {                    
                    update_option( $this->plugin_name.'-access_token',  $access_token );
                }
            }

        } catch (Exception $e) {
            
        }
    }
    
}

if (is_multisite()){
    $notifier = get_site_option($this->plugin_name."-notifier");

} else {
    $notifier = get_option($this->plugin_name."-notifier");
}

?>


<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <?php if (is_multisite() && !$this_page->is_network ) : ?>

    <?php ; else: ?>

        <?php if ($this_page->is_network) : ?>
        
            <form method="post" name="<?= $this->plugin_name ?>" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">

        <?php ; else: ?>

            <form method="post" name="<?= $this->plugin_name ?>" action="options.php">
            <?php settings_fields($this->plugin_name); ?>

        <?php endif; ?>
        
            <!-- the consumer key of the application -->
            <fieldset>
                <legend class="screen-reader-text"><span>Consumer key</span></legend>
                <label for="<?php echo $this->plugin_name; ?>-consumer_key">
                    <input type="text" id="<?php echo $this->plugin_name; ?>-consumer_key" name="<?php echo $this->plugin_name; ?>[consumer_key]" value="<?= $consumer_key ?>"/>
                    <span><?php esc_attr_e('Consumer Key', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <!-- the consumer secret of the application -->
            <fieldset>
                <legend class="screen-reader-text"><span>Consumer Secret</span></legend>
                <label for="<?php echo $this->plugin_name; ?>-consumer_secret">
                    <input type="text" id="<?php echo $this->plugin_name; ?>-consumer_secret" name="<?php echo $this->plugin_name; ?>[consumer_secret]" value="<?= $consumer_secret ?>"/>
                    <span><?php esc_attr_e('Consumer Secret', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <?php if ($this_page->is_network) : ?>

                <input type="hidden" name="action" value="agadyn_woo_twitter_network_settings"> 

            <?php endif; ?>

            <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>

        </form>

            <?php if (!empty($notifier)){

                try {
                    $connection = new Abraham\TwitterOAuth\TwitterOAuth
                        ($consumer_key, $consumer_secret, $notifier['oauth_token'], $notifier['oauth_token_secret']);

                    $user = $connection->get("account/verify_credentials", ['include_email' => 'true']);

                    if (empty($user->errors)): ?>
                        <h2> Your profile </h2>
                        <img src="<?= $user->profile_image_url ?>" width="50px" height="50px"> <?= $user->name ?> @<?= $user->screen_name ?>

                        <form method="post" action="<?= esc_url( admin_url('admin-post.php') ); ?>" onsubmit="agadyn_woo_twitter_delete_n.disabled=true; return true;">
                            <input type="hidden" name="action" value="agadyn_woo_twitter_delete_n">
                            <button class="button" id="agadyn_woo_twitter_delete_n">Remove</button>
                        </form> <br/> 

                    <?php endif; 

                } catch (Exception $e) {
                    
                }        
                
            }   

                if (!empty($consumer_key) && !empty($consumer_secret)) :?>

                    <?php $text = "Set notifiing account"; if (!empty($notifier)) $text = "Change notify account"; ?>

                    <form method="post" action="<?= esc_url( admin_url('admin-post.php') ); ?>" onsubmit="agadyn_woo_twitter_authorize_n.disabled=true; return true;">
                        <input type="hidden" name="action" value="agadyn_woo_twitter_authorize_n">
                        <button class="button-primary" id="agadyn_woo_twitter_authorize_n"><?= $text ?></button>
                    </form>

                <?php endif; 

            ?>

    <?php endif; ?>

    <?php if (!is_multisite() || !$this_page->is_network ) : 

        if(is_multisite()){
            $access_token = get_site_option($this->plugin_name.'-access_token-'.$blog_id);
        } else {                    
            $access_token = get_option($this->plugin_name.'-access_token');
        }

        if (!empty($access_token)){
            try {
                $connection = new Abraham\TwitterOAuth\TwitterOAuth
                    ($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

                $user = $connection->get("account/verify_credentials", ['include_email' => 'true']);

                if (empty($user->errors)): ?>
                    <h2> Your profile </h2>
                    <img src="<?= $user->profile_image_url ?>" width="50px" height="50px"> <?= $user->name ?> @<?= $user->screen_name ?>

                    <form method="post" action="<?= esc_url( admin_url('admin-post.php') ); ?>" onsubmit="agadyn_woo_twitter_delete.disabled=true; return true;">
                        <input type="hidden" name="action" value="agadyn_woo_twitter_delete">
                        <button class="button" id="agadyn_woo_twitter_delete">Remove</button>
                    </form> <br/> 

                <?php 

                if(is_multisite()){
                    update_site_option( $this->plugin_name.'-id-'.$blog_id,  $user->id );
                } else {                    
                    update_option( $this->plugin_name.'-id',  $user->id );
                };

                endif;

            } catch (Exception $e) {
                
            }        
            
        }        
        

        if (!empty($consumer_key) && !empty($consumer_secret)) :?>

            <?php $text = "Authorize with Twitter"; if (!empty($access_token)) $text = "Change twitter account"; ?>

            <form method="post" action="<?= esc_url( admin_url('admin-post.php') ); ?>" onsubmit="agadyn_woo_twitter_authorize.disabled=true; return true;">
                <?php wp_nonce_field(basename(__FILE__), "tweet-authorize-nonce"); ?>
                <input type="hidden" name="action" value="agadyn_woo_twitter_authorize">
                <button class="button-primary" id="agadyn_woo_twitter_authorize"><?= $text ?></button>
            </form>

        <?php endif; ?>

        <?php 

        if (!empty($access_token)){
            try {
                if (empty($user->errors)):

                $time_array = ["12am" => "00:00", "12:30am" => "00:30",
                                "1am" => "01:00", "1:30am" => "01:30",
                                "2am" => "02:00", "2:30am" => "02:30",
                                "3am" => "03:00", "3:30am" => "03:30",
                                "4am" => "04:00", "4:30am" => "04:30",
                                "5am" => "05:00", "5:30am" => "05:30",
                                "6am" => "06:00", "6:30am" => "06:30",
                                "7am" => "07:00", "7:30am" => "07:30",
                                "8am" => "08:00", "8:30am" => "08:30",
                                "9am" => "09:00", "9:30am" => "09:30",
                                "10am" => "10:00", "10:30am" => "10:30",
                                "11am" => "11:00", "11:30am" => "11:30",
                                "12pm" => "12:00", "12:30pm" => "12:30",
                                "1pm" => "13:00", "1:30pm" => "13:30",
                                "2pm" => "14:00", "2:30pm" => "14:30",
                                "3pm" => "15:00", "3:30pm" => "15:30",
                                "4pm" => "16:00", "4:30pm" => "16:30",
                                "5pm" => "17:00", "5:30pm" => "17:30",
                                "6pm" => "18:00", "6:30pm" => "18:30",
                                "7pm" => "19:00", "7:30pm" => "19:30",
                                "8pm" => "20:00", "8:30pm" => "20:30",
                                "9pm" => "21:00", "9:30pm" => "21:30",
                                "10pm" => "22:00", "10:30pm" => "22:30",
                                "11pm" => "23:00", "11:30pm" => "23:30"];

                $days_array = ["Sunday" => "sun",
                                "Monday" => "mon",
                                "Tuesday" => "tue",
                                "Wednesday" => "wed",
                                "Thursday" => "thu",
                                "Friday" => "fri",
                                "Saturday" => "sat"];

                $max_tweet_per_day = 3;

                $tweet_schedule = get_option($this->plugin_name."-tweet-schedule");

                if(!$tweet_schedule) { 
                    $tweet_schedule = [ 'days' => [], 'time' => []];
                    foreach ($days_array as $key => $value) {
                        $time[$value] = [];
                    }
                } elseif (!$tweet_schedule['days']) {
                    $tweet_schedule['days'] = [];
                }
                
                ?>
                    <h2> Set twitter schedule </h2>

                    <form method="post" action="<?= esc_url( admin_url('admin-post.php') ); ?>" onsubmit="agadyn_woo_twitter_schedule.disabled=true; return true;">

                        <?php foreach ($days_array as $day => $php_day):?>

                            <input type="checkbox" value="<?= $php_day ?>" name="days[]" 
                            <?php if (in_array($php_day, $tweet_schedule['days'])) echo "checked"; ?>
                            > <?= $day ?> 
                            
                            <?php for ($i=0; $i < $max_tweet_per_day; $i++): ?>

                                <select name="time[<?= $php_day ?>][]">
                                    <option value="null">---Select---</option>
                                    <?php foreach ($time_array as $time => $php_time):?>
                                        <option value="<?= $php_time ?>"
                                            <?php if ($php_time === $tweet_schedule['time'][$php_day][$i]) echo "selected"; ?>
                                        ><?= $time ?></option>
                                    <?php endforeach; ?>
                                </select>

                            <?php endfor; ?>

                            <br/><br/>
                        <?php endforeach; ?>                      


                        <input type="hidden" name="offset" id="agadyn_schedule_offset" value="">
                        <input type="hidden" name="action" value="agadyn_woo_twitter_schedule">
                        <button class="button-primary" id="agadyn_woo_twitter_schedule">Schedule</button>
                    </form> <br/> <br/>

                    <script type="text/javascript">
                        var offset = new Date().getTimezoneOffset();
                        console.log(offset);
                        document.getElementById("agadyn_schedule_offset").value = offset;
                    </script>

                <?php endif;

            } catch (Exception $e) {
                
            }        
            
        }   

        ?>

    <?php endif; ?>

</div>
