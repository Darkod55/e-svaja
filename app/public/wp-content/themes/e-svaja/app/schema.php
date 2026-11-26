<?php

namespace App;

/**
 * Organization VAT number for the Rank Math JSON-LD graph.
 *
 * Change it without editing this file:
 *
 *     add_filter('esvaja/org_vat_id', static function () {
 *         return 'GB000000000';
 *     });
 */
function schema_org_vat_id()
{
    $vat = 'GB266758161';
    if (function_exists('apply_filters')) {
        $vat = apply_filters('esvaja/org_vat_id', $vat);
    }

    return is_string($vat) ? $vat : '';
}

/**
 * Rank Math's `rank_math/json_ld` data is an associative array keyed by
 * entity name (`publisher`, `website`, `place`, `richSnippet`, …), not a
 * finished `@graph` list. Each value is one entity. A later Rank Math
 * build may nest entities under `@graph` or in a numeric list, so those
 * are walked as well.
 *
 * A node whose only key is `@id` is a reference to the organization, not
 * the entity itself, and is left alone. `vatID` is added to the entity
 * only when that property is missing or empty. Every other field stays
 * as Rank Math (or another filter) set it.
 *
 * The callback parameters are untyped: Rank Math can pass a non-array.
 */
function schema_filter_rank_math_json_ld($data, $jsonld)
{
    unset($jsonld);

    if (! is_array($data)) {
        return $data;
    }

    $vat = schema_org_vat_id();
    if ($vat === '') {
        return $data;
    }

    return schema_stamp_org_vat_id($data, $vat);
}

/**
 * @param mixed $node
 * @param mixed $vat
 * @return mixed
 */
function schema_stamp_org_vat_id($node, $vat)
{
    if (! is_array($node) || ! is_string($vat) || $vat === '') {
        return $node;
    }

    if (array_is_list($node)) {
        foreach ($node as $index => $item) {
            if (is_array($item)) {
                $node[$index] = schema_stamp_org_vat_id($item, $vat);
            }
        }

        return $node;
    }

    if (schema_is_organization_entity($node)) {
        if (! array_key_exists('vatID', $node) || $node['vatID'] === null || $node['vatID'] === '') {
            $node['vatID'] = $vat;
        }

        return $node;
    }

    foreach ($node as $key => $value) {
        if (is_array($value)) {
            $node[$key] = schema_stamp_org_vat_id($value, $vat);
        }
    }

    return $node;
}

/**
 * Organization when `@id` ends in `#organization`, or when `@type`
 * (string or list) contains Organization, Store, or LocalBusiness.
 *
 * @param mixed $node
 */
function schema_is_organization_entity($node)
{
    if (! is_array($node) || array_is_list($node)) {
        return false;
    }

    if (count($node) === 1 && array_key_exists('@id', $node)) {
        return false;
    }

    $id = isset($node['@id']) && is_string($node['@id']) ? rtrim($node['@id'], '/') : '';
    if ($id !== '' && str_ends_with($id, '#organization')) {
        return true;
    }

    $type = $node['@type'] ?? null;
    $names = [];
    if (is_string($type) && $type !== '') {
        $names[] = $type;
    } elseif (is_array($type)) {
        foreach ($type as $name) {
            if (is_string($name) && $name !== '') {
                $names[] = $name;
            }
        }
    }

    foreach ($names as $name) {
        if (str_contains($name, 'Organization') || str_contains($name, 'Store') || str_contains($name, 'LocalBusiness')) {
            return true;
        }
    }

    return false;
}

add_filter('rank_math/json_ld', __NAMESPACE__ . '\\schema_filter_rank_math_json_ld', 99, 2);

/**
 * Rank Math's Block_Parser only looks inside core/group, core/columns, and
 * core/column. The FAQ block on /faqs/ is an InnerBlock of an ACF block
 * (acf/module-one-column), so it never becomes FAQPage schema.
 *
 * `rank_math/schema/block/before_filter` receives the parse_blocks() list.
 * Rank Math then keeps only rank-math/* blocks, so this returns that flat
 * list from every depth. A core/block reusable ref is parsed once per
 * insertion; a ref that points at itself is not followed again.
 *
 * The callback argument is untyped: the filter can be handed a non-array.
 */
function schema_filter_rank_math_blocks($blocks)
{
    if (! is_array($blocks)) {
        return $blocks;
    }

    $found = [];
    $stack = [];
    schema_collect_rank_math_blocks($blocks, $found, $stack);

    return $found;
}

/**
 * @param mixed $blocks
 * @param array $found
 * @param array $stack
 */
function schema_collect_rank_math_blocks($blocks, &$found, &$stack)
{
    if (! is_array($blocks)) {
        return;
    }

    foreach ($blocks as $block) {
        if (! is_array($block)) {
            continue;
        }

        $name = isset($block['blockName']) && is_string($block['blockName']) ? $block['blockName'] : '';
        if ($name !== '' && str_starts_with($name, 'rank-math/')) {
            $found[] = $block;
        }

        if ($name === 'core/block') {
            schema_collect_reusable_rank_math_blocks($block, $found, $stack);
        }

        if (isset($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            schema_collect_rank_math_blocks($block['innerBlocks'], $found, $stack);
        }
    }
}

/**
 * @param mixed $block
 * @param array $found
 * @param array $stack
 */
function schema_collect_reusable_rank_math_blocks($block, &$found, &$stack)
{
    if (! is_array($block) || ! function_exists('get_post_field') || ! function_exists('parse_blocks')) {
        return;
    }

    $ref = $block['attrs']['ref'] ?? null;
    if (is_int($ref)) {
        $ref_id = $ref;
    } elseif (is_string($ref) && ctype_digit($ref)) {
        $ref_id = (int) $ref;
    } else {
        return;
    }

    if ($ref_id <= 0 || ! empty($stack[$ref_id])) {
        return;
    }

    $stack[$ref_id] = true;
    $content = get_post_field('post_content', $ref_id);
    if (is_string($content) && $content !== '') {
        $parsed = parse_blocks($content);
        if (is_array($parsed)) {
            schema_collect_rank_math_blocks($parsed, $found, $stack);
        }
    }
    unset($stack[$ref_id]);
}

add_filter('rank_math/schema/block/before_filter', __NAMESPACE__ . '\\schema_filter_rank_math_blocks');
