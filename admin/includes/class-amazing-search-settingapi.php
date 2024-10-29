<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/admin
 * @author    CodeRockz
 */


if( !class_exists( 'Amazing_Search_Settings_Api' ) ) {

	class Amazing_Search_Settings_Api {

		
		public $settings = array();

		public $sections = array();

		public $fields = array();


		public function __construct() {
			add_action( 'admin_init', array( $this, 'amazing_search_register_custom_fields' ) );
		}

		public function amazing_search_set_settings( array $settings )
		{
			$this->settings = $settings;

			return $this;
		}

		public function amazing_search_set_sections( array $sections )
		{
			$this->sections = $sections;

			return $this;
		}

		public function amazing_search_set_fields( array $fields )
		{
			$this->fields = $fields;

			return $this;
		}

		public function amazing_search_register_custom_fields()
		{
			// register setting
			foreach ( $this->settings as $setting ) {
				register_setting( $setting["option_group"], $setting["option_name"], ( isset( $setting["callback"] ) ? $setting["callback"] : '' ) );
			}

			// add settings section
			foreach ( $this->sections as $section ) {
				add_settings_section( $section["id"], $section["title"], ( isset( $section["callback"] ) ? $section["callback"] : '' ), $section["page"] );
			}

			// add settings field
			foreach ( $this->fields as $field ) {
				add_settings_field( $field["id"], $field["title"], ( isset( $field["callback"] ) ? $field["callback"] : '' ), $field["page"], $field["section"], ( isset( $field["args"] ) ? $field["args"] : '' ) );
			}
		}

	}

}