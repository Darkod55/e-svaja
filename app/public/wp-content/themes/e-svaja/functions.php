<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/

if (! file_exists($composer = __DIR__.'/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'sage'));
}

require $composer;

/*
|--------------------------------------------------------------------------
| Register The Bootloader
|--------------------------------------------------------------------------
|
| The first thing we will do is schedule a new Acorn application container
| to boot when WordPress is finished loading the theme. The application
| serves as the "glue" for all the components of Laravel and is
| the IoC container for the system binding all of the various parts.
|
*/

try {
    \Roots\bootloader();
} catch (Throwable $e) {
    wp_die(
        __('You need to install Acorn to use this theme.', 'sage'),
        '',
        [
            'link_url' => 'https://docs.roots.io/acorn/2.x/installation/',
            'link_text' => __('Acorn Docs: Installation', 'sage'),
        ]
    );
}

/*
|--------------------------------------------------------------------------
| Register Sage Theme Files
|--------------------------------------------------------------------------
|
| Out of the box, Sage ships with categorically named theme files
| containing common functionality and setup to be bootstrapped with your
| theme. Simply add (or remove) files from the array below to change what
| is registered alongside Sage.
|
*/

collect(['setup', 'filters', 'includes', 'blocks'])
    ->each(function ($file) {
        if (! locate_template($file = "app/{$file}.php", true, true)) {
            wp_die(
                /* translators: %s is replaced with the relative file path */
                sprintf(__('Error locating <code>%s</code> for inclusion.', 'sage'), $file)
            );
        }
    });

/*
|--------------------------------------------------------------------------
| Enable Sage Theme Support
|--------------------------------------------------------------------------
|
| Once our theme files are registered and available for use, we are almost
| ready to boot our application. But first, we need to signal to Acorn
| that we will need to initialize the necessary service providers built in
| for Sage when booting.
|
*/

add_theme_support('sage');

// add_action( 'wp_enqueue_scripts', 'my_plugin_print_styles',  PHP_INT_MAX );

// function my_plugin_print_styles(){
//     $wp_scripts = wp_scripts();
//     $wp_styles = wp_styles();
//     echo "<pre>";
//     // var_dump($wp_scripts);
//     var_dump($wp_styles);
//     echo "</pre>";
// }

// function wc_shipping_rate_cache_invalidation( $packages ) {
//     foreach ( $packages as &$package ) {
//         $package['rate_cache'] = wp_rand();
//     }
//     unset($package);

//     return $packages;
// }
// add_filter( 'woocommerce_cart_shipping_packages', 'wc_shipping_rate_cache_invalidation', 100 );

/**
 * Product schema data - missing from Rank Math
 * 
 * @link: https://support.rankmath.com/ticket/missing-field-hasmerchantreturnpolicy-in-offers-3/
 * @todo: move this to an SEO filter in the traits
 */
add_filter('rank_math/snippet/rich_snippet_product_entity', function($entity) {
	//return $entity;
	
    // Return policy
    $entity['offers']['hasMerchantReturnPolicy']['@type'] = 'MerchantReturnPolicy';
    $entity['offers']['hasMerchantReturnPolicy']['applicableCountry'] = 'GB';
    $entity['offers']['hasMerchantReturnPolicy']['returnPolicyCategory'] = 'https://schema.org/MerchantReturnFiniteReturnWindow';
    $entity['offers']['hasMerchantReturnPolicy']['merchantReturnDays'] = 30;
    $entity['offers']['hasMerchantReturnPolicy']['returnMethod'] = 'https://schema.org/ReturnByMail';
    $entity['offers']['hasMerchantReturnPolicy']['returnFees'] = 'https://schema.org/FreeReturn';

    // Shipping details
    $entity['offers']['shippingDetails']['@type'] = 'OfferShippingDetails';
    $entity['offers']['shippingDetails']['shippingRate']['@type'] = 'MonetaryAmount';
    $entity['offers']['shippingDetails']['shippingRate']['value'] = 5; // @todo: this needs to be product specific
    $entity['offers']['shippingDetails']['shippingRate']['currency'] = 'GBP';
    $entity['offers']['shippingDetails']['shippingDestination']['@type'] = 'DefinedRegion';
    $entity['offers']['shippingDetails']['shippingDestination']['addressCountry'] = 'GB';

    return $entity;
});

add_filter('rank_math/sitemap/remove_credit', '__return_true');
//add_filter('rank_math/sitemap/enable_caching', '__return_false');

/**
 * Fixes terms not being created as absolute urls
 *
 * @todo: move this to an SEO filter in the traits
 */
add_filter('rank_math/sitemap/entry', function( $url, $type, $object) {
    if ($type === 'term')
        if (!str_starts_with(stripslashes($url['loc']), stripslashes(home_url())))
            $url['loc'] = home_url($url['loc']);
    return $url;
}, 10, 3);

/**
 * Fixes local videos not being created as absolute urls
 *
 * @todo: move this to an SEO filter in the traits
 */
add_filter('rank_math/sitemap_url', function ($output, $url) {
    if (isset($url['videos'])) {
        // only works with default 'wp-content' directory
        $output = str_replace('>/wp-content/' , '>'. stripslashes(home_url()) .'/wp-content/', $output);
    }
    return $output;
}, 10, 2);

add_filter('woocommerce_coupon_get_discount_amount', function ($discount, $discounting_amount, $cart_item, $single, $coupon) {
    if (is_admin()) return;
	$product = $cart_item['data'];
	
    if (!$product->is_in_stock() || $product->is_on_backorder())
        return 0;

    return $discount;
}, 10, 5);
