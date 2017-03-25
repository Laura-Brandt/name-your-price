<?php
/**
 * Name Your Price for WooCommerce - Settings
 *
 * @version 0.0.1
 * @since   0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists('WC_Settings_Name_Your_Price') ) :

class WC_Settings_Name_Your_Price extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 0.0.1
	 * @since   0.0.1
	 */
	function __construct() {
		$this->id    = 'name_your_price';
		$this->label = __( 'Name Your Price', 'name-your-price-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 0.0.1
	 * @since   0.0.1
	 */
	function get_settings() {
		global $current_section;
		return apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 0.0.1
	 * @since   0.0.1
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					delete_option( $value['id'] );
					add_option( $value['id'], $value['default'], '', 'no' );
				}
			}
		}
	}

	/**
	 * Save settings.
	 *
	 * @version 0.0.1
	 * @since   0.0.1
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new WC_Settings_Name_Your_Price();
