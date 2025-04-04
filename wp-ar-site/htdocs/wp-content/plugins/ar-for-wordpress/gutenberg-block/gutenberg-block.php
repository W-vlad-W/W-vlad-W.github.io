<?php
/**
 * Plugin Name:       Gutenberg Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       gutenberg-block
 *
 * @package ArForWordpress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function ar_for_wordpress_gutenberg_block_block_init() {
	 register_block_type( __DIR__ . '/build/modelblock', array(
        'render_callback' => 'arwp_gutenberg_block_callback',
        'attributes'      => array(
            'id' => array(
                'type'    => 'number',
                'default' => 0,
            ),
        ),
    ) );


    register_block_type( __DIR__ . '/build/galleryblock', array(
        'render_callback' => 'arwp_gallery_block_callback',        
    ) );

    register_block_type( __DIR__ . '/build/uploadblock', array(
        'render_callback' => 'arwp_upload_block_callback',        
    ) );
}
add_action( 'init', 'ar_for_wordpress_gutenberg_block_block_init' );

function arwp_block_categories( $categories ) {
    $category_slugs = wp_list_pluck( $categories, 'slug' );
    return in_array( 'ar_display', $category_slugs, true ) ? $categories : array_merge(
        $categories,
        array(
            array(
                'slug'  => 'ar_display',
                'title' => __( 'AR Display', 'ar-for-wordpress' ),
                'icon'  => null,
            ),
        )
    );
}
add_filter( 'block_categories_all', 'arwp_block_categories' );



function arwp_gutenberg_block_callback($attributes){
    ob_start();

    $is_backend = defined('REST_REQUEST') && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_SPECIAL_CHARS );

    if(isset($attributes['id']) && '' != $attributes['id'] && $attributes['id'] > 0){
      $attributes['id'] = $attributes['id'];
      //$ar_model_display = ar_display_shortcode($attributes);


      if ( $is_backend ) {
          echo '<p><span>[ardisplay id='.esc_html($attributes['id']).']';
          echo '<p>'.get_the_post_thumbnail( $attributes['id'], 'thumbnail', array( 'class' => 'alignleft' ) ).'</p>';
      } else {
        //echo wp_kses($ar_model_display, ar_allowed_html());
        echo do_shortcode('[ar-display id="'.$attributes['id'].'"]');
      }

    } else {
        echo '<p><span>&nbsp;</span></p>';      
    }

    return ob_get_clean();

}


function arwp_gallery_block_callback($attributes){
    global $post;
    ob_start();

    $is_backend = defined('REST_REQUEST') && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_SPECIAL_CHARS );

    

    if(has_post_thumbnail()){
       $image_url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
      //$ar_model_display = ar_display_shortcode($attributes);


      if ( $is_backend ) {
          echo '<p><span>[ar-gallery]';
          //echo '<p><img src="'.$image_url.'" /></p>';
      } else {
        //echo wp_kses($ar_model_display, ar_allowed_html());
        echo do_shortcode('[ar-gallery]');
      }

    } else {
        echo '<p><span>&nbsp;</span></p>';      
    }

    return ob_get_clean();

}


function arwp_upload_block_callback($attributes){
    global $post;
    ob_start();

    $is_backend = defined('REST_REQUEST') && true === REST_REQUEST && 'edit' === filter_input( INPUT_GET, 'context', FILTER_SANITIZE_SPECIAL_CHARS );

    if ( $is_backend ) {
        echo '<p><span>[ar-user-upload]';
    } else {        
        echo do_shortcode('[ar-user-upload]');
    }

    return ob_get_clean();

}
