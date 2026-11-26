<?php

/**
 * Hard-coded Christmas Gifts page. Products are resolved at render time.
 * Missing products become text-only cards. No parameter is type-hinted,
 * because WooCommerce passes null and empty strings into these calls.
 */

namespace App;

/**
 * @return array<int, array{heading: string, items: array<int, array<string, mixed>>}>
 */
function christmas_gifts_sections(): array
{
    return [
        [
            'heading' => 'Under £100',
            'items' => [
                christmas_gifts_item(
                    'Molton Brown-inspired Mini Vases',
                    'small, bright and in six colours. A stocking filler that lives on a windowsill.',
                    'molton-brown-inspired'
                ),
                christmas_gifts_item(
                    'Turquoise Glass Tray',
                    'for rings, keys or a single candle.',
                    'turquoise-glass-tray'
                ),
                christmas_gifts_item(
                    'Basil Bird',
                    'our most-reviewed piece, in a choice of colours.',
                    'basil-bird-glass-sculpture'
                ),
                christmas_gifts_item(
                    'Katie Kitten, white',
                    'for the cat lover in your life.',
                    'katie-kitten-glass-sculpture',
                    ['pa_colour' => 'white']
                ),
                christmas_gifts_item(
                    'Ollie Elephant',
                    'trunk raised, the traditional sign of good luck.',
                    'ollie-elephant-glass-sculpture'
                ),
            ],
        ],
        [
            'heading' => '£100–£200',
            'band' => true,
            'items' => [
                christmas_gifts_item(
                    'Edvinas & Edita',
                    'a pair of baby emperor penguins, always sold together. A gift for a couple.',
                    'penguin-glass-sculpture',
                    ['pa_option' => 'edwinas-edita-baby-emperor-penguins']
                ),
                christmas_gifts_item(
                    'Sidney Snail',
                    'golden or black-gold, unhurried and shining.',
                    'sidney-snail-glass-sculpture'
                ),
                christmas_gifts_item(
                    'Annie Angelfish, large',
                    'Gold & Pink Bubbles or Gold & Blue. The bubbles are placed inside the glass by design.',
                    'annie-angelfish-glass-sculpture'
                ),
                christmas_gifts_item(
                    'Oasis Pyramid Vase',
                    'a pyramid form that holds the light from every side.',
                    'oasis-pyramid-vase'
                ),
                christmas_gifts_item(
                    'Naples Tall Glass Vase',
                    'tall enough for winter branches or a single stem.',
                    'naples-tall-glass-vase'
                ),
                christmas_gifts_item(
                    'Red Sands Vase and Plate',
                    'warm reds, designed to be shown together.',
                    'red-sands-vase'
                ),
                christmas_gifts_item(
                    'Palanga Vase and Plate',
                    'calm, cool tones, designed as a pair.',
                    'palanga-glass-vase'
                ),
                christmas_gifts_item(
                    'Aegean Glass Plate',
                    'a statement for a dining or console table.',
                    'aegean-glass-plate'
                ),
            ],
        ],
        [
            'heading' => 'For collectors',
            'items' => [
                christmas_gifts_item(
                    'Dave Dolphin, Golden Amber',
                    'warm amber glass that glows in lamplight.',
                    'dave-dolphin-glass-sculpture',
                    ['pa_colour' => 'golden-amber']
                ),
                christmas_gifts_item(
                    'Cecil Scorpion',
                    'a sculpture for the collector who already has everything.',
                    'cecil-scorpion-glass-sculpture'
                ),
            ],
        ],
    ];
}

/**
 * @param array<string, string> $attributes
 * @return array<string, mixed>
 */
function christmas_gifts_item(string $name, string $line, string $slug, array $attributes = []): array
{
    return [
        'name' => $name,
        'line' => $line,
        'slug' => $slug,
        'attributes' => $attributes,
    ];
}

/**
 * Friday 27 Nov 2026 00:00:00 through Monday 30 Nov 2026 23:59:59, Europe/London.
 */
function christmas_gifts_black_friday_active($now = null): bool
{
    $tz = new \DateTimeZone('Europe/London');

    if ($now instanceof \DateTimeImmutable) {
        $now = $now->setTimezone($tz);
    } else {
        $stamp = function_exists('wp_date')
            ? wp_date('Y-m-d H:i:s', null, $tz)
            : (new \DateTimeImmutable('now', $tz))->format('Y-m-d H:i:s');
        $stamp = is_string($stamp) ? $stamp : '';
        $now = $stamp !== ''
            ? new \DateTimeImmutable($stamp, $tz)
            : new \DateTimeImmutable('now', $tz);
    }

    $start = new \DateTimeImmutable('2026-11-27 00:00:00', $tz);
    $end = new \DateTimeImmutable('2026-11-30 23:59:59', $tz);

    return $now >= $start && $now <= $end;
}

function christmas_gifts_black_friday_line(): string
{
    if (! christmas_gifts_black_friday_active()) {
        return '';
    }

    return 'The Black Friday Gift Edit: complimentary gift wrapping on every order until Monday midnight.';
}

/**
 * @param mixed $slug
 * @param array<string, string> $attributes
 * @return object|null
 */
function christmas_gifts_find($slug, array $attributes = [])
{
    if (! is_string($slug) || $slug === '') {
        return null;
    }

    $product = null;

    if (function_exists('get_page_by_path')) {
        $post = get_page_by_path($slug, OBJECT, 'product');
        if ($post instanceof \WP_Post && function_exists('wc_get_product')) {
            $found = wc_get_product((int) $post->ID);
            if (is_object($found)) {
                $product = $found;
            }
        }
    }

    if (! is_object($product) && function_exists('wc_get_products')) {
        $found = wc_get_products([
            'status' => 'publish',
            'limit' => 1,
            'slug' => $slug,
            'return' => 'objects',
        ]);
        if (is_array($found) && isset($found[0]) && is_object($found[0])) {
            $product = $found[0];
        }
    }

    if (! is_object($product)) {
        return null;
    }

    if ($attributes === [] || ! method_exists($product, 'is_type') || ! $product->is_type('variable')) {
        return $product;
    }

    $variation = christmas_gifts_match_variation($product, $attributes);

    return is_object($variation) ? $variation : $product;
}

/**
 * @param array<string, string> $need
 * @return object|null
 */
function christmas_gifts_match_variation($product, array $need)
{
    if (! is_object($product) || ! method_exists($product, 'get_children') || ! function_exists('wc_get_product')) {
        return null;
    }

    $children = $product->get_children();
    if (! is_array($children)) {
        return null;
    }

    foreach ($children as $child_id) {
        $child_id = is_numeric($child_id) ? (int) $child_id : 0;
        if ($child_id < 1) {
            continue;
        }

        $variation = wc_get_product($child_id);
        if (! is_object($variation) || ! method_exists($variation, 'get_attributes')) {
            continue;
        }

        $attrs = $variation->get_attributes();
        if (! is_array($attrs)) {
            continue;
        }

        $matches = true;
        foreach ($need as $key => $value) {
            $current = $attrs[$key] ?? '';
            if (! is_string($key) || ! is_string($value) || ! is_string($current) || $current !== $value) {
                $matches = false;
                break;
            }
        }

        if ($matches) {
            return $variation;
        }
    }

    return null;
}

/**
 * £75 gift-card variation when the amount is stored as £75 or 75.
 *
 * @return object|null
 */
function christmas_gifts_gift_card()
{
    $product = christmas_gifts_find('gift-card');
    if (! is_object($product)) {
        return null;
    }

    if (! method_exists($product, 'is_type') || ! $product->is_type('variable') || ! method_exists($product, 'get_children')) {
        return $product;
    }

    $children = $product->get_children();
    if (! is_array($children) || ! function_exists('wc_get_product')) {
        return $product;
    }

    foreach ($children as $child_id) {
        $child_id = is_numeric($child_id) ? (int) $child_id : 0;
        if ($child_id < 1) {
            continue;
        }

        $variation = wc_get_product($child_id);
        if (! is_object($variation) || ! method_exists($variation, 'get_attributes')) {
            continue;
        }

        $attrs = $variation->get_attributes();
        if (! is_array($attrs)) {
            continue;
        }

        foreach ($attrs as $value) {
            if (! is_string($value)) {
                continue;
            }
            if ($value === '£75' || $value === '75' || $value === '75.00') {
                return $variation;
            }
        }
    }

    return $product;
}

function christmas_gifts_permalink($product): string
{
    if (! is_object($product) || ! method_exists($product, 'get_permalink')) {
        return '';
    }

    $url = $product->get_permalink();

    return is_string($url) ? $url : '';
}

/**
 * @param array<string, mixed> $item
 */
function christmas_gifts_render_card(array $item): void
{
    $name = isset($item['name']) && is_string($item['name']) ? $item['name'] : '';
    $line = isset($item['line']) && is_string($item['line']) ? $item['line'] : '';
    $slug = $item['slug'] ?? '';
    $attributes = isset($item['attributes']) && is_array($item['attributes']) ? $item['attributes'] : [];
    $product = christmas_gifts_find($slug, $attributes);

    if (! is_object($product) || ! function_exists('wc_get_template_part')) {
        christmas_gifts_render_missing_card($name, $line);

        return;
    }

    $post_id = method_exists($product, 'get_id') ? (int) $product->get_id() : 0;
    $post_object = $post_id > 0 && function_exists('get_post') ? get_post($post_id) : null;
    if (! $post_object instanceof \WP_Post) {
        christmas_gifts_render_missing_card($name, $line);

        return;
    }

    $previous_post = $GLOBALS['post'] ?? null;
    $previous_product = $GLOBALS['product'] ?? null;

    setup_postdata($GLOBALS['post'] = $post_object);
    $GLOBALS['product'] = $product;

    $visibility = static function ($visible, $id) use ($post_id) {
        $id = is_numeric($id) ? (int) $id : 0;

        return $post_id > 0 && $id === $post_id ? true : $visible;
    };
    $rename = static function ($title, $id = 0) use ($name, $post_id) {
        $id = is_numeric($id) ? (int) $id : 0;

        return $post_id > 0 && $id === $post_id ? $name : $title;
    };
    $image_attrs = static function ($attr) use ($name) {
        if (! is_array($attr)) {
            $attr = [];
        }
        $attr['alt'] = $name;
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';

        return $attr;
    };
    $description = static function () use ($line) {
        if ($line === '') {
            return;
        }
        echo '<p class="christmas-gifts-line">' . esc_html($line) . '</p>';
    };

    add_filter('woocommerce_product_is_visible', $visibility, 10, 2);
    add_filter('the_title', $rename, 10, 2);
    add_filter('wp_get_attachment_image_attributes', $image_attrs, 20);
    add_action('woocommerce_shop_loop_item_title', $description, 15);

    wc_get_template_part('content', 'product');

    remove_action('woocommerce_shop_loop_item_title', $description, 15);
    remove_filter('wp_get_attachment_image_attributes', $image_attrs, 20);
    remove_filter('the_title', $rename, 10);
    remove_filter('woocommerce_product_is_visible', $visibility, 10);

    $GLOBALS['product'] = $previous_product;
    $GLOBALS['post'] = $previous_post;
    if (function_exists('wp_reset_postdata')) {
        wp_reset_postdata();
    }
}

/**
 * Attachment 33507 is the page hero and the LCP image. Empty string when it is missing.
 */
/**
 * Split a WP page title for the hero.
 * "|" or " – " (en dash) puts the part after the separator on the next line in italics.
 * Otherwise the latter half of the words, split on a word boundary, is italic.
 */
function christmas_gifts_title_html($title): string
{
    $title = is_string($title) ? trim(preg_replace('/\s+/', ' ', $title) ?? '') : '';
    if ($title === '') {
        return '';
    }

    $first = $title;
    $second = '';
    if (str_contains($title, '|')) {
        $bits = explode('|', $title, 2);
        $first = trim($bits[0]);
        $second = trim($bits[1] ?? '');
    } elseif (str_contains($title, ' – ')) {
        $bits = explode(' – ', $title, 2);
        $first = trim($bits[0]);
        $second = trim($bits[1] ?? '');
    } else {
        $words = preg_split('/\s+/', $title);
        $words = is_array($words) ? array_values(array_filter($words, static function ($word) {
            return is_string($word) && $word !== '';
        })) : [];
        $count = count($words);
        if ($count > 1) {
            $first_count = (int) ceil($count / 2);
            if ($first_count >= $count) {
                $first_count = $count - 1;
            }
            $first = implode(' ', array_slice($words, 0, $first_count));
            $second = implode(' ', array_slice($words, $first_count));
        }
    }

    $html = esc_html($first);
    if ($second !== '') {
        $html .= '<br><em>' . esc_html($second) . '</em>';
    }

    return $html;
}

function christmas_gifts_render_missing_card(string $name, string $line): void
{
    echo '<div class="col-md-6 col-lg-3">';
    echo '<div class="card h-100"><div class="shop-card h-100"><div class="title-wrap">';
    echo '<h2 class="woocommerce-loop-product__title">' . esc_html($name) . '</h2>';
    if ($line !== '') {
        echo '<p class="christmas-gifts-line">' . esc_html($line) . '</p>';
    }
    echo '</div></div></div></div>';
}

function christmas_gifts_render_slider(string $heading, array $items): void
{
    $label = $heading !== '' ? $heading : 'gifts';

    echo '<div class="christmas-gifts-slider" data-christmas-slider>';
    echo '<button type="button" class="christmas-gifts-nav christmas-gifts-prev" aria-label="' . esc_attr('Previous gifts in ' . $label) . '">';
    echo '<span aria-hidden="true">&#8249;</span></button>';
    echo '<div class="christmas-gifts-track products" tabindex="0">';

    foreach ($items as $item) {
        if (is_array($item)) {
            christmas_gifts_render_card($item);
        }
    }

    echo '</div>';
    echo '<button type="button" class="christmas-gifts-nav christmas-gifts-next" aria-label="' . esc_attr('Next gifts in ' . $label) . '">';
    echo '<span aria-hidden="true">&#8250;</span></button>';
    echo '</div>';
}

function christmas_gifts_render(): void
{
    foreach (christmas_gifts_sections() as $section) {
        $heading = isset($section['heading']) && is_string($section['heading']) ? $section['heading'] : '';
        $items = isset($section['items']) && is_array($section['items']) ? $section['items'] : [];
        $band = ! empty($section['band']);

        $anchor = $heading === 'Under £100' ? ' id="under-100"' : '';
        echo '<section' . $anchor . ' class="' . ($band ? 'christmas-gifts-band' : 'christmas-gifts-section') . '">';
        if ($band) {
            echo '<div class="container-xxl p-clamp-x">';
        }
        echo '<h2 class="mb-3">' . esc_html($heading) . '</h2>';
        echo '<div class="col-for-products"><div class="woocommerce">';
        christmas_gifts_render_slider($heading, $items);
        echo '</div></div>';
        if ($band) {
            echo '</div>';
        }
        echo '</section>';
    }

    $gift = christmas_gifts_gift_card();
    $gift_url = christmas_gifts_permalink($gift);

    echo '<section class="christmas-gifts-cta">';
    echo '<div class="christmas-gifts-cta-copy">';
    echo '<h2>Not sure what they would <em>choose?</em></h2>';
    echo '<p>The £75 SVAJA Gift Card lets them decide.</p>';
    echo '</div>';
    echo '<div class="christmas-gifts-cta-actions">';
    if ($gift_url !== '') {
        echo '<a class="christmas-gifts-cta-btn christmas-gifts-cta-btn-primary" href="' . esc_url($gift_url) . '">The £75 SVAJA Gift Card</a>';
    }
    echo '<a class="christmas-gifts-cta-btn christmas-gifts-cta-btn-secondary" href="#under-100">Browse all gifts</a>';
    echo '</div></section>';
}
