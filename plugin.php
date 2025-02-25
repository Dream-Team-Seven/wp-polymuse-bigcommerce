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
    function polymuse_modify_single_product_template($html, $product_id)
    {
        global $product;
        // Retrieve the default product content
        $default_content = apply_filters('bigcommerce/template/product/single', '', $product_id);

        // Add your custom div to the default content
        $custom_div = '<div class="polymuse-custom-div"><h1>hey look at me</h1></div>';
        $html = $default_content . $custom_div;

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