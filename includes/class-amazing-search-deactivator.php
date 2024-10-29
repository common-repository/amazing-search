<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Amazing_Search
 * @subpackage Amazing_Search/includes
 * @author     CodeRockz
 */


if( !class_exists( 'Amazing_Search_Deactivator' ) ) {

	class Amazing_Search_Deactivator {

		
		public function deactivate() {
			$htaccess = get_home_path().'.htaccess';
			insert_with_markers($htaccess,'increase max execution time','');
		}	

	}

}