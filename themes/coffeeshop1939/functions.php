<?php

add_filter('acf/settings/rest_api_format', function () {
    return "standard";
});

function coffee_shop_setup() {
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'coffee_shop_setup');

function coffee_shop_api_init() {
    register_rest_field(
        array('page', 'post'),
        'featured_images',
        array('get_callback' => 'get_featured_image')
    );

    register_rest_field(
        array('post'),
        'category_details',
        array('get_callback' => 'get_post_categories')
    );

    register_rest_field(
        array('page'),
        'gallery',
        array('get_callback' => 'get_gallery_images')
    );
}

add_action('rest_api_init', 'coffee_shop_api_init');

function get_featured_image($post) {
    if (!$post['featured_media']) {
        return false;
    }

    $image_sizes = get_intermediate_image_sizes();

    $images = array();

    foreach ($image_sizes as $size) {
        if ($size === "2048x2048")
            continue;

        $image = wp_get_attachment_image_src($post['featured_media'], $size);

        $images[$size === '1536x1536' ? 'full' : $size] = array(
            'url' => $image[0],
            'width' => $image[1],
            'height' => $image[2]
        );
    }

    return $images;
}

function get_post_categories($post) {

    return array_map(
        function ($categoryId) {
            $category = get_category($categoryId, 'ARRAY_A');

            return [
                'id' => $category['term_id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ];
        },
        $post['categories']
    );
}

function get_gallery_images($post) {
    if ($post['slug'] !== 'galeria') {
        return [];
    }

    $gallery = get_post_gallery($post['id'], false);
    $galleryIds = array_map('intval', explode(",", $gallery['ids']));

    return array_map(
        function ($imageId) {
            $largeImage = wp_get_attachment_image_src($imageId, 'large');
            $fullImage = wp_get_attachment_image_src($imageId, 'full');
            
            return [
                'large' => [
                    'url' => $largeImage[0],
                    'width' => $largeImage[1],
                    'height' => $largeImage[2],
                ],
                'full' => [
                    'url' => $fullImage[0],
                    'width' => $fullImage[1],
                    'height' => $fullImage[2],
                ],
            ];
        },
        $galleryIds
    );
}