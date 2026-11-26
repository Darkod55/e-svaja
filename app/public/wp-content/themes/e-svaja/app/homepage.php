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
 *
 * Callers include WordPress image and meta filters, which pass null, '' or
 * false for empty galleries and ACF fields. Those must not throw.
 */
function is_logo_attachment($attachment_id): bool
{
    if (is_array($attachment_id) || is_object($attachment_id) || ! is_numeric($attachment_id)) {
        return false;
    }

    $attachment_id = (int) $attachment_id;
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
 * @param mixed $object_id
 * @param mixed $meta_key
 * @param mixed $single
 * @return mixed
 */
function filter_logo_attachment_metadata($value, $object_id, $meta_key, $single)
{
    if (! is_string($meta_key) || $meta_key !== '_wp_attachment_metadata' || ! is_logo_attachment($object_id)) {
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
 * @param mixed $image
 * @param mixed $attachment_id Attachment ID. WooCommerce and ACF pass '', null or false.
 * @return mixed
 */
function filter_logo_image_src($image, $attachment_id)
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
 * Front page only. Rewrites the src WordPress picked, including WooCommerce thumbnails.
 *
 * @param array|false $image
 * @return array|false
 */
function filter_homepage_webp_image_src($image)
{
    if (! is_front_page() || ! is_array($image) || empty($image[0]) || ! is_string($image[0])) {
        return $image;
    }

    $image[0] = homepage_to_webp_url($image[0]);

    return $image;
}

add_filter('wp_get_attachment_image_src', __NAMESPACE__ . '\\filter_homepage_webp_image_src', 20);

/**
 * Same swap for every srcset candidate. Scoped to the front page: the file
 * check is safe elsewhere, but archives would stat every product image.
 *
 * @param array<int, array<string, mixed>> $sources
 * @return array<int, array<string, mixed>>
 */
function filter_homepage_webp_srcset($sources)
{
    if (! is_front_page() || ! is_array($sources)) {
        return $sources;
    }

    foreach ($sources as $width => $source) {
        if (! empty($source['url']) && is_string($source['url'])) {
            $sources[$width]['url'] = homepage_to_webp_url($source['url']);
        }
    }

    return $sources;
}

add_filter('wp_calculate_image_srcset', __NAMESPACE__ . '\\filter_homepage_webp_srcset', 20);

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
        $attrs['decoding'] = 'sync';
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
 * Registered size for a homepage image. Other pages keep `full`.
 *
 * @param object|mixed $fields
 */
function home_image_size($fields, string $default = 'full'): string
{
    if (is_object($fields) && ! empty($fields->home_image_size) && is_string($fields->home_image_size)) {
        return $fields->home_image_size;
    }

    return $default;
}

/**
 * Map an uploads URL to the file on disk. Empty string when it is not an upload.
 */
function homepage_upload_file(string $url): string
{
    $path = wp_parse_url($url, PHP_URL_PATH);
    if (! is_string($path) || $path === '') {
        return '';
    }

    $uploads = wp_upload_dir();
    $base_path = wp_parse_url($uploads['baseurl'] ?? '', PHP_URL_PATH);
    if (! is_string($base_path) || $base_path === '') {
        $base_path = '/wp-content/uploads';
    }
    $base_path = rtrim($base_path, '/');
    $pos = strpos($path, $base_path . '/');
    if ($pos === false) {
        return '';
    }

    $relative = substr($path, $pos + strlen($base_path) + 1);

    return trailingslashit($uploads['basedir']) . $relative;
}

/**
 * Swap a jpg/png/gif uploads URL for its `.webp` sibling when that file exists.
 * Files that are already WebP (the hero banner, the Palanga vase) are unchanged.
 * A missing sibling keeps the original URL so live servers without the files still work.
 */
function homepage_to_webp_url(string $url): string
{
    static $cache = [];

    if (isset($cache[$url])) {
        return $cache[$url];
    }

    $cache[$url] = $url;
    $path = wp_parse_url($url, PHP_URL_PATH);
    if (! is_string($path) || ! preg_match('/\.(jpe?g|png|gif)$/i', $path)) {
        return $cache[$url];
    }

    $file = homepage_upload_file($url);
    if ($file !== '' && is_file($file . '.webp')) {
        $cache[$url] = $url . '.webp';
    }

    return $cache[$url];
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
                // Mobile LCP file. srcset still offers the 1280w original for wide screens.
                'home_image_size' => 'medium_large',
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
                // Half of the container from the md breakpoint. Stops the 1200w file loading for a ~50vw slot.
                'home_image_sizes' => '(min-width: 768px) 50vw, 100vw',
                'home_image_size' => 'large',
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
                'home_image_sizes' => '(min-width: 768px) 50vw, 100vw',
                'home_image_size' => 'large',
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
                'home_image_sizes' => '(min-width: 992px) 42vw, (min-width: 768px) 50vw, 100vw',
                'home_image_size' => 'large',
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
                // col-lg-3 in a 1320px container is about a quarter of the viewport.
                'home_image_sizes' => '(min-width: 992px) 25vw, (min-width: 768px) 50vw, 100vw',
                'home_image_size' => 'medium_large',
            ]),
            'block' => [
                'title' => 'Module Two Columns Text Image',
                'anchor' => '',
            ],
        ],
    ];
}

/**
 * Which homepage hero to render.
 *
 * Default is the seasonal hero (attachment 33507). Return 'classic' to put
 * back the previous hero (attachment 33435) without editing the template:
 *
 *     add_filter('esvaja/homepage_hero_variant', static function () {
 *         return 'classic';
 *     });
 */
function homepage_hero_variant(): string
{
    $variant = apply_filters('esvaja/homepage_hero_variant', 'seasonal');

    return is_string($variant) && $variant === 'classic' ? 'classic' : 'seasonal';
}

/**
 * Preload the hero (LCP) image and stop the front page loading block/plugin
 * assets that the hard-coded template does not render.
 *
 * Only the active hero is preloaded, so the classic banner is not fetched
 * while the seasonal hero is showing.
 */
function homepage_preload_hero(): void
{
    if (! is_front_page()) {
        return;
    }

    $classic = homepage_hero_variant() === 'classic';
    $id = $classic ? 33435 : 33507;
    // Classic img src is medium_large. Seasonal uses the full file, matching
    // the Christmas hero. imagesrcset still offers the other widths.
    $size = $classic ? 'medium_large' : 'full';
    $src = wp_get_attachment_image_url($id, $size);
    if (! $src) {
        return;
    }

    $src = homepage_to_webp_url($src);
    $srcset = wp_get_attachment_image_srcset($id, $size);
    $type = str_ends_with(strtolower((string) wp_parse_url($src, PHP_URL_PATH)), '.webp')
        ? 'image/webp'
        : (get_post_mime_type($id) ?: 'image/webp');

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
 * @param mixed $loading_attrs
 * @param mixed $tag_name
 * @param mixed $attr
 * @return array<string, string>
 */
function homepage_lcp_attributes($loading_attrs, $tag_name, $attr): array
{
    if (! is_array($loading_attrs)) {
        $loading_attrs = [];
    }

    if (! is_string($tag_name) || $tag_name !== 'img' || ! is_array($attr)) {
        return $loading_attrs;
    }

    $class = $attr['class'] ?? '';
    if (! is_string($class)) {
        return $loading_attrs;
    }

    if (str_contains($class, 'home-hero-lcp')) {
        $loading_attrs['loading'] = 'eager';
        $loading_attrs['fetchpriority'] = 'high';
        $loading_attrs['decoding'] = 'sync';

        return $loading_attrs;
    }

    if (! is_front_page()) {
        return $loading_attrs;
    }

    // The header SVG is eager, but it must not take the hero's LCP priority.
    if (str_contains($class, 'site-logo')) {
        $loading_attrs['loading'] = 'eager';
        $loading_attrs['fetchpriority'] = 'low';

        return $loading_attrs;
    }

    // Everything else on the homepage is below the hero, including product
    // cards printed by WooCommerce's own thumbnail function.
    $loading_attrs['loading'] = 'lazy';
    $loading_attrs['decoding'] = 'async';
    $loading_attrs['fetchpriority'] = 'low';

    return $loading_attrs;
}

add_filter('wp_get_loading_optimization_attributes', __NAMESPACE__ . '\\homepage_lcp_attributes', 10, 3);

/**
 * WooCommerce loop thumbnails. Lazy even if an older WooCommerce build skips
 * wp_get_loading_optimization_attributes.
 *
 * @param mixed $attrs
 * @return array<string, string>
 */
function homepage_product_image_attrs($attrs): array
{
    if (! is_array($attrs)) {
        return [];
    }

    $attrs['loading'] = 'lazy';
    $attrs['decoding'] = 'async';
    $attrs['fetchpriority'] = 'low';

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
        'woocommerce/assets/css/woocommerce.css',
        'woocommerce/assets/css/woocommerce-layout.css',
        'woocommerce/assets/css/woocommerce-smallscreen.css',
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
 * Handles the homepage does not need. WooCommerce pulls jquery-blockui and
 * js-cookie back in as dependencies of woocommerce.js, so dequeuing them is
 * not enough: they have to be taken out of the final print list.
 *
 * @return array<int, string>
 */
function homepage_script_src_needles(): array
{
    return [
        'jquery-migrate',
        'jquery-blockui',
        'jquery.blockUI',
        '/js-cookie/',
        'js.cookie',
        '/woocommerce/assets/js/frontend/woocommerce.',
        '/woocommerce/assets/js/frontend/add-to-cart',
        '/woocommerce/assets/js/frontend/cart-fragments',
        '/woocommerce/assets/js/frontend/single-product',
        '/woocommerce/assets/js/frontend/order-attribution',
        '/woocommerce/assets/js/zoom/',
        '/woocommerce/assets/js/flexslider/',
        '/woocommerce/assets/js/photoswipe/',
        '/woocommerce/assets/js/sourcebuster/',
        'woolentor-addons/assets/js/slick',
        'woolentor-addons/includes/modules/quickview/',
        'woo-custom-product-addons-pro/assets/js/',
        'woocommerce/assets/client/blocks/',
        '/wp-includes/js/thickbox/',
        '/wp-includes/js/underscore.min.js',
        '/wp-includes/js/wp-util.min.js',
        '/wp-includes/js/dist/',
    ];
}

/**
 * @param mixed $handles
 * @return array<int, string>
 */
function homepage_filter_print_scripts($handles): array
{
    if (! is_array($handles)) {
        return [];
    }

    if (! is_front_page()) {
        return $handles;
    }

    $scripts = wp_scripts();
    $needles = homepage_script_src_needles();
    $drop_handles = [
        'jquery-migrate',
        'jquery-blockui',
        'js-cookie',
        'woocommerce',
        'wc-add-to-cart',
        'wc-cart-fragments',
        'wc-add-to-cart-variation',
        'wc-single-product',
        'wc-zoom',
        'wc-flexslider',
        'photoswipe',
        'photoswipe-ui-default',
        'sourcebuster-js',
        'wc-order-attribution',
    ];

    return array_values(array_filter($handles, static function ($handle) use ($scripts, $needles, $drop_handles) {
        if (in_array($handle, $drop_handles, true)) {
            return false;
        }
        $src = $scripts->registered[$handle]->src ?? '';
        if (! is_string($src) || $src === '') {
            return true;
        }
        foreach ($needles as $needle) {
            if (str_contains($src, $needle)) {
                return false;
            }
        }

        return true;
    }));
}

add_filter('print_scripts_array', __NAMESPACE__ . '\\homepage_filter_print_scripts');

add_action('wp_enqueue_scripts', static function () {
    if (! is_front_page()) {
        return;
    }
    $scripts = wp_scripts();
    if (isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_values(array_diff(
            (array) $scripts->registered['jquery']->deps,
            ['jquery-migrate']
        ));
    }
}, 1000);

/**
 * Tiny loader. The library itself is not requested until idle or the first input.
 */
function homepage_idle_loader(string $src, string $id): string
{
    $src_json = wp_json_encode($src);

    return '<script id="' . esc_attr($id) . '">(function(){var s=' . $src_json . ';var done=false;function l(){if(done)return;done=true;var e=document.createElement("script");e.src=s;e.async=true;document.head.appendChild(e);}["pointerdown","keydown","scroll","touchstart"].forEach(function(ev){window.addEventListener(ev,l,{once:true,passive:true});});if("requestIdleCallback"in window){requestIdleCallback(l,{timeout:4000});}else{setTimeout(l,4000);}})();</script>';
}

/**
 * Rank Math's Analytics module (and gtag.js / gtm.js generally).
 */
function homepage_is_gtag_loader($src, $id = ''): bool
{
    $src = is_string($src) ? $src : '';
    $id = is_string($id) ? $id : '';

    if ($id === 'google_gtagjs') {
        return $src !== '';
    }

    return str_contains($src, 'googletagmanager.com/gtag/js')
        || str_contains($src, 'googletagmanager.com/gtm.js');
}

/**
 * Rank Math Pro prints the loader with wp_print_script_tag() (id="google_gtagjs"),
 * which never passes through script_loader_tag. Strip the src here so the
 * browser cannot fetch it while the tag is being built.
 *
 * @param mixed $attributes
 * @return array<string, mixed>
 */
function homepage_strip_gtag_script_attributes($attributes): array
{
    if (! is_array($attributes)) {
        return [];
    }

    if (! is_front_page()) {
        return $attributes;
    }

    $src = isset($attributes['src']) ? (string) $attributes['src'] : '';
    $id = isset($attributes['id']) ? (string) $attributes['id'] : '';
    if ($src === '' || ! homepage_is_gtag_loader($src, $id)) {
        return $attributes;
    }

    $GLOBALS['esvaja_gtag_src'] = $src;
    unset($attributes['src'], $attributes['async']);
    $attributes['id'] = 'google_gtagjs-blocked';
    $attributes['type'] = 'text/plain';

    return $attributes;
}

add_filter('wp_script_attributes', __NAMESPACE__ . '\\homepage_strip_gtag_script_attributes');

/**
 * Replace a directly printed gtag loader and keep google_gtagjs-inline untouched.
 * The inline block is the dataLayer / gtag('config') queue and must run immediately.
 */
function homepage_rewrite_gtag_markup(string $html): string
{
    $html = (string) preg_replace_callback(
        '#<script\b([^>]*)>\s*</script>#i',
        static function (array $match): string {
            $attrs = $match[1];
            $id = '';
            if (preg_match('#\bid=(["\'])([^"\']+)\1#i', $attrs, $id_match)) {
                $id = $id_match[2];
            }
            if (! preg_match('#\bsrc=(["\'])([^"\']+)\1#i', $attrs, $src_match)) {
                if ($id === 'google_gtagjs' || $id === 'google_gtagjs-blocked') {
                    return '';
                }

                return $match[0];
            }

            $src = html_entity_decode($src_match[2], ENT_QUOTES);
            if (! homepage_is_gtag_loader($src, $id)) {
                return $match[0];
            }

            $GLOBALS['esvaja_gtag_src'] = $src;

            return '';
        },
        $html
    );

    if (! empty($GLOBALS['esvaja_gtag_src']) && ! str_contains($html, 'google_gtagjs-delayed')) {
        $html .= homepage_idle_loader((string) $GLOBALS['esvaja_gtag_src'], 'google_gtagjs-delayed');
    }

    return $html;
}

/**
 * Capture wp_head on the front page. Rank Math echoes the tag during that hook.
 */
function homepage_gtag_buffer_start(): void
{
    if (! is_front_page()) {
        return;
    }

    ob_start();
    $GLOBALS['esvaja_gtag_ob_level'] = ob_get_level();
}

function homepage_gtag_buffer_end(): void
{
    if (! is_front_page() || empty($GLOBALS['esvaja_gtag_ob_level'])) {
        return;
    }

    $target = (int) $GLOBALS['esvaja_gtag_ob_level'];
    unset($GLOBALS['esvaja_gtag_ob_level']);

    while (ob_get_level() > $target) {
        ob_end_flush();
    }

    if (ob_get_level() !== $target) {
        return;
    }

    $html = ob_get_clean();
    echo homepage_rewrite_gtag_markup(is_string($html) ? $html : '');
}

add_action('wp_head', __NAMESPACE__ . '\\homepage_gtag_buffer_start', -9999);
add_action('wp_head', __NAMESPACE__ . '\\homepage_gtag_buffer_end', PHP_INT_MAX);

/**
 * Enqueued gtag (not Rank Math's direct print) still goes through this filter.
 *
 * @param mixed $tag
 * @param mixed $handle
 * @param mixed $src
 */
function homepage_delay_gtag($tag, $handle, $src): string
{
    if (! is_string($tag)) {
        return '';
    }

    if (! is_front_page() || ! is_string($src)) {
        return $tag;
    }

    $handle = is_string($handle) ? $handle : '';
    if (! homepage_is_gtag_loader($src, $handle)) {
        return $tag;
    }

    $tag = (string) preg_replace(
        '#<script\b[^>]*\bsrc=["\'][^"\']*(?:googletagmanager\.com/(?:gtag/js|gtm\.js))[^"\']*["\'][^>]*>\s*</script>#i',
        '',
        $tag,
        1
    );

    if (! empty($GLOBALS['esvaja_gtag_ob_level'])) {
        $GLOBALS['esvaja_gtag_src'] = $src;

        return $tag;
    }

    return $tag . homepage_idle_loader($src, 'google_gtagjs-delayed');
}

add_filter('script_loader_tag', __NAMESPACE__ . '\\homepage_delay_gtag', 20, 3);

/**
 * The Zoho form HTML is stored in an option. Leave the form and its validation
 * inline, and load only the analytics servlet the same way as gtag.
 *
 * The newsletter partial calls this directly. It must not pass the form
 * through the_content_lite: convert_chars turns && in the validation script
 * into &#038;&#038;. The filter below still covers any other front-page copy
 * that includes the servlet.
 *
 * @param mixed $content
 * @return mixed
 */
function homepage_delay_zoho_analytics($content)
{
    if (! is_front_page() || ! is_string($content) || ! str_contains($content, 'crm.zohopublic.com')) {
        return $content;
    }

    return preg_replace_callback(
        '#<script\b[^>]*\bsrc=(["\'])([^"\']*crm\.zohopublic\.com[^"\']*)\1[^>]*>\s*</script>#i',
        static function (array $match): string {
            $src = html_entity_decode($match[2], ENT_QUOTES);

            return homepage_idle_loader($src, 'zoho-analytics-delayed');
        },
        $content
    );
}

add_filter('the_content_lite', __NAMESPACE__ . '\\homepage_delay_zoho_analytics', 99);

/**
 * Printed immediately after jQuery, before the theme bundle.
 * The no-op covers a .slick() call once woolentor's copy is gone.
 * The click listener drives the CSS category row.
 */
function homepage_slider_script(): void
{
    if (! is_front_page()) {
        return;
    }

    $js = <<<'JS'
window.jQuery && (jQuery.fn.slick = jQuery.fn.slick || function () { return this; });
document.addEventListener('click', function (event) {
  var target = event.target;
  if (!target || !target.closest) return;
  var control = target.closest('#icon-slick-left, #icon-slick-right');
  if (!control) return;
  var slider = document.querySelector('.home-cat-scroll');
  if (!slider) return;
  var box = slider.querySelector('.cat-box');
  var delta = box ? box.getBoundingClientRect().width : slider.clientWidth;
  slider.scrollBy({ left: control.id === 'icon-slick-left' ? -delta : delta, behavior: 'smooth' });
});
JS;

    wp_add_inline_script('jquery', $js, 'after');
}

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\homepage_slider_script', 1001);

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

/**
 * The hero rules are small. Inlining them removes a render-blocking request
 * and keeps the rules in the HTML if the stylesheet file is not deployed.
 */
function homepage_inline_front_css(): void
{
    if (! is_front_page()) {
        return;
    }

    $path = get_theme_file_path('resources/styles/front-page.css');
    if (! is_readable($path)) {
        return;
    }

    $css = file_get_contents($path);
    if (! is_string($css) || $css === '') {
        return;
    }

    echo '<style id="e-svaja-front-page">' . $css . '</style>' . "\n";
}

add_action('wp_head', __NAMESPACE__ . '\\homepage_inline_front_css', 7);
