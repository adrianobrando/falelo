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
 * Voucher redeem action admin template
 *
 * @type \WC_Voucher $voucher current voucher instance
 *
 * @since 3.0.0
 * @version 3.0.0
 */
?>

<table class="wc-voucher-totals">
	<tr>
		<td class="label"><?php esc_html_e( 'Amount already redeemed', 'woocommerce-pdf-product-vouchers' ); ?>:</td>
		<td class="total">-<?php echo wc_price( $voucher->get_total_redeemed(), array( 'currency' => $voucher->get_voucher_currency() ) ); ?></td>
	</tr>
	<tr>
		<td class="label"><?php esc_html_e( 'Total available to redeem', 'woocommerce-pdf-product-vouchers' ); ?>:</td>
		<td class="total"><?php echo wc_price( $voucher->get_remaining_value(), array( 'currency' => $voucher->get_voucher_currency() ) ); ?></td>
	</tr>
	<tr>
		<td class="label"><label for="redemption_amount"><?php esc_html_e( 'Redemption amount', 'woocommerce-pdf-product-vouchers' ); ?>:</label></td>
		<td class="total">
			<input type="text" id="redemption_amount" name="redemption_amount" class="text js-wc-pdf-vouchers-redeem-amount" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo wc_format_localized_price( $voucher->get_remaining_value() ); ?>" />
			<div class="clear"></div>
		</td>
	</tr>
	<tr>
		<td class="label"><label for="redemption_notes"><?php esc_html_e( 'Notes (optional)', 'woocommerce-pdf-product-vouchers' ); ?>:</label></td>
		<td class="total">
			<input type="text" class="text" id="redemption_notes" name="redemption_notes" />
			<div class="clear"></div>
		</td>
	</tr>
</table>
<div class="clear"></div>
