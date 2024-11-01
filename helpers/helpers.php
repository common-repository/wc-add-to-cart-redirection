<?php
/**
 * @package WC_Add_To_Cart_Redirection
 */
// Compatibility Check
function amz_wcrd_check_woocommerce_plugin()
{
	if ( ! class_exists( 'WooCommerce' ) ) {
		return __( 'Please install and activate <strong>WooCommerce</strong> plugin first.', 'wc-add-to-cart-redirection' );
	}

	if ( version_compare( wc()->version, AMZ_WCRD_MINIMUM_WOOCOMMERCE_VERSION, '<=' ) ) {
		return sprintf( __( "Please update your WooCommerce plugin. It's outdated. %s or latest required", 'wc-add-to-cart-redirection' ), AMZ_WCRD_MINIMUM_WOOCOMMERCE_VERSION );
	}

	return false;
}

// Plugin Die Message
function amz_wcrd_plugin_die_message( $message )
{
	return '<p>' .
	       $message
	       . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'Go back', 'wc-add-to-cart-redirection' ) . '</a>';
}

function dd($code){
	echo '<pre>';
	var_dump($code);
	echo '</pre>';
	wp_die();
}

function dp($code){
	echo '<pre>';
	print_r($code);
	echo '</pre>';
	wp_die();
}