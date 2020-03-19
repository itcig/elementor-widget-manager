<?php
/**
 * Responsible for setting up constants, classes and templates.
 *
 * @author  IdeaBox
 * @package Elementor Widget Manager/Loader
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All function loads from this class Widget_Manager_Loader.
 */
class  Widget_Manager_Loader {
	/**
	 * For automatically loading action and filters.
	 */
	public function __construct() {

		// Enqueue style and script.
		add_action( 'admin_enqueue_scripts', array( $this, 'register_script' ) );

		// Action to register setting for get_option function.
		add_action( 'init', array( $this, 'register_plugin_settings' ) );

		// Adding widget manager option to elementor.
		add_action( 'admin_menu', array( $this, 'register_widget_manager_menu' ), 700 );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'unregister_elementor_widget' ) );
	}

	/**
	 * For registering script and stylesheet.
	 *
	 * @param string $hook To check if the current page.
	 * @return void
	 */
	public function register_script( $hook ) {

		// Register setting for this page only.
		if ( 'elementor_page_widget-manger' === $hook ) {
			wp_enqueue_style( 'ewm-style', EL_WIDGET_MANAGER_URL . 'build/admin.css', array( 'wp-components' ), EL_WIDGET_MANAGER_VERSION, false );
			wp_enqueue_script( 'ewm-script', EL_WIDGET_MANAGER_URL . 'build/admin.js', array( 'wp-components', 'wp-element', 'wp-api', 'wp-i18n' ), EL_WIDGET_MANAGER_VERSION, true );

			// Calling elementor widget manager class.
			$widgets = Elementor_Widget_Manager::instance()->get_registered_widgets();
			wp_localize_script( 'ewm-script', 'ewm_widgets', $widgets );
		}
	}


	/**
	 * Registering widget manager.
	 *
	 * @return void
	 */
	public function register_widget_manager_menu() {
		// Adding sub-menu to elementor.
		add_submenu_page(
			'elementor', // Parent slug.
			__( 'Widget Manager', 'el-widget-manager' ), // Page title.
			__( 'Widget Manager', 'el-widget-manager' ), // Menu title.
			'manage_options', // Capability.
			'widget-manger', // Menu slug.
			array( $this, 'render_widget_manager_page' ) // Callback function.
		);
	}

	/**
	 * Includes widget manager page from react.
	 *
	 * @return void
	 */
	public function render_widget_manager_page() {
		echo "<div id='ewm-setting-root'></div>";
	}

	/**
	 * Registering widget settings in rest-api.
	 *
	 * @return void
	 */
	public function register_plugin_settings() {
		register_setting(
			'ewm-settings-group',
			'ewm_widget',
			array(
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type' => 'string',
						),
					),
				),
				'default'      => array(),
			)
		);
	}

	/**
	 * Unregistering elementor widget settings.
	 *
	 * @return void
	 */
	public function unregister_elementor_widget( $widgets_manager ) {
		$elementor_widget_blacklist = get_option( 'ewm_widget' );

		$widgets_manager->unregister_widget_type('heading');

		//print_r( $elementor_widget_blacklist );
	}
}


$widget_manager_loader = new Widget_Manager_Loader();
