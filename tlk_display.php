<?php 
/**
 * Core logic to display social share icons at the required positions. 
 */
require_once('tlk_admin_page.php');

/**
 * Initialize Twitter_Linkedin_Kindle view
 * @return type
 */
function twitter_linkedin_kindle_share_init() {
	
    /**
     * Disabled in admin view
     */
	if (is_admin()) {
		return;
	}

	/**
     * Get stored share options
     */
	$option = twitter_linkedin_kindle_get_options_stored();
	
    // Load twitter share JS
	if ($option['active_buttons']['twitter'] == true) {
		wp_enqueue_script('twitter_linkedin_kindle_share_twitter', 'http'.(is_ssl()?'s':'').'://platform.twitter.com/widgets.js',false,null,false);
	}
	
    // Load linkedin share JS
	if ($option['active_buttons']['linkedin'] == true) {
		wp_enqueue_script('twitter_linkedin_kindle_share_linkedin', 'http'.(is_ssl()?'s':'').'://platform.linkedin.com/in.js',false,null,false);
	}
    
    // Load kindle share JS
    if ($option['active_buttons']['kindle'] == true) {
        wp_enqueue_script('twitter_linkedin_kindle_share_kindle', 'http'.(is_ssl()?'s':'').'://d1xnn692s7u6t6.cloudfront.net/widget.js',false,null,false);
    }
}    

/**
 * Handles inserting share buttons for full content
 * @param type $content
 * @return type
 */
function twitter_linkedin_kindle_contents($content)
{
	return twitter_linkedin_kindle($content, 'content');
}

/**
 * Handles inserting share buttons for excerpts
 * @param type $content
 * @return type
 */
function twitter_linkedin_kindle_excerpt($content)
{
	return twitter_linkedin_kindle($content, 'excerpt');
}

/**
 * Inserts the share buttons into the provided contents
 * @global type $single
 * @staticvar string $last_execution
 * @param type $content
 * @param type $filter
 * @return type
 */
function twitter_linkedin_kindle($content, $filter)
{
    global $single;
    static $last_execution = '';

    // Test filter and last execution
    if ($filter == 'the_excerpt' && $last_execution == 'the_content')
    {
		remove_filter('the_content', 'twitter_linkedin_kindle_contents');
		$last_execution = 'the_excerpt';
		return the_excerpt();
    }
    
	if ($filter == 'the_excerpt' && $last_execution == 'the_excerpt') {
		add_filter('the_content', 'twitter_linkedin_kindle_contents');
	}
  
    $option = twitter_linkedin_kindle_get_options_stored();
    $custom_disable = get_post_custom_values('disable_social_share');

    // If single post page and option "show in posts" enabled
    if (is_single() && ($option['show_in']['posts']) && ($custom_disable[0] != 'yes'))
    {
        $output = tlk_social_share('auto');
        $last_execution = $filter;
        return  $content . $output;
    } 
    
    // If home page and option "show on home page" enabled
	if (is_home() && ($option['show_in']['home_page']))
    {
        $output = tlk_social_share('auto');
		$last_execution = $filter;
		return  $content . $output;
	}
    
    // If page and option "show on pages" enabled
	if (is_page() && ($option['show_in']['pages']) && ($custom_disable[0] != 'yes'))
    {
        $output = tlk_social_share('auto');
		$last_execution = $filter;
        return  $content . $output;
    }  
    
    // If category page and option "show on category page" enabled
	if (is_category() && ($option['show_in']['categories']))
    {
		$output = tlk_social_share('auto');
		$last_execution = $filter;
  		return  $content . $output;
    } 
    
    // If tag page and option "show on tag page" enabled
	if (is_tag() && ($option['show_in']['tags']))
    {
		$output = tlk_social_share('auto');
		$last_execution = $filter;
  		return  $content . $output;
    } 
    
    // If author page and option "show on author page" enabled
	if (is_author() && ($option['show_in']['authors']))
    {
		$output = tlk_social_share('auto');
		$last_execution = $filter;
  		return  $content . $output;
    } 
    
    // If search page and option "show on search page" enabled
	if (is_search() && ($option['show_in']['search']))
    {
        $output = tlk_social_share('auto');
		$last_execution = $filter;
  		return  $content . $output;
    }
    
    // If archive page and option "show on archive" enabled
	if (is_date() && ($option['show_in']['date_arch']))
    {
        $output = tlk_social_share('auto');
		$last_execution = $filter;
  		return  $content . $output;
    }
	
	return $content;
}

/**
 * Manually adds the social share to a page
 * @return type
 */
function tlk_add_social_share()
{
    $option = twitter_linkedin_kindle_get_options_stored();
    $output = tlk_social_share('manual');
    echo $output;
}

/**
 * Create the social share buttons for output
 * @global type $posts
 * @param type $source
 * @return string
 */
function tlk_social_share($source)
{
	global $posts;
    
	// Get stored share options
	$option = twitter_linkedin_kindle_get_options_stored();

    // Post title and link
 	$post_link  = get_permalink();
	$post_title = get_the_title();
	
    // Start output creation
	$output = '<div class="tlk_share_buttons">';

    // Create newsletter sign up button
    if ($option['active_buttons']['newsletter'] == true && is_single() == true)
    {
        $nl_text  = $option['cta_text'];
        $nl_title = $option['cta_title'];
        $nl_link  = $option['cta_link'];

        $newsletter_text = '<div class="newsletter_signup">';
        if (!empty($nl_text))
        {
            $newsletter_text .= '<p>' . $nl_text . '</p>';
        }

        $newsletter_text .= '<a href="' . $nl_link . '">' . $nl_title . '</a>';
        $newsletter_text .= '</div>';

        if (!empty($nl_title) && !empty($nl_link))
        {
            $output .= $newsletter_text;
        }        
    }
    
    // Create twitter button
    if ($option['active_buttons']['twitter'] == true)
    {
        $data_count = ($option['twitter_count']) ? 'horizontal' : 'none';
		if ($option['twitter_id'] != '') {
            $output .= '
                <div>
                <a href="http'.(is_ssl()?'s':'').'://twitter.com/share" class="twitter-share-button" data-url="'. $post_link .'"  data-text="'. $post_title . '" data-count="'.$data_count.'" data-via="'. $option['twitter_id'] . '"></a>
                </div>';
		} else {
            $output .= '
                <div>
                <a href="http'.(is_ssl()?'s':'').'://twitter.com/share" class="twitter-share-button" data-url="'. $post_link .'"  data-text="'. $post_title . '" data-count="'.$data_count.'"></a>
                </div>';
		}
	}
    
    // Create linkedin button
	if ($option['active_buttons']['linkedin'] == true)
    {
		$counter = ($option['linkedin_count']) ? 'right' : '';
		$output .= '<div><script type="in/share" data-url="' . $post_link . '" data-counter="' .$counter. '"></script></div>';
	}
    
    // Create send-to-kindle button
    if ($option['active_buttons']['kindle'] == true)
    {
        $output .= '<script type="text/javascript">(function k(){window.$SendToKindle&&window.$SendToKindle.Widget?$SendToKindle.Widget.init({"content":".kindle-post-content","pagination":".more-link","title":".kindle-post-title","author":".kindle-post-author","published":"kindle-post-date"}):setTimeout(k,500);})();</script>';
        $output .= '<div class="kindleWidget" style="display:inline-block;padding:3px;cursor:pointer;font-size:11px;font-family:Arial;white-space:nowrap;line-height:1;border-radius:3px;border:#ccc thin solid;color:black;background:transparent url(\'https://d1xnn692s7u6t6.cloudfront.net/button-gradient.png\') repeat-x;background-size:contain;"><img style="vertical-align:middle;margin:0;padding:0;border:none;" src="https://d1xnn692s7u6t6.cloudfront.net/white-15.png" /><span style="vertical-align:middle;margin-left:3px;">Send to Kindle</span></div>';
    }

	$output .= '</div>';
	return $output;
}

function tlk_social_share_shortcode ()
{
	$option = twitter_linkedin_kindle_get_options_stored();
	$output = tlk_social_share('shortcode');
	echo $output;
}

/**
 * This script will go through different possible options to retrive the display image associated with each post.  
 * @global type $post
 * @param type $args
 * @return type
 */
function tlk_get_image($args = array() ) 
{
    global $post;
 
    $defaults = array('post_id' => $post->ID);
    $args = wp_parse_args( $args, $defaults );
 
    /* Get the first image if it exists in post content.  */
    $final_img = get_image_from_post_thumbnail($args);
 
    if(!$final_img)
        $final_img = get_image_from_attachments($args);
 
    if(!$final_img)
        $final_img = get_image_in_post_content($args);
 
    $final_img = str_replace($url, '', $final_img);
    return $final_img;
}


/**
 * Function to search through post contents and return the first available image in the content.
 * @param type $args
 * @return type
 */
function get_image_in_post_content($args = array() )
{
    $display_img = '';
    $url = get_bloginfo('url');
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field( 'post_content', $args['post_id'] ), $matches);
    $display_img = $matches [1] [0];
    return $display_img;
}


/* 
Function to find image using WP available function get_the_post_thumbnail(). 
Note: This function will be available only if your theme supports the same.
Post Thumbnail is a theme feature introduced with Version 2.9. 

Themes have to declare their support for post images before the interface for assigning these images will appear on the Edit Post and Edit Page screens. They do this by putting the following in their functions.php file:

if ( function_exists( 'add_theme_support' ) ) { 
  add_theme_support( 'post-thumbnails' ); 
}
 */

function get_image_from_post_thumbnail($args = array())
{
	if (function_exists('has_post_thumbnail')) {
		if (has_post_thumbnail( $args['post_id']))
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $args['post_id'] ), 'single-post-thumbnail' );
	}
 	return $image[0];

}


function get_image_from_attachments($args = array())
{
	if (function_exists('wp_get_attachment_image')) {
        $children = get_children(
            array(
                'post_parent'=> $args['post_id'],
                'post_type'=> 'attachment',
                'numberposts'=> 1,
                'post_status'=> 'inherit',
                'post_mime_type' => 'image',
                'order'=> 'ASC',
                'orderby'=> 'menu_order ASC'
            )
        );

        if ( empty( $children ))
            return false;

        $image = wp_get_attachment_image_src( $children[0], 'thumbnail');
        return $image;
	}
}
?>