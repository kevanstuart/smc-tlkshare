<?php
/**
 * Plugin name: Twitter-LinkedIn-Kindle Social Share
 * Description: Wordpress plugin for Twitter, LinkedIn and Kindle social share. Share box is only added after the post content.
 * Author: Kevan Stuart
 * Adapted from: Kunal Chichkar
 * Version: 1.0.0
 * Licence: GPL
 */

require_once('tlk_admin_page.php');
require_once('tlk_display.php');

/**
 * Check is_admin function doesn't exist and throw error
 */
if (!function_exists('is_admin')) 
{
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

/**
 * Functions run on activation and deactivation
 */
register_activation_hook(__FILE__,'twitter_linkedin_kindle_install'); 
register_deactivation_hook( __FILE__, 'twitter_linkedin_kindle_remove' );

/**
 * Install plugin function
 */
function twitter_linkedin_kindle_install() 
{ /* Do Nothing */ }

/**
 * Remove plugin function
 */
function twitter_linkedin_kindle_remove()
{
    delete_option('twitter_linkedin_kindle_share');
}

/**
 * Check if admin is accessing
 */
if(is_admin())
{
    add_action('admin_menu', 'twitter_linkedin_kindle_admin_menu');
}
else
{
    add_action('init', 'twitter_linkedin_kindle_share_init');
    add_shortcode('tlk_social_share', 'tlk_social_share_shortcode' );
    
    $option = twitter_linkedin_kindle_get_options_stored();
    if($option['auto'] == true)
    {
        add_filter('the_content', 'twitter_linkedin_kindle_contents');
        add_filter('the_excerpt', 'twitter_linkedin_kindle_excerpt');
    } 
}
?>