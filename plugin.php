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

    function polymuse_modify_single_product_template($content)
    {
        // Only run on BigCommerce product page
        if (is_product()) {
            // Get the model URL (you can dynamically fetch this based on product data)
            $model_url = "https://firebasestorage.googleapis.com/v0/b/polymuse-68692.appspot.com/o/models%2F20250205124059197%2FSheenChair.glb?alt=media&token=19402c2b-bb92-499e-83bf-d49c263bb09c";

            // Get the model thumbnail URL (adjust accordingly)
            $model_thumbnail_url = "https://example.com/thumbnail.jpg"; // Replace with dynamic thumbnail URL

            // Check if the content contains the swiper-slide with the product image
            if (strpos($content, 'class="swiper-slide bc-product-gallery__image-slide"') !== false) {
                // Prepare the HTML structure for the 3D model viewer as an image slide
                $model_slide = '<div class="swiper-slide bc-product-gallery__image-slide">';
                $model_slide .= '<img src="' . esc_url($model_thumbnail_url) . '" alt="3D model" class="model-thumbnail">';
                $model_slide .= '<model-viewer src="' . esc_url($model_url) . '" alt="3D Model" auto-rotate camera-controls ar ar-modes="webxr scene-viewer quick-look" style="width: 100%; height: 500px;"></model-viewer>';
                $model_slide .= '</div>';

                // Add the 3D model slide in the swiper-wrapper
                $content = preg_replace('/(<div class="swiper-wrapper"[^>]*>)/', '$1' . $model_slide, $content);
            }
        }

        return $content;
    }
    add_filter('the_content', 'polymuse_modify_single_product_template', 20);


    // Enqueue styles and scripts
    function polymuse_enqueue_assets()
    {
        error_log('polymuse_enqueue_assets called');
        wp_enqueue_style('polymuse-styles', plugins_url('styles.css', __FILE__));
        wp_enqueue_script('polymuse-script', plugins_url('polymuse.js', __FILE__), array('jquery'), '1.0', true);
    }
    add_action('wp_enqueue_scripts', 'polymuse_enqueue_assets');

}