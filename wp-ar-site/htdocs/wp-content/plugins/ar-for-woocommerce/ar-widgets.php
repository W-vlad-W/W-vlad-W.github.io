<?php
/**
 * AR Display
 * https://augmentedrealityplugins.com
**/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// Create WordPress Widget
class ar_for_woocommerce_widget extends WP_Widget {
 
    function __construct() {
        parent::__construct(
            // Widget ID
            'ar_for_woocommerce_widget', 
            // Widget name
            __('AR for Woocommerce', 'ar-for-woocommerce'), 
            // Widget description
            array( 'description' => __( 'Display AR Model Viewer', 'ar-for-woocommerce' ), )
        );
    }
     
    //Widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        echo wp_kses($args['before_widget'], ar_allowed_html());
        if ( ! empty( $title ) ){
            echo wp_kses($args['before_title'] . $title . $args['after_title'], ar_allowed_html());
        }
        echo do_shortcode( '[ardisplay id='.$instance['ar_id'].']' );
        echo wp_kses($args['after_widget'], ar_allowed_html());
    }
     
    //Widget Backend
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'Model title', 'ar-for-woocommerce' );
        }
        ?>
        <p>
        <label for="<?php echo esc_html($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'ar-for-woocommerce' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <label for="<?php echo esc_html($this->get_field_id( 'ar_id' )); ?>"><?php esc_html_e( 'AR Model:', 'ar-for-woocommerce' ); ?></label>
        <select class="widefat" id="<?php echo esc_html($this->get_field_id( 'ar_id' )); ?>" name="<?php echo esc_html($this->get_field_name( 'ar_id' )); ?>">
            <?php
            $args = array(
                'post_type'=> 'product',
                'orderby'        => 'title',
                'posts_per_page' => -1,
                'order'    => 'ASC',
                'meta_query' => array(
                    array('key' => '_glb_file', //meta key name here
                          'value' => '', 
                          'compare' => '!=',
                    )
                )
            );              
            $the_query = new WP_Query( $args );
            if($the_query->have_posts() ) : 
                while ( $the_query->have_posts() ) : 
                   $the_query->the_post();
                   $curr_id = get_the_ID();
                   $curr_title = get_the_title();
                   echo '<option value="'.esc_html($curr_id).'"';
                   if ($instance['ar_id'] == $curr_id){
                       echo ' selected';
                   }
                   echo '>'.esc_html($curr_title).'</option>';
                endwhile; 
                wp_reset_postdata(); 
            else: 
            endif;
            ?>
        </select>
        </p>
        <?php
    }
     
    // Update widget
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['ar_id'] = ( ! empty( $new_instance['ar_id'] ) ) ? wp_strip_all_tags( $new_instance['ar_id'] ) : '';
        return $instance;
    }
 
// End Class ar_for_woocommerce_widget
} 
 
// Register and load AR widget
function ar_for_woocommerce_load_widget() {
    register_widget( 'ar_for_woocommerce_widget' );
}
add_action( 'widgets_init', 'ar_for_woocommerce_load_widget' );

//Elementor Widget
function register_ar_woo_elementor_widget( $widgets_manager ) {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if (is_plugin_active( 'elementor/elementor.php' )) {	   
       require_once( __DIR__ . '/ar-elementor-widget.php' );
       \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Elementor_ar_for_woocommerce_Widget());
    }
}
add_action( 'elementor/widgets/register', 'register_ar_woo_elementor_widget' );