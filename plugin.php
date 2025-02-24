<?php
/*
Plugin Name: polymuse-bigcommerce
Plugin URI: https://yourwebsite.com/
Description: Allows users to add 3D models to WooCommerce products
Version: 1.0
Author: Corazon Palencia, Gary Simwawa, Patrick MacDonald, Xiangyu Hou, Tim Karachentsev
Author URI: https://polymuse.tech/
*/


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if bigcommerce is active
if (in_array('bigcommerce/bigcommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    error_log('BigCommerce plugin is active');
    // Add 3D model to product gallery

    function polymuse_modify_single_product_template($html, $product_id)
    {
        global $product;
        // Add 3D model viewer code here
        $model_url = "https://firebasestorage.googleapis.com/v0/b/polymuse-68692.appspot.com/o/models%2F20250205124059197%2FSheenChair.glb?alt=media&token=19402c2b-bb92-499e-83bf-d49c263bb09c";
        $model_thumbnail_url = plugins_url('3d-model-thumbnail.png', __FILE__);
        ?>
        <div class="bc-product-gallery__image polymuse-model-viewer">
            <model-viewer src="<?php echo esc_url($model_url); ?>"
                alt="3D model of <?php echo esc_attr($product->get_name()); ?>" auto-rotate camera-controls ar
                ar-modes="webxr scene-viewer quick-look" style="width: 100%; height: 100%;"></model-viewer>
        </div>
        <?php
        $html .= ob_get_clean();
        return $html;
    }
    add_filter('bigcommerce/template/product/single', 'polymuse_modify_single_product_template', 10, 2);

    function add_buttons_container()
    {
        global $product;

        if (is_product()) {

            // Create a container div for variant options
            ?>
            <div id="variant-options-container"></div>
            <?php
        }
    }
    add_action('bc_before_add_to_cart_form', 'add_buttons_container');

    // Add model-viewer script to header
    function polymuse_add_model_viewer_script()
    {
        echo '<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>';
    }
    add_action('wp_head', 'polymuse_add_model_viewer_script');

    // Enqueue styles and scripts
    function polymuse_enqueue_assets()
    {
        error_log('polymuse_enqueue_assets called');
        wp_enqueue_style('polymuse-styles', plugins_url('styles.css', __FILE__));
        wp_enqueue_script('polymuse-script', plugins_url('polymuse.js', __FILE__), array('jquery'), '1.0', true);
    }
    add_action('wp_enqueue_scripts', 'polymuse_enqueue_assets');

}