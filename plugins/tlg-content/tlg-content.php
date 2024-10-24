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
            add_action('init', [$this, 'register_taxonomy']);
            add_action('admin_menu', [$this, 'add_menus']);
            add_action('admin_enqueue_scripts', [$this, 'load_admin_assets']);
            add_action('wp_ajax_tlg_content_new_location_handler', [
                $this,
                'new_location_handler',
            ]);
            add_action(
                'save_post_landing_page',
                [$this, 'save_handler'],
                10,
                2
            );
        }

        public function save_handler($post_id, $post)
        {
            $permalink = $post->post_name;

            // Fetch the first saved location taxonomy linked to this post.
            $terms = get_the_terms($post_id, 'location');
            $location = $terms && !is_wp_error($terms) ? $terms[0] : null;

            if ($location) {
                $term_slug = $location->slug;
            } else {
                $term_slug = 'unassigned';
            }

            $parts = explode('__', $permalink);
            $post_slug = end($parts);

            $combined = $term_slug . '__' . $post_slug;

            // unhook this function to prevent infinite looping
            remove_action(
                'save_post_landing_page',
                [$this, 'save_handler'],
                10,
                2
            );

            // update the post slug
            wp_update_post([
                'ID' => $post_id,
                'post_name' => $combined,
            ]);

            // re-hook this function
            add_action(
                'save_post_landing_page',
                [$this, 'save_handler'],
                10,
                2
            );
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

        public function register_taxonomy()
        {
            $args = [
                'labels' => [
                    'name' => __('Locations'),
                    'singular_name' => __('Location'),
                ],
                'show_in_rest' => true,
                'show_admin_column' => true,
                'hierarchical' => false,
                'rewrite' => ['slug' => 'locations'],
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_tagcloud' => true,
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
                'show_in_graphql' => true,
                'graphql_single_name' => 'location',
                'graphql_plural_name' => 'locations',
                'publicly_queryable' => true,
                'capability' => 'manage_options',
            ];

            register_taxonomy('location', ['landing_page', 'post'], $args);
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
            $nonce = wp_create_nonce('tlg-content-new-location'); ?>
                <div class="wrap">
                    <h2>TLG Content</h2>
                    <p>Initialise content for a new location</p>

                    <form method="post" id="new-location">
                        <label for="location">Location</label>
                        <input type="text" name="location" required>
                        <input type="hidden" name="nonce" value="<?php echo $nonce; ?>">

                        <button type="submit">Create</button>
                    </form>

                    <div id="tlg-content-new-location-log">

                    </div>
                </div>
            <?php
        }

        public function new_location_handler()
        {
            if (
                !isset($_POST['nonce']) ||
                !wp_verify_nonce($_POST['nonce'], 'tlg-content-new-location')
            ) {
                error_log('Invalid nonce');
                wp_send_json_error('Invalid nonce');
            }

            // Location.
            $location = sanitize_text_field($_POST['location']);
            $location_key = sanitize_title($location);

            // // If location is london then return error.
            // if (strtolower($location) === 'london') {
            //     wp_send_json_error('London is not a valid location');
            // }

            // Here we want to duplicate the content from the template location.

            $array_result = [
                'success' => true,
            ];

            // Make your array as json
            wp_send_json($array_result);
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
            wp_localize_script('tlg-content', 'tlg_content_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
            ]);
        }
    }

    function custom_job_post_type_link($post_id, $post)
    {
        $permalink = $post->post_name;
        $companyname = $post->_company_name;

        if (strpos($permalink, sanitize_title($companyname))) {
            // <-- Here
            return;
        }

        // unhook this function to prevent infinite looping
        remove_action(
            'save_post_job_listing',
            'custom_job_post_type_link',
            10,
            2
        );

        // add the id to the slug
        $permalink .= '-' . sanitize_title($companyname); // <-- And here

        // update the post slug
        wp_update_post([
            'ID' => $post_id,
            'post_name' => $permalink,
        ]);

        // re-hook this function
        add_action('save_post_job_listing', 'custom_job_post_type_link', 10, 2);
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
