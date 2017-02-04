=== Plugin Name ===
Contributors: stephenafamo
Donate link: https://stephenafamo.com
Tags: woocommerce, twitter, ecommerce, social, social media
Requires at least: 3.0.1
Tested up to: 4.7.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically tweet about your WooCommerce Products and receive DM notifications when an order is placed.

== Description ==

This plugin is used to automatically send out tweets about your products. 
On the products edit screen, you will see a place to enter the tweets to be sent out for that product.


You would be required to create a Twitter App at https://apps.twitter.com.

Once you have created the twiiter app, you should save the consumer key and secret in the plugin settings page. settings -> Woo Twitter
After authorizing the account, you should set the tweeting schedule.

**NOTE**

* The product to be tweeted and the tweet to use is randomly selected.
* To receive sale notifications, you will need to set a separate twitter account as the "notifier"
* You will also need to set the App's permission to "Read, Write and Access direct messages"

**MULTISITE**

The plugin behaves slightly differently on multisite installs.

* The consumer key, secret and notifing account are set up once in the plugin's network settings which can be found under settings -> Woo Twitter in the network admin menu.

== Installation ==

1. Upload `agadyn-woo-twitter.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How many tweets can I send out a day? =

For now, the settings limit it to 3 times, future versions will change this.

= How do I create a twitter app? =

It's pretty easy to create a twitter app and get the consumer key and secret, you can read any of the following articles.
https://smashballoon.com/custom-twitter-feeds/docs/create-twitter-app/
https://blog.askupasoftware.com/how-to-create-a-twitter-application/

= I am a developer. How can I help improve this plugin? =

It's on github!!! https://github.com/stephenafamo/agadyn-woo-twitter
Send in a pull request anytime!

== Changelog ==

= 1.0 =
* Initial release.

== A brief Markdown Example ==

Ordered list:

1. Some feature
1. Another feature
1. Something else about the plugin

Unordered list:

* something
* something else
* third thing

Here's a link to [WordPress](http://wordpress.org/ "Your favorite software") and one to [Markdown's Syntax Documentation][markdown syntax].
Titles are optional, naturally.

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"

Markdown uses email style notation for blockquotes and I've been told:
> Asterisks for *emphasis*. Double it up  for **strong**.

`<?php code(); // goes in backticks ?>`