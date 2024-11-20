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
