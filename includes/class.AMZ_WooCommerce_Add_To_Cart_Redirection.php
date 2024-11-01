<?php

defined( 'ABSPATH' ) || exit;

/**
 *
 * @class AMZ_WooCommerce_Add_To_Cart_Redirection
 *
 * @package WC_Add_To_Cart_Redirection
 */
final class AMZ_WooCommerce_Add_To_Cart_Redirection
{
	/**
	 * An instance of current class
	 *
	 * @var AMZ_WooCommerce_Add_To_Cart_Redirection
	 */
	protected static $_instance = null;

	/**
	 * Meta Name for Redirection Page
	 *
	 * @var string
	 */
	protected $_meta_name = 'amz_wcrd_redirection_link';

	/**
	 * AMZ_WooCommerce_Add_To_Cart_Redirection constructor.
	 */
	public function __construct()
	{
		// Hooks
		$this->hooks();

		// Action Hook to add content/functions from outside
		do_action( 'amz_wcrd_loaded', $this );
	}

	/**
	 * Return only one instance of current class
	 *
	 * @return AMZ_WooCommerce_Add_To_Cart_Redirection
	 */
	public static function instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * All Hooks
	 */
	public function hooks()
	{
		// Init: Language - Text Domain
		add_action( 'init', array( $this, 'text_domain' ) );

		// Redirection Settings in WooCommerce Settings Panel
		add_filter( 'woocommerce_product_settings', array( $this, 'settings' ) );

		// Redirection
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'redirect' ) );

		// Redirection - AJAX (Old)
		if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
			add_filter( 'wc_add_to_cart_params', array( $this, 'redirect_old_js' ) );
		}
		// Redirection - AJAX
		add_filter( 'woocommerce_get_script_data', array( $this, 'redirect_js' ), 10, 2 );
	}

	/**
	 * Text Domain for Translation
	 */
	public function text_domain()
	{
		load_plugin_textdomain( 'wc-add-to-cart-redirection', false, AMZ_WCRD_PLUGIN_DIRNAME . '/languages' );
	}

	/**
	 * @param $settings
	 *
	 * @return array
	 */
	public function settings( $settings )
	{
		$section_title = $settings[2]['title'];

		unset( $settings[2] );
		unset( $settings[3]['checkboxgroup'] );

		$settings[3]['title'] = $section_title;

		array_splice( $settings, 3, 0, array(
			array(
				'title'    => esc_html__( 'Redirection Link', 'wc-add-to-cart-redirection' ),
				'id'       => $this->_meta_name,
				'selected' => absint( get_option( 'woocommerce_checkout_page_id' ) ),
				'type'     => 'single_select_page',
				'class'    => 'wc-enhanced-select-nostd', // Means with cross icon
				'css'      => 'min-width:300px;',
				'desc_tip' => esc_html__( 'Redirection to selected page upon product added to cart', 'wc-add-to-cart-redirection' ),
			)
		) );

		return apply_filters( 'amz_wcrd_settings', $settings );
	}

	public function redirect( $url )
	{
		if ( (bool) get_option( $this->_meta_name ) ) {
			$url = get_permalink( get_option( $this->_meta_name ) );
		}

		return apply_filters( 'amz_wcrd_redirection_link', $url );
	}

	public function redirect_old_js( $data )
	{
		$data['cart_redirect_after_add'] = (bool) get_option( $this->_meta_name ) ? 'yes' : 'no';

		return $data;
	}

	public function redirect_js( $params, $handle )
	{
		if ( 'wc-add-to-cart' == $handle ) {
			$params = array_merge( $params, array(
				'cart_redirect_after_add' => (bool) get_option( $this->_meta_name ) ? 'yes' : 'no'
			) );
		}

		return $params;
	}
}

