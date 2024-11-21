<?php
/**
 * TLG Content
 *
 * @wordpress-plugin
 * Plugin Name: TLG Global
 * Description: Custom content configuration for The Little Gym (global)
 * Requires Plugins: tlg-core
 */

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

if (!class_exists('TlgGlobal')) {
    class TlgGlobal
    {
        public function __construct()
        {
        }

        public static $plugin_slug = 'tlg-global';
        public static $plugin_name = 'TLG Global';

        public function initialize()
        {
            add_action('init', [$this, 'register_franchise_home_page']);
        }

        public function register_franchise_home_page()
        {
            $args = [
                'labels' => [
                    'name' => __('Franchise home pages'),
                    'singular_name' => __('Franchise home page'),
                ],
                'show_in_rest' => true,
                'supports' => ['title'],
                'has_archive' => true,
                'rewrite' => ['slug' => 'franchise-home-pages'],
                'exclude_from_search' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-admin-home',
                'hierarchical' => true, # set to false if you don't want parent/child relationships for the entries
                'show_in_graphql' => true, # Set to false if you want to exclude this type from the GraphQL Schema
                'graphql_single_name' => 'franchise_home_page',
                'graphql_plural_name' => 'franchise_home_pages', # If set to the same name as graphql_single_name, the field name will default to `all${graphql_single_name}`, i.e. `allDocument`.
                'public' => true, # set to false if entries of the post_type should not have public URIs per entry
                'publicly_queryable' => true,

                // Check options here: https://developer.wordpress.org/reference/functions/register_post_type/ https://developer.wordpress.org/reference/functions/get_post_type_capabilities/
                'capability' => 'manage_options',
            ];

            register_post_type('franchise_home_page', $args);
        }
    }

    function tlgGlobal()
    {
        global $tlgGlobal;

        // Instantiate only once.
        if (!isset($tlgGlobal)) {
            $tlgGlobal = new TlgGlobal();
            $tlgGlobal->initialize();
        }
        return $tlgGlobal;
    }
    // Instantiate.
    tlgGlobal();
} // class_exists check

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    /**
     * @disregard P1009 Undefined type
     */
    acf_add_local_field_group([
        'key' => 'group_673f0bc62394c',
        'title' => 'Franchise home page',
        'fields' => [
            [
                'key' => 'field_673f0bc6eb8f2',
                'label' => 'Statistics row',
                'name' => 'statistics_row',
                'aria-label' => '',
                'type' => 'group',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'wpml_cf_preferences' => 1,
                'layout' => 'block',
                'show_in_graphql' => 1,
                'graphql_description' => '',
                'graphql_field_name' => 'statisticsRow',
                'graphql_non_null' => 0,
                'sub_fields' => [
                    [
                        'key' => 'field_673f0beeeb8f3',
                        'label' => 'Heading',
                        'name' => 'heading',
                        'aria-label' => '',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'wpml_cf_preferences' => 2,
                        'default_value' => '',
                        'maxlength' => '',
                        'allow_in_bindings' => 0,
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'show_in_graphql' => 1,
                        'graphql_description' => '',
                        'graphql_field_name' => 'heading',
                        'graphql_non_null' => 0,
                    ],
                    [
                        'key' => 'field_673f0c0deb8f4',
                        'label' => 'Statistics',
                        'name' => 'statistics',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'wpml_cf_preferences' => 1,
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 3,
                        'max' => 3,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'show_in_graphql' => 1,
                        'graphql_description' => '',
                        'graphql_field_name' => 'statistics',
                        'graphql_non_null' => 0,
                        'rows_per_page' => 20,
                        'sub_fields' => [
                            [
                                'key' => 'field_673f0c34eb8f5',
                                'label' => 'Figure',
                                'name' => 'figure',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => [
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ],
                                'wpml_cf_preferences' => 2,
                                'default_value' => '',
                                'maxlength' => '',
                                'allow_in_bindings' => 0,
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'show_in_graphql' => 1,
                                'graphql_description' => '',
                                'graphql_field_name' => 'figure',
                                'graphql_non_null' => 0,
                                'parent_repeater' => 'field_673f0c0deb8f4',
                            ],
                            [
                                'key' => 'field_673f0c3deb8f6',
                                'label' => 'Label',
                                'name' => 'label',
                                'aria-label' => '',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => [
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ],
                                'wpml_cf_preferences' => 2,
                                'default_value' => '',
                                'maxlength' => '',
                                'allow_in_bindings' => 0,
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'show_in_graphql' => 1,
                                'graphql_description' => '',
                                'graphql_field_name' => 'label',
                                'graphql_non_null' => 0,
                                'parent_repeater' => 'field_673f0c0deb8f4',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'franchise_home_page',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'acfml_field_group_mode' => 'translation',
        'show_in_graphql' => 1,
        'graphql_field_name' => 'franchiseHomePage',
        'map_graphql_types_from_location_rules' => 0,
        'graphql_types' => '',
    ]);
});
