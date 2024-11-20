<?php
/**
 * TLG Content
 *
 * @wordpress-plugin
 * Plugin Name: TLG Core
 * Description: Custom content configuration for The Little Gym
 */

if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

if (!class_exists('TlgCore')) {
    class TlgCore
    {
        public function __construct()
        {
        }

        public static $plugin_slug = 'tlg-core';
        public static $plugin_name = 'TLG Core';

        public function initialize()
        {
        }
    }

    function tlgCore()
    {
        global $tlgCore;

        // Instantiate only once.
        if (!isset($tlgCore)) {
            $tlgCore = new TlgCore();
            $tlgCore->initialize();
        }
        return $tlgCore;
    }
    // Instantiate.
    tlgCore();
} // class_exists check
