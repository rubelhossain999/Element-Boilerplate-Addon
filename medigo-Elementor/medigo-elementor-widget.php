<?php
/**
 * Plugin Name: Medigo Elementor Extension
 * Description: Custom Elementor extension which includes custom widgets.
 * Plugin URI:  https://rubelhossain99.com/elementor
 * Version:     1.0.0
 * Author:      Rubel Hossain
 * Author URI:  https://rubelhossain99.com/
 * Text Domain: medigo
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Elementor_medigo_Extension {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Elementor_medigo_Extension The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_medigo_Extension An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'medigo', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		// Register Widget Styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );

		add_action('elementor/frontend/after_enqueue_scripts', [ $this, 'widget_scripts' ] );

		// Add Plugin actions
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );

        // Custome Categori
		add_action( "elementor/elements/categories_registered", [ $this, 'elementor_medigo_category' ] );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'Medigo-extension' ),
			'<strong>' . esc_html__( 'medigo', 'Medigo-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'Medigo-extension' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'Medigo-extension' ),
			'<strong>' . esc_html__( 'Medigo Elementor Extension', 'Medigo-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'Medigo-extension' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'Medigo-extension' ),
			'<strong>' . esc_html__( 'Medigo Elementor Extension', 'Medigo-extension' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'Medigo-extension' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Init Widgets
	 *
	 * Include widgets files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_widgets() {

		require_once( __DIR__ . '/widgets/medigo-basic-addon.php' );

		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \medigo_basic_addon() );

	}

	/**
	 * Init Controls
	 *
	 * Include controls files and register them
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_controls() {

		/*
		* Todo: this block needs to be commented out when the custom control is ready
		*
		*
		// Include Control files
		require_once( __DIR__ . '/controls/test-control.php' );
		// Register control
		\Elementor\Plugin::$instance->controls_manager->register_control( 'control-type-', new \Test_Control() );
		*/

	}

	// Custom CSS
	public function widget_styles() {

		//wp_register_style( 'picchi-extension-font', plugins_url( 'https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800;900&display=swap', __FILE__ ) );
		//wp_register_style( 'picchi-theme-style', plugins_url( 'js/theme.css', __FILE__ ) );
		//wp_register_style( 'picchi-owl-style', plugins_url( 'js/owl.css', __FILE__ ) );
		//wp_register_style( 'picchi-extension-style', plugins_url( 'css/style.css', __FILE__ ) );
		
		//wp_enqueue_style('picchi-extension-font');
		//wp_enqueue_style('picchi-theme-font');
		//wp_enqueue_style('picchi-owl-font');
		//wp_enqueue_style('picchi-extension-style');

	}	

    // Custom JS
	public function widget_scripts() {
		//wp_register_script( 'picchi-waypoint-js', plugins_url( 'js/waypoint.min.js', __FILE__ ) );
		// wp_register_script( 'picchi-counterup-js', plugins_url( 'js/counterup.min.js', __FILE__ ) );
		// wp_register_script( 'picchi-owl-js', plugins_url( 'js/owl.js', __FILE__ ) );
		// wp_register_script( 'picchi-main-js', plugins_url( 'js/main.js', __FILE__ ) );
		// wp_enqueue_script('picchi-waypoint-js');
		// wp_enqueue_script('picchi-counterup-js');
		// wp_enqueue_script('picchi-owl-js');
		// wp_enqueue_script('picchi-main-js');
	}

    // Custom Category
    public function elementor_medigo_category ($manager) {
	   	$manager->add_category('Medigo-category',
	   	[
	   		'title' => __( 'Medigo Themes', 'Medigo' ),
	   		'icon' => 'fa fa-plug', //default icon
		   ]);

	}


}

Elementor_medigo_Extension::instance();