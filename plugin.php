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

    function polymuse_add_thumbnail_slide($content)
    {
        if (is_product()) {
            $model_thumbnail_url = "https://yiteg94znhby2sle.public.blob.vercel-storage.com/www.google.com-ZjBvSos6qNeXxXTmKQtoj50Owjx49O.png";

            $new_slide_html = '<div class="swiper-slide bc-product-gallery__image-slide swiper-slide-active" data-index="0" style="width: 357px; opacity: 1; transform: translate3d(0px, 0px, 0px); transition-duration: 0ms;">
                                    <img src="' . esc_url($model_thumbnail_url) . '" alt="3D Model Thumbnail" srcset="' . esc_url($model_thumbnail_url) . ' 273w, ' . esc_url($model_thumbnail_url) . ' 150w, ' . esc_url($model_thumbnail_url) . ' 86w, ' . esc_url($model_thumbnail_url) . ' 167w">
                                </div>';

            // Find the swiper-wrapper and insert the new slide
            $content = preg_replace(
                '/<div class="swiper-wrapper" data-js="" style="transition-duration: 0ms;">/',
                '<div class="swiper-wrapper" data-js="" style="transition-duration: 0ms;">' . $new_slide_html,
                $content,
                1 // Limit to the first match
            );
        }
        return $content;
    }

    add_filter('the_content', 'polymuse_add_thumbnail_slide', 20);


    // Enqueue styles and scripts
    function polymuse_enqueue_assets()
    {
        error_log('polymuse_enqueue_assets called');
        wp_enqueue_style('polymuse-styles', plugins_url('styles.css', __FILE__));
        wp_enqueue_script('polymuse-script', plugins_url('polymuse.js', __FILE__), array('jquery'), '1.0', true);
    }
    add_action('wp_enqueue_scripts', 'polymuse_enqueue_assets');

}