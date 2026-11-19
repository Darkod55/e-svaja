<?php

/**
 * Hard-coded front page content and homepage-only performance hooks.
 *
 * Page 19's block markup is no longer rendered. The copy, links, product IDs
 * and attachment IDs below are the current homepage, written into the template
 * on purpose so they are not editable fields.
 */

namespace App;

/**
 * Logo box used for the header SVG and the Rank Math logo schema.
 *
 * The theme already treats the logo as 2.5:1 (customizer width × 0.4, and the
 * 200×80 size passed to wp_get_attachment_image). The SVG has no width/height
 * metadata, so WordPress was emitting width="1" height="1".
 *
 * @return array{0:int,1:int}
 */
function logo_dimensions(): array
{
    $width = (int) get_theme_mod('logo_width', 177);
    if ($width < 1) {
        $width = 177;
    }

    return [$width, max(1, (int) round($width * 0.4))];
}

/**
 * Theme logo and the Rank Math knowledge-graph logo, when that setting exists.
 */
function is_logo_attachment(int $attachment_id): bool
{
    if ($attachment_id < 1) {
        return false;
    }

    if ($attachment_id === (int) get_theme_mod('custom_logo')) {
        return true;
    }

    $titles = get_option('rank-math-options-titles');
    if (is_array($titles) && $attachment_id === (int) ($titles['knowledgegraph_logo_id'] ?? 0)) {
        return true;
    }

    $file = (string) get_post_meta($attachment_id, '_wp_attached_file', true);

    return $file !== '' && substr($file, -14) === 'svaja-logo.svg';
}

/**
 * Rank Math reads attachment metadata with the unfiltered flag, which skips
 * wp_get_attachment_metadata. get_post_meta() still runs this filter, so the
 * width/height keys exist before class-jsonld.php reads them.
 *
 * @param mixed $value
 * @return mixed
 */
function filter_logo_attachment_metadata($value, int $object_id, string $meta_key, bool $single)
{
    if ($meta_key !== '_wp_attachment_metadata' || ! is_logo_attachment($object_id)) {
        return $value;
    }

    if ($value !== null) {
        return $value;
    }

    remove_filter('get_post_metadata', __NAMESPACE__ . '\\filter_logo_attachment_metadata', 10);
    $meta = get_post_meta($object_id, '_wp_attachment_metadata', true);
    add_filter('get_post_metadata', __NAMESPACE__ . '\\filter_logo_attachment_metadata', 10, 4);

    if (! is_array($meta)) {
        $meta = [];
    }

    if (empty($meta['width']) || empty($meta['height'])) {
        [$meta['width'], $meta['height']] = logo_dimensions();
    }

    // Single meta is unwrapped from the first element by core.
    return [$meta];
}

add_filter('get_post_metadata', __NAMESPACE__ . '\\filter_logo_attachment_metadata', 10, 4);

/**
 * Keep a 1×1 SVG logo from winning the layout even if metadata is ignored.
 *
 * @param array|false $image
 * @return array|false
 */
function filter_logo_image_src($image, int $attachment_id)
{
    if (! is_array($image) || ! is_logo_attachment($attachment_id)) {
        return $image;
    }

    if ((int) ($image[1] ?? 0) > 1 && (int) ($image[2] ?? 0) > 1) {
        return $image;
    }

    [$image[1], $image[2]] = logo_dimensions();

    return $image;
}

add_filter('wp_get_attachment_image_src', __NAMESPACE__ . '\\filter_logo_image_src', 10, 2);

/**
 * Optional image attributes passed in from the hard-coded front page.
 * Other pages do not set these properties, so their markup stays the same.
 *
 * @param object|mixed $fields
 */
function home_image_attrs(array $attrs, $fields): array
{
    if (! is_object($fields)) {
        return $attrs;
    }

    if (! empty($fields->home_lcp)) {
        $attrs['class'] = trim(($attrs['class'] ?? '') . ' home-hero-lcp');
        $attrs['loading'] = 'eager';
        $attrs['fetchpriority'] = 'high';
        $attrs['decoding'] = 'async';
    }

    if (! empty($fields->home_image_alt)) {
        $attrs['alt'] = $fields->home_image_alt;
    }
    if (! empty($fields->home_image_loading)) {
        $attrs['loading'] = $fields->home_image_loading;
    }
    if (! empty($fields->home_image_decoding)) {
        $attrs['decoding'] = $fields->home_image_decoding;
    }
    if (! empty($fields->home_image_fetchpriority)) {
        $attrs['fetchpriority'] = $fields->home_image_fetchpriority;
    }
    if (! empty($fields->home_image_sizes)) {
        $attrs['sizes'] = $fields->home_image_sizes;
    }

    return $attrs;
}

/**
 * Attachment ID for a file under wp-content/uploads, used for images that
 * lived in ACF options rather than the page content.
 */
function homepage_attachment_id(string $relative): int
{
    $relative = ltrim($relative, '/');
    $relative = (string) preg_replace('#^wp-content/uploads/#', '', $relative);

    $id = (int) attachment_url_to_postid(home_url('/wp-content/uploads/' . $relative));
    if ($id) {
        return $id;
    }

    global $wpdb;
    $found = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
        $relative
    ));

    return (int) $found;
}

/**
 * Section data for front-page.blade.php.
 *
 * @return array<string, mixed>
 */
function homepage_sections(): array
{
    $below = [
        'home_image_loading' => 'lazy',
        'home_image_decoding' => 'async',
    ];

    return [
        'hero' => [
            'fields' => (object) [
                'content' => <<<'HTML'
<h1><span style="font-weight: 400;">Hand-Blown Glass Art Sculptures
&amp; Gifts - Svaja</span></h1>
Kristina's Private Edit: five glass pieces for autumn, chosen by Kristina.
HTML,
                'image' => 33435,
                'link' => [
                    'title' => 'Shop Now',
                    'url' => home_url('/collections/'),
                    'target' => '',
                ],
                'second_link' => [
                    'title' => 'See the five',
                    'url' => '#private-edit',
                    'target' => '',
                ],
                'home_lcp' => true,
                'home_image_alt' => 'Hand-blown glass art sculptures and gifts by Svaja',
                'home_image_sizes' => '100vw',
            ],
            'block' => [
                'title' => 'Module Hero Image',
                'anchor' => '',
                'className' => 'home-hero',
            ],
        ],
        'products' => [
            'fields' => (object) [
                'content' => <<<'HTML'
<h2 class="mb-2">Kristina's Private Edit</h2>
Five glass pieces for autumn gifting and home styling, chosen by Kristina.

Svaja means “dream” in my native Lithuania. This autumn I have chosen five pieces, including Cecil the scorpion for Scorpio season. I have only two of
him left, the last two we have. Kristina
HTML,
                'products' => [1587, 1562, 946, 1474, 1046],
            ],
            'block' => [
                'title' => 'Module Products',
                'anchor' => '',
            ],
        ],
        'offer' => [
            'fields' => (object) array_merge($below, [
                'image' => 33394,
                'content' => <<<'HTML'
<h2 class="mb-md-5">Art Glass Crafted to Inspire</h2>
For over 25 years, Svaja has created handcrafted art glass that brings colour, character and timeless beauty to interiors. From statement vases and decorative plates to collectible sculptures, each piece is individually crafted by skilled European artisans.

Designed for discerning homeowners, interior designers and luxury hospitality projects, our collections celebrate originality, craftsmanship and exceptional design.

Discover distinctive glass art created to be admired for years to come.
HTML,
                'link' => [
                    'title' => 'Explore Collections',
                    'url' => home_url('/collections/'),
                    'target' => '',
                ],
                'home_image_alt' => 'Palanga tall glass vase and decorative plate',
            ]),
            'block' => [
                'title' => 'Module Offer',
                'anchor' => '',
            ],
        ],
        'newsletter' => [
            'fields' => (object) array_merge($below, [
                'use_default_or_custom' => false,
                'content' => <<<'HTML'
<h2>Get Exclusive Offers!</h2>
Join now for 10% off your first order.
HTML,
                'image' => homepage_attachment_id('2023/08/svaja-ollie-elephant1.png'),
                // Truthy so the module renders the existing newsletter form partial.
                'svaja_form' => true,
                'home_image_alt' => 'Ollie the elephant, a hand-blown Svaja glass sculpture',
            ]),
            'block' => [
                'title' => 'Module Newsletter',
                'anchor' => '',
            ],
        ],
        'categories' => [
            'block' => [
                'title' => 'Module Categories',
                'anchor' => '',
            ],
            'slider_id' => uniqid(),
        ],
        'gift' => [
            'fields' => (object) array_merge($below, [
                'add_custom_content' => false,
                'content' => <<<'HTML'
<h2>Gift Cards</h2>
Give the perfect gift for an upcoming anniversary, birthday, wedding or special occasion.
HTML,
                'image' => homepage_attachment_id('2023/08/e-svaja-gift-card.png'),
                'link' => [
                    'title' => 'Get a gift card',
                    'url' => home_url('/product/gift-card/'),
                    'target' => '',
                ],
                'home_image_alt' => 'Svaja gift card',
            ]),
            'block' => [
                'title' => 'Module Gift Card',
                'anchor' => '',
            ],
        ],
        'testimonials' => [
            'fields' => (object) [
                'content' => <<<'HTML'
<h2 class="border-top border-branded-horizontal pt-4">What clients value
most about us</h2>
HTML,
                'testimonials' => [
                    ['testimonial' => "A great range of high quality products, proud to have them in my home.\n\n<strong>Michael Brack</strong>"],
                    ['testimonial' => "After searching for sometime I found the perfect gift for my mother. Everything was perfect. Thanks so much.\n\n<strong>Ruth Yeates</strong>"],
                    ['testimonial' => "Delighted with my purchase and with your excellent service.\n\n<strong>June – Cumbria</strong>"],
                ],
            ],
            'block' => [
                'title' => 'Module Testimonials',
                'anchor' => '',
            ],
        ],
        'story' => [
            'fields' => (object) array_merge($below, [
                'content' => <<<'HTML'
<h2 class="border-top border-branded-horizontal pt-4">The Svaja Story</h2>
Svaja takes its name from its founder Kristina Svajone Bobs, for her name in her native Lithuania, means “dream”. Kristina’s dream was that of creating and developing collections of usable, beautiful art.
From Svaja’s beginnings in 1999, Kristina has driven her company forward with a proven flair for designing glass that is celebrated across the world.
HTML,
                'image' => 97,
                'image_radius' => 'rounded',
                'columns' => '8-4',
                'layout' => 'flex-row',
            ]),
            'block' => [
                'title' => 'Module Two Columns Text Image',
                'anchor' => '',
            ],
        ],
    ];
}

/**
 * Preload the hero (LCP) image and stop the front page loading block/plugin
 * assets that the hard-coded template does not render.
 */
function homepage_preload_hero(): void
{
    if (! is_front_page()) {
        return;
    }

    $id = 33435;
    $src = wp_get_attachment_image_url($id, 'full');
    if (! $src) {
        return;
    }

    $srcset = wp_get_attachment_image_srcset($id, 'full');
    $type = get_post_mime_type($id) ?: 'image/webp';

    echo '<link rel="preload" as="image" href="' . esc_url($src) . '"';
    if ($srcset) {
        echo ' imagesrcset="' . esc_attr($srcset) . '" imagesizes="100vw"';
    }
    echo ' type="' . esc_attr($type) . '" fetchpriority="high">' . "\n";
}

add_action('wp_head', __NAMESPACE__ . '\\homepage_preload_hero', 2);

/**
 * WordPress can mark a later image as the LCP candidate and override the
 * attributes passed into wp_get_attachment_image. The hero class wins.
 *
 * @param array<string, string> $loading_attrs
 * @param array<string, string> $attr
 * @return array<string, string>
 */
function homepage_lcp_attributes(array $loading_attrs, string $tag_name, array $attr): array
{
    if ($tag_name !== 'img') {
        return $loading_attrs;
    }

    $class = $attr['class'] ?? '';
    if (! is_string($class)) {
        return $loading_attrs;
    }

    if (str_contains($class, 'home-hero-lcp')) {
        $loading_attrs['loading'] = 'eager';
        $loading_attrs['fetchpriority'] = 'high';
        $loading_attrs['decoding'] = 'async';

        return $loading_attrs;
    }

    // The header SVG is eager, but it must not take the hero's LCP priority.
    if (is_front_page() && str_contains($class, 'site-logo')) {
        $loading_attrs['loading'] = 'eager';
        $loading_attrs['fetchpriority'] = 'low';
    }

    return $loading_attrs;
}

add_filter('wp_get_loading_optimization_attributes', __NAMESPACE__ . '\\homepage_lcp_attributes', 10, 3);

/**
 * Product thumbnails sit under a full-bleed hero, so they are not the LCP
 * image, but the first row can still meet a tall viewport. Keep them eager
 * and do not let WordPress hand them fetchpriority=high.
 *
 * @param array<string, string> $attrs
 * @return array<string, string>
 */
function homepage_product_image_attrs(array $attrs): array
{
    $attrs['loading'] = 'eager';
    $attrs['decoding'] = 'async';
    unset($attrs['fetchpriority']);

    return $attrs;
}

function homepage_drop_assets(): void
{
    if (! is_front_page()) {
        return;
    }

    $style_needles = [
        'woocommerce-paypal-payments/assets/ppcp-blocks',
        'woolentor-blocks/assets/css/blocks.style.build.css',
        'woocommerce/assets/css/photoswipe/',
        'woolentor-addons/includes/modules/quickview/assets/css/',
        'woo-custom-product-addons-pro/assets/css/',
        'woocommerce/assets/client/blocks/',
    ];

    $script_needles = [
        'woocommerce/assets/js/frontend/add-to-cart.min.js',
        'woocommerce/assets/js/frontend/add-to-cart-variation',
        'woocommerce/assets/js/frontend/single-product',
        'woocommerce/assets/js/jquery-blockui/',
        'woocommerce/assets/js/zoom/',
        'woocommerce/assets/js/flexslider/',
        'woocommerce/assets/js/photoswipe/',
        'woocommerce/assets/js/sourcebuster/',
        'woocommerce/assets/js/frontend/order-attribution',
        'woolentor-addons/includes/modules/quickview/',
        'woo-custom-product-addons-pro/assets/js/',
        'woocommerce/assets/client/blocks/',
        '/wp-includes/js/thickbox/',
        '/wp-includes/js/underscore.min.js',
        '/wp-includes/js/wp-util.min.js',
        '/wp-includes/js/dist/',
    ];

    foreach (['global-styles', 'classic-theme-styles', 'wp-block-library', 'wp-block-library-theme'] as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }

    $styles = wp_styles();
    foreach ((array) $styles->queue as $handle) {
        $src = $styles->registered[$handle]->src ?? '';
        if (! is_string($src)) {
            continue;
        }
        foreach ($style_needles as $needle) {
            if (str_contains($src, $needle)) {
                wp_dequeue_style($handle);
                break;
            }
        }
    }

    $scripts = wp_scripts();
    foreach ((array) $scripts->queue as $handle) {
        $src = $scripts->registered[$handle]->src ?? '';
        if (! is_string($src)) {
            continue;
        }
        foreach ($script_needles as $needle) {
            if (str_contains($src, $needle)) {
                wp_dequeue_script($handle);
                break;
            }
        }
    }
}

/**
 * Block styles are enqueued from post_content even when the template never
 * prints those blocks. The front page no longer uses that content.
 */
function homepage_unhook_block_assets(): void
{
    if (! is_front_page()) {
        return;
    }

    remove_action('wp_enqueue_scripts', 'wp_enqueue_registered_block_scripts_and_styles');
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    remove_action('wp_enqueue_scripts', 'wp_enqueue_stored_styles');
    remove_action('wp_footer', 'wp_enqueue_stored_styles', 1);
}

add_action('wp', __NAMESPACE__ . '\\homepage_unhook_block_assets');
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\homepage_drop_assets', 999);
add_action('wp_print_styles', __NAMESPACE__ . '\\homepage_drop_assets', 1);
add_action('wp_print_scripts', __NAMESPACE__ . '\\homepage_drop_assets', 1);
add_action('wp_footer', __NAMESPACE__ . '\\homepage_drop_assets', 0);

/**
 * Product-addon and similar plugins echo a large style block from wp_head.
 * The homepage cards do not render those fields.
 */
function homepage_remove_unused_head_callbacks(): void
{
    if (! is_front_page()) {
        return;
    }

    foreach (['wp_head', 'wp_footer'] as $hook) {
        homepage_remove_callbacks_matching($hook, ['wcpa', 'WCPA', 'woocommerce-paypal-payments']);
    }
}

/**
 * @param array<int, string> $needles
 */
function homepage_remove_callbacks_matching(string $hook, array $needles): void
{
    global $wp_filter;

    if (empty($wp_filter[$hook]) || ! is_object($wp_filter[$hook])) {
        return;
    }

    $remove = [];
    foreach ($wp_filter[$hook]->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            $name = homepage_callback_name($callback['function'] ?? null);
            if ($name === '') {
                continue;
            }
            foreach ($needles as $needle) {
                if (stripos($name, $needle) !== false) {
                    $remove[] = [$callback['function'], (int) $priority];
                    break;
                }
            }
        }
    }

    foreach ($remove as [$function, $priority]) {
        remove_action($hook, $function, $priority);
    }
}

/**
 * @param mixed $callback
 */
function homepage_callback_name($callback): string
{
    if (is_string($callback)) {
        return $callback;
    }

    if (is_array($callback) && isset($callback[0], $callback[1])) {
        $class = is_object($callback[0]) ? get_class($callback[0]) : (string) $callback[0];

        return $class . '::' . $callback[1];
    }

    return '';
}

add_action('wp_head', __NAMESPACE__ . '\\homepage_remove_unused_head_callbacks', 0);
add_action('wp_footer', __NAMESPACE__ . '\\homepage_remove_unused_head_callbacks', 0);

add_action('wp_enqueue_scripts', function () {
    if (! is_front_page()) {
        return;
    }

    $path = get_theme_file_path('resources/styles/front-page.css');
    wp_enqueue_style(
        'e-svaja-front-page',
        get_theme_file_uri('resources/styles/front-page.css'),
        [],
        is_readable($path) ? (string) filemtime($path) : null
    );
}, 200);
