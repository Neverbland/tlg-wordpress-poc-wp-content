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
