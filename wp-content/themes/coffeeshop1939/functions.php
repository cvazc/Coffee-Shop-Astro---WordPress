<?php

function coffee_shop_setup() {
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'coffee_shop_setup');

function coffee_shop_api_init() {
    register_rest_field(
        array('page'),
        'featured_images',
        array('get_callback' => 'get_featured_image')
    );
}

add_action('rest_api_init','coffee_shop_api_init');

function get_featured_image() {
    return "Desde get_featured_image";
}