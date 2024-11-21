<?php
/**
 * TLG Content
 *
 * @wordpress-plugin
 * Plugin Name: TLG Franchise
 * Description: Custom content configuration for The Little Gym (franchise).
 * Requires Plugins: tlg-core
 */

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

if (!class_exists('TlgFranchise')) {
    class TlgFranchise
    {
        public function __construct()
        {
        }

        public static $plugin_slug = 'tlg-franchise';
        public static $plugin_name = 'TLG Franchise';

        public function initialize()
        {
            add_action('init', [$this, 'register_home_page']);
            add_action('init', [$this, 'register_moderated_home_page']);
        }

        public function register_home_page()
        {
            $args = [
                'labels' => [
                    'name' => __('Home pages'),
                    'singular_name' => __('Home page'),
                ],
                'show_in_rest' => true,
                'supports' => ['title'],
                'has_archive' => true,
                'rewrite' => ['slug' => 'home-pages'],
                'exclude_from_search' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-admin-home',
                'hierarchical' => true, # set to false if you don't want parent/child relationships for the entries
                'show_in_graphql' => true, # Set to false if you want to exclude this type from the GraphQL Schema
                'graphql_single_name' => 'home_page',
                'graphql_plural_name' => 'home_pages', # If set to the same name as graphql_single_name, the field name will default to `all${graphql_single_name}`, i.e. `allDocument`.
                'public' => true, # set to false if entries of the post_type should not have public URIs per entry
                'publicly_queryable' => true,

                // Check options here: https://developer.wordpress.org/reference/functions/register_post_type/ https://developer.wordpress.org/reference/functions/get_post_type_capabilities/
                'capability' => 'manage_options',
            ];

            register_post_type('home_page', $args);
        }

        public function register_moderated_home_page()
        {
            $args = [
                'labels' => [
                    'name' => __('Moderated home pages'),
                    'singular_name' => __('Moderated home page'),
                ],
                'show_in_rest' => true,
                'supports' => ['title'],
                'has_archive' => true,
                'rewrite' => ['slug' => 'moderated-home-pages'],
                'exclude_from_search' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-admin-home',
                'hierarchical' => true, # set to false if you don't want parent/child relationships for the entries
                'show_in_graphql' => true, # Set to false if you want to exclude this type from the GraphQL Schema
                'graphql_single_name' => 'moderated_home_page',
                'graphql_plural_name' => 'moderated_home_pages', # If set to the same name as graphql_single_name, the field name will default to `all${graphql_single_name}`, i.e. `allDocument`.
                'public' => true, # set to false if entries of the post_type should not have public URIs per entry
                'publicly_queryable' => true,

                // Check options here: https://developer.wordpress.org/reference/functions/register_post_type/ https://developer.wordpress.org/reference/functions/get_post_type_capabilities/
                'capability' => 'manage_options',
            ];

            register_post_type('moderated_home_page', $args);
        }
    }

    function tlgFranchise()
    {
        global $tlgFranchise;

        // Instantiate only once.
        if (!isset($tlgFranchise)) {
            $tlgFranchise = new TlgFranchise();
            $tlgFranchise->initialize();
        }
        return $tlgFranchise;
    }
    // Instantiate.
    tlgFranchise();
} // class_exists check

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    /**
     * @disregard P1009 Undefined type
     */
    acf_add_local_field_group([
        'key' => 'group_673f00412b53a',
        'title' => 'Home page',
        'fields' => [
            [
                'key' => 'field_673f00413dbae',
                'label' => 'Hero header',
                'name' => 'hero_header',
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
                'graphql_field_name' => 'heroHeader',
                'graphql_non_null' => 0,
                'sub_fields' => [
                    [
                        'key' => 'field_673f00853dbaf',
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
                        'key' => 'field_673f00963dbb0',
                        'label' => 'Body',
                        'name' => 'body',
                        'aria-label' => '',
                        'type' => 'wysiwyg',
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
                        'allow_in_bindings' => 0,
                        'tabs' => 'all',
                        'toolbar' => 'full',
                        'media_upload' => 1,
                        'delay' => 0,
                        'show_in_graphql' => 1,
                        'graphql_description' => '',
                        'graphql_field_name' => 'body',
                        'graphql_non_null' => 0,
                    ],
                ],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'home_page',
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
        'show_in_rest' => 1,
        'acfml_field_group_mode' => 'translation',
        'show_in_graphql' => 1,
        'graphql_field_name' => 'homePage',
        'map_graphql_types_from_location_rules' => 0,
        'graphql_types' => '',
    ]);
});

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    /**
     * @disregard P1009 Undefined type
     */
    acf_add_local_field_group([
        'key' => 'group_673f029cd735e',
        'title' => 'Moderated home page',
        'fields' => [
            [
                'key' => 'field_673f029d5ee82',
                'label' => 'Scrolling animation',
                'name' => 'scrolling_animation',
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
                'graphql_field_name' => 'scrollingAnimation',
                'graphql_non_null' => 0,
                'sub_fields' => [
                    [
                        'key' => 'field_673f02e25ee83',
                        'label' => 'Section',
                        'name' => 'section',
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
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'show_in_graphql' => 1,
                        'graphql_description' => '',
                        'graphql_field_name' => 'section',
                        'graphql_non_null' => 0,
                        'rows_per_page' => 20,
                        'sub_fields' => [
                            [
                                'key' => 'field_673f02f25ee84',
                                'label' => 'Text',
                                'name' => 'text',
                                'aria-label' => '',
                                'type' => 'wysiwyg',
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
                                'allow_in_bindings' => 0,
                                'tabs' => 'all',
                                'toolbar' => 'full',
                                'media_upload' => 1,
                                'delay' => 0,
                                'show_in_graphql' => 1,
                                'graphql_description' => '',
                                'graphql_field_name' => 'text',
                                'graphql_non_null' => 0,
                                'parent_repeater' => 'field_673f02e25ee83',
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
                    'value' => 'moderated_home_page',
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
        'show_in_rest' => 1,
        'acfml_field_group_mode' => 'translation',
        'show_in_graphql' => 1,
        'graphql_field_name' => 'moderatedHomePage',
        'map_graphql_types_from_location_rules' => 0,
        'graphql_types' => '',
    ]);
});
