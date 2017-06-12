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
 * PDF Generator class
 *
 * @since 3.0.0
 */
class WC_PDF_Product_Vouchers_PDF_Generator {


	/**
	 * Generates and saves or streams a PDF file for a voucher
	 *
	 * @since 3.0.0
	 * @param \WC_Voucher $voucher the voucher object to generate the preview image for
	 * @param bool $save (optional) whether to save the pdf to filesystem or stream the output
	 * @throws SV_WC_Plugin_Exception if the voucher image is not available
	 */
	public static function generate_voucher_pdf( WC_Voucher $voucher, $save = true ) {

		require_once( wc_pdf_product_vouchers()->get_plugin_path() . '/lib/dompdf/autoload.inc.php' );

		$upload_dir = wp_upload_dir();
		$image      = wp_get_attachment_metadata( $voucher->get_image_id() );

		// make sure the image hasn't been deleted through the media editor
		if ( ! $image ) {
			throw new SV_WC_Plugin_Exception( __( 'Voucher image not found', 'woocommerce-pdf-product-vouchers' ) );
		}

		// make sure the file exists and is readable
		if ( ! is_readable( $upload_dir['basedir'] . '/' . $image['file'] ) ) {
			/* translators: Placeholders: %s - image path */
			throw new SV_WC_Plugin_Exception( sprintf( __( 'Voucher image file missing or not readable: %s', 'woocommerce-pdf-product-vouchers' ), $upload_dir['basedir'] . '/' . $image['file'] ) );
		}

		// try to give us unlimited time if possible - large background images may take a lot of time to render in pdf
		set_time_limit( 0 );

		$dpi = $voucher->get_dpi();

		// get the width and height in points
		$width_pt  = self::convert_pixels_to_points( $image['width'], $dpi );
		$height_pt = self::convert_pixels_to_points( $image['height'], $dpi );

		// instantiate and use the dompdf class
		$dompdf = new Dompdf\Dompdf();

		$upload_dir = wp_upload_dir( null, false );

		$dompdf->set_option( 'font_cache', $upload_dir['basedir'] . '/pdf_vouchers_font_cache' );
		$dompdf->set_option( 'font_dir',   $upload_dir['basedir'] . '/pdf_vouchers_font_cache' );
		$dompdf->set_option( 'enable_remote', true );
		$dompdf->set_option( 'dpi', $dpi );

		$dompdf->loadHtmlFile( $voucher->get_render_url() );

		// (optional) setup the paper size and orientation
		$dompdf->setPaper( array( 0, 0, $width_pt, $height_pt ) );

		// render the HTML as PDF
		$dompdf->render();

		if ( ! $save ) {
			// download file
			return $dompdf->stream( 'voucher-preview-' . $voucher->get_id() );
		}

		$voucher_path = wc_pdf_product_vouchers()->get_uploads_path() . '/' . $voucher->get_voucher_path();

		// ensure the path that will hold the voucher pdf exists
		if ( ! file_exists( $voucher_path ) ) {
			@mkdir( $voucher_path, 0777, true );
		}

		// is the output path writable?
		if ( ! is_writable( $voucher_path ) ) {
			/* translators: %s - voucher file path */
			throw new SV_WC_Plugin_Exception( sprintf( __( 'Voucher path %s is not writable', 'woocommerce-pdf-product-vouchers' ), $voucher_path ) );
		}

		$file_path = $voucher->get_voucher_full_filename();

		// save the pdf as a file
		file_put_contents( $file_path, $dompdf->output() );

		// try to create a preview image of the PDF - this only works if Imagick is
		// installed and enabled
		self::generate_voucher_preview_image( $voucher );
	}


	/**
	 * Generates a preview image of the PDF for the voucher
	 *
	 * @param \WC_Voucher $voucher the voucher object to generate the preview image for
	 * @since 3.0.0
	 */
	public static function generate_voucher_preview_image( WC_Voucher $voucher ) {

		if ( ! wp_image_editor_supports( array( 'mime_type' => 'application/pdf' ) ) ) {
			return;
		}

		$file_path = $voucher->get_voucher_full_filename();
		$preview   = wp_get_image_editor( $file_path );

		if ( ! is_wp_error( $preview ) ) { // Most likely cause for error is that ImageMagick is not available.

			$preview_file_path = $voucher->get_voucher_full_filename( 'png' );

			$preview->resize( 768, null ); // medium size image width

			$result = $preview->save( $preview_file_path, 'image/png' );

			if ( ! is_wp_error( $result ) ) {
				update_post_meta( $voucher->get_id(), '_preview_image', true );
			} else {
				delete_post_meta( $voucher->get_id(), '_preview_image' );
			}
		}
	}


	/**
	 * Converts a pixel value to points
	 *
	 * In 3.0.0 moved here from \WC_Voucher, added the $dpi param.
	 *
	 * @since 2.3.0
	 * @param int $pixels the pixel value
	 * @param int $dpi the dpi value
	 * @return float the point value
	 */
	public static function convert_pixels_to_points( $pixels, $dpi ) {
		return ( (int) $pixels * 72 ) / $dpi;
	}

}
