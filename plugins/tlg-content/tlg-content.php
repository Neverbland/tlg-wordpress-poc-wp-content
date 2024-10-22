<?php
/**
 * TLG Content
 *
 * @wordpress-plugin
 * Plugin Name: TLG Content
 * Description: Custom content configuration for The Little Gym
 */

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

if (!class_exists('TlgContent')) {
    class TlgContent
    {
        public function __construct()
        {
        }

        public static $plugin_slug = 'tlg-content';
        public static $plugin_name = 'TLG Content';

        public function initialize()
        {
            add_action('init', [$this, 'register_post_type']);
            add_action('admin_menu', [$this, 'add_menus']);
            add_action('admin_enqueue_scripts', [$this, 'load_admin_assets']);
        }

        public function register_post_type()
        {
            $args = [
                'labels' => [
                    'name' => __('Landing pages'),
                    'singular_name' => __('Landing page'),
                ],
                'show_in_rest' => true,
                'supports' => ['title'],
                'has_archive' => true,
                'rewrite' => ['slug' => 'landing-pages'],
                'exclude_from_search' => true,
                'menu_position' => 5,
                'menu_icon' => 'dashicons-airplane',
                // 'taxonomies' => array('cuisines', 'post_tag') // this is IMPORTANT

                'hierarchical' => true, # set to false if you don't want parent/child relationships for the entries
                'show_in_graphql' => true, # Set to false if you want to exclude this type from the GraphQL Schema
                'graphql_single_name' => 'landing_page',
                'graphql_plural_name' => 'landing_pages', # If set to the same name as graphql_single_name, the field name will default to `all${graphql_single_name}`, i.e. `allDocument`.
                'public' => true, # set to false if entries of the post_type should not have public URIs per entry
                'publicly_queryable' => true,

                // Check options here: https://developer.wordpress.org/reference/functions/register_post_type/ https://developer.wordpress.org/reference/functions/get_post_type_capabilities/
                'capability' => 'manage_options',
            ];

            register_post_type('landing_page', $args);
        }

        public function add_menus()
        {
            $hook = add_menu_page(
                $this::$plugin_name,
                $this::$plugin_name,
                'manage_options',
                $this::$plugin_slug,
                [$this, 'menu_page'],
                'dashicons-admin-site',
                100
            );
            add_submenu_page(
                $this::$plugin_slug,
                'Initialise content for a new location',
                'New location',
                'manage_options',
                'new-location',
                [$this, 'new_location_page']
            );
        }

        public function menu_page()
        {
            ?>
                <div class="wrap">
                    <h2>TLG Content</h2>
                    <p>Custom content configuration for The Little Gym</p>
                </div>
            <?php
        }

        public function new_location_page()
        {
            ?>
                <div class="wrap">
                    <h2>TLG Content</h2>
                    <p>Initialise content for a new location</p>
                </div>
            <?php
        }

        public function load_admin_assets()
        {
            wp_enqueue_style(
                'tlg-content',
                plugin_dir_url(__FILE__) . 'assets/tlg-content.css',
                [],
                1,
                'all'
            );
            wp_enqueue_script(
                'tlg-content',
                plugin_dir_url(__FILE__) . 'assets/tlg-content.js',
                ['jquery'],
                1,
                true
            );
        }
    }

    function tlgContent()
    {
        global $tlgContent;

        // Instantiate only once.
        if (!isset($tlgContent)) {
            $tlgContent = new TlgContent();
            $tlgContent->initialize();
        }
        return $tlgContent;
    }

    // Instantiate.
    tlgContent();
} // class_exists check
