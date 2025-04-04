<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/************* AR User Upload output *******************/
if (!function_exists('ar_user_upload_wp')){
    function ar_user_upload_wp($atts) {
        global $ar_plugin_id, $post, $ar_plugin_root_url;
        add_action('wp_enqueue_scripts', 'ar_advance_register_style');
        ar_advance_register_script();
        ar_advance_register_style();
        $output_html ='';
        $output_atts ='';
        $input_field = '';
        $frame_type = '';
        $ar_frame_color = '';
        $ar_frame_opacity = '';
        $ar_user_upload_button_hidden = '';
        $ar_gallery_builder_message = '';
        if (isset($atts['message'])){
            $ar_gallery_builder_message = $atts['message'];
        }
        if (isset($atts['frame'])){
            $frame_type = $atts['frame'];
        }
        if (isset($atts['color'])){
            $ar_frame_color = $atts['color'];
        }
        if (isset($atts['opacity'])){
            $ar_frame_opacity = $atts['opacity'];
        }
        $input_field = '<input type="file" id="ar_upload_model_file" name="ar_upload_model_file" accept=".glb,.gltf,.jpg,.png" style="display: none;">';
        $ar_user_default = get_option('ar_user_default');
        $ar_user_default_image = get_option('ar_user_default_image');
        $ar_user_button_location = get_option('ar_user_button');
        $gallery_url = site_url('/wp-content/plugins/' . $ar_plugin_id . '/includes/ar-gallery.php?url=');
        $glb_url = $gallery_url.site_url('/wp-content/plugins/' . $ar_plugin_id . '/assets/images/drag-drop-upload.jpg&width=760&height=420&_wpnonce='.esc_html(wp_create_nonce( 'ar_secure_nonce' )));
        if (($ar_user_default=='Custom')AND($ar_user_default_image!='')){
            // Get the user default image URL with width and height
            $user_default_image_width = 760; // Default width if no image
            $user_default_image_height = 420; // Default height if no image
            if ($ar_user_default_image != '') {
                $upload_dir = wp_upload_dir(); // Get the WordPress upload directory
                // Check if $ar_user_default_image is a full URL and belongs to the current site
                $site_url = site_url(); // Get the current site URL
                
                $ar_user_default_image_path ='';
                // Ensure that $ar_user_default_image is a relative path, not a full URL
                if (strpos($ar_user_default_image, $upload_dir['baseurl']) !== false) {
                    // If $ar_user_default_image contains the full URL, remove the base URL
                    $ar_user_default_image_2 = str_replace($upload_dir['baseurl'], '', $ar_user_default_image);
                }elseif (strpos($ar_user_default_image, $site_url) !== false) {
                    // Strip the domain and make the path relative if it matches the site URL
                    $ar_user_default_image_2 = str_replace($site_url, '', $ar_user_default_image);
                    // Ensure there is no leading slash
                    $ar_user_default_image_2 = ltrim($ar_user_default_image, '/');
                }
                // Full path to the image file
                $ar_user_default_image_path = $upload_dir['basedir'] . '/' . $ar_user_default_image_2;
                if (file_exists($ar_user_default_image_path)) {
                    // Get the file extension
                    $file_extension = pathinfo($ar_user_default_image_path, PATHINFO_EXTENSION);
                    
                    // Convert the extension to lowercase for case-insensitive comparison
                    $file_extension = strtolower($file_extension);
                    
                    // Check if the file is one of the allowed types
                    if (in_array($file_extension, ['glb', 'gltf'])) {
                        $glb_url = $ar_user_default_image;
                    }elseif (in_array($file_extension, ['jpg', 'png'])) {
                        $ar_user_default_image_data = getimagesize($ar_user_default_image_path); // Get image size data
                        if ($ar_user_default_image_data) {
                            $user_default_image_width = $ar_user_default_image_data[0]; // Width of the user default image
                            $user_default_image_height = $ar_user_default_image_data[1]; // Height of the user default image
                        }
                        $glb_url = $gallery_url.$ar_user_default_image.'&width='.$user_default_image_width.'&height='.$user_default_image_height.'&_wpnonce='.esc_html(wp_create_nonce( 'ar_secure_nonce' ));
                    }else{
                        
                    }
                }
            }
            
        }elseif (($ar_user_default=='Featured Image')AND$featured_image_id = get_post_thumbnail_id(get_the_ID())){
                // Get the featured image ID and its URL with width and height
                $featured_image_id = get_post_thumbnail_id(get_the_ID()); // Get the featured image ID of the current post
                $featured_image = '';
                $featured_image_width = 760; // Default width if no image
                $featured_image_height = 420; // Default height if no image
                
                if ($featured_image_id) {
                    $featured_image_data = wp_get_attachment_image_src($featured_image_id, 'full'); // Retrieve the image data
                    if ($featured_image_data) {
                        $featured_image = $featured_image_data[0]; // URL of the featured image
                        $featured_image_width = $featured_image_data[1]; // Width of the featured image
                        $featured_image_height = $featured_image_data[2]; // Height of the featured image
                    }
                }

                $glb_url = $gallery_url.$featured_image.'&width='.$featured_image_width.'&height='.$featured_image_height.'&_wpnonce='.esc_html(wp_create_nonce( 'ar_secure_nonce' ));
        }else{
            $glb_url = $gallery_url.site_url('/wp-content/plugins/' . $ar_plugin_id . '/assets/images/drag-drop-upload.jpg&width=760&height=420&_wpnonce='.esc_html(wp_create_nonce( 'ar_secure_nonce' )));
        }
        
        // Display the file input and model viewer
        $button_label = wp_kses('Upload Model or Image', ar_allowed_html());
        $ar_upload_label = get_option('ar_user_upload_button');
        if ($ar_upload_label !=''){
            $button_label = get_option('ar_user_upload_button');
        }
        
        if ($ar_user_button_location=='Hidden'){
            $ar_user_upload_button_hidden =' style="display: none;"';
        }
            
        

        if ($atts == 'input'){
            return $input_field; 
        }

        $model_array['model_id'] = 'user_upload';

        /******** AR Model Gallery Builder ************/
        ob_start();
        ar_gallery_builder_form($model_array);
        $ar_options_output = ob_get_clean();
        //Assign framed, frame color and opacity to glb_url if they are set in shortcode
        $glb_url .= '&f='.$frame_type.'&fc='.str_replace('#','',$ar_frame_color).'&opacity='.$ar_frame_opacity;
        
        $settings_fields = array('ar_view_file', 'ar_scene_viewer', 'ar_qr_file', 'ar_qr_destination', 'ar_dimensions_units', 'ar_dimensions_label', 'ar_fullscreen_file', 'ar_dimensions_inches', 'ar_hide_dimensions', 'ar_hide_arview', 'ar_hide_qrcode', 'ar_hide_reset', 'ar_hide_fullscreen','ar_hide_gallery_sizes','ar_css','ar_css_positions','ar_camera_orbit', 'ar_x', 'ar_y', 'ar_z'.'ar_variants','ar_pop');
        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'ar_secure_nonce' ) ) {
            // If the nonce is invalid, stop the process
          //  wp_die( __( 'Security check failed.', 'ar-for-wordpress' ) );
        }
        foreach ($settings_fields as $k => $v){
            if (!isset($_POST[$v])){$_POST[$v]='';}
            $model_array[$v] = get_option($v);
        }

        $model_array['ar_camera_orbit_reset'] = '';
        if ($model_array['ar_camera_orbit']!=''){
            $model_array['ar_camera_orbit_reset'] = $model_array['ar_camera_orbit'];
            $model_array['ar_camera_orbit'] = 'camera-orbit="'.$model_array['ar_camera_orbit'].'"';                
        } 

        
        //$viewers = ($model_array['ar_scene_viewer'] == 1) ? 'scene-viewer webxr quick-look' : 'webxr scene-viewer quick-look';
        $viewers = 'webxr scene-viewer quick-look';
        $model_array['ar_hide_arview'] = ($model_array['ar_hide_arview'] != '') ? ' nodisplay' : '';
        $show_ar = ($model_array['ar_hide_arview'] != '') ? '' : ' ar ar-modes="'.$viewers.'" ';

        $output_html.='<button slot="ar-button" data-id="'.$model_array['model_id'].'" class="ar-button ar-button-default " id="ar-button_'.$model_array['model_id'].'"><img id="ar-img_'.$model_array['model_id'].'" src="'.esc_url( $ar_plugin_root_url. "assets/images/ar-view-btn.png" ).'" class="ar-button-img"></button>';

        $output_html .= '<div class="ar-reset-btn-container">
            <button id="ar-reset_'.$model_array['model_id'].'" class="ar-reset" onclick="document.getElementById(\'model_'.$model_array['model_id'].'\').setAttribute(\'camera-orbit\', \''.$model_array['ar_camera_orbit_reset'].'\');return getData()"><img src="'.esc_url( plugins_url( "assets/images/reset.png", dirname(__FILE__) ) ).'"></button>
            </div>';

        $output_html.='<input type="hidden" id="src_'.$model_array['model_id'].'" value="'. ar_get_secure_model_url($glb_url).'">';

        $output_html.= $atts['hotspots_html'];

        if ($model_array['ar_view_file'] == ''){
            $output_html .= '<button slot="ar-button" class="ar-button ar-button-default '.$model_array['ar_hide_arview'].'" id="ar-button_user_upload"><img id="ar-img_user_upload" src="'.esc_url($ar_plugin_root_url.("/assets/images/ar-view-btn.png")).'" class="ar-button-img"></button>';
        } else {
            $output_html .= '<button slot="ar-button" class="ar-button '.$model_array['ar_hide_arview'].'" id="ar-button_user_upload"><img id="ar-img_user_upload" src="'.esc_url($model_array['ar_view_file']).'" class="ar-button-img"></button>';
        }

        /*
        $output_html.='<div class="ar-popup-btn-container">';
        
        //Fullscreen option - if not disabled in settings
        $ar_hide_fullscreen='';
        if ((!isset($model_array['ar_hide_fullscreen']))OR($model_array['ar_hide_fullscreen']=='')){
            if ($model_array['ar_pop']=='pop'){
                                
                $output.='<button id="ar_close_'.$model_array['id'].'" class="ar_popup-btn '.$ar_show_close_on_devices.'" onclick="document.getElementById(\'ar_popup_'.$mdl_id.'\').style.display = \'none\';"><img src="'.esc_url( plugins_url( "assets/images/close.png", dirname(__FILE__) ) ).'" class="ar-fullscreen_btn-img"></button>';
            }else{
                if ($model_array['ar_fullscreen_file']!=''){
                    $ar_fullscreen_image = $model_array['ar_fullscreen_file'];
                }else{
                    $ar_fullscreen_image = esc_url( plugins_url( "assets/images/fullscreen.png", dirname(__FILE__) ) );
                }
                
                $output.='<button id="ar_pop_Btn_'.$model_array['model_id'].'" class="ar_popup-btn hide_on_devices" type="button"><img src="'.$ar_fullscreen_image.'" class="ar-fullscreen_btn-img"></button>';
            }
        }
        $output.='</div>';
        */

        load_model_viewer_js();

        $model_viewer = '
        <div class="ardisplay_viewer"><model-viewer id="model_user_upload" camera-controls '.$show_ar.' src="'.ar_get_secure_model_url($glb_url).'" alt="AR Display 3D model" class="ardisplay_viewer ar_model_user_upload" quick-look-browsers="safari chrome" '.$output_atts.' >'.$output_html.'</model-viewer></div>';
        $model_viewer .= '
        <input type="hidden" name="_ar_x" id="_ar_x" value="" />
        <input type="hidden" name="_ar_y" id="_ar_y" value="" />
        <script>
        jQuery(document).ready(function(){  
                var modelFieldOptions = {                                                   
                    glb_thumb: \''. esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ).'\',
                    site_url: \''. esc_url(get_site_url()).'\',
                    js_alert: \''. esc_html(__('Invalid file type. Please choose a USDZ, REALITY, GLB or GLTF.', 'ar-for-wordpress' )).'\',
                    uploader_title: \''. esc_html(__('Choose your AR Files', 'ar-for-wordpress' )).'\',
                    public: \'y\',
                };
                
                var modelFields_'.esc_html($model_array['model_id']).' = new ARModelFields(\''.esc_html($model_array['model_id']).'\',modelFieldOptions);

                var options = {                                                   
                    ar_x: \'\',
                    ar_y: \'\',
                    ar_z: \'\',
                    ar_pop: \''.esc_html($model_array['ar_pop']).'\',
                    ar_dimensions_units: \''.esc_html($model_array['ar_dimensions_units']).'\',
                    ar_hide_fullscreen: \'false\',
                    ar_model_list: \'1\',
                    ar_variants: \'\',
                    id: \''.esc_html($model_array['model_id']).'\',
                };
                
                var model_'.esc_html($model_array['model_id']).' = ARModelViewer(\''.esc_html($model_array['model_id']).'\',options);

            });
         
            jQuery(document).ready(function($) {
                // When the custom button with ID #ar_user_upload_button is clicked
                $("#asset_thumb_img").on("click", function() {                    
                    // Trigger the click event on the hidden file input
                    $("#ar_upload_model_file").click();
                });

                // Handle file input change
                $("#ar_upload_model_file").on("change", function(e) {
                    handleFileUpload(e.target.files[0]);
                });

                // Add drag-and-drop functionality to the model-viewer element
                $("#model_user_upload").on("dragover", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass("dragover");
                });

                $("#model_user_upload").on("dragleave", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass("dragover");
                });

                $("#model_user_upload").on("drop", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass("dragover");

                    var file = e.originalEvent.dataTransfer.files[0];
                    $("#ar_upload_model_file")[0].files = e.originalEvent.dataTransfer.files; // Update input file
                    handleFileUpload(file);
                });

                // Function to handle file upload
                function handleFileUpload(file) {
                    if (file) {
                        var fileType = file.type;
                        if (!fileType) {
                            var fileExtension = file.name.split(".").pop().toLowerCase();
                            if (fileExtension === "glb") {
                                fileType = "model/gltf-binary";
                            } else if (fileExtension === "gltf") {
                                fileType = "model/gltf+json";
                            } else if (fileExtension === "jpg" || fileExtension === "jpeg") {
                                fileType = "image/jpeg";
                            } else if (fileExtension === "png") {
                                fileType = "image/png";
                            }
                        }

                        if (fileType === "model/gltf-binary" || fileType === "model/gltf+json") {
                            var url = URL.createObjectURL(file);
                            $("#model_user_upload").attr("src", url);
                        } else if (fileType === "image/jpeg" || fileType === "image/png") {
                            // Handle image files and calculate dimensions
                            ar_upload_file_dimenions(file, function(width, height, file) {
                                var fileUrl = encodeURIComponent(URL.createObjectURL(file));
                                $("#_asset_texture_file_0").val(fileUrl);
                                // Initialize variables
                                var framedValue, frameColor, frameOpacity;
                                
                                // Check if #_ar_framed exists and set its selected value to framedValue
                                if ($("#_ar_framed").length) {
                                    framedValue = $("#_ar_framed").val();
                                }
                                
                                // Check if #_ar_frame_color exists and set its value to frameColor
                                if ($("#_ar_frame_color").length) {
                                    frameColor = $("#_ar_frame_color").val();
                                    // Check if frameColor is a valid hex color and remove the # if it exists
                                    if (/^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/.test(frameColor)) {
                                        frameColor = frameColor.replace(/^#/, \'\'); // Remove the # if it exists
                                    } else {
                                        console.log("frameColor is not a valid hex color");
                                        frameColor = null; // Set to null or handle the invalid value as needed
                                    }
                                }
                                
                                // Check if #_ar_frame_opacity exists and set its selected value to frameOpacity
                                if ($("#_ar_frame_opacity").length) {
                                    frameOpacity = $("#_ar_frame_opacity").val();
                                }
                                
                                var url = "' . site_url('/wp-content/plugins/' . $ar_plugin_id . '/includes/ar-gallery.php?url=') . '" + fileUrl + "&width=" + width + "&height=" + height + "&f=" + framedValue + "&fc=" + frameColor + "&opacity=" + frameOpacity + "&_wpnonce=" + "'.esc_html(wp_create_nonce( 'ar_secure_nonce' )).'";
                                $("#model_user_upload").attr("src", url);
                                $("#ar_asset_size_container").css("display", "block");
                                //jQuery("#ar_asset_builder_message").css("display", "none");
                                calculateImageRatio();
                            });
                        } else {
                            console.log("Unsupported file type.");
                        }
                    } else {
                        console.log("No file selected or file is empty.");
                    }
                }

                // Function to handle image upload and get dimensions
                function ar_upload_file_dimenions(file, callback) {
                    if (file && file.type.match(\'image.*\')) {
                        var reader = new FileReader();

                        reader.onload = function(event) {
                            var img = new Image();
                            img.src = event.target.result;

                            img.onload = function() {
                                var width = img.width;
                                var height = img.height;

                                callback(width, height, file);
                            };
                            
                            img.onerror = function() {
                                console.log(\'Failed to load the image for dimensions.\');
                            };
                        };

                        reader.onerror = function() {
                            console.log(\'Failed to read the file.\');
                        };

                        reader.readAsDataURL(file);
                    } else {
                        console.log(\'Please select a valid image file.\');
                    }
                }
            });
        </script>';

        //popup

        $popup_output ='
            <div id="ar_popup_'.$model_array['model_id'].'" class="ar_popup">
                <div class="ar_popup-content">
                    '.$model_viewer.'
                </div>
            </div>';

        /*$model_viewer = $model_viewer.$popup_output;

            add_action( 'wp_footer', function( $arg ) use ( $popup_output,$model_array ) {
                echo wp_kses($popup_output, ar_allowed_html());
                ?>
                <script>  
                    jQuery(document).ready(function(){       
                        var options = {                                                   
                            ar_x: <?php echo $model_array['ar_x'] ? esc_html($model_array['ar_x']) : "''";?>,
                            ar_y: <?php echo $model_array['ar_y'] ? esc_html($model_array['ar_y']) : "''";?>,
                            ar_z: <?php echo $model_array['ar_z'] ? esc_html($model_array['ar_z']) : "''";?>,
                            ar_pop: '<?php echo esc_html($model_array['ar_pop']);?>',
                            ar_dimensions_units: '<?php echo esc_html($model_array['ar_dimensions_units']);?>',
                            ar_hide_fullscreen: 'false',
                            ar_model_list: <?php echo count($model_array['ar_model_list']);?>,
                            ar_variants: '<?php echo esc_html($model_array['ar_variants']); ?>',
                            id: <?php echo esc_html($model_array['ar_model_atts']['id']);?>,
                        };
                        
                        var model_<?php echo esc_html($model_array['model_id']);?> = ARModelViewer('<?php echo esc_html($model_array['model_id']);?>',options);
                    });

                </script>
                <?php
            } );
            
        */

        if ($atts == 'modelviewer'){
            return $model_viewer;
        }
        if (isset($atts['options'])){
            if ($atts['options'] == 'top'){
                return $input_field . $ar_options_output . $model_viewer;
            }elseif ($atts['options'] == 'bottom'){
                return $input_field . $model_viewer . $ar_options_output;
            }elseif ($atts['options'] == 'none'){
                return $input_field . $model_viewer;
            }
        }else{
            return $input_field . $ar_options_output . $model_viewer;
        }
    }
}

