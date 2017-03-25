<?php
/**
 * Name Your Price for WooCommerce - Core Class
 *
 * @version 0.0.1
 * @since   0.0.1
 */

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WC_Product_Name_Your_Price')) :

    class WC_Product_Name_Your_Price
    {

        /**
         * Constructor.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function __construct()
        {
            if ('yes' === get_option('name_your_price_enabled', 'yes')) {
                add_filter('woocommerce_get_price', [$this, 'get_open_price'], PHP_INT_MAX, 2);
                add_filter('woocommerce_get_price_html', [$this, 'hide_original_price'], PHP_INT_MAX, 2);
                add_filter('woocommerce_get_variation_price_html', [$this, 'hide_original_price'], PHP_INT_MAX, 2);
                add_filter('woocommerce_is_sold_individually', [$this, 'hide_quantity_input_field'], PHP_INT_MAX, 2);
                add_filter('woocommerce_is_purchasable', [$this, 'is_purchasable'], PHP_INT_MAX, 2);
                add_filter('woocommerce_product_supports', [$this, 'disable_add_to_cart_ajax'], PHP_INT_MAX, 3);
                add_filter('woocommerce_product_add_to_cart_url', [$this, 'add_to_cart_url'], PHP_INT_MAX, 2);
                add_filter('woocommerce_product_add_to_cart_text', [$this, 'add_to_cart_text'], PHP_INT_MAX, 2);
                add_action('woocommerce_before_add_to_cart_button', [$this, 'add_open_price_input_field_to_frontend'], PHP_INT_MAX);
                add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_open_price_on_add_to_cart'], PHP_INT_MAX, 2);
                add_filter('woocommerce_add_cart_item_data', [$this, 'add_open_price_to_cart_item_data'], PHP_INT_MAX, 3);
                add_filter('woocommerce_add_cart_item', [$this, 'add_open_price_to_cart_item'], PHP_INT_MAX, 2);
                add_filter('woocommerce_get_cart_item_from_session', [$this, 'get_cart_item_open_price_from_session'], PHP_INT_MAX, 3);
            }
        }

        /**
         * is_open_price_product.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   WC_Product_Simple $_product
         *
         * @return  bool
         */
        function is_open_price_product(WC_Product_Simple $_product)
        {
            return ('yes' === get_post_meta($_product->id, '_' . 'name_your_price_enabled', true)) ? true : false;
        }

        /**
         * disable_add_to_cart_ajax.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function disable_add_to_cart_ajax($supports, $feature, $_product)
        {
            if ($this->is_open_price_product($_product) && 'ajax_add_to_cart' === $feature) {
                $supports = false;
            }
            return $supports;
        }

        /**
         * is_purchasable.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   bool              $purchasable
         * @param   WC_Product_Simple $_product
         *
         * @return  bool
         */
        function is_purchasable(bool $purchasable, WC_Product_Simple $_product)
        {
            if ($this->is_open_price_product($_product)) {
                $purchasable = true;

                // Products must exist of course
                if (!$_product->exists()) {
                    $purchasable = false;

                    // Other products types need a price to be set
                    /* } elseif ( $_product->get_price() === '' ) {
                        $purchasable = false; */

                    // Check the product is published
                } elseif ($_product->post->post_status !== 'publish' && !current_user_can('edit_post', $_product->id)) {
                    $purchasable = false;
                }
            }
            return $purchasable;
        }

        /**
         * add_to_cart_text.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function add_to_cart_text($text, $_product)
        {
            return ($this->is_open_price_product($_product)) ? __('Read more', 'woocommerce') : $text;
        }

        /**
         * add_to_cart_url.
         *
         * @version 0.0.1
         * @since   0.0.1
         */
        function add_to_cart_url($url, $_product)
        {
            return ($this->is_open_price_product($_product)) ? get_permalink($_product->id) : $url;
        }

        /**
         * hide_quantity_input_field.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   bool              $return
         * @param   WC_Product_Simple $_product
         *
         * @return  bool
         */
        function hide_quantity_input_field(bool $return, WC_Product_Simple $_product)
        {
            return ($this->is_open_price_product($_product)) ? true : $return;
        }

        /**
         * hide_original_price.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   string            $price
         * @param   WC_Product_Simple $_product
         *
         * @return  string
         */
        function hide_original_price(string $price, WC_Product_Simple $_product)
        {
            return ($this->is_open_price_product($_product)) ? '' : $price;
        }

        /**
         * get_open_price.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   string            $price
         * @param   WC_Product_Simple $_product
         *
         * @return  string
         */
        function get_open_price(string $price, WC_Product_Simple $_product)
        {
            return ($this->is_open_price_product($_product) && isset($_product->open_price)) ? $_product->open_price : $price;
        }

        /**
         * validate_open_price_on_add_to_cart.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   bool $passed
         * @param   int  $product_id
         *
         * @return  bool
         */
        function validate_open_price_on_add_to_cart(bool $passed, int $product_id)
        {
            $the_product = wc_get_product($product_id);
            if ($this->is_open_price_product($the_product)) {
                $min_price = get_post_meta($product_id, '_' . 'name_your_price_min_price', true);
                $max_price = get_post_meta($product_id, '_' . 'name_your_price_max_price', true);
                if ($min_price > 0) {
                    if (!isset($_POST['open_price']) || '' === $_POST['open_price']) {
                        wc_add_notice(get_option('name_your_price_messages_required', __('Price is required!', 'name-your-price-for-woocommerce')), 'error');
                        return false;
                    }
                    if ($_POST['open_price'] < $min_price) {
                        wc_add_notice(get_option('name_your_price_messages_too_small', __('Entered price is too small!', 'name-your-price-for-woocommerce')), 'error');
                        return false;
                    }
                }
                if ($max_price > 0) {
                    if (isset($_POST['open_price']) && $_POST['open_price'] > $max_price) {
                        wc_add_notice(get_option('name_your_price_messages_too_big', __('Entered price is too big!', 'name-your-price-for-woocommerce')), 'error');
                        return false;
                    }
                }
            }
            return $passed;
        }

        /**
         * get_cart_item_open_price_from_session.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param array  $item
         * @param array  $values
         * @param string $key
         *
         * @return array
         */
        function get_cart_item_open_price_from_session(array $item, array $values, string $key)
        {
            if (array_key_exists('open_price', $values)) {
                $item['data']->open_price = $values['open_price'];
            }
            return $item;
        }

        /**
         * add_open_price_to_cart_item_data.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   array $cart_item_data
         * @param   int   $product_id
         * @param   int   $variation_id
         *
         * @return  array
         */
        function add_open_price_to_cart_item_data(array $cart_item_data, int $product_id, int $variation_id)
        {
            if (isset($_POST['open_price'])) {
                $cart_item_data['open_price'] = $_POST['open_price'];
            }
            return $cart_item_data;
        }

        /**
         * add_open_price_to_cart_item.
         *
         * @version 0.0.1
         * @since   0.0.1
         *
         * @param   array  $cart_item_data
         * @param   string $cart_item_key
         *
         * @return  array
         */
        function add_open_price_to_cart_item(array $cart_item_data, string $cart_item_key)
        {
            if (isset($cart_item_data['open_price'])) {
                $cart_item_data['data']->open_price = $cart_item_data['open_price'];
            }
            return $cart_item_data;
        }

        /**
         * add_open_price_input_field_to_frontend.
         *
         * @version 0.0.1
         * @since   0.0.1
         * @todo    step on per product basis
         */
        function add_open_price_input_field_to_frontend()
        {
            $input = '';
            $the_product = wc_get_product();

            if ($this->is_open_price_product($the_product)) {

                $input = str_replace(
                    [
                        '%frontend_label%',
                        '%open_price_input%',
                        '%currency_symbol%'
                    ],
                    [
                        $this->getTitle(),
                        $this->getPriceInputField($the_product),
                        get_woocommerce_currency_symbol()
                    ],
                    get_option('name_your_price_frontend_template', '<label for="open_price">%frontend_label%</label> %open_price_input% %currency_symbol%')
                );
            }

            echo $input;
        }

        /**
         * Renders HTML for product input field.
         *
         * @param   WC_Product_Simple $the_product
         *
         * @return  string
         */
        private function getPriceInputField(WC_Product_Simple $the_product)
        {
            $dom = new DOMDocument();

            $input_field = $dom->createElement('input');

            /** @var DOMElement $input_field */
            $input_field = $dom->appendChild($input_field);
            $input_field->setAttribute('type', 'range');
            $input_field->setAttribute('class', 'text');
            $input_field->setAttribute('name', 'open_price');
            $input_field->setAttribute('id', 'open_price');
            $input_field->setAttribute('style', 'width:175px;text-align:center;');
            $input_field->setAttribute('step', $this->getStep());
            $input_field->setAttribute('min', $this->getMinPrice($the_product));
            $input_field->setAttribute('max', $this->getMaxPrice($the_product));
            $input_field->setAttribute('onchange', 'document.getElementById("textInput").value=this.value;');
            $input_field->setAttribute('oninput', 'document.getElementById("textInput").value=this.value;');

            $number = $dom->createElement('input');

            /** @var DOMElement $number */
            $number = $dom->appendChild($number);
            $number->setAttribute('type', 'text');
            $number->setAttribute('id', 'textInput');
            $number->setAttribute('value', $this->getDefaultPrice($the_product));

            return $dom->saveHTML();
        }

        /**
         * @return string
         */
        private function getTitle()
        {
            return get_option('name_your_price_label_frontend', __('Name Your Price', 'name-your-price-for-woocommerce'));
        }

        /**
         * @return string
         */
        private function getStep()
        {
            $default_price_step = 1 / pow(10, absint(get_option('woocommerce_price_num_decimals', 2)));
            return get_option('name_your_price_price_step', $default_price_step);
        }

        /**
         * @param WC_Product_Simple $the_product
         *
         * @return string
         */
        private function getDefaultPrice(WC_Product_Simple $the_product)
        {
            return (isset($_POST['open_price'])) ? $_POST['open_price'] : get_post_meta($the_product->id, '_' . 'name_your_price_default_price', true);
        }

        /**
         * @param WC_Product_Simple $the_product
         *
         * @return int|string
         */
        private function getMaxPrice(WC_Product_Simple $the_product)
        {
            $maxPrice = get_post_meta($the_product->id, '_' . 'name_your_price_max_price', true);
            return $maxPrice ? $maxPrice : 0;
        }

        /**
         * @param $the_product
         *
         * @return int|string
         */
        private function getMinPrice($the_product)
        {
            $minPrice = get_post_meta($the_product->id, '_' . 'name_your_price_min_price', true);
            return $minPrice ? $minPrice : 0;
        }
    }

endif;

return new WC_Product_Name_Your_Price();
