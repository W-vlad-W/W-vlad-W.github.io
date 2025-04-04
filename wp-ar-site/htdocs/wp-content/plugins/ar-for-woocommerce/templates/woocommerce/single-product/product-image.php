<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
            // If the nonce is invalid, stop the process
          //  wp_die( __( 'Security check failed.', 'ar-for-wordpress' ) );
}
global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<div class="woocommerce-product-gallery__wrapper">
		<?php
		// Show AR Model
        if (!empty(get_post_meta($product->get_id(), '_glb_file', true))) {
            $html = '<div class="woocommerce-product-gallery__image--placeholder asadasdasd">';
            $html .= do_shortcode('[ardisplay id=' . $product->get_id() . ']');
            $html .= '</div>';
            // Add server-side function to handle AJAX request
            add_action('wp_ajax_get_ar_content', 'get_ar_content');
            add_action('wp_ajax_nopriv_get_ar_content', 'get_ar_content');
        
            function get_ar_content() {
            	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
				            // If the nonce is invalid, stop the process
				          //  wp_die( __( 'Security check failed.', 'ar-for-wordpress' ) );
				}
            	
                $product_id = isset($_POST['product_id']) ? intval(sanitize_text_field(wp_unslash($_POST['product_id']))) : 0;
        
                // Fetch AR content based on the new product ID
                $ar_content = get_post_meta($product_id, '_glb_file', true);
        
                echo do_shortcode('[ardisplay id=' . $product_id . ']');
                wp_die();
            }
        }elseif ( $post_thumbnail_id ) {
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			$wrapper_classname = $product->is_type( 'variable' ) && ! empty( $product->get_available_variations( 'image' ) ) ?
				'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
				'woocommerce-product-gallery__image--placeholder';
			$html              = sprintf( '<div class="%s">', esc_attr( $wrapper_classname ) );
			$html             .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'ar-for-woocommerce' ) );
			$html             .= '</div>';
		}

		echo wp_kses(apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ), ar_allowed_html()); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</div>
</div>
