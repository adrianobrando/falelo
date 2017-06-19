<?php
/**
 * WooCommerce PDF Product Vouchers
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce PDF Product Vouchers to newer
 * versions in the future. If you wish to customize WooCommerce PDF Product Vouchers for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-pdf-product-vouchers/ for more information.
 *
 * @package   WC-PDF-Product-Vouchers/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * PDF Product Vouchers Cart handler/helper class
 *
 * @since 1.2.0
 */
class WC_PDF_Product_Vouchers_Cart {

	/** @var array checkout order item values */
	private $checkout_order_item_values = array();

	/** @var array user input fallback fields */
	private $user_input_fallback_fields;


	/**
	 * Initializes the cart handler
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

		add_filter( 'woocommerce_add_to_cart_validation',     array( $this, 'add_to_cart_validation'), 10, 6 );
		add_filter( 'woocommerce_add_cart_item_data',         array( $this, 'add_cart_item_voucher_data'), 10, 3 );
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session'), 10, 2 );
		add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_voucher_data'), 10, 3 );
		add_filter( 'woocommerce_get_item_data',              array( $this, 'display_voucher_data_in_cart'), 10, 2 );

		// remember checkour order item values
		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
			add_action( 'woocommerce_new_order_item',      array( $this, 'remember_checkout_order_item_value' ), 10, 2 );
		} else {
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'remember_checkout_order_item_value'), 10, 2 );
		}

		// create vouchers for order items on checkout
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'create_order_item_vouchers' ), 10, 3 );
	}


	/**
	 * Filter to check whether a product is valid to be added to the cart.
	 * This is used to ensure any required user input fields are supplied
	 *
	 * @since 1.2.0
	 * @param boolean $valid whether the product as added is valid
	 * @param int $product_id the product identifier
	 * @param int $quantity the amount being added
	 * @param int $variation_id optional variation id
	 * @param array $variations optional variation configuration
	 * @param array $cart_item_data optional cart item data.  This will only be
	 *        supplied when an order is being-ordered, in which case the
	 *        required fields will not be available from the REQUEST array
	 * @return true if the product is valid to add to the cart
	 */
	public function add_to_cart_validation( $valid, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		$_product_id = $variation_id ? $variation_id : $product_id;
		$product     = wc_get_product( $_product_id );

		// is this a voucher product?
		if ( ! empty( $_POST ) && WC_PDF_Product_Vouchers_Product::has_voucher_template( $product ) ) {

			$voucher_template = WC_PDF_Product_Vouchers_Product::get_voucher_template( $product );

			// set any user-input fields, which will end up in the order item meta data (which can be displayed on the frontend)
			$fields = $voucher_template->get_user_input_voucher_fields();

			foreach ( $fields as $key => $field ) {

				if ( $voucher_template->user_input_field_is_required( $key ) ) {

					if ( ! isset( $_POST[ $key ][ $_product_id ] ) || ! $_POST[ $key ][ $_product_id ] ) {
						/* translators: %s - field label */
						wc_add_notice( sprintf( __( "Field '%s' is required.", 'woocommerce-pdf-product-vouchers' ), $field['label'] ), 'error' );
						$valid = false;
					}
				}
			}
		}

		return $valid;
	}


	/**
	 * Displays any user-input voucher data in the cart
	 *
	 * @since 1.2.0
	 * @param array $data array of name/display pairs of data to display in the cart
	 * @param array $item associative array of a cart item (product)
	 *
	 * @return array of name/display pairs of data to display in the cart
	 */
	public function display_voucher_data_in_cart( $data, $item ) {

		if ( isset( $item['voucher_item_meta_data'] ) && isset( $item['voucher_template_id'] ) ) {
			// voucher data to display

			$voucher_template = new WC_Voucher_Template( $item['voucher_template_id'] );

			foreach ( $item['voucher_item_meta_data'] as $name => $value ) {
				if ( $voucher_template->is_user_input_field( $name ) && $value ) {
					$data[] = array(
						'name'    => $voucher_template->get_field_label( $name ),
						'display' => stripslashes( $value ),
						'hidden'  => false,
					);
				}
			}
		}

		return $data;
	}


	/**
	 * Persists our cart item voucher data to the session, if any
	 *
	 * @since 1.2.0
	 * @param array $cart_item associative array of data representing a cart item (product)
	 * @param array $values associative array of data for the cart item, currently in the session
	 *
	 * @return associative array of data representing a cart item (product)
	 */
	public function get_cart_item_from_session( $cart_item, $values ) {

		if ( isset( $values['voucher_item_meta_data'] ) ) {
			$cart_item['voucher_item_meta_data'] = $values['voucher_item_meta_data'];
		}
		if ( isset( $values['voucher_image_id'] ) ) {
			$cart_item['voucher_image_id'] = $values['voucher_image_id'];
		}
		if ( isset( $values['voucher_template_id'] ) ) {
			$cart_item['voucher_template_id'] = $values['voucher_template_id'];
		}
		if ( isset( $values['voucher_random'] ) ) {
			$cart_item['voucher_random'] = $values['voucher_random'];
		}

		return $cart_item;
	}


	/**
	 * Copies the voucher data from a previous order to the cart
	 *
	 * Sets the voucher data on the global $_POST array, so that validation and
	 * adding to cart works just like when adding the voucher product manually.
	 *
	 * @since 3.0.0
	 * @param array associative array of data representing cart item data
	 * @param array associative array of data representing a cart item (product)
	 * @param \WC_Order the order instance
	 * @return array associative array of data representing cart item data
	 */
	public function order_again_cart_item_voucher_data( $cart_item_data, $item, $order ) {

		if ( ! empty( $item['voucher_id'] ) && ! empty( $item['product_id'] ) ) {

			// When ordering again, we don't need to worry about multiple vouchers
			// since they will be identical except for the voucher number - so simply
			// use the first voucher's template to order again.
			$voucher = wc_pdf_product_vouchers_get_voucher( $item['voucher_id'] );

			if ( ! empty( $voucher ) ) {

				$_POST['voucher_image'][ $item['product_id'] ] = $voucher->get_image_id();

				foreach ( $voucher->get_template()->get_user_input_voucher_fields() as $key => $field ) {

					if ( $value = $voucher->get_field_value( $key ) ) {
						$_POST[ $key ][ $item['product_id'] ] = $value;
					}
				}
			}
		}

		return $cart_item_data;
	}


	/**
	 * Adds any user-supplied voucher field data to the cart item data, to
	 * set in the session
	 *
	 * @since 1.2.0
	 * @param array $cart_item_data associative-array of name/value pairs of cart item data
	 * @param int $product_id the product identifier
	 * @param int $variation_id optional product variation identifer
	 * @return array associative array of name/value pairs of cart item data to set in the session
	 */
	public function add_cart_item_voucher_data( $cart_item_data, $product_id, $variation_id ) {

		$_product_id = $variation_id ? $variation_id : $product_id;
		$product     = wc_get_product( $_product_id );

		// is this a voucher product?
		if ( WC_PDF_Product_Vouchers_Product::has_voucher_template( $product ) ) {

			$voucher_template = WC_PDF_Product_Vouchers_Product::get_voucher_template( $product );

			// record the voucher template id
			$cart_item_data['voucher_template_id'] = $voucher_template->get_id();

			// set the selected voucher image id, or default to the main one if the voucher was added from the catalog
			$cart_item_data['voucher_image_id'] = isset( $_POST['voucher_image'][ $_product_id ] ) ? $_POST['voucher_image'][ $_product_id ] : $voucher_template->get_image_id();

			// set any user-input fields, which will end up in the order item meta data (which can be displayed on the frontend)
			$fields = $voucher_template->get_user_input_voucher_fields();

			foreach ( $fields as $key => $field ) {
				if ( isset( $_POST[ $key ][ $_product_id ] ) ) {
					$cart_item_data['voucher_item_meta_data'][ $key ] = $_POST[ $key ][ $_product_id ];
				}
			}

			// add a random so that multiple of the same product can be added to the cart when "sold individually" is enabled
			$cart_item_data['voucher_random'] = uniqid( 'voucher_' );
		}

		return $cart_item_data;
	}


	/**
	 * Stores order item values with voucher templates internally for later use.
	 *
	 * TODO: update this method when WC removes the legacy_values propery from order items {IT 2017-03-07}
	 * TODO: update this method when removing support for WC < 3.0.0 {IT 2017-05-18}
	 *
	 * @internal
	 * @since 3.0.6
	 *
	 * @param int $item_id item identifier
	 * @param \WC_Order_item|array $item order item instance or an array of order item checkout values
	 */
	public function remember_checkout_order_item_value( $item_id, $item ) {

		// WC 3.0.0+ the second param is the order item object
		if ( is_object( $item ) ) {

			// since WC 3.0.0, checkout values are available in the legacy_values property
			if ( ! property_exists( $item, 'legacy_values' ) ) {
				return;
			}

			$values = $item->legacy_values;

		// pre 3.0.0, the second param will be the checkout values for the order item
		} else {
			$values = $item;
		}

		// is this a voucher product?
		if ( ! empty( $values['voucher_template_id'] ) ) {
			$this->checkout_order_item_values[ $item_id ] = $values;
		}
	}


	/**
	 * Create vouchers for any order items with a voucher template on checkout.
	 *
	 * @internal
	 * @since 3.0.6
	 *
	 * @param int $order_id the order id
	 * @param array $posted_data all data posted on checkout
	 * @param \WC_Order $order (optional) the order object - only provided since WC 3.0.0+
	 */
	public function create_order_item_vouchers( $order_id, $posted_data, $order = null ) {

		// nothing to process
		if ( empty( $this->checkout_order_item_values ) ) {
			return;
		}

		// pre WC 3.0.0, $order param was not passed in the `woocommerce_checkout_order_processed` hook
		if ( ! $order ) {
			$order = wc_get_order( $order_id );

			if ( ! $order instanceof WC_Order ) {
				return;
			}
		}

		foreach ( $this->checkout_order_item_values as $item_id => $values ) {

			$voucher_template = wc_pdf_product_vouchers_get_voucher_template( $values['voucher_template_id'] );

			// invalid voucher template
			if ( ! $voucher_template ) {
				continue;
			}

			// a single voucher or a voucher that supports quantity field
			if ( $values['quantity'] === 1 || $voucher_template->has_quantity_field() ) {

				$values['price'] = $values['line_subtotal'] / $values['quantity'];
				$values['tax']   = ! empty( $values['line_tax'] ) ? $values['line_tax'] / $values['quantity'] : 0;

				$this->create_order_item_voucher( $item_id, $values, $order, $voucher_template, $posted_data );

			} else {

				// multiple quantities were purchased, but quantity field is not supported,
				// simply generate as many vouchers as the quantity
				$price = $values['line_subtotal'] / $values['quantity'];
				$tax   = ! empty( $values['line_tax'] ) ? $values['line_tax'] / $values['quantity'] : 0;

				for ( $i = 1; $i <= $values['quantity']; $i++ ) {

					$_values             = $values;
					$_values['price']    = $price;
					$_values['tax']      = $tax;
					$_values['quantity'] = 1;

					$this->create_order_item_voucher( $item_id, $_values, $order, $voucher_template, $posted_data );
				}
			}
		}
	}


	/**
	 * Creates a voucher from an order item
	 *
	 * @since 3.0.0
	 * @param int $item_id order item identifier
	 * @param array $values order item values
	 * @param \WC_Order $order the order object
	 * @param \WC_Voucher_Template $voucher_template the vohcher template object
	 * @param array $posted_data all data posted at checkout
	 */
	private function create_order_item_voucher( $item_id, $values, WC_Order $order, WC_Voucher_Template $voucher_template, $posted_data ) {

		// create voucher
		$voucher = wc_pdf_product_vouchers_create_voucher( array(
			'voucher_template_id' => $values['voucher_template_id'],
			'voucher_image_id'    => $values['voucher_image_id'],
			'order_id'            => SV_WC_Order_Compatibility::get_prop( $order, 'id' ),
			'order_item_id'       => $item_id,
			'user_id'             => SV_WC_Order_Compatibility::get_prop( $order, 'customer_user' ),
			'product_id'          => ! empty( $values['data'] ) ? $values['data']->get_id() : null,
			'product_price'       => $values['price'],
			'product_tax'         => $values['tax'],
			'product_quantity'    => $values['quantity'],
		) );

		// voucher creation failed - log the error and move on
		if ( is_wp_error( $voucher ) ) {

			$title = ! empty( $values['data'] ) ? $values['data']->get_title() : '';

			/* translators: Placeholders: %1$s - order number, %2$s - product name, %3$s - order item id, %4$s - error message */
			wc_pdf_product_vouchers()->log( sprintf( __( 'Could not create voucher for order %1$s from item %2$s (#%3$s): %4$s', 'woocommerce-pdf-product-vouchers' ), $order->get_order_number(), $title, $item_id, $voucher->get_error_message() ) );
			return;
		}

		// store voucher purchaser information. Purchaser name may be overwritten below by user input
		$purchaser_name  = trim( sprintf( '%s %s', $posted_data['billing_first_name'], $posted_data['billing_last_name'] ) );
		$purchaser_email = $posted_data['billing_email'];

		update_post_meta( $voucher->get_id(), '_purchaser_name',  $purchaser_name );
		update_post_meta( $voucher->get_id(), '_purchaser_email', $purchaser_email );

		// store a reference to the new voucher id on item meta
		wc_add_order_item_meta( $item_id, '_voucher_id', $voucher->id );

		// set any user-input fields to the order item meta data (which can be displayed on the frontend)
		// ie recipient_name, message
		if ( isset( $values['voucher_item_meta_data'] ) ) {
			foreach ( $values['voucher_item_meta_data'] as $name => $value ) {
				if ( $voucher_template->is_user_input_field( $name ) ) {

					// make sure any max length rules are imposed
					if ( $value && $max_length = $voucher_template->get_user_input_field_max_length( $name ) ) {
						$value = substr( $value, 0, $max_length );
					}

					if ( ! $value && $this->use_fallback_value( $name, $voucher_template, $values['voucher_item_meta_data'] ) ) {
						$value = $this->get_user_input_fallback_value( $name, $values, $voucher, $posted_data );
					}

					// only store the value if there is a value
					if ( $value ) {
						$voucher->set_user_input_field( $name, $value );
					}
				}
			}
		}
	}


	/**
	 * Checks whether to use a fallback value for the user input field or not
	 *
	 * @since 3.0.0
	 * @param string $field_id field identifier
	 * @param \WC_Voucher_Template $voucher_template the voucher template
	 * @param array $posted_meta posted meta
	 * @return bool whether a fallback value should be used or not
	 */
	private function use_fallback_value( $field_id, WC_Voucher_Template $voucher_template, $posted_meta ) {

		$fallback_fields = $this->get_user_input_fallback_fields();

		// field is not fallback-capable
		if ( ! in_array( $field_id, $fallback_fields, true ) ) {
			return false;
		}

		$user_input_fields = array_keys( $voucher_template->get_user_input_voucher_fields() );

		// at least one of the user input fields has an input value
		// provided, skip fallback
		foreach ( $user_input_fields as $input_field ) {
			if ( ! empty( $posted_meta[ $input_field ] ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Returns a list of fallback-capable user input fields
	 *
	 * @since 3.0.0
	 * @return string[]
	 */
	private function get_user_input_fallback_fields() {

		if ( ! isset( $this->user_input_fallback_fields ) ) {

			/**
			 * Filter which user input fields should try to use a fallback value
			 * if the value wasn't provided in checkout
			 *
			 * @since 3.0.0
			 * @param array $fields
			 */
			$this->user_input_fallback_fields = apply_filters( 'wc_pdf_product_vouchers_user_input_fallback_fields', array( 'recipient_name', 'recipient_email' ) );
		}

		return $this->user_input_fallback_fields;
	}


	/**
	 * Returns the checkout fallback value for a user-input field
	 *
	 * @since 3.0.0
	 * @param string $field_id field identifier
	 * @param array $values checkout values
	 * @param \WC_Voucher $voucher the voucher generated at checkout
	 * @param array $posted_data all data posted at checkout
	 * @return mixed field value
	 */
	private function get_user_input_fallback_value( $field_id, $values, WC_Voucher $voucher, $posted_data ) {

		$value = '';

		switch ( $field_id ) {

			case 'recipient_name':
				$value = $voucher->get_purchaser_name();
			break;

			case 'recipient_email':
				$value = $voucher->get_purchaser_email();
			break;
		}

		/**
		 * Filters the checkout fallback value for a user-input field.
		 *
		 * @since 3.0.0
		 * @param mixed $value field value
		 * @param string $field_id field identifier
		 * @param array $values checkout values for the order item
		 * @param array $posted_data all data posted at checkout
		 * @param \WC_Voucher $voucher the voucher generated at checkout
		 */
		return apply_filters( 'wc_pdf_product_vouchers_recipient_data_fallback_value', $value, $field_id, $values, $posted_data, $voucher );
	}

}