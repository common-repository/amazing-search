<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Amazing_Search
 * @subpackage Amazing_Search/includes
 * @author     CodeRockz
 */

if( !class_exists( 'Amazing_Search_i18n' ) ) {

	class Amazing_Search_i18n {


		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {

			load_plugin_textdomain(
				'amazing-search',
				false,
				dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
			);

		}

	}

}