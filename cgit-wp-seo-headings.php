<?php

/**
 * Plugin Name:  Castlegate IT WP SEO Headings
 * Plugin URI:   https://github.com/castlegateit/cgit-wp-seo-headings
 * Description:  Yoast- and ACF-compatible SEO headings.
 * Version:      1.0.4
 * Requires PHP: 8.2
 * Author:       Castlegate IT
 * Author URI:   https://www.castlegateit.co.uk/
 * License:      MIT
 * Update URI:   https://github.com/castlegateit/cgit-wp-seo-headings
 */

if (!defined('ABSPATH')) {
    wp_die('Access denied');
}

define('CGIT_WP_SEO_HEADINGS_VERSION', '1.0.4');
define('CGIT_WP_SEO_HEADINGS_PLUGIN_FILE', __FILE__);
define('CGIT_WP_SEO_HEADINGS_PLUGIN_DIR', __DIR__);

/**
 * Return SEO heading
 *
 * @param string|null $heading
 * @return string
 */
function cgit_seo_heading(string $heading = null): string
{
    // Heading set as function parameter?
    if ($heading) {
        return $heading;
    }

    // Single post or page? Attempt to load the ACF heading field value and, if
    // it is not empty, return it.
    if (is_page() || is_single()) {
        if (function_exists('get_field')) {
            $heading = get_field('seo_heading');

            if ($heading) {
                return $heading;
            }
        }
    }

    // Set default title separator.
    $separator = html_entity_decode('&mdash;');

    // If Yoast is available and a title separator has been set in Yoast's
    // options, use that instead.
    if (class_exists('\Yoast\WP\SEO\Helpers\Options_Helper')) {
        $yoast_options = new \Yoast\WP\SEO\Helpers\Options_Helper();
        $yoast_separator = $yoast_options->get_title_separator();

        if ($yoast_separator) {
            $separator = $yoast_separator;
        }
    }

    // Generate a heading based on the page title.
    $heading = trim(wp_title($separator, false));

    // Remove the leading title separator from the heading.
    $length = mb_strlen($separator);

    if (mb_substr($heading, 0, $length) === $separator) {
        $heading = trim(mb_substr($heading, $length));
    }

    // Optionally remove the site name
    $remove_site_name = apply_filters('cgit_seo_headings_remove_default_sitename', true);

    if ($remove_site_name) {
        // Match a separator followed by site name at the end of the heading
        $regex = "/[\-\–\—\:\·\•\*\⋆\|\~\«\»\>\<]\s+";
        $regex.= preg_quote(trim(get_bloginfo('name'))).'\s?$/';

        $heading = trim(preg_replace($regex, '', $heading));
    }

    // Return the title-based heading.
    if ($heading) {
        return $heading;
    }

    // If nothing else is available, return the site name.
    return get_bloginfo('name');
}

function remove_site_name(string $string): string
{

}

/**
 * Define ACF heading field
 *
 * @return void
 */
add_action('acf/init', function () {
    // Assemble list of all public post types.
    $types = array_values(get_post_types(['public' => true]));
    $types = apply_filters('cgit_seo_headings_post_types', $types);

    // Assemble list of ACF field group locations based on post types.
    $location = [];

    foreach ($types as $type) {
        $location[] = [
            [
                'param' => 'post_type',
                'operator' => '==',
                'value' => $type,
            ]
        ];
    }

    // Set field group menu order.
    $menu_order = apply_filters('cgit_seo_headings_menu_order', 500);

    // Assemble ACF field group parameters.
    $args = [
        'title' => 'SEO fields',
        'key' => 'cgit_seo_headings__seo_headings_fields',
        'location' => $location,
        'menu_order' => $menu_order,
        'fields' => [
            [
                'label' => 'Heading',
                'key' => 'cgit_seo_headings__seo_headings_fields__seo_heading',
                'name' => 'seo_heading',
                'type' => 'text',
            ],
        ],
    ];

    $args = apply_filters('cgit_seo_headings_field_group_args', $args);

    // Register ACF field group.
    acf_add_local_field_group($args);
});

/**
 * Move Yoast fields below ACF fields
 *
 * @param mixed $priority
 * @return mixed
 */
add_filter('wpseo_metabox_prio', function ($priority) {
    $move = apply_filters('cgit_seo_headings_move_yoast', true);

    if ($move) {
        return 'low';
    }

    return $priority;
});
