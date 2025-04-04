<?php
/**
 * AR Display
 * AR For WordPress
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH'))
    exit;

$ar_licence_key = get_option('ar_licence_key');

if ($ar_licence_key==''){
    ?>
<div class="notice notice-success is-dismissible ar-update-notice" style="background-color: #f7f9fc; border: 1px solid #e1e7f0; border-radius: 8px; padding: 20px;">
    <h3 style="font-size: 1.5em; font-weight: bold; color: #2d2d2d;"><?php echo esc_html($plugin_info['name']). esc_html( ar_output(' has been updated to version ', $ar_plugin_id)) . esc_html($plugin_info['version']) . wp_kses('! Check out the new features and improvements.', ar_allowed_html()); ?></h3>
    
    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 20px;">
        <!-- Left Section -->
        <div style="flex: 1; margin-right: 20px; max-width: 45%; min-width: 300px; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <h4 style="color: #13383e; font-size: 1.5em; font-weight: bold;margin: 10px 0px;">🎉 Unlock Premium Features & Take Your AR Experience to the Next Level! 🚀</h4>
            <p><strong style="color: #333;font-size: 16px;">Version 7.0+ is here, and it’s packed with powerful new features!</strong></p>
            <ul style="list-style-type: disc; padding-left: 20px; color: #444;">
                <li><strong>Unlimited 3D Models</strong> – Add as many models as you want!</li>
                <li><strong>AR Model Shop</strong> – Import 3D models directly into your site.</li>
                <li><strong>AR Gallery</strong> – Show your jpg images as a 3D model in AR.</li>
                <li><strong>User Uploads</strong> – Let users drag and drop their own models or images into the viewer.</li>
                <li><strong>Dynamic Shortcodes</strong> – Display models easily with the [<code>ardisplay</code>] shortcode.</li>
                <li><strong>Model Protection</strong> – Encrypt URLs to restrict model downloads.</li>
                <li><strong>Custom Model Controls</strong> – Adjust exposure, shadow softness, scale, and more for each model.</li>
                <li><strong>Advanced Settings</strong> – Configure AR restrictions, model rotation, and interaction prompts.</li>
            </ul>
        </div>

        <!-- Right Section -->
        <div style="flex: 1; max-width: 45%; min-width: 300px; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
            <center>
                <h4 style="color: #13383e; font-size: 1.5em; font-weight: bold;margin: 10px 0px;">🎁 Limited Time Offer: Get 50% OFF Your First 6 Months of Premium!</h4>
                <p style="color: #333;font-size: 16px;">Unlock <strong>unlimited models</strong> and <strong>advanced AR features</strong> today. Use coupon code:</p>
                <h3 style="font-size: 2em; font-weight: bold; color: #f37a23; margin: 20px 0;">AR50OFF or AR50OFFANNUAL</h3>
                <p style="color: #333; margin-bottom: 20px;font-size: 16px;">at checkout to get 50% off for the first 6 months or 6 months free on an Annual Subscription!</p>
                <a href="https://augmentedrealityplugins.com/product-category/<?php echo esc_html($ar_plugin_id); ?>/" target="_blank" class="ar_model_shop_btn button" style="font-size: 16px;">Subscribe to Premium Now</a>
                <p style="color: #333; margin-top: 20px;font-size: 16px;"><a href="https://augmentedrealityplugins.com" target="_blank">augmentedrealityplugins.com</a></p>
            </center>
        </div>
    </div>
</div>
<?php }else{ ?>
<div class="notice notice-success is-dismissible ar-update-notice">
    <h3><?php echo esc_html($plugin_info['name']). esc_html( ar_output(' has been updated to version ', $ar_plugin_id)) . esc_html($plugin_info['version']) . wp_kses('! Check out the new features and improvements.', ar_allowed_html()); ?></h3>
    <?php 
    $limit = 1;
    $icons_only = 1;
    echo ar_changelog_retrieve($limit, $icons_only);?>
    <p></p>
</div>
<?php } ?>