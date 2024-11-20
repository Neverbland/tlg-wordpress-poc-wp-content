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
            // add_action(
            //     'save_post_landing_page',
            //     [$this, 'save_handler'],
            //     10,
            //     2
            // );
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

    // function custom_job_post_type_link($post_id, $post)
    // {
    //     $permalink = $post->post_name;
    //     $companyname = $post->_company_name;

    //     if (strpos($permalink, sanitize_title($companyname))) {
    //         // <-- Here
    //         return;
    //     }

    //     // unhook this function to prevent infinite looping
    //     remove_action(
    //         'save_post_job_listing',
    //         'custom_job_post_type_link',
    //         10,
    //         2
    //     );

    //     // add the id to the slug
    //     $permalink .= '-' . sanitize_title($companyname); // <-- And here

    //     // update the post slug
    //     wp_update_post([
    //         'ID' => $post_id,
    //         'post_name' => $permalink,
    //     ]);

    //     // re-hook this function
    //     add_action('save_post_job_listing', 'custom_job_post_type_link', 10, 2);
    // }

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

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    /**
     * @disregard P1009 Undefined type
     */
    acf_add_local_field_group([
        'key' => 'group_67068953b2313',
        'title' => 'Landing page',
        'fields' => [
            [
                'key' => 'field_6707c979e1240',
                'label' => 'Page builder',
                'name' => 'page_builder',
                'aria-label' => '',
                'type' => 'flexible_content',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'layouts' => [
                    'layout_6707c97ef3222' => [
                        'key' => 'layout_6707c97ef3222',
                        'name' => 'text',
                        'label' => 'Text',
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_6707d0f9e1241',
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
                            ],
                        ],
                        'min' => '',
                        'max' => '',
                    ],
                    'layout_6707d12fe1243' => [
                        'key' => 'layout_6707d12fe1243',
                        'name' => 'hero',
                        'label' => 'Hero',
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_6707d15ce1245',
                                'label' => 'Title',
                                'name' => 'title',
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
                                'default_value' => '',
                                'maxlength' => '',
                                'allow_in_bindings' => 0,
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'show_in_graphql' => 1,
                                'graphql_description' => '',
                                'graphql_field_name' => 'title',
                                'graphql_non_null' => 0,
                            ],
                            [
                                'key' => 'field_6707d165e1246',
                                'label' => 'Image',
                                'name' => 'image',
                                'aria-label' => '',
                                'type' => 'image',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => [
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ],
                                'return_format' => 'array',
                                'library' => 'all',
                                'min_width' => '',
                                'min_height' => '',
                                'min_size' => '',
                                'max_width' => '',
                                'max_height' => '',
                                'max_size' => '',
                                'mime_types' => '',
                                'allow_in_bindings' => 0,
                                'preview_size' => 'medium',
                                'show_in_graphql' => 1,
                                'graphql_description' => '',
                                'graphql_field_name' => 'image',
                            ],
                        ],
                        'min' => '',
                        'max' => '',
                    ],
                    'layout_6707d392da3a4' => [
                        'key' => 'layout_6707d392da3a4',
                        'name' => 'related_landing_pages',
                        'label' => 'Related landing pages',
                        'display' => 'block',
                        'sub_fields' => [
                            [
                                'key' => 'field_6707d3a0da3a6',
                                'label' => 'Related landing pages',
                                'name' => 'related_landing_pages',
                                'aria-label' => '',
                                'type' => 'relationship',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => [
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ],
                                'post_type' => [
                                    0 => 'landing_page',
                                ],
                                'post_status' => '',
                                'taxonomy' => '',
                                'filters' => [
                                    0 => 'search',
                                    1 => 'post_type',
                                    2 => 'taxonomy',
                                ],
                                'return_format' => 'object',
                                'min' => '',
                                'max' => '',
                                'allow_in_bindings' => 0,
                                'elements' => '',
                                'bidirectional' => 0,
                                'show_in_graphql' => 1,
                                'graphql_description' => '',
                                'graphql_field_name' => 'relatedLandingPages',
                                'graphql_connection_type' => 'one_to_many',
                                'bidirectional_target' => [],
                            ],
                        ],
                        'min' => '0',
                        'max' => '3',
                    ],
                ],
                'min' => '',
                'max' => '',
                'button_label' => 'Add Row',
                'show_in_graphql' => 1,
                'graphql_description' => '',
                'graphql_field_name' => '',
                'graphql_non_null' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'landing_page',
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
        'show_in_graphql' => 1,
        'graphql_field_name' => 'landingPage',
        'map_graphql_types_from_location_rules' => 0,
        'graphql_types' => '',
    ]);
});

// https://wpml.org/forums/topic/unable-to-query-a-post-by-a-slug-with-graphql/
add_filter('request', function ($vars) {
    if (isset($vars['graphql']) && !empty($vars['name'])) {
        $vars['suppress_filters'] = true;
    }

    return $vars;
});
