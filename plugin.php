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
            // Get the model URL dynamically
            $model_url = "https://firebasestorage.googleapis.com/v0/b/polymuse-68692.appspot.com/o/models%2F20250205124059197%2FSheenChair.glb?alt=media&token=19402c2b-bb92-499e-83bf-d49c263bb09c";

            // Get the model thumbnail URL dynamically
            $model_thumbnail_url = "https://yiteg94znhby2sle.public.blob.vercel-storage.com/www.google.com-ZjBvSos6qNeXxXTmKQtoj50Owjx49O.png";

            // Insert the 3D model as the main product image in the gallery
            $content = preg_replace_callback(
                '/<div class="swiper-wrapper" data-js="" style="transition-duration: 0ms;">(.*?)<\/div>/s',
                function ($matches) use ($model_url) {
                    // Prepare the HTML for the 3D model viewer slide
                    $model_slide = '<div class="swiper-slide bc-product-gallery__image-slide swiper-slide-active" style="width: 357px; opacity: 1; transform: translate3d(0px, 0px, 0px);">
                                    <model-viewer src="' . esc_url($model_url) . '" alt="3D model" auto-rotate camera-controls ar ar-modes="webxr scene-viewer quick-look" style="width: 100%; height: 100%;"></model-viewer>
                                </div>';

                    // Insert the model slide as the first slide
                    return '<div class="swiper-wrapper" data-js="" style="transition-duration: 0ms;">' . $model_slide . $matches[1] . '</div>';
                },
                $content
            );

            // Add a thumbnail to the product gallery slider
            $content = preg_replace_callback(
                '/<div class="swiper-wrapper bc-product-gallery__thumbs"(.*?)>(.*?)<\/div>/s',
                function ($matches) use ($model_thumbnail_url) {
                    // Prepare the HTML for the 3D model thumbnail button
                    $thumb_button = '<button class="bc-product-gallery__thumb-slide swiper-slide" data-js="bc-gallery-thumb-trigger" data-index="0" aria-label="View 3D Model">
                                    <img src="' . esc_url($model_thumbnail_url) . '" alt="3D Model Thumbnail">
                                </button>';

                    // Insert the thumbnail button as the first thumbnail
                    return '<div class="swiper-wrapper bc-product-gallery__thumbs' . $matches[1] . '>' . $thumb_button . $matches[2] . '</div>';
                },
                $content
            );

            // Prepare the HTML structure for the 3D model viewer in the product description
            $model_viewer = '<section class="bc-single-product__description"><h4 class="bc-single-product__section-title">3D Model</h4>';
            $model_viewer .= '<div data-thumb="' . esc_url($model_thumbnail_url) . '" ';
            $model_viewer .= 'data-thumb-alt="3D Model" ';
            $model_viewer .= 'data-thumb-srcset="' . esc_url($model_thumbnail_url) . ' 100w" ';
            $model_viewer .= 'data-thumb-sizes="(max-width: 100px) 100vw, 100px" ';
            $model_viewer .= 'class="polymuse-model-viewer" style="height: 500px;">';
            $model_viewer .= '<model-viewer src="' . esc_url($model_url) . '" alt="3D model" auto-rotate camera-controls ar ar-modes="webxr scene-viewer quick-look" style="width: 100%; height: 100%;"></model-viewer>';
            $model_viewer .= '</div></section>';

            // Append the custom content after the product description
            $content = str_replace('<div class="bc-product__description">', '<div class="bc-product__description">' . $model_viewer, $content);
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