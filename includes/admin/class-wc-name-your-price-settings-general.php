<?php
/**
 * Name Your Price for WooCommerce - General Section Settings
 *
 * @version 0.0.1
 * @since   0.0.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WC_Name_Your_Price_Settings_General')) :

    class WC_Name_Your_Price_Settings_General extends WC_Name_Your_Price_Settings_Section
    {
        /**
         * get_section_settings.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function get_section_settings()
        {
            $default_price_step = 1 / pow(10, absint(get_option('woocommerce_price_num_decimals', 2)));
            $settings = [
                [
                    'title' => __('Name Your Price Options', 'name-your-price-for-woocommerce'),
                    'type' => 'title',
                    'id' => 'name_your_price_options',
                ],
                [
                    'title' => __('Name Your Price', 'name-your-price-for-woocommerce'),
                    'desc' => '<strong>' . __('Enable', 'name-your-price-for-woocommerce') . '</strong>',
                    'desc_tip' => __('Let your WooCommerce store customers enter price for the product manually.', 'name-your-price-for-woocommerce'),
                    'id' => 'name_your_price_enabled',
                    'default' => 'yes',
                    'type' => 'checkbox',
                ],
                [
                    'type' => 'sectionend',
                    'id' => 'name_your_price_options',
                ],
                [
                    'title' => __('Options', 'name-your-price-for-woocommerce'),
                    'type' => 'title',
                    'id' => 'name_your_price_messages_options',
                ],
                [
                    'title' => __('Frontend Label', 'name-your-price-for-woocommerce'),
                    'id' => 'name_your_price_label_frontend',
                    'default' => __('Name Your Price', 'name-your-price-for-woocommerce'),
                    'type' => 'text',
                    'css' => 'width:300px;',
                ],
                [
                    'title' => __('Frontend Template', 'name-your-price-for-woocommerce'),
                    'desc_tip' => __('Here you can use') . ': ' . '%frontend_label%, %open_price_input%, %currency_symbol%',
                    'id' => 'name_your_price_frontend_template',
                    'default' => '<label for="open_price">%frontend_label%</label> %open_price_input% %currency_symbol%',
                    'type' => 'textarea',
                    'css' => 'min-width:300px;width:50%;',
                ],
                [
                    'title' => __('Price Step', 'name-your-price-for-woocommerce'),
                    'id' => 'name_your_price_price_step',
                    'default' => $default_price_step,
                    'type' => 'number',
                    'custom_attributes' => ['step' => '0.0001', 'min' => '0.0001'],
                ],
                [
                    'title' => __('Message on Empty Price', 'name-your-price-for-woocommerce'),
                    'id' => 'name_your_price_messages_required',
                    'default' => __('Price is required!', 'name-your-price-for-woocommerce'),
                    'type' => 'text',
                    'css' => 'width:300px;',
                ],
                [
                    'title' => __('Message on Price too Small', 'name-your-price-for-woocommerce'),
                    'id' => 'name_your_price_messages_too_small',
                    'default' => __('Entered price is too small!', 'name-your-price-for-woocommerce'),
                    'type' => 'text',
                    'css' => 'width:300px;',
                ],
                [
                    'title' => __('Message on Price too Big', 'name-your-price-for-woocommerce'),
                    'id' => 'name_your_price_messages_too_big',
                    'default' => __('Entered price is too big!', 'name-your-price-for-woocommerce'),
                    'type' => 'text',
                    'css' => 'width:300px;',
                ],
                [
                    'type' => 'sectionend',
                    'id' => 'name_your_price_messages_options',
                ],
            ];
            return $settings;
        }

    }

endif;

return new WC_Name_Your_Price_Settings_General();
