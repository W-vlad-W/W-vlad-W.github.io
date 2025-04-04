=== AR for WordPress ===
Contributors: webandprint  
Donate link: https://webandprint.design  
Tags: Augmented Reality, AR, 3D, Model Viewer, 3D Model  
Requires at least: 4.6  
Tested up to: 6.7.2
Stable tag: 7.9
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

**Augmented Reality for WordPress** lets you showcase 3D models in an interactive viewer and AR on iOS and Android, with no app downloads needed.

== Description ==

### **Experience the Future with Augmented Reality for WordPress Plugin!**

Transform your website into an immersive 3D experience with our all-in-one solution. Captivate your audience by showcasing 3D models in an interactive viewer and Augmented Reality (AR) directly in web browsers, available on both iOS and Android. No app downloads required!

#### **Key Features**:
- **3D Gallery Builder**: Display your 3D artwork in Augmented Reality with just a photo!
- **User Upload**: Allow users to upload their own models or images and view them in AR on the fly.
- **AR Magic**: Supports GLB, GLTF, USDZ, and Reality model files for a seamless 3D viewing experience in the browser and AR.
- **Try Before You Buy**: Let users visualize products in 3D within their environment. This immersive experience increases conversion rates, reduces returns, and boosts profitability.

**Easy-to-use and feature-rich** – With seamless integration into popular builders like **Gutenberg** and **Elementor**, enjoy a hassle-free experience and the flexibility of API integrations.

### **Features**

- **View 3D Models in AR**: No app required, view models in both 3D and AR modes.
- **Zoom Functionality**: Users can zoom in and out to get a closer look at the models.
- **Simple Interface**: Easy-to-use interface designed for everyone.
- **Responsive Design**: Desktop view shows 3D models, while mobile/tablet devices support both 3D and AR views.
- **Model Placement**: Place models on horizontal (floor) or vertical (wall) surfaces in AR mode.
- **QR Code Support**: Display a QR code in desktop view, allowing users to scan and switch to an AR capable device.
- **Free Version**: The free version is limited to 1 model only.

== **Premium Subscription** ==  
**Unlock premium features and go beyond with our [AR Plugin Premium Subscription](https://augmentedrealityplugins.com)**

#### **Premium Features**:
- **Unlimited 3D Models**: Add as many models as you like.
- **AR Shop**: Buy and import 3D models into your site.
- **User Upload**: Let users upload their own models or images and display them instantly.
- **Dynamic Shortcode**: Use the `[ardisplay]` shortcode to display your product models, including variations.
- **Encrypted URLs**: Protect models with encrypted URLs, restricting direct downloads.
- **AR Gallery Shortcode**: Display featured images of your posts or WooCommerce products as 3D models.
- **Advanced Model Settings**: Control exposure, shadow softness, scale, field of view, zoom restraints, and more.
- **Supports Model Variants**: Display different variations of your models.
- **Background and Environment Images**: Add background and environment images for a complete AR experience.
- **AR Restrictions**: Prevent resizing and restrict model rotation to maintain accurate scaling in AR.
- **Hotspot Annotations**: Add clickable hotspots to your models for additional information.
- **Animation Controls**: Enable animation play/pause button and autoplay options.
- **Thumbnails for Multiple Models**: Display thumbnails for multiple models in one viewer window (users can only view one model at a time).
- **API Integration**: Send and receive JSON data for your models.
- **Hide AR Button**: Option to restrict users to only 3D view.
- **Customizable AR View**: Option to hide the QR code, show AR button as an image or text, and open AR models directly from a QR code.

== **How to Use the Plugin** ==

1. [Watch the tutorial video](https://www.youtube.com/watch?v=jO7wR-meeGI) to get started.
2. [Step-by-step guide](https://augmentedrealityplugins.com/support/#getstarted) for installation and usage.

== **See it in Action** ==

- **[Demo Site](https://augmentedrealityplugins.com)**: Experience the plugin in action and see the powerful AR features at work.

== **Sample 3D Files and Resources** ==

- **[Download Sample Files](https://augmentedrealityplugins.com/support/)**: Try out sample 3D models to get a feel for the plugin.

---

**Why Choose AR for WordPress?**

- **Engage your audience** like never before with AR and 3D model displays.
- **Boost sales** and reduce returns with an immersive, visual shopping experience.
- **Easy setup** with seamless integration into your WordPress website, compatible with popular page builders.

Elevate your website today with **AR for WordPress** and stay ahead of the competition by embracing the future of eCommerce and interactive content.

== Installation ==

1. Upload `ar-for-wordpress.zip` to the `/wp-content/plugins/` directory and expand it
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the settings page to get started

== Frequently Asked Questions ==

= What 3D model formats are supported? =

AR for WordPress and AR for WooCommerce display your 3D models using an iOS App built using Apple’s ARkit software. This system supports the use of GLb/GLTF and USDZ/REALITY file formats. You can also use DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped versions of these files which will be automatically converted to GLB format.

= Does it support Android and iOS? =

The plugin uses the model-viewer scripts which supports most iOS and Android devices. Viewing of the models in 3D is done directly in the browser and launches the native Android WebXR and iOS Quick Look apps for AR viewing. For optimum performance it requires your site to have an SSL certificate (https://).

= Are there any additional costs? =
If you use the free version of the plugins then there are no costs involved, however you will have limitations to the number of 3D models you can have on your site and the features for manipulating your model display are limited.

If you use a premium version of the plugins then there is a monthly subscription fee for the plugin to support unlimited models and to access the full feature set for the duration of your subscription. https://augmentedrealityplugins.com

= What if I don't have any 3D models? =

If you don’t have 3D models there are a number of solutions available to you, including downloading existing models from online libraries, creating your own models using your mobile phone or tablet’s cameras or commissioning a 3D model to be created. Please visit our 3D Model Resources section for more details. https://augmentedrealityplugins.com/support/#3d

== Screenshots ==

1. Mixmaaster Model in AR

2. Mixmaaster Model in 3D

3. Leather Chair in AR

4. Framed Gallery Builder Photo in AR

5. Transparent Gallery Builder image in 3D

6. Add/Edit AR Models

7. 3D Gallery Builder

8. Settings Page

9. Widgets

10. Elementor Elements

11. User Upload

12. Gutenberg Blocks

== Changelog ==

=7.9=
* Featured image function fix

=7.8=
* Domain Licence Verification improvements
* ar_qr_url warning fixed
* Vulnerability for injection of javascript using the shortcodes fixed

=7.7=
* User Upload model viewer will always use "webxr scene-viewer quick-look"
* Qr code destination fix

=7.6=
* JQuery Focus Manager for AR Model Viewer added
* Free Version page loading conflict issue resolved

=7.5=
* CSS Positioning version numbering to fix caching issue
* Play/Pause button fix when multiple models displayed
* Updated Gutenburg Block editing
* Elementor Theme model viewer layout improvement

=7.4=
* Version addition to enqueuing CSS and JS
* Set featured image security enhancement

=7.3=
* Custom progress bar color
* Fixed custom css positionining issues
* Registration wp_remote_post fallback improvements

=7.2=
* Play Pause Animation issue fixed also updated to pause rotation too
* Disable and Hiding of elements issue fixed
* User Upload minor issues fixed
* AR Model Shop Import issue fixed

=7.1=
* JS and CSS Enqueuing issue fixed
* Multiple Models Thumbnails issue fixed

=7.0=
* AR Shop - purchase a 3D model and import it into your site
* AR Gallery shortcode to display the featured image of your post as a 3d model artwork to hang on the wall
* AR Gallery Frame, color and opacity options. 
* AR Gallery Support for transparent pngs
* User Upload - provide the ability for front end users to drag and drop a model or image file into a model viewer and have it automatically display it. Images are converted to 3D model artworks.
* Encrypted model file URLs to restrict direct downloads
* Improved Settings page layout
* Duplicate a Model Post on All AR Models page
* Code overhaul to better optimise and secure the plugin scripts

=6.2=
* List of Models on Settings page when Limit Exceeded with links to edit or delete
* PHP 8.1 compatibility update

=6.1=
* QR Code fix issue where it appears as white box

=6.0=
* Emissive Lighting Option
* Light Color Option with ColorPicker
* Settings Page option to Hide Posts which sets the default Visibility of new model posts to Private - AR for WordPress
* Further QR Code API improvements

=5.91=
* Bug and styling fixes

=5.9=
* QR Code and performance issue bug fixes

=5.8=
* QR Code image improvements
* Add New Model Page display improvements

=5.7=
* Fixed incompatibility issues with QR code generation if imagick not enabled

=5.6=
* Custom View in AR and View in 3D text options
* 3D Gallery Uploader bug fixes
* Reality File Mime Type update

=5.5=
* Fix 3D Gallery image upload issue
* QR code script adjustment

=5.4=
* QR Code implementation of php QR Code library to replace Google API
* Alternative model for AR display option
* Show Model Fields Toggle Option
* Saving model bug fixes

=5.3=
* Right-to-left (RTL) language display issues fixed

=5.2=
* Javascript improvements
* improved compatibility with AR for Woocommerce plugin

=5.1=
* CSS update to fix AR button clickabilitiy
* Fixed issue where 3D Gallery Builder would apply default GLB file when no image was chosen
* Added script to reload Gutenburg post editing page when post is published or updated
* Added option to link Hotspots to URLs
* Restriction to ensure the chosen subscription domain matches the domain of the site

=5.0=
* Interface redesign
* Addition of 3D Gallery Builder - Create a 3D model of your image file in a pircture frame
* Improved loading and user friendliness

=4.8=
* Documentation Links

=4.7=
* Addition of Animation Selector in model editor

=4.6=
* Fix of blocks_categories deprecated error
* Fix for erros when directly accessing files

=4.5=
* Firefox fallback options
* Addition of Alternative Model For Mobile option

=4.4.1=
* Fix to Javascript
* Addition of areditor shortcode to show a simplified model editor on the front end of site.

=4.4=
* Fix to Elementor Pro issue
* Improvements to opening AR models directly from QR codes
* Improvements to arview shortcode

=4.3=
* Improvement of QR Code Destination to open the model in AR view with friendly urls

=4.2=
* Addition of Rotation Limits - Restrict the amount the user can rotate the model in the viewer
* Addition of Disable Zoom option
* Addition of QR Code Destination option on a per model basis
* Improvements of AR Model Editing page layout

=4.1=
* Addition of Loading and hidden model viewer whilst loading on QR code stand alone model links
* Improvements to category option in ardisplay shortcode
* Further Android intent URL improvements

=4.0=
* Admin Interface styling improvements
* Admin - Auto update of model when new file uploaded or chosen from Media Library
* Open AR automatically with QR code when global setting is Model Viewer
* Zoom constraints improvements
* Popup close button improvements when using ar-view shortcode
* Set Featured AR image now using REST API
* Android intent URL improvements

=3.9=
* AR View shortcode improvement to fix Android AR loading issue

=3.8=
* Reset to Initial View Option Globally and per model
* Improvements to ar-view shortcode

=3.7=
* Custom QR Code Image override generated QR Code on each product
* Custom QR Code Destination override the URL used for generated QR Code on each product
* Disable Prompt on each model
* Improved JS handling

=3.6=
* Global setting to choose units for Dimensions - m, cm, mm, in
* Disable model interaction - no rotating and zooming in browser view only
* ar-view shortcode addition of the buttons attribute to display links as html buttons
* Fail safe in place if qr code cannot be generated

=3.5=
* Improved licence key feedback on settings page
* Elementor Fix

=3.4=
* Addition of Gutenberg Blocks
* Addition of text option for ar-view shortcode

=3.3=
* Asset Builder improvements
* Shortcode display improvements
* API Additions - Delete and Featured Image options

=3.2=
* Hide Dimensions option added to API
* cURL fallback using file_get_contents

=3.1=
* Hide Dimensions on a per model basis

=3.0=
* Fullscreen popup improvements
* Set Featured Image improvements
* Replaced file_get_contents with cURL
* Display of AR not supported message for desktops

=2.9=
* Fixed issue with Disable Fullscreen and Dimensions conflict
* Improved Set Current Camera View as Initial button response

=2.8=
* Minor bug fixes

=2.7=
* Endpoint API functionality
* Improvements to Licence Key system
* Improvements to AR standalone Button - Shows 3D model and note if clicked on Desktop
* Settings Page Layout Improvements
* Fixed Exposure, Shadow Intensity and Shadow Softness set to 0 issue
* Added global QR Code Destination option to settings page, which take mobile users directly to the model-viewer or to the parent page the model is displayed on

=2.6=
* Custom Play and Pause buttons and positioning for animated models
* Option for Dimensions to be displayed in inches
* Link to view Model Post
* Improved Licence key checking

=2.5=
* WordPress Widget
* Elementor Widget

=2.4=
* Custom element positioning within Model Viewer - Applicable to individual models
* CSS Style Editing - Applicable to individual models
* Code Improvements

=2.3.8=
* Global custom element positioning within Model Viewer
* Global CSS Style Editing

=2.3.7=
* Call To Action Button - Displays on 3D Model view and in AR view on Android

=2.3.6=
* Set Featured Image button - creates a PNG file of the current model view, adds it to the media library and sets it as the featured image

=2.3.5=
* Improved licence key check
* Option to prioritise Scene Viewer over WebXR on Android devices

=2.3.4=
* Option to prioritise Scene Viewer over WebXR on Android devices

=2.3.3=
* Option to disable the interaction prompt and model rotation/wriggle

=2.3.2=
* Improved AR model admin layout
* Hotspot functionality - add hotspot annotations to your models
* Editing of placement, QR button, AR Button, animation button and scale settings dynamically update in model view in admin editing pages

= 2.3.1=
* Legacy Lighting option
* Option to set initial camera view
* Editing of field of view, exposure, shadow and zoom settings dynamically update in model view in admin editing pages

= 2.3.0=
* Removed loading icon when clicking AR button

= 2.2.9=
* Fixed Android loading issue when scene viewer crashes. Prioritised webxr

= 2.2.8=
* Improved Internationalization of plugin

= 2.2.7=
* Internationalization of plugin
* Updated scaling inputs to default to 1 and include increment stepper
* Validation of AR model urls to be secure - replace http:// with https://
* Restricted optional settings to Premium Plans only
* Premium upgrade banner improvements

= 2.2.6=
* Added support for .REALITY file formats to display on iOS

= 2.2.5=
* Fixed imagedestroy issue
* Fixed AR thumbnails opening models in 1st viewer on page only

= 2.2.4=
* Improved Licence Key check system

= 2.2.3=
* AR Button changes to loading image when tapped to show model is loading into AR viewer

= 2.2.2=
* Added Environment Image Upload ability
* Improved Skybox image handling

= 2.2.1=
* Improved Licence System
* Added Field of View setting
* Added Zoom in and out contraints settings

= 2.2.0=
* Added support for GLB/GLTF animation display and play/pause controls in browser view 

= 2.1.9=
* Added Fullscreen disable option to settings page
* Added support for Poster images when loading 3D models
* Moved dimensions to top left to avoid conflict with Thumbnails when viewing multiple models in one shortcode
* Fixed issue with dimensions checkbox hiding thumbnail slides
* Fixed issue with multiple model thumbnails and QR code button
* Improved CSS cursor pointers on 3D model viewer elements
* Added Copy function to AR Shortcode column in Model/Product list
* Improved admin page layout

= 2.1.8=
* Fixed conflict issue with Revolution Slider

= 2.1.7=
* Fixed issue with AR buttons conflicting with some themes

= 2.1.6=
* Added Upgrade Ribbon to Settings page
* Fixed issue with Licence key saving
* Improved shortcode examples display

= 2.1.5=
* Fixed issue with thumbnail slider changing ios src

= 2.1.4=
* Fixed issue with ar-view shortcode and AR View Hide setting

= 2.1.3=
* Prioritised Scene-viewer over WebXR to improve Android compatibility

= 2.1.2=
* QR Code fully functioning

= 2.1.1=
* QR Code showing blank issue fixed

= 2.1.0=
* QR Code image issue fixed to be loaded inline
* GLTF uploading issue resolved

= 2.0.9=
* Settings Saving issues fixed
* JS issues fixed

= 2.0.8=
* Display model dimensions options
* Multiple models in the one viewer
* Show/hide QR Code
* Show/Hide AR View button
* Shortcode to display QR Code anywhere on page
* Shortcode to display AR View Button anywhere on page
* Custom AR View button image file
* Custom QR Code logo image file
* Improved Settings page

= 2.0.7=
* Asset Builder - Improvements and additional models

= 2.0.6=
* Asset Builder - Improvements and additional models
* Function Consolidation

= 2.0.5=
* Asset Builder - Choose from ready made 3D models and add your own texture file to create a GLTF model on the fly

= 2.0.4=
* Support for zipped GLTF files Added
* Model conversion for DAE, DXF, 3DS, OBJ, PDF, PLY, STL, or Zipped versions of these files

= 2.0.3=
* Scaling Options Added

= 2.0.2=
* Fixed FullScreen Issues
* Skybox/Background Image support on Fullscreen mode
* Streamlined Licencing system

= 2.0.1=
* Improved Model Viewer display
* QR Code Implementation
* Fullscreen popup
* Variant Support

= 2.0.0=
*Total overhaul of plugin to include iOS and Android support directly in the browser
* No need for an app
* Use of Model Viewer with USDZ and GLB 3D model files

= 1.0.0=
* New Enhancement
* Product Description is added for mobile app

= 1.0.4=
* New Enhancement
* You can put AR thumbnail icon anywhere in post/page

= 1.0.3=
* New Enhancement
* Allow to select model type of "2D Model" or "3D Model"
* If 2D model then Allow to generate 3D box(3D Model) automatically of given width, height, depth, format
* If 3D model then Allow to upload 3D model

= 1.0.1=
* Bug Fixes
* Minor CSS + JS improvements

= 1.0.0=
* First Official Launch Version
