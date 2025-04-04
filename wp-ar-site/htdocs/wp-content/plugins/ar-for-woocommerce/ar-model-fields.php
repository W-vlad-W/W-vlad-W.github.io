<?php
/**
 * AR Display
 * AR for Woocommerce
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH'))
    exit;


add_action('admin_enqueue_scripts', 'ar_advance_register_script');
add_action('admin_enqueue_scripts', 'ar_advance_register_style');
add_action('admin_enqueue_scripts', 'ar_wc_advance_register_script');

/* Adding a custom AR Display Product Data tab*/

function ar_woo_tab( $tabs ) {
  $tabs['ardisplay'] = array(
    'label'  => __( 'AR Models', 'ar-for-woocommerce' ),
    'target' => 'ardisplay_panel',
    'class'  => array(),
  );
  return $tabs;
}
add_filter( 'woocommerce_product_data_tabs', 'ar_woo_tab' );

function ar_woo_tab_panel($prod_id, $variation_id='') {
    global $post, $wpdb, $shortcode_examples, $ar_whitelabel, $wp, $ar_wcfm, $ar_css_styles, $ar_css_names, $js_displayed;
    if ((isset($prod_id)) AND($prod_id!='')){
        //$post = wc_get_product( $variation->$prod_id );
        //echo 'its set';
    }elseif(isset($wp->query_vars['wcfm-products-manage'])){
        $post = get_post($wp->query_vars['wcfm-products-manage']);
        $prod_id = $post->ID;
    }else{
        $prod_id = $post->ID;
    }
    
    $suffix = $variation_id ? '_var_'.$variation_id : '';
    $class = $variation_id ? '' : 'panel woocommerce_options_panel';
    
    //Model Count
    $model_count = ar_model_count();
    $model_array=array();
    $model_array['id'] = $prod_id;

  echo '
  <div id="ardisplay_panel'.esc_html($suffix).'" class="'.esc_html($class).' armodel_fields_panel" style="padding:10px !important">
    <div class="options_group">';

        ar_model_fields($prod_id, $model_array, $variation_id);
          
          echo '
    </div>
    </div>';

    /*
    <script language="javascript">
        jQuery(document).ready(function($){
            modelFields[<?php echo esc_html($prod_id);?>] = new ARModel('<?php echo esc_html($prod_id);?>','<?php echo esc_html($variation_id);?>');
        });
    </script>
    */


    if(!$variation_id){
        
        $wc_model = 0;
        $model_id = $model_array['id'];
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
            $post   = get_post( $arr['id'] );
            $public = 'y';
        }
        ?>

        
        <script>
            
            jQuery(document).ready(function(){  
                var modelFieldOptions = {                                                   
                    product_parent: '<?php echo esc_html($product_parent);?>',
                    usdz_thumb: '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) );?>',
                    glb_thumb: '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) );?>',
                    site_url: '<?php echo esc_url(get_site_url());?>',
                    js_alert: '<?php echo esc_html(__('Invalid file type. Please choose a USDZ, REALITY, GLB or GLTF.', 'ar-for-woocommerce' ));?>',
                    uploader_title: '<?php echo esc_html(__('Choose your AR Files', 'ar-for-woocommerce' ));?>',
                    suffix: '<?php echo esc_html($suffix);?>',
                    ar_animation_selection: '<?php echo esc_html($ar_animation_selection);?>', 
                    public: '<?php echo esc_html($public);?>',
                    wc_model: '<?php echo esc_html($wc_model);?>',
                };
                
                var modelFields_<?php echo esc_html($model_array['id']);?> = new ARModelFields(<?php echo esc_html($model_array['id']);?>,modelFieldOptions);
            });
            
        </script>

    <?php

    }
    ?>
    <script>
        jQuery(document).ready(function($){
            modelFields[<?php echo esc_html($model_array['id']); ?>] = new ARModelWoo('<?php echo esc_html($prod_id);?>','<?php echo esc_html($variation_id);?>');

            jQuery('#_ar_light_color<?php echo esc_html($suffix);?>').wpColorPicker({
                palettes: ['#ff0000', '#00ff00', '#0000ff', '#ffffff', '#000000', '#cccccc'],
                change: function(event, ui) {                    
                    // Handle color change event (optional)
                    console.log('Selected color:', ui.color.toString());
                    modelFields[<?php echo esc_html($model_array['id']); ?>].modelobj[0].setAttribute("light-color", ui.color.toString());
                }
            });
        });
    </script>
    <?php
    
    if (!isset($js_displayed)){
        ar_model_js($model_array, $variation_id);
        $js_displayed=1;
    }
}

add_action( 'woocommerce_product_data_panels', 'ar_woo_tab_panel' );
/**
 * Add a bit of style.
 */
function ar_woo_custom_style() {
    echo '
    <style>
        #woocommerce-product-data .ardisplay_options.active:hover > a:before,
        #woocommerce-product-data .ardisplay_options > a:before,
        .ardisplay_options.active:hover > a:before,
        .ardisplay_options > a:before {
            background: url( \''.esc_url( plugins_url( "assets/images/chair.png", __FILE__ ) ).'\' ) center center no-repeat;
            content: " " !important;
            background-size: 100%;
            width: 13px;
            height: 13px;
            display: inline-block;
            line-height: 1;
        }
        @media only screen and (max-width: 900px) {
            #woocommerce-product-data .ardisplay_options.active:hover > a:before,
            #woocommerce-product-data .ardisplay_options > a:before,
            #woocommerce-product-data .ardisplay_options:hover a:before {
                background-size: 35%;
            }
        }
        .ardisplay_options:hover a:before {
            background: url( \''.esc_url( plugins_url( "assets/images/chair.png", __FILE__ ) ).'\' ) center center no-repeat;
        }

    </style>';
}
add_action( 'admin_head', 'ar_woo_custom_style' );

//Save Woocommerce product custom fields
//add_action( 'woocommerce_process_product_meta_simple', 'save_ar_option_fields'  );
//add_action( 'woocommerce_process_product_meta_variable', 'save_ar_option_fields'  );
add_action( 'woocommerce_new_product', 'save_ar_option_fields', 10, 1  );
add_action( 'woocommerce_update_product', 'save_ar_option_fields', 10, 1  );
add_action( 'woocommerce_save_product_variation', 'save_ar_variation', 10, 2 );



add_action('plugins_loaded', function(){
  if($GLOBALS['pagenow']=='post.php'){
    add_action('admin_print_scripts', 'ar_woo_admin_scripts');
  }
});

function ar_model_fields($prod_id, $model_array, $variation_id=''){
    global $post, $wpdb, $shortcode_examples, $shortcode_examples_wc, $ar_whitelabel, $wp, $ar_wcfm, $ar_css_styles, $ar_css_names, $ar_css_import_global, $hotspot_count, $jsArray;
    $plan_check = get_option('ar_licence_plan');
                            

    wp_enqueue_script('ar_model_fields', plugins_url('assets/js/ar-model-fields.js', __FILE__), array('jquery','wp-color-picker'), '1.3', false);



    $suffix = $variation_id ? "_var_".$variation_id : '';
    $button_atts = $variation_id ? 'data-variation="'.$variation_id.'"' : '';
    ?>

    <?php wp_nonce_field( 'ar-for-woocommerce', 'arwc-editpost-nonce' ); ?>
       
                        <div id="ar_shortcode_instructions">
                    <div style="width:100%;height:80px;background-color:#12383d">
                        <div class="ar_admin_view_title">
                         <img src="<?php echo esc_url( plugins_url( "assets/images/ar-for-woocommerce-box.jpg", __FILE__ ) );?>" style="padding: 10px 30px 10px 10px; height:60px" align="left">
                                <h1 style="color:#ffffff; padding-top:20px;font-size:20px"><?php esc_html_e('AR for Woocommerce','ar-for-woocommerce'); ?></h1>
                            </div>
                            <?php
                        if ((substr(get_option('ar_licence_valid'),0,5)!='Valid')AND($model_count>=2)){?>
                        
                        </div>
                            <p><b><a href="edit.php?post_type=armodels&page"><?php esc_html_e( 'Please check your subscription & license key.</a> If you are using the free version of the plugin then you have exceeded the limit of allowed models.', 'ar-for-woocommerce' );?></a></b></p>
                    </div><?php
                    return 1;
                    }else{
                        $model_array=array();
                        $model_array['id'] = $prod_id;
                ?>
                        <div  class="ar_admin_view_post">
                            <?php if (get_post_meta( $model_array['id'], '_glb_file', true )!=''){
                               // echo '<div class="ar_admin_view_post">'.sprintf( __('<a href="%s" target="_blank"><button type="button" class="button ar_admin_button" style="margin-right:20px">'.__('View Model Post','ar-for-woocommerce').'</button></a>'), esc_url( get_permalink($model_array['id']) ) ).'</div>';
                                echo '<a href="'.esc_url( get_permalink($model_array['id']) ).'" target="_blank"><button type="button" class="button ar_admin_button" style="margin-right:20px">';
                                echo esc_html(__('View Product','ar-for-woocommerce'));
                                echo '</button></a>';
                            }
                            ?>
                        </div>
                        <div  class="ar_admin_view_shortcode" onclick="copyToClipboard('ar_shortcode<?php echo esc_html($suffix);?>');document.getElementById('copied').innerHTML='-&nbsp;Copied!';" style="cursor: pointer;">
                            <span class="dashicons dashicons-admin-page" style="color:#fff;float:left;padding-left:20px;"></span><div style="float: left;"><b>Shortcode</b> <span id="copied" class="ar_label_tip"></span></div>
                                <a heref="#" style="float:left;">
                                <input id="ar_shortcode<?php echo esc_html($suffix);?>" type="text" class="button ar_admin_button" value="[ar-display id=<?php echo esc_html($model_array['id']);?>]" readonly style="width:164px;background: none !important; border: none !important;color:#f37a23 !important;font-size: 16px;float:left;">
                                </a>
                                
                           </div>
                        
                    </div>
                            
                </div>
                <div style="clear:both"></div>
                <!-- Tab links -->
                <?php
                $redirect_url = admin_url('admin.php?page=ar-modelshop');
                $encoded_redirect_url = urlencode($redirect_url);
                ?>
                <div class="ar_tab">
                  <button class="ar_tablinks ar_tablinks<?php echo esc_html($suffix)?>" onclick="ar_open_tab(event, 'model_files_content<?php echo esc_html($suffix)?>', 'model_files_tab<?php echo esc_html($suffix)?>')" id="model_files_tab<?php echo esc_html($suffix)?>" type="button"><?php esc_html_e( 'Model Files', 'ar-for-woocommerce' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                  <?php if (!$variation_id){?>
                        <button class="ar_tablinks ar_tablinks<?php echo esc_html($suffix)?>" onclick="ar_open_tab(event, 'asset_builder_content<?php echo esc_html($suffix)?>', 'asset_builder_tab<?php echo esc_html($suffix)?>')" id="asset_builder_tab<?php echo esc_html($suffix)?>" type="button"><?php esc_html_e( '3D Gallery Builder', 'ar-for-woocommerce' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                        <button class="ar_tablinks" onclick="ar_open_tab(event, 'user_upload_content', 'user_upload_tab')" id="user_upload_tab" type="button"><?php esc_html_e( 'User Upload', 'ar-for-woocommerce' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                  <?php } ?>
                  <button class="ar_tablinks ar_tablinks<?php echo esc_html($suffix)?>" onclick="ar_open_tab(event, 'instructions_content<?php echo esc_html($suffix)?>','instructions_tab<?php echo esc_html($suffix)?>')" id="instructions_tab<?php echo esc_html($suffix)?>" type="button"><?php esc_html_e( 'Shortcodes', 'ar-for-woocommerce' );?><span style=" vertical-align: super;font-size: smaller;"> </span></button>
                  <a href="https://armodelshop.com?from_plugin=true&redirect_url=<?php echo esc_url($encoded_redirect_url); ?>" target="_blank"><button class="ar_tablinks" id="support_tab" type="button"> <?php esc_html_e( 'AR Model Shop', 'ar-for-woocommerce' );?><span style=" vertical-align: super;font-size: smaller;">&#8599;</span></button></a>
                  <a href="https://augmentedrealityplugins.com/support/" target="_blank"><button class="ar_tablinks ar_tablinks<?php echo esc_html($suffix)?>" id="support_tab<?php echo esc_html($suffix)?>" type="button"><?php esc_html_e( 'Support', 'ar-for-woocommerce' );?><span style=" vertical-align: super;font-size: smaller;">&#8599;</span></button></a>
                </div>
                
            <div id="model_files_content<?php echo esc_html($suffix)?>" class="ar_tabcontent<?php echo esc_html($suffix)?>" style="display:block;">
                <a href="#" class="wctoggle-model-fields" data-status='hidden'>Show Model Fields</a>
                <br><br>
                <div>
                    <?php if (!$variation_id){?>
                        <div class="ar_model_files_advert hide_on_devices">
                            <center>
                                <img src="<?php echo esc_url( plugins_url( "ar-for-woocommerce/assets/images/ar_asset_ad_icon.jpg", dirname(__FILE__) ) ); ?>" style="height:60px">
                                <h4><?php esc_html_e('Hang your artwork in AR with just a photo!', 'ar-for-woocommerce' );?></h4>
                                <button type="button" id="asset_builder_button" onclick="ar_open_tab(event, 'asset_builder_content', 'asset_builder_tab');/*ar_activeclass('asset_builder_tab');*/" class="button ar_admin_button" style="margin-right:20px"><?php esc_html_e('3D Gallery Builder', 'ar-for-woocommerce' );?></button>
                                <!---<p><a href="https://wordpress.org/support/plugin/ar-for-woocommerce/reviews/#new-post" target="_blank">Rate this plugin!</a> <a href="https://wordpress.org/support/plugin/ar-for-woocommerce/reviews/#new-post" target="_blank"><img src="<?php echo esc_url( plugins_url( "assets/images/5-stars.png", dirname(__FILE__) ) );?>" style="width: 45px;vertical-align: middle;"></a></p>-->
                            </center>
                        </div>
                        <div class="ar_model_shop_advert hide_on_devices">
                            <center>
                                <a href = "https://armodelshop.com?from_plugin=true&redirect_url=<?php echo esc_url($encoded_redirect_url); ?>" target="_blank"><img src = "<?php echo esc_url( plugins_url( "ar-for-woocommerce/assets/images/ar-model-shop-icon.png", dirname(__FILE__) ) ); ?>" style="width:60px"></a>
                                <h4><?php esc_html_e('Purchase an ','ar-for-woocommerce');?><b><?php esc_html_e('AR Model','ar-for-woocommerce');?></b><?php esc_html_e(' for your site.','ar-for-woocommerce'); ?></h4>
                                <button 
                                    id="open-ar-modelshop" 
                                    class="button ar_admin_button" 
                                    onclick="event.preventDefault(); window.open('https://armodelshop.com?from_plugin=true&redirect_url=<?php echo esc_url($encoded_redirect_url); ?>', '_blank')">
                                    Open AR Model Shop <sup>&#x2197;</sup>
                                </button>
                            </center>
                        </div>
                
                    <?php } ?>
                
                    <div class="ar_model_files_fields">
                        <?php if (get_post_meta( $model_array['id'], '_glb_file'.$suffix, true )!=''){
                            $glb_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ); 
                            $path_parts = pathinfo(sanitize_text_field( get_post_meta( $model_array['id'], '_glb_file'.$suffix, true ) ));
                            $glb_filename = $path_parts['basename'];
                            $ar_glb_pulse = '';
                        }else{
                            $glb_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); 
                            $glb_filename = '';
                            $ar_glb_pulse = 'ar_file_icons_pulse';
                            if (strpos($glb_filename, '&ratio=') !== false && strpos($glb_filename, '&o=') !== false) {
                                $glb_filename = '3D Gallery Build';
                            }
                        }
                        if (get_post_meta( $model_array['id'], '_usdz_file'.$suffix, true )!=''){
                            $usdz_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) );
                            $path_parts = pathinfo(sanitize_text_field( get_post_meta( $model_array['id'], '_usdz_file'.$suffix, true ) ));
                            //print_r($path_parts);
                            $usdz_filename = $path_parts['basename'];
                            $ar_usdz_pulse = '';
                        }else{
                            $usdz_upload_image = esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); 
                            $usdz_filename = '';
                            $ar_usdz_pulse = 'ar_file_icons_pulse';
                        }
                        
                       
                        ?>
                        <div style="width:48%; float:left;padding-right:10px; ">
                            
                            <center><strong><?php esc_html_e( 'GLTF/GLB 3D Model', 'ar-for-woocommerce' );?></strong> 
                            <img src="<?php echo esc_url($glb_upload_image);?>" id="glb_thumb_img<?php echo esc_html($suffix);?>" class="ar_file_icons <?php echo esc_attr($ar_glb_pulse)?>" onclick="document.getElementById('upload_glb_button<?php echo esc_html($suffix);?>').click();document.getElementById('glb_thumb_img<?php echo esc_html($suffix);?>').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ); ?>';document.getElementById('glb_thumb_img<?php echo esc_html($suffix);?>').classList.remove('ar_file_icons_pulse');">
                            <img src="<?php echo esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;"  onclick="document.getElementById('_glb_file<?php echo esc_html($suffix);?>').value = '';document.getElementById('glb_filename<?php echo esc_html($suffix);?>').innerHTML = '';document.getElementById('glb_thumb_img<?php echo esc_html($suffix);?>').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); ?>';document.getElementById('glb_thumb_img<?php echo esc_html($suffix);?>').classList.add('ar_file_icons_pulse');">
                            <br clear="all"><br><span id="glb_filename<?php echo esc_html($suffix);?>" class="ar_filenames"><?php echo esc_html($glb_filename);?></span>
                            <div class="nodisplay glb-file-container" align="center"><?php
                                if(!$variation_id){?>
                                <input type="hidden" id="uploader_modelid" value="">
                                <?php
                                }
        
                                /*woocommerce_wp_text_input( array(
                                    'id'                => '_glb_file'.$suffix,
                                    'label'             => __('GLB/GLTF 3D Model', 'ar-for-woocommerce' ),
                                    'desc_tip'          => 'true',
                                    'class'             => 'ar_input_field _glb_file_field',
                                    'wrapper_class' => 'form-row-first',
                                    'description'       => __( 'Upload a GLB or GLTF 3D model file. You can also upload a DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped version of these files and they will be converted automatically.', 'ar-for-woocommerce' ),
                                    'custom_attributes' => ['data-model'=>($variation_id ? $variation_id : $prod_id)],
                                    'value' => get_post_meta( $model_array['id'], '_glb_file'.$suffix, true ),
                                ) ); */?>
                                <input type="text" pattern="https?://.+" title="<?php esc_html_e('Secure URLs only','ar-for-woocommerce'); ?> https://" placeholder="https://" name="_glb_file<?php echo esc_html($suffix);?>" id="_glb_file<?php echo esc_html($suffix);?>" class="regular-text _glb_file_field" value="<?php echo esc_html(get_post_meta( $model_array['id'], '_glb_file'.$suffix, true ));?>"> 
                                
                                <input id="upload_glb_button<?php echo esc_html($suffix);?>" data-suffix="<?php echo esc_html($suffix);?>" class="button nodisplay upload_glb_button" type="button" value="<?php esc_html_e( 'Upload', 'ar-for-woocommerce' );?>" <?php echo $variation_id ? 'data-variation="'.esc_attr($variation_id).'"' : '';?> data-model="<?php echo esc_attr($prod_id); ?>" /> 
                            </div>
                            
                            </center>
                        </div>
                        <div style="width:48%; float:left;">
                            <center>
                            <strong><?php echo esc_html_e( 'USDZ/REALITY 3D Model', 'ar-for-woocommerce' );?></strong>
                            <img src="<?php echo esc_url($usdz_upload_image);?>" id="usdz_thumb_img<?php echo esc_html($suffix);?>"  class="ar_file_icons <?php echo  esc_attr($ar_usdz_pulse);?>" onclick="document.getElementById('upload_usdz_button<?php echo esc_html($suffix);?>').click();document.getElementById('usdz_thumb_img<?php echo esc_html($suffix);?>').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon_tick.jpg", __FILE__ ) ); ?>';document.getElementById('usdz_thumb_img<?php echo esc_html($suffix);?>').classList.remove('ar_file_icons_pulse');">
                            <img src="<?php echo esc_url( plugins_url( "assets/images/delete.png", __FILE__ ) );?>" style="width: 15px;vertical-align: middle;" <?php echo esc_attr($button_atts); ?> onclick="document.getElementById('_usdz_file<?php echo esc_html($suffix);?>').value = '';document.getElementById('usdz_filename<?php echo esc_html($suffix);?>').innerHTML = '';document.getElementById('usdz_thumb_img<?php echo esc_html($suffix);?>').src = '<?php echo esc_url( plugins_url( "assets/images/ar_model_icon.jpg", __FILE__ ) ); ?>';document.getElementById('usdz_thumb_img<?php echo esc_html($suffix);?>').classList.add('ar_file_icons_pulse');">
                            <br> <span class="ar_label_tip"><?php esc_html_e('Optional', 'ar-for-woocommerce' );?></span>
                            <br clear="all"><br><span id="usdz_filename<?php echo esc_attr($suffix);?>" class="ar_filenames"><?php echo esc_attr($usdz_filename);?></span>
    
                            <div class="nodisplay usdz-file-container"><?php
                                
                                /*woocommerce_wp_text_input( array(
                                    'id'                => '_usdz_file'.$suffix,
                                    'label'             => __('USDZ/REALITY 3D Model', 'ar-for-woocommerce' ),
                                    'desc_tip'          => 'true',
                                    'class'             => 'ar_input_field _usdz_file_field',
                                    'description'       => __( 'Upload a USDZ or REALITY 3D model file for iOS devices', 'ar-for-woocommerce' ),
                                    'value' => get_post_meta( $model_array['id'], '_usdz_file'.$suffix, true ),
                                ) );*/?>
                                <input type="text" pattern="https?://.+" title="<?php esc_html_e('Secure URLs only','ar-for-woocommerce'); ?> https://" placeholder="https://" name="_usdz_file<?php echo esc_html($suffix);?>" id="_usdz_file<?php echo esc_html($suffix);?>" class="regular-text _usdz_file_field" value="<?php echo esc_html(get_post_meta( $model_array['id'], '_usdz_file'.$suffix, true ));?>">
        
                                <input id="upload_usdz_button<?php echo esc_html($suffix);?>" class="button upload_usdz_button nodisplay" type="button" value="<?php esc_html_e( 'Upload', 'ar-for-woocommerce' );?>" data-model="<?php echo esc_html($prod_id); ?>" <?php echo $variation_id ? 'data-variation="'.esc_attr($variation_id).'"' : '';?> />
                            </div>
                             
                            
                            </center>
                        </div>
                        <div style="clear:both"></div> 
                    </div>
                </div>
            </div>
            <?php 
            
            if($plan_check!='Premium') { 
                    $premium_only = '<b> - '.__('Premium Plans Only', 'ar-for-woocommerce').'</b>'; 
                    $disabled = ' disabled';
                    $readonly = ['readonly' => 'readonly'];
                    $custom_attributes = $readonly;
                    echo '<div style="pointer-events: none;">'; //disable mouse clicking 
                }else{
                    $disabled = '';
                    $readonly = '';
                    $premium_only = '';
                    //Used for Scale inputs
                    $custom_attributes = array(
                        'step' => '0.1',
                        'min' => '0.1');
                }
                ?>
                    
                <?php /* Asset Builder */ ?>
                <div id="asset_builder_content<?php echo esc_html($suffix)?>" class="ar_tabcontent<?php echo esc_html($suffix)?>" style="padding:0px;">
                    <div id="asset_builder">
                        
                            <div class="asset_builder_img" style="max-width:50%;" onclick="toggleMaxWidth(this)">
                                <img src="<?php echo esc_url(plugins_url('ar-for-woocommerce/assets/images/wall_art_guide.jpg', dirname(__FILE__))); ?>" style="max-width:100%; max-height:200px;">
                            </div>
                            
                            <script>
                                function toggleMaxWidth(element) {
                                    var imgElement = element.querySelector('img');
                                    if (element.style.maxWidth === '100%') {
                                        element.style.maxWidth = '50%';
                                        imgElement.style.maxHeight = '200px';
                                    } else {
                                        element.style.maxWidth = '100%';
                                        imgElement.style.maxHeight = null;
                                    }
                                }
                            </script>
                         <div id="asset_builder_top_content" style="padding:6px 10px;">   
                            <?php global $ar_plugin_id; 
                            $asset_image = plugins_url( "ar-for-woocommerce/assets/images/ar_asset_icon.jpg", dirname(__FILE__) );
                            if (get_post_meta( $model_array['id'], '_glb_file', true )!=''){
                                $glb_file = sanitize_text_field(get_post_meta( $model_array['id'], '_glb_file', true ));
                                
                                // Parse the URL to get its components
                                $url_components = parse_url($glb_file);
                                if (isset($url_components['query'])){
                                // Extract the query string (the part after the ? in the URL)
                                $query_string = $url_components['query'];
                                
                                // Parse the query string into an associative array
                                parse_str($query_string, $query_parts);
                                
                                // Now $query_parts will contain the parts of the URL as an associative array
                                $url = $query_parts['url']; // Extracts the 'url' part
                                $ratio = $query_parts['ratio']; // Extracts the 'ratio' part
                                $orientation = $query_parts['o']; // Extracts the 'o' part
                                $asset_image = $url;
                                }
                            }
                            
                            $nodisplay = ' class=""';
                            for($i = 0; $i<1; $i++) { //Previously 10 - Cube will require 6
                            if ($i>0){$nodisplay = ' class="nodisplay"';}
                            ?>
                               <div  id="texture_container_<?php echo esc_html($i)?>" <?php echo esc_attr($nodisplay);?> style="padding:10px 0px">
                                 <p><strong><?php esc_html_e( 'Image', 'ar-for-woocommerce' );?></strong> <span id="ar_asset_builder_texture_done"></span><br>
                                <img src="<?php echo esc_url( $asset_image ); ?>" id="asset_thumb_img" style="max-heigth:200px"  class="ar_file_icons" onclick="document.getElementById('upload_asset_texture_button_<?php echo esc_html($i); ?>').click();">
                                <span id="texture_<?php echo esc_html($i)?>">
                                <input type="hidden" name="_asset_texture_file_<?php echo esc_html($i); ?>" id="_asset_texture_file_<?php echo esc_html($i); ?>" class="regular-text" value="<?php if (isset($url)){echo esc_url($url);}?>"> <input id="upload_asset_texture_button_<?php echo esc_html($i); ?>" class="upload_asset_texture_button_<?php echo esc_html($i); ?> upload_asset_texture_button button nodisplay" type="button" value="<?php esc_html_e( 'Upload', 'ar-for-woocommerce' );?>" /> <img src="<?php echo esc_url( plugins_url( "ar-for-woocommerce/assets/images/delete.png", dirname(__FILE__) ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer" onclick="document.getElementById('_asset_texture_file_<?php echo esc_html($i); ?>').value = '';document.getElementById('ar_asset_builder_texture_done').innerHTML = '';document.getElementById('asset_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_asset_ad_icon.jpg", dirname(__FILE__) ) ); ?>';">
                                <input type="text" name="_asset_texture_id_<?php echo esc_html($i); ?>" id="_asset_texture_id_<?php echo esc_html($i); ?>" class="nodisplay"></span></p>
                                
                                </div>
                            
                            <?php }
                            ?><input type="text" name="_asset_texture_flip" id="_asset_texture_flip" class="nodisplay">
            
                        
                        
                        <input type="hidden" name="_ar_asset_file" id="_ar_asset_file" class="regular-text" value="">
                        <input type="hidden" name="_ar_asset_id" id="_ar_asset_id" class="regular-text" value="<?php echo esc_html($model_array['id']); ?>">
                        <input type="hidden" name="_ar_asset_url" id="_ar_asset_url" class="regular-text" value="<?php echo esc_url(site_url('/wp-content/plugins/'.$ar_plugin_id.'/ar-gallery.php'));?>">
                        <input type="hidden" name="_ar_asset_ratio" id="_ar_asset_ratio" value="<?php if (isset($ratio)){echo esc_html($ratio); } ?>">
                        <input type="hidden" class="ar_model_id" name="ar_model_id[]" value="<?php echo esc_html($model_array['id']); ?>">
                        
      
                        <div style="min-height:100px">
                         <div id="ar_asset_size_container" <?php if (!isset($ratio)){echo ' style="display:none;"'; } ?>>
                              <div style="float:left;padding-right:10px">
                                  <strong><?php esc_html_e( 'Orientation', 'ar-for-woocommerce' );?></strong> <span id="ar_asset_builder_ratio_done">&#10003;</span><br>
                                  <select name="_ar_asset_orientation" id="_ar_asset_orientation">
                                    <option value="portrait" <?php echo (isset($orientation) && $orientation == 'portrait') ? 'selected' : ''; ?>>Portrait</option>
                                    <option value="landscape" <?php echo (isset($orientation) && $orientation == 'landscape') ? 'selected' : ''; ?>>Landscape</option>
                                </select></p>
                              </div>
                             <div style="float:left;padding-right:10px">
                                 <strong><?php esc_html_e( 'Image Ratio', 'ar-for-woocommerce' );?></strong> <span id="ar_asset_builder_ratio_done">&#10003;</span><br>
                                  <?php
                                    $ratio = isset($query_parts['ratio']) ? $query_parts['ratio'] : null;
                                    
                                    // If $ratio is set to '1', set it to '1.0'
                                    if ($ratio == '1') {
                                        $ratio = '1.0';
                                    }
                                    
                                    // Define the array of options
                                    $ratio_options = array(
                                        '1.0'     => '1:1',
                                        '1.4142'  => 'A4-A1',
                                        '1.5'     => '2:3',
                                        '1.25'    => '4:5',
                                        '1.33'    => '3:4'
                                    );
                                    
                                    ?>
                                    
                                    <select id="_ar_asset_ratio_select">
                                    <?php
                                    // Loop through the array and generate the <option> elements
                                    foreach ($ratio_options as $value => $label) {
                                        // Check if the current value matches the selected ratio
                                        $selected = ($value == $ratio) ? ' selected' : '';
                                        echo "<option id='ar_asset_ratio_options' value='".esc_html($value)."'".esc_html($selected).">".esc_html($label)."</option>";
                                    }
                                    ?>
                                    </select>
                                  
                              </div>
                              <div style="float:left;">
                                  <strong><?php esc_html_e( 'Print Size', 'ar-for-woocommerce' );?></strong> <span id="ar_asset_builder_ratio_done">&#10003;</span><br>
                                  <select id="ar_asset_size">
                                        <option  id="ar_asset_size_options" value="-1" selected="selected">Select your Asset Below First</option>
                                  </select></p>
                              </div>
                          </div>
                            
                            <span id="ar_asset_builder_submit_container" style="display:none;">
                                <br clear="all"><!--<br>
                                <button id = "ar_asset_builder_submit" class="button ar_admin_button" >Build Asset</button>-->
                                
                                <strong><span style="color:#f37a23"><?php esc_html_e( 'Please Publish/Update your post to build the Gallery Asset. You may need to refresh your browser once updated to ensure the latest files are displayed.', 'ar-for-woocommerce' );?></span></strong>
                                <br><br>
                                
                            </span>
                            </div>
                        </div>
                    </div>
                </div>  
                
                <?php 
                $support_links = '';
                if (!$ar_whitelabel){
                    $support_links = '<br><a href="admin.php?page=wc-settings&tab=ar_display">'.__('AR Display Settings', 'ar-for-woocommerce').'</a> | <a href="https://augmentedrealityplugins.com/support/" target="_blank">'.__('Documentation', 'ar-for-woocommerce').'</a> | <a href="https://augmentedrealityplugins.com/support/3d-model-resources/" target="_blank">'.__('Sample 3D Models', 'ar-for-woocommerce').'</a> | <a href="https://augmentedrealityplugins.com/support/3d-model-resources/#hdr" target="_blank">'.__('Sample HDR Images', 'ar-for-woocommerce').'</a> ';
                }
        
                /* User Upload */ ?>
                <div id="user_upload_content<?php echo esc_html($suffix)?>" class="ar_tabcontent ar_tabcontent<?php echo esc_html($suffix)?>" style="padding:20px;">
                    <h3><?php esc_html_e( 'Allow user to upload their own image or model file?', 'ar-for-woocommerce' );?></h3>
                    <input type="checkbox" name="_ar_user_upload" id="_ar_user_upload" value="1" class="ar-ui-toggle _ar_user_upload" <?php if (get_post_meta( $prod_id, '_ar_user_upload'.$suffix, true )=='1'){echo 'checked';}?>><br>
                <br><?php esc_html_e( 'Using the [ar_user-upload] shortcode displays an Image/Model upload button and an empty model viewer. Model files (gltf & glb) will be displayed in the model viewer and image files (jpg & png) will be converted to hanging artworks with the 3D Gallery Builder.', 'ar-for-woocommerce' );?>
                <br><br><?php esc_html_e( 'The uploaded file will be attached to the product order in the cart.<br><br>Single product page location settings for the <i>Upload Button</i> and <i>Model Viewer</i> can be found on the <a href="admin.php?page=wc-settings&tab=ar_display">AR Display Settings Page.</a>', 'ar-for-woocommerce' );?>
                <br>
                <?php echo wp_kses($support_links, ar_allowed_html()); ?>
                </div>
                <?php /* Instructions */ ?>
                <div id="instructions_content<?php echo esc_html($suffix)?>" class="ar_tabcontent ar_tabcontent<?php echo esc_html($suffix)?>">
                        <br>                            
                        <?php echo wp_kses($shortcode_examples_wc, ar_allowed_html());
                        echo wp_kses($shortcode_examples, ar_allowed_html());
                        //echo '<p>'.__( 'Models can be uploaded as a GLB or GLTF file for viewing in AR and within the broswer display. You can also upload a USDZ or REALITY file for iOS, otherwise a USDZ file is generated on the fly. The following formats can be uploaded and will be automatically converted to GLB format - DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped versions of these files. Model conversion accuracy cannot be guaranteed, please check your model carefully.', 'ar-for-woocommerce' );
                        echo wp_kses($support_links, ar_allowed_html());;
                        ?>
                </div>
                <?php
                $ar_open_tabs=get_option('ar_open_tabs'); 
                $ar_open_tabs_array = explode(',',$ar_open_tabs);
                $jsArray = wp_json_encode($ar_open_tabs_array);
                ?>   
                <div style="clear:both"></div>
                <div class="ar_admin_viewer" id="ar_admin_options">
                    <input type="hidden" name="ar_open_tabs" id="ar_open_tabs" value="<?php echo esc_html($ar_open_tabs);?>">
                    <button class="ar_accordian" id="ar_display_options_acc" type="button"><?php esc_html_e('Display Options', 'ar-for-woocommerce' ); echo esc_html($premium_only);?></button>
                    <div id="ar_display_options_panel" class="ar_accordian_panel">
                        <?php
            
            // Skybox File Input
            echo '<div style="position: relative; display: inline-block; width: 100%;">';
            woocommerce_wp_text_input( array(
                'id'                => '_skybox_file'.$suffix,
                'label'             => __( 'Skybox Image', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'class'             => 'ar_input_field ar_input_upload _skybox_file_field',
                'description'       => __( 'Upload a HDR, JPG or PNG file to use as the Skybox or background image - Optional', 'ar-for-woocommerce' ),
                'custom_attributes' => $readonly,
                'value' => get_post_meta( $model_array['id'], '_skybox_file'.$suffix, true ),
            ) );
            echo '<input id="upload_skybox_button" class="button upload_skybox_button ar-upload-button" type="button" value="'.esc_html(__('Upload','ar-for-woocommerce')).'" '.esc_attr($disabled).'  '.esc_attr($button_atts).' />';
            echo '</div>';
            
            // Environment Image
            echo '<div style="position: relative; display: inline-block; width: 100%;">';
            woocommerce_wp_text_input( array(
                'id'                => '_ar_environment'.$suffix,
                'label'             => __( 'Environment Image', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'class'             => 'ar_input_field ar_input_upload _environment_file_field',
                'description'       => __( 'Upload a HDR, JPG or PNG file to use as the environment image - Optional', 'ar-for-woocommerce' ),
                'custom_attributes' => $readonly,
                'value' => get_post_meta( $model_array['id'], '_ar_environment'.$suffix, true ),
            ) );
            echo '<input id="upload_environment_button" class="button upload_environment_button ar-upload-button" type="button" value="'.esc_html(__('Upload','ar-for-woocommerce')).'" '.esc_attr($disabled).' '.esc_attr($button_atts).' />';
            echo '</div>';
            
            // Poster Image
            echo '<div style="position: relative; display: inline-block; width: 100%;">';
            woocommerce_wp_text_input( array(
                'id'                => '_ar_poster'.$suffix,
                'label'             => __( 'Poster Image', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'class'             => 'ar_input_field ar_input_upload _poster_file_field',
                'description'       => __( 'Upload a JPG or PNG file to use as the loading poster image, defaults to the product featured image - Optional', 'ar-for-woocommerce' ),
                'custom_attributes' => $readonly,
                'value' => get_post_meta( $model_array['id'], '_ar_poster'.$suffix, true ),
            ) );
            echo '<input id="upload_poster_button" class="button upload_poster_button ar-upload-button" type="button" value="'.esc_html(__('Upload','ar-for-woocommerce')).'" '.esc_attr($disabled).' '.esc_attr($button_atts).' />';
            echo '</div>';
            ?>
            <?php
            //Placement
            $ar_placement = get_post_meta( $prod_id, '_ar_placement'.$suffix, true );
            woocommerce_wp_select( array(
                'id'        => '_ar_placement'.$suffix,
                'label'     => __( 'Model placement', 'ar-for-woocommerce' ),
                    'options' => array(
                        'floor' => __('Floor - Horizontal', 'ar-for-woocommerce'),
                        'wall' => __('Wall - Vertical', 'ar-for-woocommerce')
                    ),
                'desc_tip'          => 'true',
                'class'             => 'ar_input_field _ar_placement_field',
                'description'       => __( 'Place your model on a horizontal or vertical surface', 'ar-for-woocommerce' ),
                'custom_attributes' => $disabled,
                'value' => $ar_placement,
            ) );
            
            //Scale Inputs
            $ar_x = get_post_meta($prod_id, '_ar_x'.$suffix, true );
            if ( ! $ar_x ) {
                $ar_x = 1;
            }
            woocommerce_wp_text_input( array(
                'id'                => '_ar_x'.$suffix,
                'label'             => __( 'Scale X', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'description'       => __( '1 = 100%, only affects desktop view, not available in AR', 'ar-for-woocommerce' ),
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-input _ar_x_field ar_number_field',
                'type' => 'number',
                'value' => $ar_x,
                'custom_attributes' => $custom_attributes
                ) 
            );
            $ar_y = get_post_meta($prod_id, '_ar_y'.$suffix, true );
            if ( ! $ar_y ) {
                $ar_y = 1;
            }
            woocommerce_wp_text_input( array(
                'id'                => '_ar_y'.$suffix,
                'label'             => __( 'Scale Y', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'description'       => __( '1 = 100%, only affects desktop view, not available in AR', 'ar-for-woocommerce' ),
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-input _ar_y_field ar_number_field',
                'type' => 'number',
                'value' => $ar_y,
                'custom_attributes' => $custom_attributes
                ) 
            );
            $ar_z = get_post_meta($prod_id, '_ar_z'.$suffix, true );
            if ( ! $ar_z ) {
                $ar_z = 1;
            }
            woocommerce_wp_text_input( array(
                'id'                => '_ar_z'.$suffix,
                'label'             => __( 'Scale Z', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'description'       => __( '1 = 100%, only affects desktop view, not available in AR', 'ar-for-woocommerce' ),
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-input _ar_z_field ar_number_field',
                'type' => 'number',
                'value' => $ar_z,
                'custom_attributes' => $custom_attributes
                ) 
            );
            echo '
          <br clear="all">';
            //Zoom and Field of View Inputs
            $fov_in_array=array();
            $fov_in_array['default']=__('Default', 'ar-for-woocommerce' );
            for ($x = 10; $x <= 180; $x+=10) {
                $fov_in_array [$x] = $x.' '.__('Degrees', 'ar-for-woocommerce' );
            }
            $arfieldview = get_post_meta($prod_id, '_ar_field_of_view'.$suffix, true );
            $arzoomin = get_post_meta($prod_id, '_ar_zoom_in'.$suffix, true );
            $arzoomout = get_post_meta($prod_id, '_ar_zoom_out'.$suffix, true );

            woocommerce_wp_select( array(
                'id'                => '_ar_field_of_view'.$suffix,
                'label'             => __( 'Field of View', 'ar-for-woocommerce' ),
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-input _ar_field_of_view',
                'options' =>  $fov_in_array,
                'value'=> $arfieldview,
                )
            );
            $zoom_in_array=array();
            $zoom_in_array['default']=__('Default', 'ar-for-woocommerce' );
            for ($x = 100; $x >= 0; $x-=10) {
                $zoom_in_array [$x] = $x.'%';
            }
            woocommerce_wp_select( array(
                'id'                => '_ar_zoom_in'.$suffix,
                'label'             => __( 'Zoom In', 'ar-for-woocommerce' ),
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-input _ar_zoom_in',
                'options' =>  $zoom_in_array,
                'value'=> $arzoomin,
                
                )
            );
            $zoom_out_array=array();
            $zoom_out_array['default']=__('Default', 'ar-for-woocommerce' );
            for ($x = 0; $x <= 100; $x+=10) {
                $zoom_out_array [$x] = $x.'%';
            }
            woocommerce_wp_select( array(
                'id'                => '_ar_zoom_out'.$suffix,
                'label'             => __( 'Zoom Out', 'ar-for-woocommerce' ),
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-input _ar_zoom_out',
                'options' =>  $zoom_out_array,
                'value'=> $arzoomout,
                )
            );
            echo '
          <br clear="all">';
          
          woocommerce_wp_text_input( array(
                'id'                => '_ar_light_color'.$suffix,
                'label'             => __( 'Light Color', 'ar-for-woocommerce' ),
                //'desc_tip'            => 'true',
                'class'             => 'ar_input_field _light_color',
                //'description'     => __( 'Upload a HDR, JPG or PNG file to use as the environment image - Optional', 'ar-for-woocommerce' ),
                'custom_attributes' => $readonly,
                'value' => get_post_meta( $model_array['id'], '_ar_light_color'.$suffix, true ),
            ) );
          //Exposure and Shadow Inputs
            $ar_exposure = get_post_meta($prod_id, '_ar_exposure'.$suffix, true );
            if ((!$ar_exposure)AND($ar_exposure!='0')){ $ar_exposure = 1; }
            $custom_attributes = array(
                    'step' => '0.1',
                    'min' => '0',
                    'max' => '2');
            woocommerce_wp_text_input( array(
                'id'                => '_ar_exposure'.$suffix,
                'label'             => __( 'Exposure', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-slider _ar_exposure',
                'type' => 'range',
                'value' => $ar_exposure,
                'custom_attributes' => $custom_attributes
                ) 
            );
            echo '
          <br clear="all">';
            $ar_shadow_intensity = get_post_meta($prod_id, '_ar_shadow_intensity'.$suffix, true );
            if ((!$ar_shadow_intensity)AND($ar_shadow_intensity!='0')){ $ar_shadow_intensity = 1; }
            $custom_attributes = array(
                    'step' => '0.1',
                    'min' => '0',
                    'max' => '2');
            woocommerce_wp_text_input( array(
                'id'                => '_ar_shadow_intensity'.$suffix,
                'label'             => __( 'Shadow Intensity', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-slider _ar_shadow_intensity',
                'type' => 'range',
                'value' => $ar_shadow_intensity,
                'custom_attributes' => $custom_attributes
                ) 
            );echo '
          <br clear="all">';
            $ar_shadow_softness = get_post_meta($prod_id, '_ar_shadow_softness'.$suffix, true );
            if ((!$ar_shadow_softness)AND($ar_shadow_softness!='0')){ $ar_shadow_softness = 1; }
            $custom_attributes = array(
                    'step' => '0.1',
                    'min' => '0',
                    'max' => '1');
            woocommerce_wp_text_input( array(
                'id'                => '_ar_shadow_softness'.$suffix,
                'label'             => __( 'Shadow Softness', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'wrapper_class' => 'scale_input',
                'class'             => 'ar-slider _ar_shadow_softness',
                'type' => 'range',
                'value' => $ar_shadow_softness,
                'custom_attributes' => $custom_attributes
                ) 
            );
            echo '
          <br clear="all">
          <div>';
            // Variants
          $arvariants = get_post_meta($prod_id, '_ar_variants'.$suffix, true );
          $arlighting = get_post_meta($prod_id, '_ar_environment_image'.$suffix, true );
          $aranimation = get_post_meta($prod_id, '_ar_animation'.$suffix, true );
          $arautoplay = get_post_meta($prod_id, '_ar_autoplay'.$suffix, true );
          $ar_emissive = get_post_meta($prod_id, '_ar_emissive'.$suffix, true );

            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_variants'.$suffix, 
                'label'         => __('Model includes variants', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Does your model include texture variants? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_variants',
                'custom_attributes' => $readonly,
                'value' => $arvariants,
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_environment_image'.$suffix, 
                'label'         => __('Legacy lighting', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'The default lighting is designed as a neutral lighting environment that is evenly lit on all sides, but there is also a baked-in legacy lighting primarily for frontward viewing available', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_environment_image',
                'custom_attributes' => $readonly,
                'value' => $arlighting,
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_emissive'.$suffix, 
                'label'         => __('Emissive lighting', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Emissive lighting to simulate objects that emit light, such as glowing objects or light sources', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_emissive',
                'custom_attributes' => $readonly,
                'value' => $ar_emissive,
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_animation'.$suffix, 
                'label'         => __('Animation - Play/Pause', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Show a play/pause button if your GLB/GLTF contains animation. Only displays on desktop view - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_animation',
                'custom_attributes' => $readonly,
                'value' => $aranimation,
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_autoplay'.$suffix, 
                'label'         => __('Animation - Auto Play', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Auto Play your animation if your GLB/GLTF contains animation. Only animates on desktop view - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_autoplay',
                'custom_attributes' => $readonly,
                'value' => $arautoplay,
                )
            );
            //check if animations in the file and list
            $variation_att = '';
            if($readonly && $variation_id){
                $variation_att = array_merge($readonly, ['data-variation'=>$variation_id, 'data-model'=>$prod_id]);

            } else if(!$readonly && $variation_id){
                $variation_att = ['data-variation'=>$variation_id, 'data-model'=>$prod_id];
            } else if($readonly && !$variation_id){
                $variation_att = array_merge($readonly, ['data-variation'=>$variation_id, 'data-model'=>$prod_id]);
            } else {
                $variation_att = ['data-variation'=>$variation_id, 'data-model'=>$prod_id];
            }?>
            <p class="form-field " id="animationDiv<?php echo esc_html($model_array['id']); ?>" style="display:none"><br clear="all"><label for="_ar_animation_selection"><?php esc_html_e( 'Animation Selection', 'ar-for-woocommerce' );?></label> <select name="_ar_animation_selection<?php echo esc_html($suffix);?>" id="_ar_animation_selection<?php echo esc_html($suffix);?>" class="ar-input" <?php echo esc_attr($disabled);?>></select></p>
            </div>
            </div> <!-- end of Accordian Panel -->
            <?php
            if($variation_id == ''){
            ?>
                    <button class="ar_accordian" id="ar_rotation_acc" type="button"><?php esc_html_e('Rotation Limits', 'ar-for-woocommerce' ); echo esc_html($premium_only);?></button>
                    <div id="ar_rotation_panel" class="ar_accordian_panel">
            <?php
            
                woocommerce_wp_checkbox( array( 
                    'id'            => '_ar_rotate_limit'.$suffix, 
                    'label'         => __('Rotation - Set Limits', 'ar-for-woocommerce' ), 
                    'desc_tip'          => 'true',
                    'description'   => __( 'Restrict the rotation of your model- Optional', 'ar-for-woocommerce' ),
                    'class'             => 'ar-ui-toggle ar_rotate_limit',
                    'custom_attributes' => $variation_att,
                    )
                );

                //$hide_rotate_limit = 'display:none';
                if (get_post_meta( $prod_id, '_ar_rotate_limit'.$suffix, true )){
                    $hide_rotate_limit = '';
                }
                //if ar_rotate_limit is true show limit options
                $ar_compass_top_value = '';
                $ar_compass_top_selected = '';
                if (get_post_meta( $prod_id, '_ar_compass_top_value'.$suffix, true )){
                    $ar_compass_top_value = get_post_meta( $prod_id, '_ar_compass_top_value'.$suffix, true );
                    $ar_compass_top_selected = 'style="background-color:#f37a23 !important"';
                }
                $ar_compass_bottom_value = '';
                $ar_compass_bottom_selected = '';
                if (get_post_meta( $prod_id, '_ar_compass_bottom_value'.$suffix, true )){
                    $ar_compass_bottom_value = get_post_meta( $prod_id, '_ar_compass_bottom_value'.$suffix, true );
                    $ar_compass_bottom_selected = 'style="background-color:#f37a23 !important"';
                }
                $ar_compass_left_value = '';
                $ar_compass_left_selected = '';
                if (get_post_meta( $prod_id, '_ar_compass_left_value'.$suffix, true )){
                    $ar_compass_left_value = get_post_meta( $prod_id, '_ar_compass_left_value'.$suffix, true );
                    $ar_compass_left_selected = 'style="background-color:#f37a23 !important"';
                }
                $ar_compass_right_value = '';
                $ar_compass_right_selected = '';
                if (get_post_meta( $prod_id, '_ar_compass_right_value'.$suffix, true )){
                    $ar_compass_right_value = get_post_meta( $prod_id, '_ar_compass_right_value'.$suffix, true );
                    $ar_compass_right_selected = 'style="background-color:#f37a23 !important"';
                }
                
                ?>
                <div style="clear:both"></div>
                
                <div id="ar_rotation_limits<?php echo esc_html($suffix);?>" class="ar_rotation_limits_containter" style="<?php echo esc_attr($hide_rotate_limit);?>">
                    <center>
                        <h3><?php esc_html_e( 'Rotation Limits', 'ar-for-woocommerce' ); ?></h3>
                        <p><?php esc_html_e( 'Set your initial camera view first.<br>Then rotate your model to each of your desired limits and click the arrows to apply.', 'ar-for-woocommerce' ); ?></p>
                        <div class="ar-compass-container">
                            <img src="<?php echo esc_url( plugins_url( "assets/images/rotate_up_arrow.png", __FILE__ ) );?>" alt="Compass" id="ar-compass-image<?php echo esc_html($suffix);?>" class="ar-compass-image">
                            <button id = "ar-compass-top<?php echo esc_html($suffix);?>" class="ar-compass-button ar-compass-top ar-compass-btn-<?php echo esc_html($suffix);?>" data-variation="<?php echo esc_html($variation_id); ?>" data-model="<?php echo esc_html($prod_id); ?>" <?php echo esc_html($ar_compass_top_selected); ?> data-rotate="0" type="button">&UpArrowBar;</button>
                            <button id = "ar-compass-bottom<?php echo esc_html($suffix);?>" class="ar-compass-button ar-compass-bottom ar-compass-btn-<?php echo esc_html($suffix);?>" <?php echo esc_html($ar_compass_bottom_selected); ?> data-rotate="180" type="button"  data-variation="<?php echo esc_html($variation_id); ?>" data-model="<?php echo esc_html($prod_id); ?>">&DownArrowBar;</button>
                            <button id = "ar-compass-left<?php echo esc_html($suffix);?>" class="ar-compass-button ar-compass-left ar-compass-btn-<?php echo esc_html($suffix);?>" <?php echo esc_html($ar_compass_left_selected); ?> data-rotate="270" type="button"  data-variation="<?php echo esc_html($variation_id); ?>" data-model="<?php echo esc_html($prod_id); ?>">&LeftArrowBar;</button>
                            <button id = "ar-compass-right<?php echo esc_html($suffix);?>" class="ar-compass-button ar-compass-right ar-compass-btn-<?php echo esc_html($suffix);?>" <?php echo esc_html($ar_compass_right_selected); ?> data-rotate="90" type="button"  data-variation="<?php echo esc_html($variation_id); ?>" data-model="<?php echo esc_html($prod_id); ?>">&RightArrowBar;</button>
                        </div>
                    </center>
                    <input id="_ar_compass_top_value<?php echo esc_html($suffix);?>" name="_ar_compass_top_value<?php echo esc_html($suffix);?>" class="_ar_compass_top_value" type="hidden" value="<?php echo esc_html($ar_compass_top_value);?>" <?php echo esc_attr($disabled);?>> 
                    <input id="_ar_compass_bottom_value<?php echo esc_html($suffix);?>" name="_ar_compass_bottom_value<?php echo esc_html($suffix);?>" class="_ar_compass_bottom_value" type="hidden" value="<?php echo esc_html($ar_compass_bottom_value);?>" <?php echo esc_attr($disabled);?>> 
                    <input id="_ar_compass_left_value<?php echo esc_html($suffix);?>" name="_ar_compass_left_value<?php echo esc_html($suffix);?>" class="_ar_compass_left_value" type="hidden" value="<?php echo esc_html($ar_compass_left_value);?>" <?php echo esc_attr($disabled);?>> 
                    <input id="_ar_compass_right_value<?php echo esc_html($suffix);?>" name="_ar_compass_right_value<?php echo esc_html($suffix);?>" class="_ar_compass_right_value" type="hidden" value="<?php echo esc_html($ar_compass_right_value);?>" <?php echo esc_attr($disabled);?>> 
                </div>
            
            
            </div> <!-- end of Accordian Panel -->
            <?php } ?>
            <button class="ar_accordian" id="ar_disable_elements_acc" type="button"><?php esc_html_e('Disable/Hide Elements', 'ar-for-woocommerce' ); if ($disabled!=''){echo ' - '.esc_html(__('Premium Plans Only', 'ar-for-woocommerce'));}?></button>
            <div id="ar_disable_elements_panel" class="ar_accordian_panel">
            <?php
            $arviewhide = get_post_meta( $prod_id, '_ar_view_hide'.$suffix, true );
            $autorotate = get_post_meta( $prod_id, '_ar_rotate'.$suffix, true );
            $hidedimensions = get_post_meta( $prod_id, '_ar_hide_dimensions'.$suffix, true );
            $arprompt = get_post_meta( $prod_id, '_ar_prompt'.$suffix, true );
            $arresizing = get_post_meta( $prod_id, '_ar_resizing'.$suffix, true );
            $qrhide = get_post_meta( $prod_id, '_ar_qr_hide'.$suffix, true );
            $hidereset = get_post_meta( $prod_id, '_ar_hide_reset'.$suffix, true );
            $disablezoom = get_post_meta( $prod_id, '_ar_disable_zoom'.$suffix, true );

            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_view_hide'.$suffix, 
                'label'         => __('AR View Button', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Disable the ability for the user to view the model in the AR view? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_view_hide',
                'value' => $arviewhide,
                'custom_attributes' => $readonly
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_rotate'.$suffix, 
                'label'         => __('Auto Rotate', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Turn off the auto rotation on your model? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_rotate',
                'value' => $autorotate,
                'custom_attributes' => $readonly,
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_hide_dimensions'.$suffix, 
                'label'         => __('Dimensions', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Disable the ability for the user to view the dimensions of a model? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_hide_dimensions',
                'value' => $hidedimensions,
                'custom_attributes' => $readonly
                )
            );
            //Prompt
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_prompt'.$suffix, 
                'label'         => __('Interaction Prompt', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Turn off the rotation and cursor prompt on your model? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle',
                'value' => $arprompt,
                'custom_attributes' => $readonly,
                )
            );
            
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_resizing'.$suffix, 
                'label'         => __('Resizing in AR', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Disable the ability for the user to rezise the model in the AR view on Android devices only? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_resizing',
                'value' => $arresizing,
                'custom_attributes' => $readonly
                )
            );
            
            
            
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_qr_hide'.$suffix, 
                'label'         => __('QR Code', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Hide the QR code on the desktop view? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_qr_hide',
                'value' => $qrhide,
                'custom_attributes' => $readonly
                )
            );
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_hide_reset'.$suffix, 
                'label'         => __('Reset Button', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Disable the ability for the user to reset the initial view of a model? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_hide_reset',
                'value' => $hidereset,
                'custom_attributes' => $readonly
                )
            );
            ?>
            <br clear="all">
            <?php
            woocommerce_wp_checkbox( array( 
                'id'            => '_ar_disable_zoom'.$suffix, 
                'label'         => __('Zoom', 'ar-for-woocommerce' ), 
                'desc_tip'          => 'true',
                'description'   => __( 'Disable the ability for the user to zoom in and out? - Optional', 'ar-for-woocommerce' ),
                'class'             => 'ar-ui-toggle _ar_disable_zoom',
                'value' => $disablezoom,
                'custom_attributes' => $readonly
                )
            );
            
        
            if($variation_id == ''){ 
            ?>
             </div> <!-- end of Accordian Panel -->
             <button class="ar_accordian" id="ar_qr_code_acc" type="button"><?php esc_html_e('QR Code Options', 'ar-for-woocommerce' ); echo esc_html($premium_only);?></button>
                        <div id="ar_qr_code_panel" class="ar_accordian_panel">
            <?php $ar_qr_destination = get_post_meta( $prod_id, '_ar_qr_destination_mv'.$suffix, true );?>
            <p class="form-field _ar_qr_dest ">
                 <label for="_ar_qr_image"><?php esc_html_e('QR Code Destination', 'ar-for-woocommerce' );?></label>
                        <select id="_ar_qr_destination_mv<?php echo esc_html($suffix);?>" name="_ar_qr_destination_mv<?php echo esc_html($suffix);?>" class="ar-input" <?php echo  esc_attr($disabled);?>>
                          <option value=""><?php esc_html_e('Use Global Setting', 'ar-for-woocommerce' );?></option>
                          <option value="parent-page" <?php
                            if ($ar_qr_destination=='parent-page'){
                                echo 'selected';
                            }
                          ?>><?php esc_html_e('Parent Page', 'ar-for-woocommerce' );?></option>
                          <option value="model-viewer" <?php
                            if ($ar_qr_destination=='model-viewer'){
                                echo 'selected';
                            }
                          ?>
                          ><?php esc_html_e('AR View', 'ar-for-woocommerce' );?></option>
                          </select></p>
                       
            <?php //Custom QR Image
            $qrimage = get_post_meta( $prod_id, '_ar_qr_image'.$suffix, true );
            
            echo '<div style="position: relative; display: inline-block; width: 100%;">';
            woocommerce_wp_text_input( array(
                'id'                => '_ar_qr_image'.$suffix,
                'label'             => __( 'Custom QR Image', 'ar-for-woocommerce' ),
                'desc_tip'          => 'true',
                'class'             => 'ar_input_field ar_input_upload',
                'description'       => __( 'Upload a JPG or PNG file to use as a custom QR Code Image - Optional. Requires Imagick PHP Extension', 'ar-for-woocommerce' ),
                'value'             => $qrimage,
                'custom_attributes' => $readonly
            ) );
            echo '<input id="upload_qr_image_button" class="button upload_qr_image_button ar-upload-button" type="button" value="'.esc_html(__('Upload','ar-for-woocommerce')).'" '.esc_attr($disabled).'  '.esc_attr($button_atts).' />';
            echo '</div>';
            
            
            
            //Custom QR Code Destination ?>
            <p class="form-field _ar_qr_dest ">
            <label for="_ar_qr_dest<?php echo esc_html($suffix);?>"><?php esc_html_e( 'Custom QR Code URL', 'ar-for-woocommerce' ); ?></label>
            <input type="url" pattern="https?://.+" name="_ar_qr_dest<?php echo esc_html($suffix);?>" id="_ar_qr_dest<?php echo esc_html($suffix);?>" class="regular-text ar_input_field" style="width:300px" value="<?php echo esc_html(get_post_meta( $prod_id, '_ar_qr_dest'.$suffix, true ));?>" <?php echo esc_attr($disabled);?> > </p>
            </div> <!-- end of Accordian Panel -->
            
            <button class="ar_accordian" id="ar_additional_interactions_acc" type="button"><?php esc_html_e('Additional Interactions', 'ar-for-woocommerce' ); echo esc_html($premium_only);?></button>
            <div id="ar_additional_interactions_panel" class="ar_accordian_panel" style="overflow:scroll">
            <p class="form-field _ar_cta_field ">
            <label for="_ar_cta<?php echo esc_html($suffix);?>"><?php esc_html_e( 'Call To Action Button', 'ar-for-woocommerce' ); ?></label><span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Button Displays in 3D Model view and in AR view on Android only', 'ar-for-woocommerce' );?>"></span>
            <input type="text" name="_ar_cta<?php echo esc_html($suffix);?>" id="_ar_cta<?php echo esc_html($suffix);?>" class="regular-text ar_input_field" value="<?php echo esc_html(get_post_meta( $prod_id, '_ar_cta'.$suffix, true ));?>" <?php echo esc_attr($disabled);?> style="width:140px;" > </p>
            <p class="form-field _ar_cta_field ">
            <label for="_ar_cta_url<?php echo esc_html($suffix);?>"><?php esc_html_e( 'Call To Action URL', 'ar-for-woocommerce' ); ?></label>
            <input type="url" pattern="https?://.+" name="_ar_cta_url<?php echo esc_html($suffix);?>" id="_ar_cta_url<?php echo esc_html($suffix);?>" class="regular-text ar_input_field" value="<?php echo esc_html(get_post_meta( $prod_id, '_ar_cta_url'.$suffix, true ));?>" <?php echo esc_attr($disabled);?> > </p>
            
            <p class="form-field _ar_hotspot_field ">
            <label for="_ar_hotspot_text<?php echo esc_html($suffix);?>"><?php esc_html_e( 'Hotspots', 'ar-for-woocommerce' );?></label><span class="woocommerce-help-tip" data-tip="<?php esc_html_e( 'Add your text which can include html and an optional link, click the Add Hotspot button, then click on your model where you would like it placed', 'ar-for-woocommerce' );?>"></span>
            <input type="text" name="_ar_hotspot_text<?php echo esc_html($suffix);?>" id="_ar_hotspot_text<?php echo esc_html($suffix);?>" class="regular-text hotspot_annotation ar_input_field" placeholder="<?php esc_html_e( 'Hotspot Text', 'ar-for-woocommerce' );?>" <?php echo esc_attr($disabled);?> value="<?php echo esc_html(get_post_meta( $prod_id, '_ar_hotspot_text'.$suffix, true ));?>"> <input type="text" name="_ar_hotspot_link<?php echo esc_html($suffix);?>" id="_ar_hotspot_link<?php echo esc_html($suffix);?>" class="regular-text hotspot_annotation ar_input_field" placeholder="<?php esc_html_e( 'Hotspot Link', 'ar-for-woocommerce' );?>" <?php echo esc_attr($disabled);?> value="<?php echo esc_html(get_post_meta( $prod_id, '_ar_hotspot_link'.$suffix, true ));?>">
            <input type="checkbox" name="_ar_hotspot_check<?php echo esc_html($suffix);?>" id="_ar_hotspot_check<?php echo esc_html($suffix);?>" class="regular-text" value="y" style="display:none;" <?php ;?>>
            <input type="button" class="button" data-variation="<?php echo esc_html($variation_id);?>" onclick="enableHotspot()" value="<?php esc_html_e( 'Add Hotspot', 'ar-for-woocommerce' );?>" <?php echo esc_attr($disabled);?> style="float:right;"> </p>
            
            
            
            <?php 
            if (get_post_meta( $prod_id, '_ar_hotspots'.$suffix, true )){
                $_ar_hotspots = get_post_meta( $prod_id, '_ar_hotspots'.$suffix, true );
                $hotspot_count = count($_ar_hotspots['annotation']);
                $hide_remove_btn = '';
                foreach ($_ar_hotspots['annotation'] as $k => $v){
                    if (isset($_ar_hotspots["link"][$k])){
                        $link = $_ar_hotspots["link"][$k];
                    }else{
                        $link ='';
                    }
                    echo '<div id="_ar_hotspot_container_'.esc_html($k).'"><p class="form-field _ar_autoplay_field "><label for="_ar_hotspot">Hotspot '.esc_html($k).'</label><span id="_ar_hotspot_field_'.esc_html($k).'">
                    <input hidden="true" id="_ar_hotspots[data-normal]['.esc_html($k).']" name="_ar_hotspots[data-normal]['.esc_html($k).']" value="'.esc_html($_ar_hotspots['data-normal'][$k]).'">
                    <input hidden="true" id="_ar_hotspots[data-position]['.esc_html($k).']" name="_ar_hotspots[data-position]['.esc_html($k).']" value="'.esc_html($_ar_hotspots['data-position'][$k]).'">
                    <input type="text" class="regular-text hotspot_annotation" id="_ar_hotspots[annotation]['.esc_html($k).']" name="_ar_hotspots[annotation]['.esc_html($k).']" hotspot_name="hotspot-'.esc_html($k).'" value="'.esc_html($v).'">
                    <input type="text" class="regular-text hotspot_annotation" id="_ar_hotspots[link]['.esc_html($k).']" name="_ar_hotspots[link]['.esc_html($k).']" hotspot_link="hotspot-'.esc_html($k).'" value="'.esc_url($link).'" placeholder="Link">
                    </span></div>';
                
                }
            }else{
                $hotspot_count = 0;
                $hide_remove_btn = 'style="display:none;"';
                echo '<div id="_ar_hotspot_container_0"></div>';
            }
            ?>
            <p class="form-field _ar_hotspot_field "><label for="_ar_remove_hotspot"></label> <input id="_ar_remove_hotspot" type="button" class="button" <?php echo esc_attr($hide_remove_btn);?> onclick="removeHotspot()" data-variation="<?php echo esc_html($variation_id);?>" value="Remove last hotspot" <?php echo esc_attr($disabled);?>></p>
            
                <?php } ?>
            </div> <!-- end of Accordian Panel -->  
                        <button class="ar_accordian" id="ar_alternative_acc" type="button"><?php esc_html_e('Alternative Model For Mobile', 'ar-for-woocommerce' ); echo esc_html($premium_only); ?></button><?php
                        if ($disabled!=''){echo ' - '.esc_html(__('Premium Plans Only', 'ar-for-woocommerce'));}
                        ?>
                        <div id="ar_additional_interactions_panel" class="ar_accordian_panel"><br>
                <div class="ar_admin_label"><?php esc_html_e( 'Display a different AR model when viewing on mobile devices', 'ar-for-woocommerce' );?></div>
                <?php 
                $temp_post = $post;
                //Get list of AR Models
                $args = array(
                    'post_type'=> 'armodels',
                    'orderby'        => 'title',
                    'posts_per_page' => -1,
                    'order'    => 'ASC'
                );              
                $the_query = new WP_Query( $args );
                if($the_query->have_posts() ) { 
                    while ( $the_query->have_posts() ) { 
                        $the_query->the_post();
                        $mob_title = get_the_title();
                        $mob_id = get_the_ID();
                        if (($mob_title)){
                            $ar_id_array[$mob_id] = $mob_title;
                        }
                    } 
                    wp_reset_postdata(); 
                }
                $post = $temp_post;
                ?>
                
                <div class="ar_admin_field"><select name="_ar_mobile_id<?php echo esc_html($suffix);?>" id="_ar_mobile_id<?php echo esc_html($suffix);?>" class="ar-input" <?php echo esc_attr($disabled);?>>
                    <option value=''></option>
                    <?php
                    foreach ($ar_id_array as $mob_id => $mob_title){
                        if ($mob_id != $post->ID){
                            echo '<option value="'.esc_html($mob_id).'" '.selected( get_post_meta( $post->ID, '_ar_mobile_id', true ), $mob_id ).'>'.esc_html($mob_title).' (#'.esc_html($mob_id).')</option>';
                        }
                    }
                    ?>
                </select></div>

                <div style="clear:both"><br></div>

                            <div class="ar_admin_label"><?php esc_html_e( 'Display a different AR model when viewing on AR mode', 'ar-for-woocommerce' );?></div>
                            <div class="ar_admin_field"><select name="_ar_alternative_id<?php echo esc_html($suffix);?>" id="_ar_alternative_id<?php echo esc_html($suffix);?>" class="ar-input /*ar-input-wide*/" <?php echo esc_attr($disabled);?>>
                                <option value=''></option>
                                <?php
                                foreach ($ar_id_array as $mob_id => $mob_title){
                                    if ($mob_id != $model_array['id']){
                                        echo '<option value="'.esc_html($mob_id).'" '.selected( get_post_meta( $model_array['id'], '_ar_alternative_id'.$suffix, true ), $mob_id ).'>'.esc_html($mob_title).' (#'.esc_html($mob_id).')</option>';
                                    }
                                }
                                ?>
                            </select></div>
                            <div style="clear:both"></div>
                
                </div> <!-- end of Accordian Panel -->
                <?php 
                if($variation_id == ''){ ?>
                        <button class="ar_accordian" id="ar_element_positions_acc" type="button"><?php esc_html_e('Element Positions and CSS Styles', 'ar-for-woocommerce' );echo esc_html($premium_only);?></button>
                        <div id="ar_additional_interactions_panel" class="ar_accordian_panel">
                        <div style="float:left">
                        <p class="form-field _ar_css_field"><label for="_ar_css"><?php esc_html_e( 'Override Global Settings', 'ar-for-woocommerce' );?></label><input type="checkbox" name="_ar_css_override<?php echo esc_html($suffix);?>" id="_ar_css_override<?php echo esc_html($suffix);?>" class="regular-text" value="1" <?php if (get_post_meta( $prod_id, '_ar_css_override'.$suffix, true )=='1'){echo 'checked';$hide_custom_css='';}else{$hide_custom_css='/*style="display:none;"*/';} echo esc_attr($disabled);?>> </p>
                        </div>
                        <div style="float:left;padding-top:10px">
                        <input type="button" class="button" data-variation="<?php echo esc_html($variation_id);?>" onclick="importCSS()" value="<?php esc_html_e( 'Import Global Settings', 'ar-for-woocommerce' );?>" <?php echo esc_attr($disabled);?>><br  clear="all"><br>
                        </div>
                        <br clear="all">
                        <div id="ar_custom_css_div" <?php echo esc_attr($hide_custom_css);?>>
                            
                            <?php //CSS Positions
                            $ar_css_positions = get_post_meta( $prod_id, '_ar_css_positions'.$suffix, true );
                            foreach ($ar_css_names as $k => $v){
                                ?>
                                <div>
                                  <div style="width:160px;float:left;"><strong>
                                      <?php echo esc_html($k);?> </strong></div>
                                  <div style="float:left;"><select id="_ar_css_positions[<?php echo esc_html($k);?>]" name="_ar_css_positions[<?php echo esc_html($k);?>]" <?php echo  esc_attr($disabled);?>>
                                      <option value="">Default</option>
                                      <?php 
                                      foreach ($ar_css_styles as $pos => $css){
                                        echo '<option value = "'.esc_attr($pos).'"';
                                        if (isset($ar_css_positions[$k])){
                                            if ($ar_css_positions[$k]==$pos){echo ' selected';}
                                        }
                                        echo '>'.esc_html($pos).'</option>';
                                      }?>
                                      
                                      </select></div>
                                </div>
                                <br  clear="all">
                                <br>
                            <?php
                            }
                            ?>
                            <div>
                            <div style="width:160px;float:left;"><strong>
                              <?php
                                $ar_css = get_post_meta( $prod_id, '_ar_css'.$suffix, true );
                                $ar_css_import_global='';
                                if (get_option('ar_css')!=''){
                                    $ar_css_import_global = get_option('ar_css');
                                }
                                $ar_css_import=ar_curl(esc_url( plugins_url( "assets/css/ar-display-custom.css", __FILE__ ) ));
                                esc_html_e('CSS Styling', 'ar-for-woocommerce' );
                                ?>
                                </strong>
                            </div>
                      <div style="float:left;"><textarea id="_ar_css<?php echo esc_html($suffix);?>" name="_ar_css<?php echo esc_html($suffix);?>" style="width: 400px; height: 200px; margin-bottom:20px" <?php echo  esc_attr($disabled);?>><?php echo esc_attr($ar_css); ?></textarea></div>
                    </div>
                </div>
            </div> <!-- end of Accordian Panel --> 
            <?php } ?>
            </div>
            <?php
          /* Display the 3D model if it exists */

            $ar_plugin = new AR_Plugin();
            
            $hide_ar_view = '';
            if (get_post_meta($model_array['id'], '_glb_file'.$suffix, true )==''){ 
                //$hide_ar_view = 'display:none;';
            }
            echo '<div class="ar_admin_viewer" id="ar_admin_model_'.esc_html($model_array['id']).'" style="'.esc_html($hide_ar_view).'"><div id="ar_admin_modelviewer">';
            echo '<div style="width: 100%; border: 1px solid #f8f8f8;">';
            echo do_shortcode('[ar-display id="'.$model_array['id'].'"]');
            echo '</div>'; 
            $ar_camera_orbit = get_post_meta( $prod_id, '_ar_camera_orbit'.$suffix, true );?>
            

            <?php if($variation_id == ''){ ?>
            <button id="downloadPosterToBlob" onclick="downloadPosterToDataURL('<?php echo esc_html($model_array['id']); ?>')" class="button" type="button" style="margin:10px">Set Featured Image</button>
            <input type="hidden" id="_ar_poster_image_field" name="_ar_poster_image_field">
            <input id="camera_view_button" class="button" type="button" style="float:right;margin: 10px" value="<?php esc_html_e( 'Set Current Camera View as Initial', 'ar-for-woocommerce' );?>" <?php echo esc_attr($disabled);?> />
            <div id="_ar_camera_orbit_set" style="float:right;margin: 10px;display:none"><span style="color:green;margin-left: 7px; font-size: 19px;">&#10004;</span></div><input id="_ar_camera_orbit" name="_ar_camera_orbit" type="text" value="<?php echo esc_html($ar_camera_orbit);?>" style="display:none;"><br clear="all" style="float:right;">
            
           
            
            
            <?php 
            }
            echo '</div></div>';
          
        
           
           } 
           if($plan_check!='Premium') { 
                echo '</div>'; 
            //close the div that disables mouse clicking 
            } 
}

function ar_model_js($model_array, $variation_id=''){
    global $ar_css_import_global, $hotspot_count,$jsArray;
    $ar_css_import = '';

    $suffix = ($variation_id != '') ? "_var_".$variation_id : '';

    ?>
    <script>
        
    //Rotation Limits Compass
            const modelViewer<?php echo esc_html($model_array['id']); ?> = document.querySelector('#model_<?php echo esc_html($model_array['id']); ?>');
        //Animation Selector
         
        //Custom CSS Importing
        function importCSS(){
            var css_content = '<?php if ($ar_css_import_global!=''){ echo esc_js(ar_encodeURIComponent($ar_css_import_global));}else{echo esc_js(ar_encodeURIComponent($ar_css_import));}?>';
            document.getElementById('_ar_css').value = decodeURI(css_content);
            <?php 
            $ar_css_positions = get_option('ar_css_positions');
            if (is_array($ar_css_positions)){
                foreach ($ar_css_positions as $k => $v){
                      echo "document.getElementById('_ar_css_positions[".esc_html($k)."]').value = '".esc_html($v)."';
                      ";
                }
            }
            ?>
        }
        
        document.getElementById('_ar_css_override').addEventListener('change', function() {
            var element = document.getElementById("ar_custom_css_div");
            if (document.getElementById("_ar_css_override").checked == true){
                element.style.display = "block";
            }else{
                element.style.display = "none";
            }
        });
        
    
    </script>
    <!-- HOTSPOTS -->
    
    <?php load_model_viewer_components_js(); ?>


    <script>
        var hotspotCounter = <?php echo $hotspot_count ? esc_html($hotspot_count) : '0'; ?>;
        function addHotspot(MouseEvent) {
            //var _ar_hotspot_check = document.getElementById('_ar_hotspot_check').value;
            if (document.getElementById("_ar_hotspot_check").checked != true){
            return;
                
            }
            var inputtext = document.getElementById('_ar_hotspot_text').value;
        
            // if input = nothing then alert error if it isnt then add the hotspot
            if (inputtext == ""){
                alert("<?php esc_html_e( 'Enter hotspot text first, then click the Add Hotspot button.', 'ar-for-woocommerce' );?>");
                return;
            }else{
                var inputlink = document.getElementById('_ar_hotspot_link').value;
                if (inputlink){
                    inputtext = '<a href="'+inputlink+'" target="_blank">'+inputtext+'</a>';
                }
                const viewer = document.querySelector('#model_<?php echo esc_html($model_array['id']); ?>');
            
                const x = event.clientX;
                const y = event.clientY;
                const positionAndNormal = viewer.positionAndNormalFromPoint(x, y);
                
                // if the model is not clicked return the position in the console
                if (positionAndNormal == null) {
                    console.log('no hit result: mouse = ', x, ', ', y);
                    return;
                }
                const {position, normal} = positionAndNormal;
                
                // create the hotspot
                const hotspot = document.createElement('button');
                hotspot.slot = `hotspot-${hotspotCounter ++}`;
                hotspot.classList.add('hotspot');
                hotspot.id = `hotspot-${hotspotCounter}`;
                hotspot.dataset.position = position.toString();
                if (normal != null) {
                    hotspot.dataset.normal = normal.toString();
                }
                viewer.appendChild(hotspot);
                // adds the text to last hotspot
                var element = document.createElement("div");
                element.classList.add('annotation');
                element.innerHTML = inputtext;
                document.getElementById(`hotspot-${hotspotCounter}`).appendChild(element);
                
                //Add Hotspot Input fields
                var hotspot_container = document.getElementById(`_ar_hotspot_container_${hotspotCounter -1}`);
                
            
                hotspot_container.insertAdjacentHTML('afterend', `<div id="_ar_hotspot_container_${hotspotCounter}"><p class="form-field _ar_autoplay_field "><label for="_ar_animation">Hotspot ${hotspotCounter}</label><span class="ar_admin_field" id="_ar_hotspot_field_${hotspotCounter}">`);
                
                var hotspot_fields = document.getElementById(`_ar_hotspot_field_${hotspotCounter}`);
                var inputList = document.createElement("input");
                inputList.setAttribute('type','text');
                inputList.setAttribute('class','regular-text hotspot_annotation');
                inputList.setAttribute('id',`_ar_hotspots[link][${hotspotCounter}]`);
                inputList.setAttribute('name',`_ar_hotspots[link][${hotspotCounter}]`);
                inputList.setAttribute('hotspot_name',`hotspot-${hotspotCounter}`);
                inputList.setAttribute('value',document.getElementById('_ar_hotspot_link').value);
                inputList.setAttribute('placeholder','Link');
                hotspot_fields.insertAdjacentElement('afterend', inputList);   
                
                
                var inputList = document.createElement("input");
                inputList.setAttribute('type','text');
                inputList.setAttribute('class','regular-text hotspot_annotation');
                inputList.setAttribute('id',`_ar_hotspots[annotation][${hotspotCounter}]`);
                inputList.setAttribute('name',`_ar_hotspots[annotation][${hotspotCounter}]`);
                inputList.setAttribute('hotspot_name',`hotspot-${hotspotCounter}`);
                inputList.setAttribute('value',document.getElementById('_ar_hotspot_text').value);
                hotspot_fields.insertAdjacentElement('afterend', inputList);
                
                var inputList = document.createElement("input");
                inputList.setAttribute('hidden','true');
                inputList.setAttribute('id',`_ar_hotspots[data-position][${hotspotCounter}]`);
                inputList.setAttribute('name',`_ar_hotspots[data-position][${hotspotCounter}]`);
                inputList.setAttribute('value',hotspot.dataset.position);
                hotspot_fields.insertAdjacentElement('afterend', inputList);
                
                var inputList = document.createElement("input");
                inputList.setAttribute('hidden','true');
                inputList.setAttribute('id',`_ar_hotspots[data-normal][${hotspotCounter}]`);
                inputList.setAttribute('name',`_ar_hotspots[data-normal][${hotspotCounter}]`);
                inputList.setAttribute('value',hotspot.dataset.normal);
                hotspot_fields.insertAdjacentElement('afterend', inputList);
                
                hotspot_fields.insertAdjacentHTML('afterend', '</span></p></div>');
                var additionalPanel = document.getElementById("ar_additional_interactions_panel");

                // Check if the element exists
                if (additionalPanel) {
                    // Get the current height and add 100px to it
                    var newHeight = additionalPanel.offsetHeight + 100;
                
                    // Set the new height to the element
                    //additionalPanel.style.height = newHeight + "px";
                    additionalPanel.style.maxHeight = newHeight + "px";
                }
                //Reset hotspot text box and checkbox
                document.getElementById('_ar_hotspot_text').value = "";
                document.getElementById('_ar_hotspot_link').value = "";
                document.getElementById("_ar_hotspot_check").checked = false;
                
                //Show Remove Hotspot button
                document.getElementById('_ar_remove_hotspot').style = "display:block;";
            }
        }
        function enableHotspot(event){
            var suffix = '';
            //if(event.target.hasAttribute('data-variation')){
            //    suffix = '_var_' + event.target.getAttribute('data-variation');
            //}
            var inputtext = document.getElementById('_ar_hotspot_text' + suffix).value;
            if (inputtext == ""){
                alert("<?php esc_html_e( 'Enter hotspot text first, then click Add Hotspot button.', 'ar-for-woocommerce' );?>");
                return;
            }else{
                document.getElementById("_ar_hotspot_check").checked = true;
            }
        }
        function removeHotspot(){
            var el = document.getElementById(`_ar_hotspot_container_${hotspotCounter}`);
            var el2 = document.getElementById(`hotspot-${hotspotCounter}`);
            if (el == null){
                alert("No hotspots to delete");
            }else{
                hotspotCounter --;
                el.remove(); // Removes the last added hotspot fields
                el2.remove(); // Removes the last added hotspot from model
            }
        }
    </script>
    <script nonce="<?php esc_html(wp_create_nonce('set_ar_featured_image')); ?>">
            
            //Save screenshot of model
            function downloadPosterToDataURL() {
                const modelViewer = document.querySelector('#model_<?php echo esc_html($model_array['id']); ?>');
                var btn = document.getElementById("downloadPosterToBlob");
                btn.innerHTML = 'Creating Image';
                btn.disabled = true;
                const url = modelViewer.toDataURL("image/png").replace("image/png", "image/octet-stream");
                const a = document.createElement("a");
                document.getElementById("_ar_poster_image_field").value=url;
                var xhr = new XMLHttpRequest();
                //document.getElementById("nonce").value="<?php wp_create_nonce('set_ar_featured_image'); ?>"
                var data = new FormData();
                data.append('post_ID', document.getElementById("post_ID").value);
                
                if(document.getElementById("original_post_title")){
                    data.append('post_title', document.getElementById("original_post_title").value);
                } else if(document.getElementsByClassName("wp-block-post-title")) {
                    data.append('post_title', document.getElementsByClassName("wp-block-post-title")[0].value);
                } else {
                    data.append('post_title','armodel-' + document.getElementById("post_ID").value);
                }
                data.append('_ar_poster_image_field',document.getElementById("_ar_poster_image_field").value);
                data.append('action',"set_ar_featured_image");
                data.append('nonce',"<?php echo esc_html(wp_create_nonce('set_ar_featured_image')); ?>");
                //data.nonce = "<?php wp_create_nonce('set_ar_featured_image'); ?>";
               // console.log(data);
                xhr.open("POST", wpApiSettings.root + "arforwp/v2/set_ar_featured_image/", true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader("X-WP-Nonce", wpApiSettings.nonce);
                /*xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var attachmentID = xhr.responseText; 
                    wp.media.featuredImage.set( attachmentID );
                   }
                };*/

                //convert to json
                var object = {};
                data.forEach(function(value, key){
                    object[key] = value;
                });
                var json = JSON.stringify(object);


                xhr.onload = function () { 
                    var attachmentID = xhr.responseText; 
                    wp.media.featuredImage.set( attachmentID );
                    btn.innerHTML = 'Set AR Poster Image';
                    btn.disabled = false;
                }
                
                xhr.send(json);
                return false;
            }
        </script>
    <?php
    
    //Output Upload Choose AR Model Files Javascript
    //echo ar_upload_button_js($model_array['id'], $variation_id);
}
add_action( 'woocommerce_variation_header', function( \WP_Post $variation ) {

            $variation = wc_get_product( $variation->ID );
            $variation_id = $variation->get_id();
            echo '<span class="ardisplay_options" style="padding-left: 20px"> <a style="text-decoration:none;" id="ar_variation_button_'.esc_html($variation_id).'" > AR Model</a></span>';
            echo '<div id="ar_variation_'.esc_html($variation_id).'" style="display:none;">
                    <div class="ar_variation_view">
                        ';
                            ar_woo_tab_panel($variation_id, $variation_id);
                            echo '
                        <div class="ar-popup-btn-container hide_on_devices"><button type="button" id="arqr_close_'.esc_html($variation_id).'_pop" class="ar_popup-btn hide_on_devices" style="cursor: pointer"  onclick="document.getElementById(\'ar_variation_'.esc_html($variation_id).'\').style.display = \'none\';"><img src="'.esc_url( plugins_url( "assets/images/close.png", __FILE__ ) ).'" class="ar-fullscreen_btn-img"></button></div>
                    </div>
                </div>';
                ?>
                <script>
                //let modelFields = [];
                    
                    //console.log(modelFields);

                ( function($) {
                 
                    $("#ar_variation_button_<?php echo esc_html($variation_id);?>").click(function(){
                      $("#ar_variation_<?php echo esc_html($variation_id);?>").toggle();
                    });
                    
                    //console.log(modelFields);
                    
                  
                } ) ( jQuery ); 
                </script>
    <?php
            

        } );



function ar_woo_admin_scripts(){
  wp_enqueue_script('jquery');
  wp_enqueue_script('media-upload');
}
?>