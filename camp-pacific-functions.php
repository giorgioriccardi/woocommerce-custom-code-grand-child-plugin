<?php
/**
 * Plugin Name: Camp Pacific Functions
 * Plugin URI: https://camppacific.com
 * Description: Camp Pacific Functions adds the ability to inject custom PHP, CSS, JS to the WP site, theme or child-theme without necessarily being tight to the theme container itself.
 * Version: 1.0.1
 * Author: Giorgio Riccardi @CampPacific
 * Author URI: https://camppacific.com
 * Requires at least: 3.0.0
 * Tested up to:      4.8.2
 * Requires PHP: 			5.6

 * @package CP_Theme_Customization

 * License: GPL v3

 * Camp Pacific Functions Plugin
 * Copyright © 2017-2018, Camp Pacific - www.camppacific.com

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Debug before Safety first!

// function tl_save_error() {
//     update_option( 'plugin_error',  ob_get_contents() );
// }
// add_action( 'activated_plugin', 'tl_save_error' );
// /* Then to display the error message: */
// echo get_option( 'plugin_error' );
// /* Or you could do the following on a Windows machine: */
// file_put_contents( 'C:\errors' , ob_get_contents() ); // or any suspected variable

// Safety first!

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class CampPacificFunctions {

  const OPTION_INSTALL_DATE = 'camppacificfunctions-install-date';
	const OPTION_ADMIN_NOTICE_KEY = 'camppacificfunctions-hide-notice';

  /**
	 * Method run on plugin activation
	 */
	public static function plugin_activation() {
		// for admin bar banner
		// include nag class
		require_once( plugin_dir_path( __FILE__ ) . '/classes/class-nag.php' );

		// insert install date
		CPF_Nag::insert_install_date();
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		// admin bar banner
		add_action( 'init', array( $this, 'frontend_hooks' ) );
		add_action( 'admin_init', array( $this, 'admin_hooks' ) );

		// custom functions and styles
		add_action( 'init', array( $this, 'theme_customization_setup' ), -1 );
		require_once( 'custom-code/functions.php' );
	}

	// start custom functions and styles code

	/**
	 * Setup all the things
	 */
	public function theme_customization_setup() {
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_customization_css' ), 999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_customization_js' ) );
		add_filter( 'template_include',   array( $this, 'theme_customization_template' ), 11 );
		add_filter( 'wc_get_template',    array( $this, 'theme_customization_wc_get_template' ), 11, 5 );
	}

	/**
	 * Enqueue the CSS
	 *
	 * @return void
	 */
	public function theme_customization_css() {
		wp_enqueue_style( 'custom-css', plugins_url( '/custom-code/style.css', __FILE__ ) );
	}

	/**
	 * Enqueue the Javascript
	 *
	 * @return void
	 */
	public function theme_customization_js() {
		wp_enqueue_script( 'custom-js', plugins_url( '/custom-code/custom.js', __FILE__ ), array( 'jquery' ) );
	}

	/**
	 * Look in this plugin for template files first.
	 * This works for the top level templates (IE single.php, page.php etc). However, it doesn't work for
	 * template parts yet (content.php, header.php etc).
	 *
	 * Relevant trac ticket; https://core.trac.wordpress.org/ticket/13239
	 *
	 * @param  string $template template string.
	 * @return string $template new template string.
	 */
	public function theme_customization_template( $template ) {
		if ( file_exists( untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/custom-code/templates/' . basename( $template ) ) ) {
			$template = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/custom-code/templates/' . basename( $template );
		}

		return $template;
	}

	/**
	 * Look in this plugin for WooCommerce template overrides.
	 *
	 * For example, if you want to override woocommerce/templates/cart/cart.php, you
	 * can place the modified template in <plugindir>/custom-code/templates/woocommerce/cart/cart.php
	 *
	 * @param string $located is the currently located template, if any was found so far.
	 * @param string $template_name is the name of the template (ex: cart/cart.php).
	 * @return string $located is the newly located template if one was found, otherwise
	 *                         it is the previously found template.
	 */
	public function theme_customization_wc_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		$plugin_template_path = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/custom-code/templates/woocommerce/' . $template_name;

		if ( file_exists( $plugin_template_path ) ) {
			$located = $plugin_template_path;
		}

		return $located;
	}

	// end custom functions and styles code

	// start admin bar banner code

	/**
	 * Setup the admin hooks
	 *
	 * @return void
	 */
	public function admin_hooks() {

		// Check if user is an administrator
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// include plugin links class
		require_once( plugin_dir_path( __FILE__ ) . '/classes/class-plugin-links.php' );

		// setup plugin links
		$plugin_links = new CPF_Plugin_Links();
		$plugin_links->setup();
	}

	/**
	 * Setup the frontend hooks
	 *
	 * @return void
	 */
	public function frontend_hooks() {
		// Don't run in admin or if the admin bar isn't showing
		if ( is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// CPF actions and filers
		add_action( 'wp_head', array( $this, 'print_css' ) );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 1000 );

	}

	/**
	 * Add the admin bar menu
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar;

		// Add top menu
		$wp_admin_bar->add_menu( array(
			'id'     => 'cpf-bar',
			'parent' => 'top-secondary',
			'title'  => __( 'Camp Pacific', 'camp-pacific-functions' ),
			'href'   => false
		) );

		// Add powered by
		$wp_admin_bar->add_menu( array(
			'id'     => 'cpf-bar-powered-by',
			'parent' => 'cpf-bar',
			'title'  => 'Powered by Camp Pacific WP Development',
			'class'	 => '',
			'href'   => 'https://camppacific.com',
			'target' => '_blank'
		) );

	}

	/**
	 * Print the custom CSS
	 */
	public function print_css() {
		echo "<style type=\"text/css\" media=\"screen\"> #wp-admin-bar-cpf-bar >
      .ab-item { padding-right: 26px !important;
      background: url('" . plugins_url( 'assets/images/camp-pacific-icon.svg', __FILE__ ) . "') center right no-repeat !important;
      background-size: contain !important; }
      #wp-admin-bar-cpf-bar.hover >
      .ab-item { background-color: #32373c !important; } #wp-admin-bar-cpf-bar #wp-admin-bar-cpf-bar-template-file .ab-item, #wp-admin-bar-cpf-bar #wp-admin-bar-cpf-bar-template-parts { text-align: right; } #wp-admin-bar-cpf-bar-template-parts.menupop >
      .ab-item: before { right: auto !important; }
      #wp-admin-bar-cpf-bar-powered-by
      { text-align: right; } #wp-admin-bar-cpf-bar-powered-by a { color:#c6ff00 !important; } </style>\n";
	}

	// end admin bar banner code

} // end Class CampPacificFunctions

/**
 * Camp Pacific Functions main function
 */
function __camp_pacific_functions_main() {
	new CampPacificFunctions();
}

// Init plugin
add_action( 'plugins_loaded', '__camp_pacific_functions_main' );

// Register hook
register_activation_hook( __FILE__, array( 'CampPacificFunctions', 'plugin_activation' ) );
