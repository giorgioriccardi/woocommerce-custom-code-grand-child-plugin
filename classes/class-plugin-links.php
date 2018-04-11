<?php

class GRCF_Plugin_Links {

	/**
	 * Setup class
	 */
	public function setup() {
		add_filter( 'plugin_action_links_grc-functions/grc-functions.php', array( $this, 'add_links' ) );
	}

	/**
	 * Add to links
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	public function add_links( $links ) {
		// array_unshift( $links, '<a href="https://giorgioriccardi.com" target="_blank" style="color:#c6ff00;font-weight:bold;">' . __( 'GRC Website', 'grc-functions' ) . '</a>' );

		// array_unshift( $links, '<img class="" alt="" id="" height="24" width="24" style="position: relative; top: 7px; " src="' . esc_url( plugins_url( 'assets/images/grc-icon.svg', dirname(__FILE__) ) ) . '">');

		array_unshift( $links, '<img alt="GRC Logo" style="position: relative; top: 7px; height: 24px; width: 24px; " src="' . esc_url( plugins_url( 'assets/images/grc-icon.svg', dirname(__FILE__) ) ) . '">');
		return $links;
	}

}
