<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return ' ...';
});
// add_filter('excerpt_more', function () {
//     return sprintf(' &hellip; <a class="" href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
// });

remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

/**
 * Remove product data tabs
 */
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {

  unset( $tabs['description'] );      	// Remove the description tab
  // unset( $tabs['reviews'] ); 			// Remove the reviews tab
  // unset( $tabs['additional_information'] );  	// Remove the additional information tab

  return $tabs;
}, 98 );

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

add_action( 'enqueue_block_editor_assets', function(){
  wp_register_script( 'slick', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', [ 'jquery', 'acf' ] );
  wp_register_script( 'module-categories', get_template_directory_uri() . '/app/blocks/blocks-woo/module-categories/module-categories.js', [ 'jquery', 'acf', 'slick' ], null, true );
});

add_action( 'wp_head', function () {
  echo '<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>';
  echo '<link rel="preconnect" href="https://fonts.googleapis.com/" crossorigin>';
} );

add_filter( 'woocommerce_package_rates', function ( $rates, $package ) {
  // Only unset rates if free_shipping is available
  if ( isset( $rates['free_shipping:1'] ) ) {
     unset( $rates['flat_rate:3'] );
  }
  return $rates;
}, 10, 2 );

//Changing cart to Basket
add_filter( 'ngettext', function ( $string ) {
  $string = str_ireplace( 'Cart', 'Basket', $string );
  return $string;
} );
add_filter( 'gettext', function ( $string ) {
  $string = str_ireplace( 'Cart', 'Basket', $string );
  return $string;
} );

// Fixing hasMerchantReturnPolicy error in GSC
add_filter( 'rank_math/json_ld', function( $data, $jsonld ) {
    if ( empty( $data['richSnippet'] ) || ! in_array( $data['richSnippet']['@type'], [ 'Product', 'ProductGroup' ] ) )
		return $data;

	$data['shippingDetails'] = [
		'@context'     => 'https://schema.org/',
		'@type'        => 'OfferShippingDetails',
		'@id'          => '#shipping_policy',
		'deliveryTime' => [
			'@type'        => 'ShippingDeliveryTime',
			'handlingTime' => ['@type' => 'QuantitativeValue', 'minValue' => 0, 'maxValue' => 1, 'unitCode' => 'DAY',],
			'transitTime' => ['@type' => 'QuantitativeValue', 'minValue' => 1, 'maxValue' => 14, 'unitCode' => 'DAY'],
		],
		'shippingRate' => ['@type' => 'MonetaryAmount', 'value' => 7.50, 'currency' => 'GBP',],
		'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'UK']
	];
	$data['hasMerchantReturnPolicy'] = [
		'@context'     => 'https://schema.org/',
		'@type'        => 'MerchantReturnPolicy',
		'@id'          => '#merchant_policy',
		'applicableCountry' => 'UK',
		'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
		'merchantReturnDays' => 14,
		'returnMethod' => 'https://schema.org/ReturnByMail',
		'returnFees' => 'https://schema.org/FreeReturn'
	];
	if ( 'Product' ===  $data['richSnippet']['@type'] ) {
		$data['richSnippet']['offers']['shippingDetails'] = [ '@id' => '#shipping_policy' ];
		$data['richSnippet']['offers']['hasMerchantReturnPolicy'] = ['@id' => '#merchant_policy'];
		return $data;
	}

	if ( empty( $data['richSnippet']['hasVariant'] ) ) {
		return $data;
	}

	foreach ( $data['richSnippet']['hasVariant'] as $key => $value ) {
		if ( empty( $value['offers'] ) ) {
			continue;
		}

		$data['richSnippet']['hasVariant'][ $key ]['offers']['shippingDetails'] = [ '@id' => '#shipping_policy' ];
		$data['richSnippet']['hasVariant'][ $key ]['offers']['hasMerchantReturnPolicy'] = [ '@id' => '#merchant_policy' ];
	}

	return $data;

}, 99, 2);

// Fixing aggregateReviews error in GSC
add_filter('rank_math/snippet/rich_snippet_product_entity',	function ($entity) {
	$score = get_post_meta(get_the_ID(), '_wc_average_rating', true);
	$count = get_post_meta(get_the_ID(), '_wc_review_count', true) + 1;

    if (!$score || $score < 1)
        $score = 5.00;

    if (!$count || $count < 1)
        $count = 1;

	$entity['aggregateRating'] = array(
		'@type'       => 'aggregateRating',
		'ratingValue' => $score,
		'ratingCount' => $count,
	);
	return $entity;
});

// Remove rel=nofollow from regular comment reply links

add_filter('comment_reply_link', function ($link, $args, $comment, $post){
    return str_replace("rel='nofollow' ", "", $link);
}, 100, 4);

// Remove rel=nofollow from cancel comment reply links
add_filter('cancel_comment_reply_link', function ($formatted_link, $link, $text){
    return str_replace('rel="nofollow"', "", $formatted_link );
}, 10, 3);
