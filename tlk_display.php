<?php 
/**
 * Core logic to display social share icons at the required positions. 
 */
function tlkDisplayInit() {
  if ( is_admin() ) {
    return;
  }
  
  $options = tlkOptions();

  if ( $options['twitter']['display'] ) {
    wp_enqueue_script(
      'tlk-twitter-share', 
      'https://platform.twitter.com/widgets.js',
      false,
      null,
      false
    );
  }
  
  // Load linkedin share JS
  if ( $options['linkedin']['display'] ) {
    wp_enqueue_script(
      'tlk-linkedin-share', 
      'https://platform.linkedin.com/in.js',
      false,
      null,
      false
    );
  }
    
  if ($options['kindle']['display'] ) {
    wp_enqueue_script(
      'tlk-kindle-share', 
      'https://d1xnn692s7u6t6.cloudfront.net/widget.js',
      false,
      null,
      false
    );
  } 
}

function tlkStyles() {
  wp_enqueue_style(
    'tlk-css',
    plugin_dir_url( __FILE__ ) . 'tlk_display.css',
    false
  );
}

function tlkDisplay() {
  global $posts;

  $options = tlkOptions();

  $postLink  = get_permalink();
  $postTitle = get_the_title();

  $output = '<div class="tlk-share-buttons">';

  if ( $options['auto_display'] || $options['twitter']['display'] ) {
    $output .= tlkTwitterButton( $options['twitter'], $postLink, $postTitle );
  }

  if ( $options['auto_display'] || $options['linkedin']['display'] ) {
    $output .= tlkLinkedInButton( $options['linkedin'], $postLink );
  }

  if ( $options['auto_display'] || $options['kindle']['display'] ) {
    $output .= tlkKindleButton();
  }

  $output .= '</div>';

  return $output;
}

function tlkTwitterButton( $option, $postLink, $postTitle ) {
  $showCount = $option['count']
    ? 'horizontal'
    : 'none';

  $prefix = 'http' . is_ssl() 
    ? 's' 
    : '';

  $dataVia = $option['show_handle'] && $option['handle']
    ? "data-via=\"{$option['handle']}\""
    : '';
  
  return "<div class=\"twitter-button\">
    <a href=\"{$prefix}://twitter.com/intent/tweet\" 
      class=\"twitter-share-button\" 
      data-url=\"{$postLink}\"
      data-text=\"{$postTitle}\"
      data-count=\"{$showCount}\"
      {$dataVia}
    ></a>
  </div>";
}

function tlkLinkedInButton( $option, $postLink ) {
  $showCount = $option['count']
    ? 'right'
    : '';

  return "<div class=\"linkedin-button\">
    <script type=\"in/share\"
      data-url=\"{$postLink}\"
      data-counter=\"{$showCount}\"
    ></script>
  </div>";
}

function tlkKindleButton() {
  $script = '<script type="text/javascript">(function k(){window.$SendToKindle&&window.$SendToKindle.Widget?$SendToKindle.Widget.init({}):setTimeout(k,500);})();</script>';

  return $script . '<div class="kindleWidget kindle-button">
    <img src="https://d1xnn692s7u6t6.cloudfront.net/white-15.png" />
    <span>Send to Kindle</span>
  </div>';
}

function tlkShortcode() {
  return tlkDisplay();
}

function tlkDisplayInContent( $content ) {
  return tlkDisplayContent( $content, 'content' );
}

function tlkDisplayInExcerpt( $content ) {
  return tlkDisplayContent( $content, 'excerpt' );
}

function tlkDisplayContent( $content, $filter ) {
  if ( is_page() || is_search() || is_author() ) {
    return $content;
  }

  $tlkOutput = tlkDisplay();
    
  return $content . $tlkOutput;
}
