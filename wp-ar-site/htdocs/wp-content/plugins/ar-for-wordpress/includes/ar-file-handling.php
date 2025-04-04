<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


if(!function_exists('ar_wp_custom_file_write')){
    function ar_wp_custom_file_write($file_path, $data) {
        // Initialize the WP Filesystem API
        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        
        global $wp_filesystem;

        // Initialize the API
        $creds = request_filesystem_credentials( site_url() );
        
        if ( ! WP_Filesystem( $creds ) ) {
            return false; // Exit if unable to initialize the Filesystem
        }

        // Check if the file exists or can be created
        if ( ! $wp_filesystem->exists( $file_path ) ) {
            // Create the file if it doesn't exist
            $wp_filesystem->put_contents( $file_path, '' );
        }

        // Open the file and write data
        if ( $wp_filesystem->put_contents( $file_path, $data, FS_CHMOD_FILE ) ) {
            return true; // Data successfully written
        } else {
            return false; // Writing failed
        }
    }
}

if(!function_exists('ar_wp_is_writable')){
    function ar_wp_is_writable($path) {
        // Load the WordPress filesystem API if it's not already loaded
        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        global $wp_filesystem;

        // Initialize the WordPress filesystem
        if ( ! WP_Filesystem() ) {
            return new WP_Error( 'filesystem_init_failed', __( 'Could not initialize filesystem.', 'ar-for-wordpress' ) );
        }

        // Check if the path is writable
        if ( $wp_filesystem->is_writable( $path ) ) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('ar_wp_readfile')) {
    function ar_wp_readfile($file_path) {
        // Load the WordPress filesystem API if it's not already loaded
        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        global $wp_filesystem;

        // Initialize the WordPress filesystem
        if ( ! WP_Filesystem() ) {
            return new WP_Error( 'filesystem_init_failed', __( 'Could not initialize filesystem.', 'ar-for-wordpress' ) );
        }

        // Check if the file exists and is readable
        if ( ! $wp_filesystem->exists( $file_path ) ) {
            return new WP_Error( 'file_not_found', __( 'The file does not exist.', 'ar-for-wordpress' ) );
        }

        // Check file permissions to ensure it's readable
        if ( ! $wp_filesystem->is_readable( $file_path ) ) {
            return new WP_Error( 'file_not_readable', __( 'The file is not readable.', 'ar-for-wordpress' ) );
        }

        // Read the file contents
        $file_contents = $wp_filesystem->get_contents( $file_path );

        if ( false === $file_contents ) {
            return new WP_Error( 'read_error', __( 'Could not read the file.', 'ar-for-wordpress' ) );
        }

        return $file_contents;
    }
}

if(!function_exists('ar_wp_get_page_by_title')){
    function ar_wp_get_page_by_title( $title, $post_type = 'page' ) {
        $args = array(
            'post_type'   => $post_type, // Type of post (e.g., 'page', 'post', etc.)
            'title'       => $title,     // The title of the page to search for
            'post_status' => 'publish',  // Ensure the post is published (modify as needed)
            'numberposts' => 1           // We only need one result
        );

        $query = new WP_Query( $args );

        // If a page is found, return the first result
        if ( $query->have_posts() ) {
            return $query->posts[0]; // Return the page/post object
        }

        return null; // Return null if no page is found
    }
}
//used to return the file contents in secure download and QR code popup
if(!function_exists('ar_return_file')){
    function ar_return_file($file_contents) {
        //No foreseeable way to escape the file contents without corrupting them
        echo $file_contents;
    }
}


// Check Zip Archive
if (!function_exists('ar_check_zip_archive')){
    function ar_check_zip_archive($filename) {
        $zip = new ZipArchive();
        $allowed_mime_types = get_allowed_mime_types(); // Get the list of allowed MIME types

        // Open the ZIP file
        if ($zip->open($filename) !== TRUE) {
            return false; // Return false if ZIP file cannot be opened
        }

        // Loop through each file in the ZIP archive
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $info = $zip->statIndex($i);
            $file_name = $info['name'];

            // Get the file extension
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Check if the file extension is allowed
            $file_type_check = wp_check_filetype_and_ext($filename, $file_name);

            // Check if the extension and MIME type are both valid
            if (!in_array($file_type_check['type'], $allowed_mime_types)) {
                $zip->close(); // Close the zip before returning
                return false; // Invalid file type found
            }
        }

        $zip->close(); // Close the zip archive
        return true; // All files are valid
    }
}

/********** Curl Get File **********/
if (!function_exists('ar_curl')) {
    function ar_curl($url) {
        // First, attempt with wp_remote_get() (using WordPress HTTP API)
        $response = wp_remote_get($url, array('timeout' => 60));

        // Check if wp_remote_get fails or if the response body is empty or only whitespace
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            //error_log('WP Remote Get Error: ' . $error_message);
            // Fall back to cURL if wp_remote_get failed
            $data = ar_curl_with_curl($url);
            if ($data) {
                return $data;
            } else {
                // Fall back to file_get_contents if both methods failed
                return ar_curl_with_file_get_contents($url);
            }
        } else {
            // Get the response body
            $body = wp_remote_retrieve_body($response);

            // Check if the body is empty or contains only spaces
            if (empty($body) || ctype_space($body)) {
                //error_log('WP Remote Get returned an empty or whitespace-only response body for URL: ' . $url);
                // Fall back to cURL if the response body is empty or only spaces
                $data = ar_curl_with_curl($url);
                if ($data) {
                    return $data;
                } else {
                    // Fall back to file_get_contents if both methods failed
                    return ar_curl_with_file_get_contents($url);
                }
            }
            return $body; // Return the valid body if it's not empty or just spaces
        }
    }

    // Function to handle cURL requests
    function ar_curl_with_curl($url) {
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Set timeout to 60 seconds

        // Set user-agent (optional but may help with certain restrictions)
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $data = curl_exec($ch);

        // Check if there was a cURL error
        if (curl_errno($ch)) {
            $error_message = curl_error($ch);
            //error_log('cURL Error: ' . $error_message);
            curl_close($ch);
            return false;
        }

        curl_close($ch);
        return $data;
    }

    // Function to handle file_get_contents fallback
    function ar_curl_with_file_get_contents($url) {
        // Fall back to file_get_contents if both wp_remote_get and cURL failed
        $data = @file_get_contents($url);

        // If file_get_contents fails, log the error
        if ($data === false) {
            //error_log('file_get_contents failed to fetch the URL: ' . $url);
            return false;
        }

        return $data;
    }
}


if (!function_exists('get_local_file_contents')){
    function get_local_file_contents( $file_path ) {
        ob_start();
        include $file_path;
        $contents = ob_get_clean();
    
        return $contents;
    }
}

/************* Upload AR Model Files Javascript *******************/
if (!function_exists('ar_model_fields_js')){
    function ar_model_fields_js($model_id, $variation_id='') { 
        global $ar_plugin_id;
        $wc_model = 0;
        $suffix = $variation_id ? "_var_".$variation_id : '';

        $arpost = get_post( $model_id );
        $ar_animation_selection = get_post_meta( $model_id, '_ar_animation_selection', true ); 

        if($arpost->post_type == 'product'){
            $product=wc_get_product($model_id);
            $product_parent=$product->get_parent_id();
            $wc_model = 1;
            if($product_parent==0){
                $product_parent = $model_id;
            }
        } else {
            $product_parent = $model_id;
        }

        $public = '';
        //Check if on admin edit page or public page
        if (is_admin()){
            $screen = get_current_screen();
        }
        if (!isset($screen)){
            //Showing Editor on Public Side
            $post   = get_post( $model_id );
            $public = 'y';
        }
        
        add_action('wp_footer', function (){ 
           
        ?>
            <script>
            
                var modelFieldOptions = {                                                   
                    product_parent: '<?php echo esc_html($product_parent);?>',
                    usdz_thumb: '<?php echo esc_url( plugins_url( "../assets/images/ar_model_icon_tick.jpg", __FILE__ ) );?>',
                    glb_thumb: '<?php echo esc_url( plugins_url( "../assets/images/ar_model_icon_tick.jpg", __FILE__ ) );?>',
                    site_url: '<?php echo esc_url(get_site_url());?>',
                    js_alert: '<?php echo esc_html(__('Invalid file type. Please choose a USDZ, REALITY, GLB or GLTF.', 'ar-for-wordpress' ));?>',
                    uploader_title: '<?php echo esc_html(__('Choose your AR Files', 'ar-for-wordpress' ));?>',
                    suffix: '<?php echo esc_html($suffix);?>',
                    ar_animation_selection: '<?php echo esc_html($ar_animation_selection);?>', 
                    public: '<?php echo esc_html($public);?>',
                    wc_model: '<?php echo esc_html($wc_model);?>',
                };
                
                var modelFields_<?php echo esc_html($model_id);?> = new ARModelFields(<?php echo esc_html($model_id);?>,modelFieldOptions);
                    
                
            </script>

        <?php   
        });
        
        
    }
}

/********** AR 3D Model Conversion **************/
if (!function_exists('ar_model_conversion')){
    function ar_model_conversion($model) {
        $link = 'https://augmentedrealityplugins.com/converters/glb_conversion.php';
        ob_start();
        $response = wp_remote_get( $link.'?model_url='.rawurlencode($model));
        if ( !is_wp_error($response) && isset( $response[ 'body' ] ) ) {
            return $response['body'];
        }
        ob_flush();
    }
 }
 
 

if (!function_exists('ar_remove_asset')){
    function ar_remove_asset($dir) {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") ar_remove_asset($dir."/".$object); else wp_delete_file($dir."/".$object);
           }
         }
         reset($objects);
         WP_Filesystem_Direct::rmdir($dir);
       }
    }
}



