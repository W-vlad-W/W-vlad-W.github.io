<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action( 'wp_ajax_set_ar_featured_image',  'set_ar_featured_image'  );
add_action( 'wp_ajax_nopriv_set_ar_featured_image',  'set_ar_featured_image'  );

if (!function_exists('arwp_rest_api_nonce')){
    function arwp_rest_api_nonce() {
        wp_localize_script('arwp-rest-api', 'wpApiSettings', array(
            'root'  => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'arwp_rest_api_nonce');


add_action( 'rest_api_init', function () {
    //Path to REST route and the callback function
    register_rest_route( 'arforwp/v2', '/set_ar_featured_image/', array(
            'methods' => 'POST', 
            'callback' => 'set_ar_featured_image' ,
            'permission_callback' => function (WP_REST_Request $request) {
                if (!is_user_logged_in()) {
                    return new WP_Error('rest_not_logged_in', __('You must be logged in to access this API.'), array('status' => 401));
                }
                if (!current_user_can('manage_options')) {
                    return new WP_Error('rest_forbidden', __('You do not have permission to access this API.'), array('status' => 403));
                }
                return true;
            },
    ) );
});

if (!function_exists('set_ar_featured_image')){
    function set_ar_featured_image(WP_REST_Request $request){
        // Check if the current user has the 'manage_options' capability (Admin-level permission)
        
        global $ar_plugin_id;
        $data = $request->get_params();
  
        $post_id = $data['post_ID'];
        $post_title = $data['post_title'];
        $base64_string = $data['_ar_poster_image_field'];
        
        // Sanitize and prepare the image name
        $image_name = sanitize_file_name($post_title . "_model_poster_image.png");

        // Prevent double extensions or invalid file names
        if (strpos($image_name, '.php') !== false || strpos($image_name, '.htaccess') !== false) {
            // Handle error, you could send a response or log it.
            return new WP_Error('invalid_image', 'Invalid image name.', array('status' => 400));
        } 
        $parsedUrl = isset($_SERVER["SCRIPT_URI"]) ? wp_parse_url(esc_url_raw(wp_unslash($_SERVER["SCRIPT_URI"]))) : '';
        $plugin_folder = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . '/';
        $image_data = '';
        $base64_data = '';
        $pattern = '/^data:image\/octet-stream;base64,/';

        if ( preg_match( $pattern, $base64_string, $type ) ) {
            $base64_data = substr( $base64_string, strpos( $base64_string, ',' ) + 1 );
            $base64_data = base64_decode( $base64_data );        
        }    
        $upload_dir = wp_upload_dir();
        $uploads_path = $upload_dir['basedir'] . '/' . $ar_plugin_id.'/';
        
        $url = $upload_dir['baseurl'] . '/' . $ar_plugin_id.'/' . $image_name;
        ar_wp_custom_file_write($uploads_path.$image_name,$base64_data);
        
        upload_image($image_name, $url, $post_id, $post_title);
    }
}

if (!function_exists('upload_image')){
    function upload_image($image_name, $url, $post_id, $post_title, $return = 0) {
        $image = "";

        if($url != "") {
            $file = array();
            $file['name'] = $image_name;
            $file['tmp_name'] = download_url($url);
            if (is_wp_error($file['tmp_name'])) {
                @wp_delete_file($file['tmp_name']);
                //var_dump( $file['tmp_name']->get_error_messages( ) );
                echo 'Error found.';
            } else {
                $attachmentId = media_handle_sideload($file, $post_id);
                if ( is_wp_error($attachmentId) ) {
                    @wp_delete_file($file['tmp_name']);
                    //var_dump( $attachmentId->get_error_messages( ) );
                    echo 'Error found.';
                } else {                
                    $image = wp_get_attachment_url( $attachmentId );
                    if($return){                       
                        return $attachmentId;
                    } else {
                        echo esc_html($attachmentId);
                        die();
                    }
                }
            }
            
        }
    }
}