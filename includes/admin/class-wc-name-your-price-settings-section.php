<?php
/**
 * Name Your Price for WooCommerce - Section Settings
 *
 * @version 0.0.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WC_Name_Your_Price_Settings_Section')) :

    class WC_Name_Your_Price_Settings_Section
    {

        /**
         * Constructor.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function __construct()
        {
            $this->id = '';
            $this->desc = __('General', 'name-your-price-for-woocommerce');

            add_filter('woocommerce_get_sections_name_your_price', [$this, 'settings_section']);
            add_filter('woocommerce_get_settings_name_your_price' . '_' . $this->id, [$this, 'get_settings'], PHP_INT_MAX);
        }

        /**
         * settings_section.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function settings_section($sections)
        {
            $sections[$this->id] = $this->desc;
            return $sections;
        }

        /**
         * get_settings.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function get_settings()
        {
            return array_merge($this->get_section_settings(), [
                [
                    'title' => __('Reset Section Settings', 'name-your-price-for-woocommerce'),
                    'type' => 'title',
                    'id' => 'name_your_price' . '_' . $this->id . '_reset_options',
                ],
                [
                    'title' => __('Reset Settings', 'name-your-price-for-woocommerce'),
                    'desc' => '<strong>' . __('Reset', 'name-your-price-for-woocommerce') . '</strong>',
                    'id' => 'name_your_price' . '_' . $this->id . '_reset',
                    'default' => 'no',
                    'type' => 'checkbox',
                ],
                [
                    'type' => 'sectionend',
                    'id' => 'name_your_price' . '_' . $this->id . '_reset_options',
                ],
            ]);
        }

    }

endif;
