<?php

/**
 * Theme blocks.
 */

namespace App;

add_action( 'init', function () {
    if( function_exists('register_block_type') ) {
        register_block_type( __DIR__ . '/blocks/blocks-general/module-hero' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-hero-video' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-hero-image' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-newsletter' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-testimonials-offer' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-testimonials' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-offer' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-two-columns-text-image' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-two-columns-with-icon' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-gift-card' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-icons' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-contact' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-form' );
        register_block_type( __DIR__ . '/blocks/blocks-woo/module-products' );
        register_block_type( __DIR__ . '/blocks/blocks-woo/module-categories' );
        register_block_type( __DIR__ . '/blocks/blocks-woo/module-new' );
        register_block_type( __DIR__ . '/blocks/blocks-general/module-one-column' );
    }
});

//add_filter( 'allowed_block_types_all', function ( $allowed_blocks, $post ) {
//
//    global $post;
//    if ( $post->post_type === 'post' ) {
//        return [
//            'core/paragraph',
//            'core/heading',
//            'core/image',
//            'core/list',
//        ];
//    }
//
//	return [
//		'acf/module-hero',
//		'acf/module-hero-video',
//		'acf/module-hero-image',
//		'acf/module-newsletter',
//		'acf/module-testimonials-offer',
//		'acf/module-testimonials',
//		'acf/module-offer',
//		'acf/module-two-columns-text-image',
//		'acf/module-two-columns-with-icon',
//		'acf/module-gift-card',
//		'acf/module-icons',
//        'acf/module-contact',
//        'acf/module-products',
//        'acf/module-categories',
//        'acf/module-new',
//        'core/shortcode',
//        'acf/module-form'
//    ];
//
//}, 25, 2 );
