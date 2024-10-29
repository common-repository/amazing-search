<?php
/**
 * Fired during plugin activation
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Amazing_Search
 * @subpackage Amazing_Search/includes
 * @author     CodeRockz
 */

if( !class_exists( 'Amazing_Search_Activator' ) ) {

	class Amazing_Search_Activator {

		public function __construct() {

	    }

		public function activate() {

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

$max_execution = "<IfModule mod_php5.c>
	php_value max_execution_time 300
</IfModule>";

			$htaccess = get_home_path().'.htaccess';
			if(file_exists( $htaccess )) {
				if(!strpos($htaccess,$max_execution)){
					insert_with_markers($htaccess,'increase max execution time',$max_execution);
				}
			}

			update_option('amazing-search-activation-time',time());

		}

	}

}