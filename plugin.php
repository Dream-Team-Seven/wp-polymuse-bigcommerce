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

    // Add model-viewer script to header
    function polymuse_add_model_viewer_script()
    {
        echo '<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>';
    }
    add_action('wp_head', 'polymuse_add_model_viewer_script');

    // Add 3D model to product gallery
    // Modify the BigCommerce product page template to append a 3D model viewer without overwriting existing content
    function polymuse_modify_single_product_template($html, $product_id)
    {
        global $product;

        // Get the 3D model URL
        $model_url = "https://firebasestorage.googleapis.com/v0/b/polymuse-68692.appspot.com/o/models%2F20250205124059197%2FSheenChair.glb?alt=media&token=19402c2b-bb92-499e-83bf-d49c263bb09c";

        // Start output buffering to capture the additional content
        ob_start();
        ?>
        <!-- Custom 3D Model Viewer -->
        <div class="polymuse-custom-div">
            <h1>Hey, look at this 3D model!</h1>
            <model-viewer src="<?php echo esc_url($model_url); ?>" alt="3D Model" auto-rotate camera-controls
                style="width: 100%; height: 500px;"></model-viewer>
        </div>
        <?php
        // Get the generated content
        $custom_content = ob_get_clean();

        // Append the custom content to the original product page HTML
        $html .= $custom_content;

        // Log for debugging (optional)
        error_log('polymuse_modify_single_product_template: ' . $html);

        return $html;
    }
    add_filter('bigcommerce/template/product/single', 'polymuse_modify_single_product_template', 10, 2);

    // Enqueue styles and scripts
    function polymuse_enqueue_assets()
    {
        error_log('polymuse_enqueue_assets called');
        wp_enqueue_style('polymuse-styles', plugins_url('styles.css', __FILE__));
        wp_enqueue_script('polymuse-script', plugins_url('polymuse.js', __FILE__), array('jquery'), '1.0', true);
    }
    add_action('wp_enqueue_scripts', 'polymuse_enqueue_assets');

}