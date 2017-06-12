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
 * @package   WC-PDF-Product-Vouchers/Admin/Meta-Boxes/Views
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2017, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Voucher redemption admin template
 *
 * @type \WC_Voucher $voucher current voucher instance
 * @type array $redemption redemption data array
 *
 * @since 3.0.0
 * @version 3.0.0
 */

$order_id = ! empty( $redemption['order_id'] ) ? $redemption['order_id'] : null;
$user_id  = ! empty( $redemption['user_id'] )  ? $redemption['user_id']  : null;
$notes    = ! empty( $redemption['notes'] )    ? $redemption['notes']    : null;

$order = $order_id ? wc_get_order( $order_id ) : null;
$user  = $user_id  ? get_user_by( 'id', $user_id ) : null;

?>

<tr class="redemption <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-key="<?php echo esc_attr( $i ); ?>">

	<td class="thumb"><div></div></td>

	<td class="item">
		<?php esc_html_e( 'Redemption', 'woocommerce-pdf-product-vouchers' ); ?>

		<table class="redemption-meta">
			<tr>
				<th><?php esc_html_e( 'Date', 'woocommerce-pdf-product-vouchers' ); ?>:</th>
				<td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ', ' . get_option( 'time_format' ), strtotime( $redemption['date'] ) ) ); ?></td>
			</tr>

			<?php if ( $order || $user ) : ?>
			<tr>
				<th><?php esc_html_e( 'Added By', 'woocommerce-pdf-product-vouchers' ); ?>:</th>
				<td>
					<?php if ( $order ) : ?>
						<?php /* translators: %s - order number */ sprintf( esc_html_e( 'Order %s', 'woocommerce-pdf-product-vouchers' ), '<a href="' . get_edit_post_link( SV_WC_Order_Compatibility::get_prop( $order, 'id' ) ) . '">' . $order->get_order_number() . '</a>' ); ?>
					<?php elseif ( $user ) : ?>
						<a href="<?php echo get_edit_user_link( $user->ID ); ?>"><?php echo esc_attr( $user->display_name ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
			<?php endif; ?>

			<?php if ( $notes ) : ?>
			<tr class="view">
				<th><?php esc_html_e( 'Notes', 'woocommerce-pdf-product-vouchers' ); ?>:</th>
				<td><?php echo wp_kses_post( $notes ); ?></td>
			</tr>
			<?php endif; ?>

		</table>

		<div class="edit" style="display: none;">

			<?php /* hidden redemption fields to maintain data integrity */ ?>
			<input type="hidden" name="_redemptions[<?php echo $i; ?>][date]"     value="<?php echo esc_attr( $redemption['date'] ); ?>" />
			<input type="hidden" name="_redemptions[<?php echo $i; ?>][order_id]" value="<?php echo esc_attr( $order_id ); ?>" />
			<input type="hidden" name="_redemptions[<?php echo $i; ?>][user_id]"  value="<?php echo esc_attr( $user_id ); ?>" />

			<textarea name="_redemptions[<?php echo $i; ?>][notes]" placeholder="<?php esc_attr_e( 'Notes', 'woocommerce-pdf-product-vouchers' ); ?>"><?php echo esc_html( $notes ); ?></textarea>

			<?php
			/**
			 * Triggered after voucher redemption editable fields
			 *
			 * @since 3.0.0
			 * @param \WC_Voucher $voucher
			 * @param array $redemption
			 */
			do_action( 'wc_pdf_product_vouchers_admin_after_voucher_redemption_fields', $voucher, $redemption );
			?>
		</div>
	</td>

	<td class="value" width="1%">
		<div class="view">
			<?php echo wc_price( $redemption['amount'] ); ?>
		</div>
		<div class="edit" style="display: none;">
			<input type="text" name="_redemptions[<?php echo $i; ?>][amount]" class="js-wc-pdf-vouchers-redeem-amount" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo wc_format_localized_price( $redemption['amount'] ); ?>" />
		</div>
	</td>

	<td class="actions" width="1%">
		<?php if ( $voucher->is_editable() ) : ?>
			<div class="wc-voucher-edit-item-actions">
				<a class="edit-voucher-item tips js-edit-voucher-redemption" href="#" data-tip="<?php esc_attr_e( 'Edit redemption', 'woocommerce-pdf-product-vouchers' ); ?>"></a>
				<a class="delete-voucher-item js-delete-voucher-redemption" href="#"></a>
			</div>
		<?php endif; ?>
	</td>

</tr>
