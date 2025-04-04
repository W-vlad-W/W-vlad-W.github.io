<?php

// Echo transaleted content
if (!function_exists('ar_output')){
    function ar_output($content, $ar_plugin_id, $e = null) {
        $translated_string = '';

        // Determine the translated string based on plugin ID
        //if ($ar_plugin_id == 'ar-for-wordpress') {
            /* translators: %s represents dynamic content to be inserted */
            $translated_string = __('Dynamic content: %s', 'ar-for-woocommerce');
        //} 
        $translated_string = str_replace('Dynamic content: ','',$translated_string);
        // If we just need to return the content
        if ($e === null) {
            return sprintf($translated_string, $content); // Insert dynamic content into the translation string
            //return esc_html($content,'ar-for-wordpress');
        } else {
            echo esc_html(sprintf($translated_string,$content)); // Echo the translated content
            //esc_html_e($content,'ar-for-wordpress');
        }
    }
}

/************* Save Variations Custom AR Fields *************/
$ar_save_variation_executed = false;

if (!function_exists('save_ar_variation')){
    function save_ar_variation( $variation_id, $i ) {
        
        $suffix = '_var_'.$variation_id;

        $post_id = $variation_id;      

        //echo $_POST['_glb_file'.$suffix].' - '.$i.'<br /><br />';
        if (!isset($_POST['arwc-editpost-nonce'])) {
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['arwc-editpost-nonce'])), 'ar-for-woocommerce')) {
            // Nonce is invalid, so do not save the data.
            return;
        } 


        $ar_post ='';
        if ( isset( $_POST['_usdz_file'.$suffix] ) ) {
            update_post_meta( $variation_id, '_usdz_file'.$suffix, sanitize_text_field( wp_unslash($_POST['_usdz_file'.$suffix]) ) );
        }
        //echo "here"; exit;
        if (( isset( $_POST['_glb_file'.$suffix] ) ) || ( isset( $_POST['_ar_asset_file'.$suffix] ) )):
            if ((isset($_POST['_ar_asset_file'.$suffix]) && $_POST['_ar_asset_file'.$suffix]!='' )AND(isset($_POST['_asset_texture_file_0']) && $_POST['_asset_texture_file_0'] !='')){
                //Asset Builder overrides the GLB field
                $path_parts = pathinfo(sanitize_text_field( wp_unslash($_POST['_ar_asset_file'.$suffix]) ));

                $path_parts['filename'] .= '_' . (isset($_POST['ar_asset_ratio']) ? sanitize_text_field(wp_unslash($_POST['ar_asset_ratio'])) : ''). '_' . (isset($_POST['ar_asset_orientation']) ? sanitize_text_field(wp_unslash($_POST['ar_asset_orientation'])) : '');
                $path_parts['basename'] = $path_parts['filename'] . '.zip';
            }else{
                $path_parts = pathinfo(sanitize_text_field( wp_unslash($_POST['_glb_file'.$suffix]) ));
            }
            /***ZIP***/
            /***if zip file, then extract it and put gltf into _glb_file***/
            $zip_gltf='';
            if (isset($path_parts['extension'])){
                if (strtolower($path_parts['extension'])=='zip'){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_path = $upload_dir['path'].'/ar_asset_'.$post_id.'/';
                    if ( isset($_POST['_ar_asset_file'.$suffix]) && $_POST['_ar_asset_file'.$suffix] !='' ){
                        
                        $src_file=$destination_path.'/temp.zip';
                    }else{
                        //$destination_path = $upload_dir['path'].'/'.$path_parts['filename'].'/';
                        $src_file=$upload_dir['path'].'/'.$path_parts['basename'];
                    }
                    //Delete old Asset folder
                    if (file_exists($destination_path)) {
                        ar_remove_asset($destination_path);
                    }
                    //Create new Asset folder
                    if (!wp_mkdir_p($destination_path, 0755, true)) {
                        die('Failed to create folders...');
                    }
                    
                    if (  $_POST['_ar_asset_file'.$suffix] !='' ){
                        // If the function it's not available, require it.
                        if ( ! function_exists( 'download_url' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                        }
                        
                        //copy zip from asset_builder to local site
                        $src_file = download_url( sanitize_text_field( wp_unslash($_POST['_ar_asset_file'.$suffix]) ) );
                        if(ar_check_zip_archive($src_file)){
                            $unzipfile = unzip_file( $src_file  , $destination_path);
                        }
                        wp_delete_file($src_file);
                    }else{
                        if(ar_check_zip_archive($src_file)){
                            $unzipfile = unzip_file( $src_file, $destination_path);
                        }
                    }
                    if ( $unzipfile ) {
                        //echo 'Successfully unzipped the file! '. sanitize_text_field( $_POST['_ar_asset_file']);       
                    } else {
                        esc_html_e('There was an error unzipping the file.', 'ar-for-woocommerce' );
                    }
                        
                    if ( $unzipfile ) {
                        $file= glob($destination_path . "/*.gltf");
                        foreach($file as $filew){
                            $path_parts2=pathinfo($filew);
                            if ( $_POST['_ar_asset_file'.$suffix] !='' ){
                                
                                if (( isset( $_POST['_ar_asset_file'.$suffix] ) )AND( isset( $_POST['_asset_texture_file_0'.$suffix] ) )){
                                    for($i=0;$i<10;$i++){
                                        if (isset($_POST['_asset_texture_file_'.$i.$suffix])){
                                            $asset_textures[$i]['newfile']=sanitize_text_field( wp_unslash($_POST['_asset_texture_file_'.$i.$suffix]));
                                            $asset_textures[$i]['filename']=isset($_POST['_asset_texture_id_'.$i.$suffix]) ? sanitize_text_field( wp_unslash($_POST['_asset_texture_id_'.$i.$suffix])) : '';
                                        }
                                    }
                                    $flip = isset($_POST['_asset_texture_flip']) ? sanitize_text_field(wp_unslash($_POST['_asset_texture_flip'])) : '';
                                    asset_builder_texture($upload_dir['path'].'/ar_asset_'.$post_id.'/',$path_parts2['basename'],$asset_textures,$flip);
                                }
                            }else{
                               // $_POST['_glb_file'] = $path_parts['dirname'].'/'.$path_parts['filename'].'/'.$path_parts2['basename'];
                            }
                            $_POST['_glb_file'.$suffix] = $upload_dir['url'].'/ar_asset_'.$post_id.'/'.$path_parts2['basename'];
                            $zip_gltf='1'; //If set to 1 then ignore the model conversion process below
                        }
                    } else {
                        esc_html_e('There was an error unzipping the file.', 'ar-for-woocommerce');
                               
                    }
                }
            }
            /***Hotspot saving***/
            if (isset($_POST['_ar_hotspots'.$suffix])){
                
                if ( count($_POST['_ar_hotspots'.$suffix]) ){
                    $hotspot_link = isset($_POST['_ar_hotspots'.$suffix]['link']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots'.$suffix]['link'])) : array();

                    $hotspot_annotation = isset($_POST['_ar_hotspots'.$suffix]['annotation']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots'.$suffix]['annotation'])) : array();

                    $hotspot_normal = isset($_POST['_ar_hotspots'.$suffix]['data-normal']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots'.$suffix]['data-normal'])) : array();

                    $hotspot_position = isset($_POST['_ar_hotspots'.$suffix]['data-position']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots'.$suffix]['data-position'])) : array();

                    //$sanitized_hotspot = sanitize_post_var_array('_ar_hotspots');
                    //print_r($sanitized_hotspot);
                    $sanitized_hotspot = array(
                                    'data-normal' => $hotspot_normal,
                                    'data-position' => $hotspot_position,
                                    'annotation' => $hotspot_annotation,
                                    'link' => $hotspot_link,
                                );
                    $_ar_hotspots = count($sanitized_hotspot) ? wp_json_encode($sanitized_hotspot) : '';

                    update_post_meta( $post_id, '_ar_hotspots', $sanitized_hotspot );                       
                    //die($_ar_hotspots);
                }
            }
            /***Model Conversion***/
            /***if model file for conversion then convert and put gltf into _glb_file***/
            $allowed_files=array('dxf', 'dae', '3ds','obj','pdf','ply','stl','zip');
            if (isset($path_parts['extension'])){
                if ((in_array(strtolower($path_parts['extension']),$allowed_files))AND($zip_gltf=='')){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_file = $upload_dir['path'].'/'.$path_parts['filename'].'.glb';;
     
                    ar_wp_custom_file_write($destination_file, ar_model_conversion(sanitize_text_field( wp_unslash($_POST['_glb_file'.$suffix]) )) ); 
            
                    $_POST['_glb_file'.$suffix]= $path_parts['dirname'].'/'.$path_parts['filename'].'.glb';
                }
            }
            
            update_post_meta( $variation_id, '_glb_file'.$suffix, sanitize_text_field( wp_unslash($_POST['_glb_file'.$suffix]) ) );
        endif;

        if ((isset( $_POST['_usdz_file'.$suffix] )) OR( isset($_POST['_glb_file'.$suffix]))){
            $ar_placement_sfx = isset($_POST['_ar_placement'.$suffix]) ? sanitize_text_field( wp_unslash($_POST['_ar_placement'.$suffix])) : '';
            update_post_meta( $variation_id, '_ar_placement'.$suffix, $ar_placement_sfx );
            update_post_meta( $variation_id, '_ar_display'.$suffix, '1' );
        }else{
            update_post_meta( $variation_id, '_ar_display'.$suffix, '' );
        }//update global option $ar_open_tabs
        
        update_option( 'ar_open_tabs', ( isset($_POST['ar_open_tabs']) ? sanitize_text_field(wp_unslash($_POST['ar_open_tabs'])) : ''));

        $field_array=array('_skybox_file','_ar_environment','_ar_poster','_ar_qr_image','_ar_qr_destination','_ar_qr_destination_mv','_ar_variants','_ar_rotate','_ar_prompt','_ar_x','_ar_y','_ar_z','_ar_field_of_view','_ar_zoom_out','_ar_zoom_in','_ar_exposure','_ar_camera_orbit','_ar_environment_image','_ar_shadow_intensity','_ar_shadow_softness','_ar_resizing','_ar_view_hide','_ar_qr_hide','_ar_hide_dimensions','_ar_hide_reset','_ar_animation','_ar_autoplay','_ar_animation_selection','_ar_emissive','_ar_light_color','_ar_disable_zoom','_ar_rotate_limit','_ar_compass_top_value','_ar_compass_bottom_value','_ar_compass_left_value','_ar_compass_right_value','_ar_cta','_ar_cta_url','_ar_css_override','_ar_css_positions','_ar_css','_ar_mobile_id','_ar_alternative_id','_ar_framed','_ar_frame_color','_ar_frame_opacity');

        foreach ($field_array as $k => $v){
            if ( isset( $_POST[$v.$suffix] ) ) {
                update_post_meta($variation_id, $v.$suffix, sanitize_text_field(wp_unslash($_POST[$v.$suffix])) );
            }else{
                update_post_meta($variation_id, $v.$suffix, '');
            }

        }

        update_post_meta($variation_id, '_ar_shortcode'.$suffix, '[ar-display id='.$post_id.']');
        
    }
 }

//end update


/************* Save Custom AR Fields *************/

if (!function_exists('save_ar_option_fields')){
    function save_ar_option_fields( $post_id ) {
        global $ar_plugin_id;
        
        if (!isset($_POST['arwc-editpost-nonce'])) {
            return;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['arwc-editpost-nonce'])), 'ar-for-woocommerce')) {
            // Nonce is invalid, so do not save the data.
            return;
        } 

        $arpost = get_post( $post_id ); 

        if($arpost->post_type == 'product'){

            $product = wc_get_product($post_id);            
            
            if(is_a($product, 'WC_Product') && $product->is_type('variable')){
                $variations = $product->get_children();



                if(count($variations)){

                    //print_r($variations);
                    //print_r($_POST);
                    //wp_die();

                    foreach($variations as $key=>$var_id){
                        $suffix = '_var_'.$var_id;
                        //echo $suffix.'<br />';
                        if(isset( $_POST['_usdz_file'.$suffix]) || isset( $_POST['_glb_file'.$suffix])){
                            
                            save_ar_variation($var_id, $key);
                        }
                    }

                    //wp_die();

                }
            }
        }

        $ar_save_variation_executed = true;

        $ar_post ='';
        if ( isset( $_POST['_usdz_file'] ) ) {
            update_post_meta( $post_id, '_usdz_file', sanitize_text_field(wp_unslash($_POST['_usdz_file']) ) );
        }
        
        
        if (( isset( $_POST['_glb_file'] ) ) || ( isset( $_POST['_ar_asset_file'] ) )):
            if ((isset($_POST['_ar_asset_file'.$suffix]) && $_POST['_ar_asset_file'.$suffix] !='' )AND(isset($_POST['_asset_texture_file_0']) && $_POST['_asset_texture_file_0'] !='')){
                //Add the ratio and orientation to the url.
                
                //Asset Builder overrides the GLB field
                $path_parts = pathinfo(sanitize_text_field(wp_unslash( $_POST['_ar_asset_file']) ));

                $path_parts['filename'] .= '_' . (isset($_POST['ar_asset_ratio']) ? sanitize_text_field(wp_unslash($_POST['ar_asset_ratio'])) : ''). '_' . (isset($_POST['ar_asset_orientation']) ? sanitize_text_field(wp_unslash($_POST['ar_asset_orientation'])) : '');
                $path_parts['basename'] = $path_parts['filename'] . '.zip';
                
        //print_r($path_parts);print_r($_POST);exit;
            }else{
                $path_parts = pathinfo(sanitize_text_field(wp_unslash( $_POST['_glb_file'] )));
            }
            
            /***ZIP***/
            /***if zip file, then extract it and put gltf into _glb_file***/
            $zip_gltf='';
            if (isset($path_parts['extension'])){
                if (strtolower($path_parts['extension'])=='zip'){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_path = $upload_dir['path'].'/ar_asset_'.$post_id.'/';
                    if ( $_POST['_ar_asset_file'] !='' ){
                        
                        $src_file=$destination_path.'/temp.zip';
                    }else{
                        //$destination_path = $upload_dir['path'].'/'.$path_parts['filename'].'/';
                        $src_file=$upload_dir['path'].'/'.$path_parts['basename'];
                    }
                    //Delete old Asset folder
                    if (file_exists($destination_path)) {
                        ar_remove_asset($destination_path);
                    }
                    //Create new Asset folder
                    if (!wp_mkdir_p($destination_path, 0755, true)) {
                        die('Failed to create folders...');
                    }
                    
                    if (  $_POST['_ar_asset_file'] !='' ){
                        // If the function it's not available, require it.
                        if ( ! function_exists( 'download_url' ) ) {
                            require_once ABSPATH . 'wp-admin/includes/file.php';
                        }
                        
                        //copy zip from asset_builder to local site
                        $src_file = download_url( sanitize_text_field( $path_parts['dirname'].'/'.$path_parts['basename'] ) );
                        $unzipfile = unzip_file( $src_file  , $destination_path);
                        wp_delete_file($src_file);
                    }else{
                        $unzipfile = unzip_file( $src_file, $destination_path);
                    }
                    if ( $unzipfile ) {
                        //echo 'Successfully unzipped the file! '. sanitize_text_field( $_POST['_ar_asset_file']);       
                    } else {
                        esc_html_e('There was an error unzipping the file.', 'ar-for-woocommerce' );
                    }
                        
                
                    if ( $unzipfile ) {
                        $file= glob($destination_path . "/*.gltf");
                        //echo $destination_path.'<br>';
                        foreach($file as $filew){
                            $path_parts2=pathinfo($filew);
                            if ( $_POST['_ar_asset_file'] !='' ){
                                //print_r($_POST);exit;
                                if (( isset( $_POST['_ar_asset_file'] ) )AND( isset( $_POST['_asset_texture_file_0'] ) )){
                                    for($i=0;$i<10;$i++){
                                        if (isset($_POST['_asset_texture_file_'.$i])){
                                            $asset_textures[$i]['newfile']=isset($_POST['_asset_texture_file_'.$i]) ? sanitize_text_field(wp_unslash($_POST['_asset_texture_file_'.$i])) : '';
                                            $asset_textures[$i]['filename']=isset($_POST['_asset_texture_id_'.$i]) ? sanitize_text_field(wp_unslash($_POST['_asset_texture_id_'.$i])) : '';
                                        }
                                    }
                                    $flip = isset($_POST['_asset_texture_flip']) ? sanitize_text_field(wp_unslash($_POST['_asset_texture_flip'])) : '';
                                    asset_builder_texture($upload_dir['path'].'/ar_asset_'.$post_id.'/',$path_parts2['basename'],$asset_textures,$flip);
                                    
                                }
                            }else{
                               // $_POST['_glb_file'] = $path_parts['dirname'].'/'.$path_parts['filename'].'/'.$path_parts2['basename'];
                            }
                            $_POST['_glb_file'] = $upload_dir['url'].'/ar_asset_'.$post_id.'/'.$path_parts2['basename'];
                            $zip_gltf='1'; //If set to 1 then ignore the model conversion process below
                            //echo  $_POST['_glb_file'].'<br>';
                        }
                        
                    } else {
                        esc_html_e('There was an error unzipping the file.', 'ar-for-woocommerce');
                               
                    }
                }
            }
            /***Hotspot saving***/
            if (isset($_POST['_ar_hotspots'])){
                if ( count($_POST['_ar_hotspots']) ){
                    $hotspot_link = isset($_POST['_ar_hotspots']['link']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots']['link'])) : array();

                    $hotspot_annotation = isset($_POST['_ar_hotspots']['annotation']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots']['annotation'])) : array();

                    $hotspot_normal = isset($_POST['_ar_hotspots']['data-normal']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots']['data-normal'])) : array();

                    $hotspot_position = isset($_POST['_ar_hotspots']['data-position']) ? array_map('sanitize_text_field', wp_unslash($_POST['_ar_hotspots']['data-position'])) : array();

                    //$sanitized_hotspot = sanitize_post_var_array('_ar_hotspots');
                    //print_r($sanitized_hotspot);
                    $sanitized_hotspot = array(
                                    'data-normal' => $hotspot_normal,
                                    'data-position' => $hotspot_position,
                                    'annotation' => $hotspot_annotation,
                                    'link' => $hotspot_link,
                                );
                    $_ar_hotspots = count($sanitized_hotspot) ? wp_json_encode($sanitized_hotspot) : '';

                    update_post_meta( $post_id, '_ar_hotspots', $sanitized_hotspot );                       
                    //die($_ar_hotspots);
                }
            }
            /***Model Conversion***/
            /***if model file for conversion then convert and put gltf into _glb_file***/
            $allowed_files=array('dxf', 'dae', '3ds','obj','pdf','ply','stl','zip');
            if (isset($path_parts['extension'])){
                if ((in_array(strtolower($path_parts['extension']),$allowed_files))AND($zip_gltf=='')){
                    WP_Filesystem();
                    $upload_dir = wp_upload_dir();
                    $destination_file = $upload_dir['path'].'/'.$path_parts['filename'].'.glb';;

                    ar_wp_custom_file_write($destination_file, ar_model_conversion(sanitize_text_field(wp_unslash( $_POST['_glb_file']) )) ); 
                    
                    $_POST['_glb_file']= $path_parts['dirname'].'/'.$path_parts['filename'].'.glb';
                }
            }
            
            update_post_meta( $post_id, '_glb_file', sanitize_text_field(wp_unslash( $_POST['_glb_file']) ) );
        endif;
        if ((isset( $_POST['_usdz_file'] )) OR( isset($_POST['_glb_file']))){
            update_post_meta( $post_id, '_ar_placement', ( isset($_POST['_ar_placement']) ? sanitize_text_field(wp_unslash($_POST['_ar_placement'])) : '') );
            update_post_meta( $post_id, '_ar_display', '1' );
        }else{
            update_post_meta( $post_id, '_ar_display', '' );
        }
        if (isset($_POST['ar_open_tabs'])){
            update_option( 'ar_open_tabs', ( isset($_POST['ar_open_tabs']) ? sanitize_text_field(wp_unslash($_POST['ar_open_tabs'])) : ''));
        }
        $field_array=array('_skybox_file','_ar_environment','_ar_poster','_ar_qr_image','_ar_qr_destination','_ar_qr_destination_mv','_ar_variants','_ar_user_upload','_ar_rotate','_ar_prompt','_ar_x','_ar_y','_ar_z','_ar_field_of_view','_ar_zoom_out','_ar_zoom_in','_ar_exposure','_ar_camera_orbit','_ar_environment_image','_ar_shadow_intensity','_ar_shadow_softness','_ar_resizing','_ar_view_hide','_ar_qr_hide','_ar_hide_dimensions','_ar_hide_reset','_ar_animation','_ar_autoplay','_ar_animation_selection','_ar_emissive','_ar_light_color','_ar_disable_zoom','_ar_rotate_limit','_ar_compass_top_value','_ar_compass_bottom_value','_ar_compass_left_value','_ar_compass_right_value','_ar_cta','_ar_cta_url','_ar_css_override','_ar_css_positions','_ar_css','_ar_mobile_id','_ar_alternative_id','_ar_framed','_ar_frame_color','_ar_frame_opacity');
        foreach ($field_array as $k => $v){
            if ( isset( $_POST[$v] ) ) {
                //_ar_css_positions is an array so cannot be escaped
                update_post_meta( $post_id, $v, wp_unslash($_POST[$v]));
            }else{
                update_post_meta( $post_id, $v, '');
            }
        }
        update_post_meta( $post_id, '_ar_shortcode', '[ar-display id='.$post_id.']');
  
    }
 }


/************* AR User Upload Shortcode functions *************/


$ar_user_button_location = get_option('ar_user_button');
if ($ar_user_button_location ===''){$ar_user_button_location = 'woocommerce_before_add_to_cart_button';}

add_action($ar_user_button_location, 'ar_user_upload_wc');
function ar_user_upload_wc(){
    if (function_exists('is_product') && is_product()) { // Check if it's a WooCommerce single product page
        global $post; // Access the global post object to get the current product
        if ($post) { // Ensure the post object exists
            $product_id = $post->ID; // Get the current product ID
            $ar_user_upload = get_post_meta($product_id, '_ar_user_upload', true);  // Get the post meta value for '_ar_user_upload'
            if ($ar_user_upload =='1') {
                echo wp_kses(ar_user_upload_wp('input'), ar_allowed_html());
            }
        }
    }
}

$ar_user_modelviewer_location = get_option('ar_user_modelviewer');
if ($ar_user_modelviewer_location ===''){$ar_user_modelviewer_location = 'woocommerce_before_add_to_cart_button';}

add_action($ar_user_modelviewer_location, 'ar_user_upload_mv_wc');
function ar_user_upload_mv_wc(){
    if (function_exists('is_product') && is_product()) { // Check if it's a WooCommerce single product page
        global $post; // Access the global post object to get the current product
        if ($post) { // Ensure the post object exists
            $product_id = $post->ID; // Get the current product ID
            $ar_user_upload = get_post_meta($product_id, '_ar_user_upload', true);  // Get the post meta value for '_ar_user_upload'
            if ($ar_user_upload =='1') {
                echo wp_kses(ar_user_upload_wp('modelviewer'), ar_allowed_html());
            }
        }
    }
}

add_action('wp_enqueue_scripts', 'enqueue_custom_wc_script');

function enqueue_custom_wc_script() {
    if (function_exists('is_product') && is_product()) {
        wp_enqueue_script('wc-add-to-cart'); // Ensure WooCommerce Add to Cart script is loaded
        wp_enqueue_script('jquery'); // Ensure jQuery is loaded
        
        // Force WooCommerce's parameters to be printed
        wp_localize_script('wc-add-to-cart', 'wc_add_to_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint("%%endpoint%%")
        ));
    }
}


add_filter('woocommerce_add_to_cart_validation', 'handle_model_user_upload', 10, 3);

function handle_model_user_upload($passed, $product_id, $quantity) {
    if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
            // If the nonce is invalid, stop the process
          //  wp_die( __( 'Security check failed.', 'ar-for-wordpress' ) );
    }

    if(!isset($_FILES['ar_upload_model_file'])) { return true; }

    if (isset($_FILES['ar_upload_model_file']) && !empty($_FILES['ar_upload_model_file']['name'])) {
        // Get WordPress upload directory path
        $upload_dir = wp_upload_dir();
        $custom_dir = $upload_dir['basedir'] . '/ar-for-woocommerce';
        $custom_url = $upload_dir['baseurl'] . '/ar-for-woocommerce';

        // Create the custom directory if it doesn't exist
        if (!file_exists($custom_dir)) {
            wp_mkdir_p($custom_dir);
        }

        // Override upload directory
        add_filter('upload_dir', function($dirs) use ($custom_dir, $custom_url) {
            $dirs['path'] = $custom_dir;
            $dirs['url'] = $custom_url;
            $dirs['basedir'] = $custom_dir;
            $dirs['baseurl'] = $custom_url;
            return $dirs;
        });

        // Handle the upload
        $upload = wp_handle_upload($_FILES['ar_upload_model_file'], array('test_form' => false));

        // Reset the upload directory to default
        remove_filter('upload_dir', '__return_empty_array');

        if (isset($upload['file'])) {
            // Save the file URL in session to retrieve it later
            WC()->session->set('uploaded_file_path', $upload['url']);
        } else {
            return $passed;
            wc_add_notice(__('File upload failed. Please try again.', 'ar-for-woocommerce'), 'error');
            return false;
        }
    } else {
        wc_add_notice(__('Please upload a file.', 'ar-for-woocommerce'), 'error');
        return false;
    }

    return $passed;
}

add_filter('woocommerce_add_cart_item_data', 'add_uploaded_file_to_cart_item', 10, 2);

function add_uploaded_file_to_cart_item($cart_item_data, $product_id) {
    if (WC()->session->__isset('uploaded_file_path')) {
        $cart_item_data['uploaded_file'] = WC()->session->get('uploaded_file_path');
        // Remove the session data after use
        WC()->session->__unset('uploaded_file_path');
    }
    return $cart_item_data;
}

add_filter('woocommerce_get_item_data', 'display_uploaded_file_in_cart', 10, 2);

function display_uploaded_file_in_cart($item_data, $cart_item) {
    if (isset($cart_item['uploaded_file'])) {
    // Get the filename from the URL
    $file_url = esc_url($cart_item['uploaded_file']);
    $file_name = basename(parse_url($file_url, PHP_URL_PATH)); // Extracts the filename from the URL

    $item_data[] = array(
        'key'   => __('Uploaded File', 'ar-for-woocommerce'),
        'value' => '<a href="' . $file_url . '" target="_blank" rel="noopener noreferrer">' . esc_html($file_name) . '</a>',
    );
    }
    return $item_data;
}

add_action('woocommerce_checkout_create_order_line_item', 'save_uploaded_file_to_order_item', 10, 4);

function save_uploaded_file_to_order_item($item, $cart_item_key, $values, $order) {
    if (isset($values['uploaded_file'])) {
        $item->add_meta_data(__('Uploaded File', 'ar-for-woocommerce'), $values['uploaded_file']);
    }
}

//********* Copy Woocommerce Template File ********//
add_action( 'wp_ajax_ar_copy_file_action', 'ar_copy_file' );
add_action( 'wp_ajax_nopriv_ar_copy_file_action', 'ar_copy_file' );

if (!function_exists('ar_copy_file')){
    function ar_copy_file() {
      $file_to_copy = plugin_dir_path( __FILE__ ) . 'templates/woocommerce/single-product/product-image.php';
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
            // If the nonce is invalid, stop the process
          //  wp_die( __( 'Security check failed.', 'ar-for-woocommerce' ) );
        }
      // Define the file to copy
      if (isset($_POST['gallery'])) {
            //$gallery = sanitize_text_field($_POST['gallery']);
            $file_to_copy = plugin_dir_path( __FILE__ ) . 'templates/woocommerce/single-product/gallery-product-image.php';
      }
    
      // Define the destination path
      $destination_path = get_stylesheet_directory() . '/woocommerce/single-product/product-image.php';
    
    // Create the destination directory if it doesn't exist
      $destination_directory = dirname( $destination_path );
      if ( ! file_exists( $destination_directory ) ) {
        wp_mkdir_p( $destination_directory, 0755, true );
      }
      
      // Copy the file
      if ( ! file_exists( $destination_path ) ) {
        if ( copy( $file_to_copy, $destination_path ) ) {
          echo esc_html(__('Copied', 'ar-for-woocommerce'));
        } else {
          echo esc_html(__('File copying failed', 'ar-for-woocommerce'));
        }
      } else {
        echo esc_html(__('File already exists in your theme', 'ar-for-woocommerce'));
      }
    
      wp_die();
    }
}
//********* Delete Woocommerce Template File ********//
if (!function_exists('check_and_delete_woocommerce_template')){
    function check_and_delete_woocommerce_template() {
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
            // If the nonce is invalid, stop the process
          //  wp_die( __( 'Security check failed.', 'ar-for-woocommerce' ) );
        }
        global $ar_plugin_id, $_POST;
        // Get the path to the template file
        $template_file = get_stylesheet_directory() . '/woocommerce/single-product/product-image.php';
        $ar_delete_template_css ="display:block";
        // If the file exists, display a button to delete the file
        if (isset($_POST['delete_template_file'])) {
            
            // If the delete button was clicked, delete the file
            if (wp_delete_file($template_file)) {
                echo '<div class="notice notice-success"><p>File deleted successfully.</p></div>';
                $ar_delete_template_css ="display:none";
            } else {
                echo '<div class="notice notice-error"><p>Failed to delete the file.</p></div>';
            }
        }
        echo '<div id="ar_delete_template" style="'.esc_html($ar_delete_template_css).'">';
        // Display the delete button
        echo '<form method="post">';
        echo '<input type="hidden" name="delete_template_file" value="1">';
        echo '<button type="submit" class="button button-danger" onclick="return confirm(\''.esc_html(__('Are you sure you want to delete the woocommerce single product template file from your theme?', 'ar-for-woocommerce' )).'\');">Delete File</button>';
        echo '</form>';
        echo '</div>';
    }
}

if(!function_exists('allow_custom_attributes')){
    function allow_custom_attributes($allowed_tags) {
        if (isset($allowed_tags['div'])) {
            $allowed_tags['div']['style'] = true;
            $allowed_tags['div']['data-*'] = true;
        }
        return $allowed_tags;
    }
    add_filter('wp_kses_allowed_html', 'allow_custom_attributes', 10, 1);
}

 ?>