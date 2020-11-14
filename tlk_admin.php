<?php
/** 
 * The main admin page for this plugin. The logic for different user input and 
 * form submittion is written here. 
 */
function tlkAdminMenu() {
  add_options_page(
    'TLK Share Buttons', 
    'TLK Share Buttons', 
    'manage_options', 
    'tlk-share', 
    'tlkAdminPage'
  );
}

function tlkAdminPage() {
  $optionName = 'tlk-share-options';

  if ( ! current_user_can( 'manage_options' ) )  {
		wp_die( __( 'Insuffient permission' ) );
  }

  $options = tlkOptions();

  if ( isset( $_POST['submit'] ) ) {
    $options = tlkSaveOptions( $options, $_POST );
  }

  echo tlkAdminDisplay( $options ); 
}

function tlkSaveOptions( $options, $post ) {
  $options['auto_display'] = ( 
    isset( $post['auto_display'] ) && 
    $post['auto_display'] === 'on' 
  );

  $options['twitter']['display'] = ( 
    isset( $post['tlk_twitter'] ) && 
    $post['tlk_twitter'] === 'on' 
  );

  $options['twitter']['show_width'] = (
    isset( $post['tlk_twitter_width'] ) &&
    $post['tlk_twitter_width'] === 'on'
  );

  $options['twitter']['width'] = ( 
    strlen( $post['tlk_twitter_width_num'] ) > 0
  )
    ? esc_html( $post['tlk_twitter_width_num'] )
    : 90;

  $options['twitter']['show_handle'] = (
    isset( $post['tlk_twitter_name'] ) &&
    $post['tlk_twitter_name'] === 'on'
  );
  
  $options['twitter']['handle'] = ( 
    isset( $post['tlk_twitter_name_text'] ) && 
    strlen( $post['tlk_twitter_name_text'] ) > 0
  )
    ? esc_html( $post['tlk_twitter_name_text'] )
    : '';

  $options['twitter']['count'] = ( 
    isset( $post['tlk_twitter_counter'] ) && 
    $post['tlk_twitter_counter'] === 'on' 
  );

  $options['linkedin']['display'] = ( 
    isset( $post['tlk_linkedin'] ) && 
    $post['tlk_linkedin'] === 'on'
  );

  $options['linkedin']['show_width'] = (
    isset( $post['tlk_linkedin_width'] ) &&
    $post['tlk_linkedin_width'] === 'on'
  );

  $options['linkedin']['width'] = (
    strlen( $post['tlk_linkedin_width_num'] ) > 0
  )
    ? esc_html( $post['tlk_linkedin_width_num'] )
    : 90;

  $options['linkedin']['count'] = ( 
    isset( $post['tlk_linkedin_counter'] ) && 
    $post['tlk_linkedin_counter'] === 'on'
  );
  
  $options['kindle']['display'] = ( 
    isset( $post['tlk_kindle'] ) && 
    $post['tlk_kindle'] === 'on' 
  );

  update_option( 'tlk-share-options', json_encode( $options ) );

  return $options;
}

function tlkOptionsDefault() {
  $defaults = array(
    'auto_display' => true,
    'twitter' => array(
      'display' => true,
      'width' => 90
    ),
    'linkedin' => array(
      'display' => true,
      'width' => 90
    ),
    'kindle' => array(
      'display' => true
    )
  );

  return json_encode( $defaults );
}

function tlkAdminDisplay(  $options  ) {
  $title = __( 'Twitter, LinkedIn, Kindle Social Share', 'menu-share' );
  $autoLabel = __( 'Enable Social Share buttons', 'menu-share' );
  
  $twitter = twitterOptions( $options['twitter'] );
  $linkedIn = linkedInOptions( $options['linkedin'] );
  $kindle = kindleOptions( $options['kindle'] );

  $autoDisplayChecked = $options['auto_display']
    ? 'checked=checked'
    : '';

  $autoDisplay = sprintf(
    '<label for=\"auto_display\">%1$s %2$s</label>',
    '<input name="auto_display" type="checkbox" id="auto_display" ' . $autoDisplayChecked . '/>',
    __( 'Enable Social Share buttons', 'menu-share' ),
  );

  return 
  "<div class=\"wrap\">
    <h1>{$title}</h1>
    <form id=\"tlk_settings\" name=\"tlk_form\" method=\"post\" action=\"\">
      <table class=\"form-table\" role=\"presentation\">
        <tbody>
          <tr>
            <th scope=\"row\">General Options</th>
            <td>
              <fieldset>
                <legend class=\"screen-reader-text\">
                  <span>General Settings</span>
                </legend>
                {$autoDisplay}
              </fieldset>
            </td>
          </tr>
          <tr>
            <th scope=\"row\">Twitter Options</th>
            <td>
              <fieldset>
                <legend class=\"screen-reader-text\">
                  <span>Twitter Settings</span>
                </legend>
                {$twitter}
              </fieldset>
            </td>
          </tr>
          <tr>
            <th scope=\"row\">LinkedIn Settings</th>
            <td>
              <fieldset>
                <legend class=\"screen-reader-text\">
                  <span>LinkedIn Settings</span>
                </legend>
                {$linkedIn}
              </fieldset>
            </td>
          </tr>
          <tr>
            <th scope=\"row\">Kindle Settings</th>
            <td>
              <fieldset>
                <legend class=\"screen-reader-text\">
                  <span>Kindle Settings</span>
                </legend>
                {$kindle}
              </fieldset>
            </td>
          </tr>
        </tbody>
      </table>
      <input type=\"submit\" name=\"submit\" id=\"submit\" class=\"button button-primary\" value=\"Save Changes\">
    </form>
  </div>";
}

function twitterOptions( $option ) {
  $enabled = $option['display']
    ? 'checked=checked'
    : '';

  $enable = sprintf(
    '<label for=\"tlk_twitter\">%1$s %2$s</label>',
    '<input class="tlk_enabled" name="tlk_twitter" type="checkbox" id="tlk_twitter" ' . $enabled . '/>',
    __( 'Enable the Twitter button', 'menu-share' )
  );

  $showWidth = $option['show_width']
    ? 'checked=checked'
    : '';

  $setWidth = sprintf(
    '<label for="tlk_twitter_width">%1$s %2$s %3$s %4$s</label>',
    '<input name="tlk_twitter_width" type="checkbox" id="tlk_twitter_width" ' . $showWidth . '/>',
    __( 'Set the button width to' ),
    '<input name="tlk_twitter_width_num" value="' . esc_html( $option['width'] ) . '" type="number" step="10" min="50" id="tlk_twitter_width_num" class="small-text"/>',
    __( 'px wide' )
  );

  $showHandle = strlen( $option['show_handle'] ) > 0
    ? 'checked=checked'
    : '';

  $setHandle = sprintf(
    '<label for="tlk_twitter_name">%1$s %2$s %3$s %4$s</label>',
    '<input name="tlk_twitter_name" type="checkbox" id="tlk_twitter_name" ' . $showHandle . '/>',
    __( 'Set your twitter handle to' ),
    '<input name="tlk_twitter_name_text" value="' . esc_html( $option['handle'] ) . '" type="text" id="tlk_twitter_name_text"/>',
    __( '(excluding "@")' )
  );

  $showCount = strlen( $option['count'] ) > 0
    ? 'checked=checked'
    : '';

  $setCount = sprintf(
    '<label for=\"tlk_twitter_counter\">%1$s %2$s</label>',
    '<input name="tlk_twitter_counter" type="checkbox" id="tlk_twitter_counter" ' . $showCount . '/>',
    __( 'Show the Retweet count', 'menu-share' )
  );

  return sprintf(
    '%1$s<br/>%2$s<br/>%3$s<br/>%4$s',
    $enable, $setWidth, $setHandle, $setCount
  );
}

function linkedInOptions( $option ) {
  $enabled = $option['display']
    ? 'checked=checked'
    : '';

  $enable = sprintf(
    '<label for=\"tlk_linkedin\">%1$s %2$s</label>',
    '<input class="tlk_enabled" name="tlk_linkedin" type="checkbox" id="tlk_linkedin" ' . $enabled . '/>',
    __( 'Enable the LinkedIn button', 'menu-share' )
  );

  $showWidth = $option['show_width']
    ? 'checked=checked'
    : '';

  $setWidth = sprintf(
    '<label for="tlk_linkedin_width">%1$s %2$s %3$s %4$s</label>',
    '<input name="tlk_linkedin_width" type="checkbox" id="tlk_linkedin_width" ' . $showWidth . '/>',
    __( 'Set the button width to' ),
    '<input name="tlk_linkedin_width_num" value="' . esc_html( $option['width'] ) . '" type="number" step="10" min="50" id="tlk_linkedin_width_num" class="small-text"/>',
    __( 'px wide' )
  );

  $showCount = strlen( $option['count'] ) > 0
    ? 'checked=checked'
    : '';

  $setCount = sprintf(
    '<label for=\"tlk_linkedin_counter\">%1$s %2$s</label>',
    '<input name="tlk_linkedin_counter" type="checkbox" id="tlk_linkedin_counter" ' . $showCount . '/>',
    __( 'Show the LinkedIn share count', 'menu-share' )
  );

  return sprintf(
    '%1$s<br/>%2$s<br/>%3$s',
    $enable, $setWidth, $setCount
  );
}

function kindleOptions( $option ) {
  $enabled = $option['display']
    ? 'checked=checked'
    : '';

  $enable = sprintf(
    '<label for=\"tlk_kindle\">%1$s %2$s</label>',
    '<input class="tlk_enabled" name="tlk_kindle" type="checkbox" id="tlk_kindle" ' . $enabled . '/>',
    __( 'Enable the Kindle button', 'menu-share' )
  );

  return $enable;
}

function tlkAdminScripts( $hook ) {
  if ( 'settings_page_tlk-share' !== $hook ) {
    return;
  }

  wp_enqueue_script(
    'tlk-javascript',
    plugins_url( '/tlk_admin.js', __FILE__ ),
    array('jquery'),
    '1.0.0',
    true
  );
}
