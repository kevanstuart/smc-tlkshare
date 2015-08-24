<?php
/*
The main admin page for this plugin. The logic for different user input and form submittion is written here. 
*/

function twitter_linkedin_kindle_admin_menu() 
{
    add_options_page('TLK Social Share', 'TLK Social Share', 'manage_options', 'tlk-social-share', 'twitter_linkedin_kindle_admin_page');
}

function twitter_linkedin_kindle_admin_page() 
{
	$option_name = 'twitter_linkedin_kindle_share';
    
    /**
     * Check permissions for admin page
     */
    if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

    
    $active_buttons = array(
          'twitter'    => 'Twitter'
        , 'linkedin'   => 'LinkedIn'
        , 'kindle'     => 'Kindle'
        , 'newsletter' => 'Newsletter Signup'
	);

    $show_in = array(
        'posts'      => 'Single posts',
 		'pages'      => 'Pages',
		'home_page'  => 'Home page',
		'tags'       => 'Tags',
		'categories' => 'Categories',
		'authors'    => 'Author archives',
		'search'     => 'Search results',
		'date_arch'  => 'Archives'
	);
	
	$out = '';
	
    /**
     * If POST twitter_linkedin_kindle_share_position
     */
	if (isset($_POST['Submit']))
    {
		$option = array();
        
        // Auto insert?
		$option['auto'] = (isset($_POST['twitter_linkedin_kindle_share_auto_display']) && $_POST['twitter_linkedin_kindle_share_auto_display']=='on') ? true : false;

        // Active buttons?
		foreach (array_keys($active_buttons) as $item)
        {
			$option['active_buttons'][$item] = (isset($_POST['twitter_linkedin_kindle_share_active_'.$item]) && 
                                                      $_POST['twitter_linkedin_kindle_share_active_'.$item]=='on') ? true : false;
		}
        
        // Show in pages?
		foreach (array_keys($show_in) as $item)
        {
			$option['show_in'][$item] = (isset($_POST['twitter_linkedin_kindle_share_show_'.$item]) && 
                                               $_POST['twitter_linkedin_kindle_share_show_'.$item]=='on') ? true : false;
		}

        // Twitter ID
		$option['twitter_id']     = esc_html($_POST['twitter_linkedin_kindle_share_twitter_id']);
        
        // Twitter box and linkedin box widths
        $option['twitter_width']  = esc_html($_POST['twitter_linkedin_kindle_share_twitter_width']);
		$option['linkedin_width'] = esc_html($_POST['twitter_linkedin_kindle_share_linkedin_width']);
        
        // Show counts on twitter and linkedin boxes
		$option['twitter_count']  = (isset($_POST['twitter_linkedin_kindle_share_twitter_count']) && 
                                           $_POST['twitter_linkedin_kindle_share_twitter_count']=='on') ? true : false;
		$option['linkedin_count'] = (isset($_POST['twitter_linkedin_kindle_share_linkedin_count']) && 
                                           $_POST['twitter_linkedin_kindle_share_linkedin_count']=='on') ? true : false;

        // CTA details
        $option['cta_text']  = esc_html($_POST['twitter_linkedin_kindle_share_cta_text']);
        $option['cta_title'] = esc_html($_POST['twitter_linkedin_kindle_share_cta_title']);
        $option['cta_link']  = esc_html($_POST['twitter_linkedin_kindle_share_cta_link']);
        
		update_option($option_name, $option);
        
		// Put a settings updated message on the screen
		$out .= '<div class="updated"><p><strong>'.__('Settings saved.', 'menu-test' ).'</strong></p></div>';
	}
	
	// Get twitter_linkedin_kindle_share_options
	$option = twitter_linkedin_kindle_get_options_stored();
	
    $auto           = ($option['auto']) ? 'checked="checked"' : '';
	$twitter_count  = ($option['twitter_count'])  ? 'checked="checked"' : '';
	$linkedin_count = ($option['linkedin_count']) ? 'checked="checked"' : '';

	$out .= '
	<div class="wrap">

	<h2>' . __( 'Twitter, LinkedIn, Kindle Social Share', 'menu-test' ) . '</h2>
	<div id="poststuff" style="padding-top:10px; position:relative;">
		<div style="float:left; width:74%; padding-right:1%;">
            <form name="form1" method="post" action="">
                <div class="postbox">
                    <h3>' . __("General options", 'menu-test' ) . '</h3>
                    <div class="inside">
                    <table>
                        <tr><td style="padding-bottom:20px;" valign="top">'.__("Auto Display", 'menu-test' ).':</td>
                        <td style="padding-bottom:20px;">
                            <input type="checkbox" name="twitter_linkedin_kindle_share_auto_display" '.$auto.' />
                            <span class="description">'.__("Enable Auto display of Social Share buttons", 'menu-test' ).'</span>
                        </td></tr>
	
                        <tr><td style="padding-bottom:20px;" valign="top">'.__("Code for Manual Display", 'menu-test' ).':</td>
                        <td style="padding-bottom:20px;">
                        <code>&lt;?php if(function_exists(&#39;tlk_add_social_share&#39;)) tlk_add_social_share(); ?&gt;</code>
                        </td></tr>

                        <tr><td valign="top" style="width:130px;">'.__("Active share buttons", 'menu-test' ).':</td>
                        <td style="padding-bottom:30px;">';
	
    foreach ($active_buttons as $name => $text) {
        $checked = ($option['active_buttons'][$name]) ? 'checked="checked"' : '';
        $out .= '<div style="width:150px; float:left;">
                <input type="checkbox" name="twitter_linkedin_kindle_share_active_'.$name.'" '.$checked.' /> '
                . __($text, 'menu-test' ).' &nbsp;&nbsp;</div>';

    }
	
	$out .= '</td></tr>
			<tr><td valign="top" style="width:130px;">'.__("Show buttons in these pages", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">';

    foreach ($show_in as $name => $text) {
        $checked = ($option['show_in'][$name]) ? 'checked="checked"' : '';
        $out .= '<div style="width:150px; float:left;">
                <input type="checkbox" name="twitter_linkedin_kindle_share_show_'.$name.'" '.$checked.' /> '
                . __($text, 'menu-test' ).' &nbsp;&nbsp;</div>';
    }

    $pages   = get_pages();
    $select  = '<select style="width:100%;" name="twitter_linkedin_kindle_share_cta_link">';
    foreach( $pages as $page ){
        $select .= '<option value="' . get_page_link( $page->ID ) . '" ' . selected( get_page_link( $page->ID ), $option['cta_link'] ) . '>' . $page->post_title . '</option>';
    }
    $select .= '</select>';

	$out .= '</td></tr>';
	$out .= 
                            '<tr><td style="padding-bottom:20px;" valign="top">'.__("Your Twitter ID", 'menu-test' ).':</td>
                                <td style="padding-bottom:20px;">
                                    <input type="text" name="twitter_linkedin_kindle_share_twitter_id" value="'.$option['twitter_id'].'" size="30"/>  
                                    <span class="description">'.__("Specify your twitter id without @", 'menu-test' ).'</span>
                                </td></tr> 
                        </table>
                    </div>
                </div>

                <div class="postbox">
                    <h3>' . __("Adjust Width and Count Display", 'menu-test' ) . '</h3>
                    <div class="inside">
                        <table>
                            <tr><td style="padding-bottom:20px; padding-right:10px;" valign="middle">'.__("Twitter Button width", 'menu-test' ).':</td>
                                <td style="padding-bottom:20px;">
                                    <input type="text" name="twitter_linkedin_kindle_share_twitter_width" value="'.stripslashes($option['twitter_width']).'" size="5"> px <br />
                                </td>

                                <td style="padding-bottom:20px; padding-left:50px; padding-right:10px;" valign="middle">'.__("Linkedin Button width", 'menu-test' ).':</td>
                                <td style="padding-bottom:20px;">
                                    <input type="text" name="twitter_linkedin_kindle_share_linkedin_width" value="'.stripslashes($option['linkedin_width']).'" size="5"> px <br />
                                </td>
                            </tr>   

                            <tr><td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("Twitter counter", 'menu-test' ).':</td>
                                <td style="padding-bottom:20px;">
                                    <input type="checkbox" name="twitter_linkedin_kindle_share_twitter_count" '.$twitter_count.' />
                                </td>
                                <td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("LinkedIn counter", 'menu-test' ).':</td>
                                <td style="padding-bottom:20px;">
                                    <input type="checkbox" name="twitter_linkedin_kindle_share_linkedin_count" '.$linkedin_count.' />
                                </td>	
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="postbox">
                    <h3>' . __("Newsletter Signup Display") . '</h3>
                    <div class="inside">
                        <table style="width:50%;">
                            <tr>
                                <td colspan="2" valign="middle">'.__("Call-To-Action Text", 'cta-text' ).':</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-bottom:20px;">
                                    <textarea style="width:100%;height:100px;" name="twitter_linkedin_kindle_share_cta_text">'.stripslashes($option['cta_text']).'</textarea>
                                </td>
                            </tr>

                            <tr>
                                <td style="width:50%;" valign="middle">'.__("Call-To-Action Title", 'cta-title' ).':</td>
                                <td>
                                    <input style="width:100%;" type="text" name="twitter_linkedin_kindle_share_cta_title" value="'.stripslashes($option['cta_title']).'">
                                </td>
                            </tr>

                            <tr>
                                <td valign="middle">' . __("Call-To-Action Link", 'cta-link') . ':</td>
                                <td>' . $select . '</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="'.esc_attr('Save Changes').'" />
                </p>
            </form>
        </div>
	
	</div>';
	echo $out; 
}

/**
 * Retrieves share options previously stored
 * @return boolean
 */
function twitter_linkedin_kindle_get_options_stored () {
    
	// Get option array
	$option = get_option('twitter_linkedin_kindle_share');
	 
	if ($option===false)
    {
		$option = twitter_linkedin_kindle_get_options_default();
		add_option('twitter_linkedin_kindle_share', $option);
	} 
    elseif ($option=='above' or $option=='below')
    {
		// Versions below 1.2.0 compatibility
		$option = twitter_linkedin_kindle_get_options_default();
	}
    else if(!is_array($option))
    {
		// Versions below 1.2.2 compatibility
		$option = json_decode($option, true);
	}
	
    // Set auto to true
	if (!isset($option['auto'])) {
		$option['auto'] = true;
	}
    
    // Set twitter box width
	if (!isset($option['twitter_width'])) {
		$option['twitter_width'] = '95';
	}
    
    // Set linkedin box width
	if (!isset($option['linkedin_width'])) {
		$option['linkedin_width'] = '105';
	}
    
    // Set twitter count 
	if (!isset($option['twitter_count'])) {
		$option['twitter_count'] = true;
	}
    
    // Set linkedin count
	if (!isset($option['linkedin_count'])) {
		$option['linkedin_count'] = true;
	}
    
	return $option;
}

/**
 * Return defaults for the options array 
 * @return array
 */
function twitter_linkedin_kindle_get_options_default ()
{
    $option = array(
                    'auto' => true
        , 'active_buttons' => array('twitter'=>true, 'linkedin'=>true, 'kindle'=>true)
               , 'show_in' => array('posts'=>true, 'page'=>false,'home_page'=>true, 'tags'=>true, 'categories'=>true, 'authors'=>true, 'search'=>true, 'date_arch'=>true)
         , 'twitter_width' => '95'
        , 'linkedin_width' => '105'
         , 'twitter_count' => true
        , 'linkedin_count' => true
    );
	return $option;
}
?>