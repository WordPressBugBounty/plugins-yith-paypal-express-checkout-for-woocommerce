<?php
/**
 * Plugin Name: YITH PayPal Express Checkout for WooCommerce
 * Plugin URI: https://yithemes.com/themes/plugins/yith-paypal-express-checkout-for-woocommerce/
 * Description: <code><strong>YITH PayPal Express Checkout for WooCommerce</strong></code> allows to make payments immediate with PayPal Express Checkout and forget about customers’ complaints about pending orders. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 1.50.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Domain Path: /languages/
 * Text Domain: yith-paypal-express-checkout-for-woocommerce
 * Requires Plugins: woocommerce
 *
 * WC requires at least: 9.8
 * WC tested up to: 10.0
 *
 * @package YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants.
if ( defined( 'YITH_PAYPAL_EC_VERSION' ) ) {
	return;
} else {
	define( 'YITH_PAYPAL_EC_VERSION', '1.50.0' );
}

! defined( 'YITH_PAYPAL_EC_DIR' ) && define( 'YITH_PAYPAL_EC_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_PAYPAL_EC_FILE' ) && define( 'YITH_PAYPAL_EC_FILE', __FILE__ );
! defined( 'YITH_PAYPAL_EC_URL' ) && define( 'YITH_PAYPAL_EC_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_PAYPAL_EC_INC' ) && define( 'YITH_PAYPAL_EC_INC', YITH_PAYPAL_EC_DIR . '/includes/' );
! defined( 'YITH_PAYPAL_EC_TEMPLATE_PATH' ) && define( 'YITH_PAYPAL_EC_TEMPLATE_PATH', YITH_PAYPAL_EC_DIR . '/templates/' );
! defined( 'YITH_PAYPAL_EC_INIT' ) && define( 'YITH_PAYPAL_EC_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_PAYPAL_EC_ASSETS_URL' ) && define( 'YITH_PAYPAL_EC_ASSETS_URL', YITH_PAYPAL_EC_URL . 'assets' );
! defined( 'YITH_PAYPAL_EC_SLUG' ) && define( 'YITH_PAYPAL_EC_SLUG', 'yith-paypal-express-checkout-for-woocommerce' );

// Plugin Framework Loader.
if ( file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php';
}


if ( ! function_exists( 'yith_paypal_ec_install_premium_woocommerce_admin_notice' ) ) {
	/**
	 * Print an admin notice if woocommerce is deactivated
	 *
	 * @since  1.0
	 * @return void
	 * @use    admin_notices hooks
	 */
	function yith_paypal_ec_install_premium_woocommerce_admin_notice() { ?>
		<div class="error">
			<p><?php esc_html_e( 'YITH PayPal Express Checkout for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-paypal-express-checkout-for-woocommerce' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'yith_paypal_ec_install' ) ) {
	/**
	 * Install the plugin.
	 */
	function yith_paypal_ec_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_paypal_ec_install_premium_woocommerce_admin_notice' );
			return;
		} else {
			// Let's start the game.
			if ( function_exists( 'yith_plugin_fw_load_plugin_textdomain' ) ) {
				yith_plugin_fw_load_plugin_textdomain( 'yith-paypal-express-checkout-for-woocommerce', basename( dirname( __FILE__ ) ) . '/languages' );
			}
			require_once YITH_PAYPAL_EC_INC . 'class.yith-paypal-ec.php';

			yith_paypal_ec()->run();

		}
	}
}
add_action( 'plugins_loaded', 'yith_paypal_ec_install', 11 );

/* Remove old cron */
if ( ! function_exists( 'yith_paypal_ec_deactivate' ) ) {
	/**
	 * Deactivate plugin.
	 */
	function yith_paypal_ec_deactivate() {
		wp_clear_scheduled_hook( 'yith_paypal_ec_payment_renew_orders' );
	}
}
register_deactivation_hook( __FILE__, 'yith_paypal_ec_deactivate' );

add_action( 'before_woocommerce_init', 'declare_wc_features_support'  );

/***
 * Declare support for WooCommerce features.
 */
 function declare_wc_features_support() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}