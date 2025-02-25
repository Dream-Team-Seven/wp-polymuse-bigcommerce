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

    function polymuse_modify_bigcommerce_gallery() {
        // Check if we're on a single product page
        if (is_product()) {
            // Get the model URL and thumbnail URL
            $model_url = "https://firebasestorage.googleapis.com/v0/b/polymuse-68692.appspot.com/o/models%2F20250205124059197%2FSheenChair.glb?alt=media&token=19402c2b-bb92-499e-83bf-d49c263bb09c";
            $model_thumbnail_url = "https://yiteg94znhby2sle.public.blob.vercel-storage.com/www.google.com-ZjBvSos6qNeXxXTmKQtoj50Owjx49O.png";
    
            // Define the paths
            $plugin_dir = plugin_dir_path(__FILE__);
            $theme_dir = get_stylesheet_directory();
            $override_dir = $theme_dir . '/bigcommerce/templates/public/components/products';
            $original_template = WP_PLUGIN_DIR . '/bigcommerce/templates/public/components/products/product-gallery.php';
            $override_template = $override_dir . '/product-gallery.php';
    
            // Create the override directory if it doesn't exist
            if (!is_dir($override_dir)) {
                wp_mkdir_p($override_dir);
            }
    
            // Copy the original template if it doesn't exist in the override directory
            if (!file_exists($override_template) && file_exists($original_template)) {
                copy($original_template, $override_template);
            }
    
            // Modify the template
            if (file_exists($override_template)) {
                $template_content = file_get_contents($override_template);
    
                // Inject the 3D model viewer and thumbnail
                $viewer_html = '<div class="swiper-slide bc-product-gallery__image-slide" data-index="0">
                                    <model-viewer src="' . esc_url($model_url) . '" alt="3D model" auto-rotate camera-controls ar ar-modes="webxr scene-viewer quick-look" style="width: 100%; height: 100%;"></model-viewer>
                                </div>';
    
                $thumbnail_html = '<button class="bc-product-gallery__thumb-slide swiper-slide" data-js="bc-gallery-thumb-trigger" data-index="0" aria-label="View 3D Model">
                                        <img src="' . esc_url($model_thumbnail_url) . '" alt="3D Model Thumbnail">
                                    </button>';
    
                // Insert the viewer and thumbnail into the content
                $template_content = preg_replace(
                    '/<div class="swiper-wrapper" data-js="bc-product-image-zoom">/',
                    '<div class="swiper-wrapper" data-js="bc-product-image-zoom">' . $viewer_html,
                    $template_content,
                    1
                );
    
                $template_content = preg_replace(
                    '/<div class="swiper-wrapper bc-product-gallery__thumbs">/',
                    '<div class="swiper-wrapper bc-product-gallery__thumbs">' . $thumbnail_html,
                    $template_content,
                    1
                );
    
                // Save the modified template
                file_put_contents($override_template, $template_content);
            }
        }
    }
    add_action('wp', 'polymuse_modify_bigcommerce_gallery');


    // Enqueue styles and scripts
    function polymuse_enqueue_assets()
    {
        error_log('polymuse_enqueue_assets called');
        wp_enqueue_style('polymuse-styles', plugins_url('styles.css', __FILE__));
        wp_enqueue_script('polymuse-script', plugins_url('polymuse.js', __FILE__), array('jquery'), '1.0', true);
    }
    add_action('wp_enqueue_scripts', 'polymuse_enqueue_assets');

}