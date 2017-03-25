<?php
/**
 * Name Your Price for WooCommerce - Per Product Section Settings
 *
 * @version 0.0.1
 * @since   0.0.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WC_Name_Your_Price_Settings_Per_Product')) :

    class WC_Name_Your_Price_Settings_Per_Product
    {

        /**
         * Constructor.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function __construct()
        {
            $this->id = 'per_product';
            if ('yes' === get_option('name_your_price_enabled', 'yes')) {
                add_action('add_meta_boxes', [$this, 'add_meta_box']);
                add_action('save_post_product', [$this, 'save_meta_box'], PHP_INT_MAX, 2);
                add_filter('name_your_price_save_meta_box_value', [$this, 'save_meta_box_value'], PHP_INT_MAX, 3);
            }
        }

        /**
         * save_meta_box_value.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function save_meta_box_value($option_value, $option_name, $module_id)
        {
            if (true === apply_filters('name_your_price', false, 'per_product_settings')) {
                return $option_value;
            }
            if ('no' === $option_value) {
                return $option_value;
            }
            return $option_value;
        }

        /**
         * add_notice_query_var.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function add_notice_query_var($location)
        {
            remove_filter('redirect_post_location', array($this, 'add_notice_query_var'), 99);
            return add_query_arg(array('name_your_price_admin_notice' => true), $location);
        }

        /**
         * get_meta_box_options.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function get_meta_box_options()
        {
            $options = [
                [
                    'name' => 'name_your_price_enabled',
                    'default' => 'no',
                    'type' => 'select',
                    'options' => [
                        'yes' => __('Yes', 'name-your-price-for-woocommerce'),
                        'no' => __('No', 'name-your-price-for-woocommerce'),
                    ],
                    'title' => '<strong>' . __('Enabled', 'name-your-price-for-woocommerce') . '</strong>',
                ],
                [
                    'name' => 'name_your_price_default_price',
                    'default' => '',
                    'type' => 'price',
                    'title' => __('Default Price', 'name-your-price-for-woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')',
                    'tooltip' => __('Default (i.e. Suggested) price', 'name-your-price-for-woocommerce'),
                ],
                [
                    'name' => 'name_your_price_min_price',
                    'default' => 1,
                    'type' => 'price',
                    'title' => __('Min Price', 'name-your-price-for-woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')',
                ],
                [
                    'name' => 'name_your_price_max_price',
                    'default' => '',
                    'type' => 'price',
                    'title' => __('Max Price', 'name-your-price-for-woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')',
                ],
            ];
            return $options;
        }

        /**
         * save_meta_box.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function save_meta_box($post_id, $post)
        {
            // Check that we are saving with current metabox displayed.
            if (!isset($_POST['name_your_price_' . $this->id . '_save_post'])) {
                return;
            }
            // Save options
            foreach ($this->get_meta_box_options() as $option) {
                if ('title' === $option['type']) {
                    continue;
                }
                $is_enabled = (isset($option['enabled']) && 'no' === $option['enabled']) ? false : true;
                if ($is_enabled) {
                    $option_value = (isset($_POST[$option['name']])) ? $_POST[$option['name']] : $option['default'];
                    $the_post_id = (isset($option['product_id'])) ? $option['product_id'] : $post_id;
                    $the_meta_name = (isset($option['meta_name'])) ? $option['meta_name'] : '_' . $option['name'];
                    update_post_meta($the_post_id, $the_meta_name, apply_filters('name_your_price_save_meta_box_value', $option_value, $option['name']));
                }
            }
        }

        /**
         * add_meta_box.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function add_meta_box()
        {
            add_meta_box(
                'name_your_price_' . $this->id,
                __('Name Your Price', 'name-your-price-for-woocommerce'),
                array($this, 'create_meta_box'),
                'product',
                'normal',
                'high'
            );
        }

        /**
         * create_meta_box.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function create_meta_box()
        {
            $current_post_id = get_the_ID();
            $html = '';
            $html .= '<table class="widefat striped">';
            foreach ($this->get_meta_box_options() as $option) {
                $is_enabled = (isset($option['enabled']) && 'no' === $option['enabled']) ? false : true;
                if ($is_enabled) {
                    if ('title' === $option['type']) {
                        $html .= '<tr>';
                        $html .= '<th colspan="2" style="text-align:left;">' . $option['title'] . '</th>';
                        $html .= '</tr>';
                    } else {
                        $custom_attributes = '';
                        $the_post_id = (isset($option['product_id'])) ? $option['product_id'] : $current_post_id;
                        $the_meta_name = (isset($option['meta_name'])) ? $option['meta_name'] : '_' . $option['name'];
                        if (get_post_meta($the_post_id, $the_meta_name)) {
                            $option_value = get_post_meta($the_post_id, $the_meta_name, true);
                        } else {
                            $option_value = (isset($option['default'])) ? $option['default'] : '';
                        }
                        $input_ending = '';
                        if ('select' === $option['type']) {
                            if (isset($option['multiple'])) {
                                $custom_attributes = ' multiple';
                                $option_name = $option['name'] . '[]';
                            } else {
                                $option_name = $option['name'];
                            }
                            $options = '';
                            foreach ($option['options'] as $select_option_key => $select_option_value) {
                                $selected = '';
                                if (is_array($option_value)) {
                                    foreach ($option_value as $single_option_value) {
                                        $selected .= selected($single_option_value, $select_option_key, false);
                                    }
                                } else {
                                    $selected = selected($option_value, $select_option_key, false);
                                }
                                $options .= '<option value="' . $select_option_key . '" ' . $selected . '>' . $select_option_value . '</option>';
                            }
                        } else {
                            $input_ending = ' id="' . $option['name'] . '" name="' . $option['name'] . '" value="' . $option_value . '">';
                            if (isset($option['custom_attributes'])) {
                                $input_ending = ' ' . $option['custom_attributes'] . $input_ending;
                            }
                        }
                        switch ($option['type']) {
                            case 'price':
                                $field_html = '<input class="short wc_input_price" type="number" step="0.0001"' . $input_ending;
                                break;
                            case 'date':
                                $field_html = '<input class="input-text" display="date" type="text"' . $input_ending;
                                break;
                            case 'textarea':
                                $field_html = '<textarea style="min-width:300px;"' . ' id="' . $option['name'] . '" name="' . $option['name'] . '">' . $option_value . '</textarea>';
                                break;
                            case 'select':
                                $field_html = '<select' . $custom_attributes . ' id="' . $option['name'] . '" name="' . $option_name . '">' . $options . '</select>';
                                break;
                            default:
                                $field_html = '<input class="short" type="' . $option['type'] . '"' . $input_ending;
                                break;
                        }
                        $html .= '<tr>';
                        $maybe_tooltip = (isset($option['tooltip']) && '' != $option['tooltip']) ?
                            ' <img style="display:inline;" class="question-icon" src="' . wc_name_your_price()->plugin_url() . '/assets/images/question-icon.png' . '" title="' . $option['tooltip'] . '">' :
                            '';
                        $html .= '<th style="text-align:left;width:150px;">' . $option['title'] . $maybe_tooltip . '</th>';
                        if (isset($option['desc']) && '' != $option['desc']) {
                            $html .= '<td style="font-style:italic;">' . $option['desc'] . '</td>';
                        }
                        $html .= '<td>' . $field_html . '</td>';
                        $html .= '</tr>';
                    }
                }
            }
            $html .= '</table>';
            $html .= '<input type="hidden" name="name_your_price_' . $this->id . '_save_post" value="name_your_price_' . $this->id . '_save_post">';
            echo $html;
        }

    }

endif;

return new WC_Name_Your_Price_Settings_Per_Product();
