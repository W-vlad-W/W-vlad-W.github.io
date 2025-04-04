<?php
/**
 * Plugin Name: AR for WooCommerce
 * Plugin URI: https://augmentedrealityplugins.com
 * Description: AR for WooCommerce Augmented Reality plugin.
 * Version: 7.9
 * Author: Web and Print Design 
 * Author URI: https://webandprint.design
 * License:  GPL2
 * Text Domain: ar-for-woocommerce
 * Domain Path: /languages
 * WC requires at least: 4.2
 * WC tested up to: 9.7.1
 **/

if ( ! defined( 'ABSPATH' ) ) exit; 


$ar_plugin_id='ar-for-woocommerce';
$ar_plugin_root_url = plugin_dir_url(__FILE__);
$ar_wc_active = true;

add_action( 'init', function() {
    // Ensure plugin data is available
    if ( ! function_exists( 'get_plugin_data' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    // Get plugin data
    $ar_plugin_data = get_plugin_data( __FILE__ );
    
    // Declare the global variable
    global $ar_version;
    $ar_version = $ar_plugin_data['Version'];
});

add_action( 'plugins_loaded', 'arwc_load_text_domain', 20 );
function arwc_load_text_domain() {
    load_plugin_textdomain( 'ar-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

if(!class_exists('AR_Plugin')){
    require_once(plugin_dir_path(__FILE__). '/includes/ar-class.php');
    require_once(plugin_dir_path(__FILE__). '/includes/ar-model.php');
}

add_action( 'plugins_loaded', 'run_ar_plugin' );
if (!function_exists('run_ar_plugin')){
    function run_ar_plugin() {
        $plugin = new AR_Plugin();
        $plugin->run();
    }
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );


// Functions Load
require_once(plugin_dir_path(__FILE__). 'ar-wc-functions.php');

// AR Model Custom Fields (Save 3D Model Files and Images)
require_once(plugin_dir_path(__FILE__). 'ar-model-fields.php');
// AR Initialisation
require_once(plugin_dir_path(__FILE__). 'includes/ar-initialise.php');

// AR Settings Page and Licence Checks
require_once(plugin_dir_path(__FILE__). 'includes/ar-settings.php');

// AR Model File Handling
require_once(plugin_dir_path(__FILE__). 'includes/ar-file-handling.php');

// AR User Upload
require_once(plugin_dir_path(__FILE__). 'includes/ar-user-upload.php');

// AR Gallery Builder
require_once(plugin_dir_path(__FILE__). 'includes/ar-gallery-builder.php');

// AR QR Code
require_once(plugin_dir_path(__FILE__). 'includes/ar-qrcode.php');

// AR Standalone
require_once(plugin_dir_path(__FILE__). 'includes/ar-standalone.php');

// Widgets Load
require_once(plugin_dir_path(__FILE__). 'ar-widgets.php');

// Endpoint API Load
require_once(plugin_dir_path(__FILE__). 'ar-wc-api.php');

// Endpoint for Media upload
require_once(plugin_dir_path(__FILE__). 'includes/ar-add-media.php');

// AR Model Shop
require_once(plugin_dir_path(__FILE__). 'includes/ar-model-shop.php');

// Secure Encrypted URLs for Model Files
require_once(plugin_dir_path(__FILE__). 'includes/ar-secure-url-generate.php');

// Block Gutenberg Load
require_once(plugin_dir_path(__FILE__). 'gutenberg-block/gutenberg-block.php');

// Plugin Updates 
$this_file = __FILE__;
$update_check = "https://augmentedrealityplugins.com/plugins/check-update-ar-for-woocommerce.txt";

require_once(plugin_dir_path(__FILE__) . 'ar-updates.php');

// Add the data to the custom columns for the AR Model Products
add_action( 'manage_product_posts_custom_column' , 'ar_advance_custom_armodels_column', 10, 2 );

// Add js file for changing model when variation changed


function ar_wc_advance_register_script() {
    global $ar_version;
    wp_enqueue_script('ar_wc_model', plugins_url('assets/js/ar-variations.js', __FILE__), array('jquery'), $ar_version, false);
}

// Add column to indicate product has an AR Model - Admin Page
function ar_woo_advance_custom_edit_wp_columns($columns) {
    unset( $columns['date'] );
    $columns['Shortcode'] = __('AR Shortcode', 'ar-for-woocommerce' );
    $ARimgSrc = esc_url( plugins_url( "assets/images/chair.png", __FILE__ ) );    
    $columns['thumbs'] = '<div class="ar_tooltip"><img src="'.$ARimgSrc.'" width="15"><span class="ar_tooltip_text">'.__('AR Model', 'ar-for-woocommerce' ).'</span></div>'; //name of the column
    $columns['date'] = __( 'Date', 'ar-for-woocommerce' );
    return $columns;
}
add_filter( 'manage_edit-product_columns', 'ar_woo_advance_custom_edit_wp_columns' );





add_filter('woocommerce_settings_tabs_array', 'add_ar_display_tab', 50);
function add_ar_display_tab($tabs) {
    $tabs['ar_display'] = __('AR Display', 'ar-for-woocommerce'); // Register the 'ar_display' tab
    return $tabs;
}


// Add AR Display setting tab to Woocommerce > Settings section
//add_action( 'woocommerce_settings_tabs', 'wc_settings_tabs_ar_display_tab' );

function wc_settings_tabs_ar_display_tab() {

    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
            // If the nonce is invalid, stop the process
          //  wp_die( __( 'Security check failed.', 'ar-for-wordpress' ) );
    }

    $current_tab = ( isset($_GET['tab']) && $_GET['tab'] === 'ar_display' ) ? 'nav-tab-active' : '';
    echo '<a href="admin.php?page=wc-settings&tab=ar_display" class="nav-tab '.esc_attr($current_tab).'">'.esc_html(__( "AR Display", "ar-for-woocommerce" )).'</a>';
}

// The settings tab content
add_action( 'woocommerce_settings_ar_display', 'ar_subscription_setting' );

// Add links to Settings page on Plugins page
add_filter( 'plugin_action_links_ar-for-woocommerce/ar-woocommerce.php', 'arwc_settings_link' );
function arwc_settings_link( $links ) {
	$url = esc_url( add_query_arg(
		'page',
		'wc-settings',
		get_admin_url() . 'admin.php'
	) );
	$settings_link = "<a href='$url&tab=ar_display'>" . __( 'Settings', 'ar-for-woocommerce' ) . '</a>';
	array_push($links,$settings_link);
	$url = esc_url( add_query_arg(
		'post_type',
		'armodels',
		'https://wordpress.org/plugins/ar-for-woocommerce/#developers'
	) );
	$settings_link = "<a href='$url'>" . __( 'Whats New', 'ar-for-woocommerce' ) . '</a>';
	array_push($links,$settings_link);
	return $links;
}

//Add documentation link to the plugin on the plugins page
add_filter('plugin_row_meta', 'ar_woo_plugin_documentation_link', 10, 2);

function ar_woo_plugin_documentation_link($links, $file) {
    if (plugin_basename(__FILE__) === $file) {
        $documentation_link = '<a href="https://augmentedrealityplugins.com/support/" target="_blank">Documentation</a>';
        $links[] = $documentation_link;
    }
    return $links;
}

if ((!isset($ar_wcfm))){
    $shortcode_examples_wc = '<b>[ar-display]</b> - '.esc_html(__('Place on the Woocommerce product page to display the 3D model for the product id and includes all variations. Can be used in the product description or in your theme templates. See settings page for template options.', 'ar-for-woocommerce' )).'<br>';
    $shortcode_examples = '
        <b>[ar-display id=X]</b> - '.esc_html(__('Displays the 3D model for a given model/post id.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-display id=\'X,Y,Z\']</b> - '.esc_html(__('Displays the 3D models for multiple comma seperated model/post ids within 1 viewer and thumbnails to select model.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-display cat=X]</b> - '.esc_html(__('Displays the 3D models for a given category within 1 viewer and thumbnails to select model.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-display cat=\'X,Y,Z\']</b> - '.esc_html(__('Displays the 3D models for multiple comma seperated category ids within 1 viewer and thumbnails to select model.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-gallery]</b> - '.esc_html(__('Displays the 3D Gallery Model using the featured image of the current post. Includes a size selector.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-user-upload]</b> - '.esc_html(__('Displays the Model Viewer allowing the end user to drag and drop a model or image file to have it display.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-view id=X text=true (OR) buttons=true]</b> - '.esc_html(__('Display either the AR View button, the text link \'text=true\' "View in AR / View in 3D" or html buttons \'buttons=true\' for a given model/post id without the need for the 3D Model viewer being displayed. Custom text can be set on the AR Settings page.', 'ar-for-woocommerce' )).'<br>
        <b>[ar-qr]</b> - '.esc_html(__('QR Code shortcode display for the page or post the shortcode is added to.<br>', 'ar-for-woocommerce' ));
        
    $ar_rate_this_plugin = '<h3 style="margin-top:0px">'.esc_html(__('Rate This Plugin', 'ar-for-woocommerce' )).'</h3><img src="'.esc_url( plugins_url( "assets/images/5-stars.png", __FILE__ ) ).'" style="height:30px"><br>
    '.esc_html(__('We really hope you like using AR For WordPress and would be very greatful if you could leave a rating for it on the WordPress Plugin repository.', 'ar-for-woocommerce' )).'<br>
    <a href="https://wordpress.org/support/plugin/ar-for-woocommerce/reviews/" target="_blank">'.esc_html(__('Please click here to leave a rating for AR For WordPress.', 'ar-for-woocommerce' )).'</a>';
}

?>