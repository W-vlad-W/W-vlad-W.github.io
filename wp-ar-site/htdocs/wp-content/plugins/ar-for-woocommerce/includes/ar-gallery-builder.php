<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if(!function_exists('ar_gallery_builder_form')){
    function ar_gallery_builder_form($model_array){
        
        if (!isset($model_array['model_id'])){
            $model_array['model_id']='';
        }
        $frame_type = '';
        $ar_frame_color = '';
        $ar_frame_opacity = '';
        $asset_builder_style = '';
        if ($model_array['model_id'] == 'user_upload'){
            $asset_builder_style = 'ar-user-upload-panel';
        }
        if ((!isset($ar_gallery_builder_message))OR($ar_gallery_builder_message =='')){
            $ar_gallery_builder_message = 'Upload an image file in Jpg or Png format or a 3D Model Gltf/Glb file.';
        }

        if ($model_array['model_id'] == 'user_upload'){
           $model_array['id'] = 'user_upload'; 
        }

    

        ?>
        <div id="asset_builder" class = "<?php echo esc_html($asset_builder_style); ?>">
            <?php if ($model_array['model_id'] != 'user_upload'){ ?>          
            <div class="asset_builder_img" style="max-width:50%;" onclick="toggleMaxWidth(this)">
                <img src="<?php echo esc_url(plugins_url('assets/images/wall_art_guide.jpg', dirname(__FILE__))); ?>" style="max-width:100%; max-height:200px;">
            </div>
            <?php } ?>
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
            $asset_image = plugins_url( "assets/images/ar_asset_icon.jpg", dirname(__FILE__) );
            if (($model_array['model_id'] != 'user_upload')AND(get_post_meta( $model_array['id'], '_glb_file', true )!='')){
                $glb_file = sanitize_text_field(get_post_meta( $model_array['id'], '_glb_file', true ));
            
                // Parse the URL to get its components
                $url_components = wp_parse_url($glb_file);
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

            $nodisplay = '';
            for($i = 0; $i<1; $i++) { //Previously 10 - Cube will require 6
            if ($i>0){$nodisplay = ' class="nodisplay"';}
            ?>
               <div  id="texture_container_<?php echo esc_html($i)?>" <?php echo esc_html($nodisplay);?> style="padding: 0px 20px 10px 0px; float: left;">
                 <!--<p><strong><?php echo wp_kses('Image File', ar_allowed_html());?></strong> <span id="ar_asset_builder_texture_done"></span><br>-->
                <img src="<?php echo esc_url( $asset_image ); ?>" id="asset_thumb_img" style="max-heigth:200px;padding-top:30px"  class="ar_file_icons" onclick="document.getElementById('upload_asset_texture_button_<?php echo esc_html($i); ?>').click();">
                <span id="texture_<?php echo esc_html($i)?>">
                <input type="hidden" name="_asset_texture_file_<?php echo esc_html($i); ?>" id="_asset_texture_file_<?php echo esc_html($i); ?>" class="regular-text" value="<?php if (isset($url)){echo esc_url($url);}?>"> <input id="upload_asset_texture_button_<?php echo esc_html($i); ?>" class="upload_asset_texture_button_<?php echo esc_html($i); ?> upload_asset_texture_button button nodisplay" type="button" value="<?php echo wp_kses('Upload', ar_allowed_html());?>" /> 
                <?php if ($model_array['model_id'] != 'user_upload'){ ?>
                <img src="<?php echo esc_url( plugins_url( "assets/images/delete.png", dirname(__FILE__) ) );?>" style="width: 15px;vertical-align: middle;cursor:pointer; padding-top:10px" onclick="document.getElementById('_asset_texture_file_<?php echo esc_html($i); ?>').value = '';document.getElementById('ar_asset_builder_texture_done').innerHTML = '';document.getElementById('asset_thumb_img').src = '<?php echo esc_url( plugins_url( "assets/images/ar_asset_ad_icon.jpg", dirname(__FILE__) ) ); ?>';">
                <?php } ?>
                <input type="text" name="_asset_texture_id_<?php echo esc_html($i); ?>" id="_asset_texture_id_<?php echo esc_html($i); ?>" class="nodisplay"></span></p>
                
                </div>
            
            <?php }
            ?><input type="text" name="_asset_texture_flip" id="_asset_texture_flip" class="nodisplay">

            
            
            <input type="hidden" name="_ar_asset_file" id="_ar_asset_file" class="regular-text" value="">
            <input type="hidden" name="_ar_asset_id" id="_ar_asset_id" class="regular-text" value="<?php if ($model_array['model_id'] != 'user_upload'){ echo esc_html($model_array['id']); } ?>">
            <input type="hidden" name="_ar_asset_url" id="_ar_asset_url" class="regular-text" value="<?php echo esc_url(site_url('/wp-content/plugins/'.$ar_plugin_id.'/includes/ar-gallery.php'));?>">
            <input type="hidden" name="_ar_asset_ratio" id="_ar_asset_ratio" value="<?php if (isset($ratio)){echo esc_html($ratio); } ?>">
            

            <div style="min-height:100px;padding-top:10px;float: left;">
                <!---<div id="ar_asset_builder_message" class="ar_asset_builder_message"><?php echo wp_kses($ar_gallery_builder_message, ar_allowed_html()); ?></div>-->
             <div id="ar_asset_size_container" class="ar_asset_size_container" <?php if (!isset($ratio)){echo ' style="display:none;"'; } ?>>
                  <div style="float:left;padding:5px;display:none">
                      <strong><?php echo wp_kses( 'Orientation', ar_allowed_html());?></strong><br>
                      <select name="_ar_asset_orientation" id="_ar_asset_orientation">
                        <option value="portrait" <?php echo (isset($orientation) && $orientation == 'portrait') ? 'selected' : ''; ?>>Portrait</option>
                        <option value="landscape" <?php echo (isset($orientation) && $orientation == 'landscape') ? 'selected' : ''; ?>>Landscape</option>
                    </select>
                  </div>
                    <?php 
                    $ratio = isset($query_parts['ratio']) ? $query_parts['ratio'] : null;
                    // Define the array of options
                    $ratio_options = array(
                        '1.0'     => '1:1',
                        '1.4142'  => 'A4-A1',
                        '1.5'     => '2:3',
                        '1.25'    => '4:5',
                        '1.33'    => '3:4'
                    );
                        
                    $atts_hide = '';
                    if (isset($atts['ratio'])){
                        if ($atts['ratio'] == 'false'){
                            $atts_hide = 'display:none;'; 
                        }elseif(in_array($atts['ratio'], $ratio_options, true)){
                            $ratio = null;
                            foreach ($ratio_options as $key => $value) {
                                if ($value === $atts['ratio']) {
                                    $ratio = $key;
                                    break; // Exit loop once the match is found
                                }
                            }
                        }
                    } 
                    // If $ratio is set to '1', set it to '1.0'
                    if ($ratio == '1') {
                        $ratio = '1.0';
                    }
                    if ((isset($atts['hide_all']))AND($atts['hide_all'] == 'true')){
                        $atts_hide = 'display:none;'; 
                    }
                    ?>
                    <div style="float:left;padding:5px;<?php echo esc_html($atts_hide); ?>">
                     <strong><?php echo wp_kses('Image Ratio', ar_allowed_html());?></strong><br>
                        <select id="_ar_asset_ratio_select">
                        <?php
                        // Loop through the array and generate the <option> elements
                        foreach ($ratio_options as $value => $label) {
                            // Check if the current value matches the selected ratio
                            $selected = ($value == $ratio) ? ' selected' : '';
                            echo "<option id='ar_asset_ratio_options' value='".esc_html($value),"'".esc_html($selected).">".esc_html($label)."</option>";
                        }
                        ?>
                        </select>
                      
                  </div>
                  <?php 
                    $atts_hide = '';
                    $ar_asset_default_size = '';
                    if (isset($atts['size'])){
                        if ($atts['size'] == 'false'){
                            $atts_hide = 'display:none;';
                        }elseif (is_numeric($atts['size']) && $atts['size'] >= 1 && $atts['size'] <= 3) {
                            $ar_asset_default_size  = round($atts['size'], 2); // Round to 2 decimal place 
                        }
                    }
                    if ((isset($atts['hide_all']))AND($atts['hide_all'] == 'true')){
                        $atts_hide = 'display:none;'; 
                    }
                    ?>
                  <div style="float:left;padding:5px;<?php echo esc_html($atts_hide); ?>">
                      <input type="hidden" name="_ar_asset_default_size" id="_ar_asset_default_size" class="regular-text" value="<?php echo wp_kses( $ar_asset_default_size, ar_allowed_html());?>">
                      <strong><?php echo wp_kses( 'Print Size', ar_allowed_html());?></strong><br>
                      <select id="ar_asset_size">
                            <option  id="ar_asset_size_options" value="-1" selected="selected"></option>
                      </select>
                  </div>
                  <br clear="all">
                  <?php 
                    $atts_hide = '';
                    if (isset($atts['frame'])){
                        if ($atts['frame'] == 'false'){
                            $atts_hide = 'display:none;'; 
                        }elseif (isset($atts['frame']) && in_array($atts['frame'], ['none', 'mounted', 'framed'], true)) {
                            $frame_type = $atts['frame'] === 'none' ? 0 : ($atts['frame'] === 'mounted' ? 1 : 2);
                        }
                    }
                    if ((isset($atts['hide_all']))AND($atts['hide_all'] == 'true')){
                        $atts_hide = 'display:none;'; 
                    }
                    ?>
                  <div style="float:left;padding:5px;<?php echo esc_html($atts_hide); ?>">
                      <strong><?php echo wp_kses( 'Framed', ar_allowed_html());?></strong><br>
                      <?php if ($model_array['model_id'] != 'user_upload'){$frame_type= get_post_meta( $model_array['id'], '_ar_framed', true ); }?>
                      <select id="_ar_framed" name="_ar_framed" >
                          <option value="0">None</option>
                          <option <?php if ($frame_type == '1'){ echo ' selected';} ?> value="1">Mounted</option>
                          <option <?php if ($frame_type == '2'){ echo ' selected';} ?> value="2">Framed</option>
                      </select>
                     </div>
                  <?php 
                    $atts_hide = '';
                    if (isset($atts['color'])){
                        if ($atts['color'] == 'false'){
                            $atts_hide = 'display:none;'; 
                        }elseif (isset($atts['color']) && preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $atts['color'])) {
                            $ar_frame_color = $atts['color'];
                        }
                    }
                    if ((isset($atts['hide_all']))AND($atts['hide_all'] == 'true')){
                        $atts_hide = 'display:none;'; 
                    } ?>
                  <div style="float:left;padding:5px;<?php echo esc_html($atts_hide); ?>">
                      <strong><?php echo wp_kses( 'Color', ar_allowed_html());?></strong><br>
                      
                    <?php if ($model_array['model_id'] != 'user_upload'){ $ar_frame_color = get_post_meta( $model_array['id'], '_ar_frame_color', true ); }?>
                    <input id="_ar_frame_color" name="_ar_frame_color" type="text" value="<?php echo esc_html($ar_frame_color); ?>">
                    
                    <input id="_ar_wpnonce" name="_ar_wpnonce" type="hidden" value="<?php  echo esc_html(wp_create_nonce( 'ar_secure_nonce' )); ?>">
                   </div>
                 <?php 
                    $atts_hide = '';
                    if (isset($atts['opacity'])){
                        if ($atts['opacity'] == 'false'){
                            $atts_hide = 'display:none;'; 
                        }elseif(is_numeric($atts['opacity']) && $atts['opacity'] >= 0 && $atts['opacity'] <= 1){
                            $ar_frame_opacity = round($atts['opacity'], 1); // Round to 1 decimal place
                        }
                    }
                    if ((isset($atts['hide_all']))AND($atts['hide_all'] == 'true')){
                        $atts_hide = 'display:none;'; 
                    } ?>
                  <div style="float:left;padding:5px;<?php echo esc_html($atts_hide); ?>">
                      <strong><?php echo wp_kses( 'Opacity', ar_allowed_html());?></strong><br>
                      <?php if ($model_array['model_id'] != 'user_upload'){ $ar_frame_opacity = get_post_meta( $model_array['id'], '_ar_frame_opacity', true ); } ?>
                        <select id="_ar_frame_opacity" name="_ar_frame_opacity">
                            <?php
                            for ($i = 1; $i >= 0; $i -= 0.1) {
                                $selected = ($ar_frame_opacity == number_format($i, 1)) ? 'selected="selected"' : '';
                                echo '<option value="' . esc_attr(number_format($i, 1)) . '" ' . esc_html($selected) . '>' . esc_html(number_format($i, 1)) . '</option>' . PHP_EOL;
                            }
                            ?>
                        </select>
                  </div>
                  <br clear="all">
              </div>
                
                <span id="ar_asset_builder_submit_container" style="display:none;">
                    <br clear="all"><!--<br>
                    <button id = "ar_asset_builder_submit" class="button ar_admin_button" >Build Asset</button>-->
                    <strong><span style="color:#f37a23"><?php echo wp_kses( 'Please Publish/Update your post to build the Gallery Asset. You may need to refresh your browser once updated to ensure the latest files are displayed.', ar_allowed_html());?></span></strong>
                    <br><br>
                    
                </span>
                </div>
            </div>
        </div>
<?php
    }
}
