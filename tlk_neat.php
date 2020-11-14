<?php
/**
 * Plugin name: TLK Social Sharing Neat
 * Description: Wordpress plugin for Twitter, LinkedIn and Kindle social share.
 * Share box is only added after the post content.
 * Author: Kevan Stuart
 * Adapted from: Kunal Chichkar
 * Version: 1.1.1
 * Licence: GPL
 */
defined( 'ABSPATH' ) or die( 'No script access allowed at this time' );

if ( ! function_exists( 'is_admin' ) ) {
  header('Status: 403 Forbidden');
  header('HTTP/1.1 403 Forbidden');
  exit();
}

register_activation_hook( __FILE__, 'tlkActivate');
register_deactivation_hook( __FILE__, 'tlkDeactivate');

function tlkActivate() {
  add_option( 'tlk-share-version', 'neat' );
  add_option( 'tlk-share-options' );
}

function tlkDeactivate() {
  delete_option( 'tlk-share-version' );
  delete_option( 'tlk-share-options' );
}

function tlkOptions() {
  $options = get_option( 'tlk-share-options' );

  if ( ! $options ) {
    $options = tlkOptionsDefault();
    add_option( 'tlk-share-options', $options );
  }

  return json_decode( $options, true );
}

require_once 'tlk_admin.php';
require_once 'tlk_display.php';

if ( is_admin() ) {
  add_action( 'admin_enqueue_scripts', 'tlkAdminScripts' );
  add_action( 'admin_menu', 'tlkAdminMenu' );
} else {
  add_action('init', 'tlkDisplayInit');
  add_action( 'wp_enqueue_scripts', 'tlkStyles' );
  add_filter( 'the_excerpt', 'tlkDisplayInExcerpt' );
  add_filter( 'the_content', 'tlkDisplayInContent' );
  add_shortcode( 'tlk_social_share', 'tlkShortcode' );
}