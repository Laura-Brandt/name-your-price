<?php

/*
Plugin Name: Name Your Price for WooCommerce
Description: Name Your Price for WooCommerce.
Version: 0.0.1
Author: Andreas Kind
Copyright: 2017 Andreas Kind
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
    !in_array($plugin, apply_filters('active_plugins', get_option('active_plugins', array()))) &&
    !(is_multisite() && array_key_exists($plugin, get_site_option('active_sitewide_plugins', array())))
) {
    return;
}

if ('name-your-price.php' === basename(__FILE__)) {
    // Check if Pro is active, if so then return
    $plugin = 'name-your-price-for-woocommerce-pro/name-your-price-for-woocommerce-pro.php';
    if (
        in_array($plugin, apply_filters('active_plugins', get_option('active_plugins', array()))) ||
        (is_multisite() && array_key_exists($plugin, get_site_option('active_sitewide_plugins', array())))
    ) {
        return;
    }
}

if (!class_exists('Name_Your_Price')) :

    /**
     * Main WC_Product_Name_Your_Price Class
     *
     * @class   WC_Product_Name_Your_Price
     * @since   0.0.1
     * @version 0.0.1
     */

    final class Name_Your_Price
    {

        /**
         * Plugin version.
         *
         * @var   string
         * @since 0.0.1
         */
        public $version = '0.0.1';

        /**
         * @var   Name_Your_Price The single instance of the class
         * @since 0.0.1
         */
        protected static $_instance = null;

        /**
         * Main WC_Product_Name_Your_Price Instance
         *
         * Ensures only one instance of WC_Product_Name_Your_Price is loaded or can be loaded.
         *
         * @version 0.0.1
         * @since   0.0.1
         * @static
         * @return  Name_Your_Price - Main instance
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * WC_Product_Name_Your_Price Constructor.
         *
         * @version 0.0.1
         * @since   0.0.1
         * @access  public
         */
        function __construct()
        {

            // Set up localisation
            load_plugin_textdomain('name-your-price-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/langs/');

            // Include required files
            $this->includes();

            // Settings & Scripts
            if (is_admin()) {
                add_filter('woocommerce_get_settings_pages', [$this, 'add_woocommerce_settings_tab']);
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function includes()
        {
            // Settings
            require_once('includes/admin/class-wc-name-your-price-settings-section.php');
            $settings = array();
            $settings[] = require_once('includes/admin/class-wc-name-your-price-settings-general.php');
            if (is_admin() && get_option('name_your_price_version', '') !== $this->version) {
                foreach ($settings as $section) {
                    foreach ($section->get_settings() as $value) {
                        if (isset($value['default']) && isset($value['id'])) {
                            add_option($value['id'], $value['default'], '', 'no');
                        }
                    }
                }
                update_option('name_your_price_version', $this->version);
            }
            // Metaboxes (per Product Settings)
            require_once('includes/admin/class-wc-name-your-price-settings-per-product.php');
            // Core
            require_once('includes/class-wc-name-your-price-core.php');
        }

        /**
         * Add Name Your Price settings tab to WooCommerce settings.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function add_woocommerce_settings_tab($settings)
        {
            $settings[] = include('includes/admin/class-wc-name-your-price-settings.php');
            return $settings;
        }

        /**
         * Get the plugin url.
         *
         * @version 0.0.1
         * @since   0.0.1
         * @return  string
         */
        function plugin_url()
        {
            return untrailingslashit(plugin_dir_url(__FILE__));
        }

        /**
         * Get the plugin path.
         *
         * @version 0.0.1
         * @since   0.0.1
         * @return  string
         */
        function plugin_path()
        {
            return untrailingslashit(plugin_dir_path(__FILE__));
        }

    }

endif;

if (!function_exists('wc_name_your_price')) {
    /**
     * Returns the main instance of WC_Product_Open_Pricing to prevent the need to use globals.
     *
     * @version 0.0.1
     * @since   0.0.1
     * @return  Name_Your_Price
     */
    function wc_name_your_price()
    {
        return Name_Your_Price::instance();
    }
}

wc_name_your_price();
