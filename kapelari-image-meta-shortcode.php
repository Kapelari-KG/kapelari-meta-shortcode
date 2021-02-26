<?php
/******************************************************************

Plugin Name: Kapelari KG – Bilddaten Shortcode
Plugin URI: http://www.kapelari.com
Description: Dieses Plugin erstellt einen Shortcode, welcher Bild-Metadaten ausgibt. [kkg_meta title="Bildnachweis" meta="title" prefix="Fotografen:"]
Version: 1.0

Author: Bernhard Kapelari | Kapelari KG
Author URI: http://www.kapelari.com

License: WTFPL
License URI: http://www.wtfpl.net/

******************************************************************/



function kkg_meta_shortcode( $atts = [], $content = null ) {
  // normalize attribute keys, lowercase
  $atts = array_change_key_case( (array) $atts, CASE_LOWER );


  /* post_except = Beschriftung
   * post_content = Beschreibung
   * post_title = Titel
   * post_alt = eigene abfrage
   */

  // override default attributes with user attributes
  $kkg_meta_atts = shortcode_atts(
      array(
          'title' => 'Bildnachweis',
          'prefix' => "Fotografen:",
          'meta' => 'post_title',
      ), $atts
  );



  


  // start box
  $o = '<div class="kkg-meta-box">';
  
  // title
  $o .= '<h2>' . esc_html__( $kkg_meta_atts['title'], 'kkg_meta' ) . '</h2>';

  $query_images_args = array(
      'post_type'      => 'attachment',
      'post_mime_type' => 'image',
      'post_status'    => 'inherit',
      'posts_per_page' => - 1,
      
  );
  
  $query_images = new WP_Query( $query_images_args );
  $result = [];

  if($query_images) {
    foreach ( $query_images->posts as $image ) {
      if($image->{$kkg_meta_atts['meta']}){
        if($kkg_meta_atts['meta'] == 'alt') {
          $result[] .= get_post_meta($image->ID, '_wp_attachment_image_alt', TRUE);
        }else {
          $result[] .= $image->{$kkg_meta_atts['meta']};
        }
      }
    }

    $result = array_unique($result);
    $o .= "<p class='kkg-meta-list'>";
    $o .= "<strong>" . $kkg_meta_atts['prefix'] . "</strong> ";
    $o .= implode(', ', $result); 
    $o .= "</p>";
  }

  // end box
  $o .= '</div>';

  // return output
  return $o;
}

/**
* Central location to create all shortcodes.
*/
function kkg_meta_shortcodes_init() {
  add_shortcode( 'kkg_meta', 'kkg_meta_shortcode' );
}

add_action( 'init', 'kkg_meta_shortcodes_init' );