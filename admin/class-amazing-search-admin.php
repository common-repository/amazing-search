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
 * @author     CodeRockz
 */


if( !class_exists( 'Amazing_Search_Admin' ) ) {

	class Amazing_Search_Admin {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */

		public $settings;

		public $search_settings;

		public $callbacks_mngr;

		private $api;

		private $helper;

		private $database_query;

		public $credentials;

		public $associate;

		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version = $version;

			require_once AMAZING_SEARCH_PLUGIN_DIR . 'admin/includes/class-amazing-search-callbackmanager.php';
			$this->callbacks_mngr = new Amazing_Search_Manager_Callbacks();

			require_once AMAZING_SEARCH_PLUGIN_DIR . 'admin/includes/class-amazing-search-settingapi.php';
			$this->settings = new Amazing_Search_Settings_Api();

			require_once AMAZING_SEARCH_PLUGIN_DIR . 'admin/includes/class-amazing-search-api.php';
			$this->api = new Amazing_Search_Api();
			$this->api->set_credentials();

    		require_once AMAZING_SEARCH_PLUGIN_DIR . 'includes/class-amazing-search-helper.php';
    		$this->helper = new Amazing_Search_Helper();

			$this->credentials = array (
				'access_key_id' => 'Access Key ID',
				'secret_access_key' => 'Secret Access Key'
			);

			$this->associate = array (
				'associate_country' => 'Country',
				'tracking_id' => 'Tracking ID',
				'onelink_ad_instance_id' => 'OneLink Ad Instance ID'
			);

			$this->setting = array (
				'button_text' => 'Small Button Text',
				'button_color' => 'Button Color',
				'button_text_color' => 'Button Text Color',
				'new_tab_open' => 'Open link in new tab',
				'nofollow_attribute' => 'Link has nofollow attribute',
				'show_review' => 'Showing review quantity',
				'show_rating' => 'Showing star rating',
				'show_sale_label' => 'Showing sale label( If Available )',
				'custom_css' => 'Custom CSS'
			);

			$this->set_settings();
			$this->set_sections();
			$this->set_fields();

		}

		/**
		 * Register the stylesheets for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_styles() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Amazing_Search_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Amazing_Search_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/amazing-search-admin.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'farbtastic' );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    1.0.0
		 */
		public function enqueue_scripts() {

			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in Amazing_Search_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The Amazing_Search_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_script( 'farbtastic' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/amazing-search-admin.js', array( 'farbtastic', 'jquery' ), $this->version, true );

			$amazing_search_nonce = wp_create_nonce('amazing_search_nonce');
	        wp_localize_script($this->plugin_name, 'amazing_search_ajax_obj', array(
	            'amazing_search_ajax_url' => admin_url('admin-ajax.php'),
	            'nonce' => $amazing_search_nonce,
	        ));

        	wp_enqueue_script($this->plugin_name);

        	# code editor for custom css input field of settings tab
        	wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			
		}


		public function amazing_search_menus_sections() {

	        add_menu_page(
				__('Amazing Search', 'amazing-search'),
	            __('Amazing Search', 'amazing-search'),
				'manage_options',
				'amazing-search-settings',
				array($this, "amazing_search_main_layout"),
				'dashicons-search',
				null
			);

	    }

	    public function amazing_search_main_layout() {
	        include_once AMAZING_SEARCH_PLUGIN_DIR . '/admin/partials/amazing-search-admin-layout.php';
	    }

	    public function settings_link( $links ) {

	    	if ( array_key_exists( 'deactivate', $links ) ) {
				$links['deactivate'] = str_replace( '<a', '<a class="amazing-search-deactivate-link"', $links['deactivate'] );
			}

	        $links[] = '<a href="admin.php?page=amazing-search-settings">Settings</a>';
	        return $links;
	    }


		public function set_sections()
		{
			$args = array(
				
				array(
					'id' => 'amazing_search_credential_tab_section',
					'title' =>  __( 'Credential Details', 'amazing-search' ),
					'callback' => array( $this->callbacks_mngr, 'credential_tab_helper_text' ),
					'page' => 'amazing_search_credential_tab'
				),
				array(
					'id' => 'amazing_search_associate_tab_section',
					'title' => __( 'Amazon Associate Details', 'amazing-search' ),
					'callback' => array( $this->callbacks_mngr, 'associate_tab_helper_text' ),
					'page' => 'amazing_search_associate_tab'
				),
				array(
					'id' => 'amazing_search_settings_tab_section',
					'title' => __( 'Amazing Search Settings', 'amazing-search' ),
					'page' => 'amazing_search_settings_tab'
				)
			);

			$this->settings->amazing_search_set_sections( $args );
		}

		public function set_fields()
		{
			$args = array();

			foreach ( $this->credentials as $key => $value ) {

				$callback = 'amazing_search_input_field';
				$class='amsearch-key-secret';

				$args[] = array(
					'id' => $key,
					'title' => $value,
					'callback' => array( $this->callbacks_mngr, $callback ),
					'page' => 'amazing_search_credential_tab',
					'section' => 'amazing_search_credential_tab_section',
					'args' => array(
						'option_name' => 'amazing_search_credential_tab_option',
						'label_for' => $key,
						'placeholder' => $value,
						'class' => $class
					)

				);

			}

			foreach ( $this->associate as $key => $value ) { 

				if ($key == 'associate_country') {
					$callback = 'amazing_search_county_select_field';
				} elseif($key == 'tracking_id' || $key == 'onelink_ad_instance_id') {
					$callback = 'amazing_search_input_field';
				}

				$args[] = array(
					'id' => $key,
					'title' => $value,
					'callback' => array( $this->callbacks_mngr, $callback ),
					'page' => 'amazing_search_associate_tab',
					'section' => 'amazing_search_associate_tab_section',
					'args' => array(
						'option_name' => 'amazing_search_associate_tab_option',
						'label_for' => $key,
						'placeholder' => $value
					)

				);
			}

			foreach ( $this->setting as $key => $value ) {

				if ($key == 'button_text') {
					$callback = 'amazing_search_input_field';
					$class='';
				} elseif($key == 'button_color' || $key == 'button_text_color') {
					$callback = 'amazing_search_colorpicker_field';
					$class='';
				} elseif($key == 'show_review' || $key == 'show_rating' || $key == 'new_tab_open' || $key == 'nofollow_attribute'|| $key == 'show_sale_label') {
					$callback = 'amazing_search_checkbox_field';
					$class='ui-toggle';
				} elseif($key == 'custom_css') {
					$callback = 'amazing_search_textarea_field';
					$class='';
				}

				$args[] = array(
					'id' => $key,
					'title' => $value,
					'callback' => array( $this->callbacks_mngr, $callback ),
					'page' => 'amazing_search_settings_tab',
					'section' => 'amazing_search_settings_tab_section',
					'args' => array(
						'option_name' => 'amazing_search_settings_tab_option',
						'label_for' => $key,
						'placeholder' => $value,
						'class' => $class
					)
				);
			}

			$this->settings->amazing_search_set_fields( $args );
		}


		public function set_settings()
		{
			$args = array();

			foreach( $this->credentials as $key => $value ) {
				$args[] = array(
							'option_group' => 'amazing_search_credential_tab_section',
							'option_name' => 'amazing_search_credential_tab_option',
							'callback' => array( $this->callbacks_mngr, 'input_field_sanitization' )
						);
			}	

			foreach( $this->associate as $key => $value ) {
				if ($key == 'associate_country') {
					$sanitize_callback = 'select_field_sanitization';
				} elseif($key == 'tracking_id' || $key == 'onelink_ad_instance_id') {
					$sanitize_callback = 'input_field_sanitization';
				}

				$args[] = array(
							'option_group' => 'amazing_search_associate_tab_section',
							'option_name' => 'amazing_search_associate_tab_option',
							'callback' => array( $this->callbacks_mngr, $sanitize_callback )
						);

			}

			foreach( $this->setting as $key => $value ) { 

				if ($key == 'button_text' || $key == 'button_color' || $key == 'button_text_color') {
					$sanitize_callback = 'input_field_sanitization';
				} elseif($key == 'show_review' || $key == 'show_rating' || $key == 'new_tab_open' || $key == 'nofollow_attribute' || $key == 'show_sale_label' ) {
					$sanitize_callback = 'checkbox_field_sanitization';
				} elseif($key == 'custom_css') {
					$sanitize_callback = 'textarea_field_sanitization';
				}

				$args[] = array(
					'option_group' => 'amazing_search_settings_tab_section',
					'option_name' => 'amazing_search_settings_tab_option',
					'callback' => array( $this->callbacks_mngr, $sanitize_callback )
				);
			}

			$this->settings->amazing_search_set_settings( $args );
		}

		# increase auto update time for gutenberg editor
		public function amazing_search_block_editor_settings( $editor_settings, $post ) {
			
			$editor_settings['autosaveInterval'] = 86400; //number of second [default value is 10]
			return $editor_settings;
		}

		public function amazing_search_review_notice() {
		    $options = get_option('amazing_search_review_notice');

		    $activation_time = get_option('amazing-search-activation-time');

		    $notice = '<div class="amazing-search-review-notice notice notice-success is-dismissible">';
	        $notice .= '<img class="amazing-search-review-notice-left" src="'.AMAZING_SEARCH_PLUGIN_URL.'admin/images/amazing-search-logo.png" alt="amazing-search">';
	        $notice .= '<div class="amazing-search-review-notice-right">';
	        $notice .= '<p><b>We have worked relentlessly to develop the plugin and it would really appreciate us if you dropped a short review about the plugin. Your review means a lot to us and we are working to make the plugin more awesome. Thanks for using Amazing Search.</b></p>';
	        $notice .= '<ul>';
	        $notice .= '<li><a val="later" href="#">Remind me later</a></li>';
	        $notice .= '<li><a class="amazing-search-review-request-btn" style="font-weight:bold;" val="given" href="#" target="_blank">Review Here</a></li>';
    		$notice .= '<li><a val="never" href="#">I would not</a></li>';	        
	        $notice .= '</ul>';
	        $notice .= '</div>';
	        $notice .= '</div>';
	        
		    if(!$options && time()>= $activation_time + (60*60*24*15)){
		        echo $notice;
		    } else if(is_array($options)) {
		        if((!array_key_exists('review_notice',$options)) || ($options['review_notice'] =='later' && time()>=($options['updated_at'] + (60*60*24*30) ))){
		            echo $notice;
		        }
		    }
		}

		public function amazing_search_save_review_notice() {
		    $notice = sanitize_text_field($_POST['notice']);
		    $value = array();
		    $value['review_notice'] = $notice;
		    $value['updated_at'] = time();

		    update_option('amazing_search_review_notice',$value);
		    wp_send_json_success($value);
		}

		public function amazing_search_get_deactivate_reasons() {

			$reasons = array(
				array(
					'id'          => 'could-not-understand',
					'text'        => 'I couldn\'t understand how to make it work',
					'type'        => 'textarea',
					'placeholder' => 'Would you like us to assist you?'
				),
				array(
					'id'          => 'found-better-plugin',
					'text'        => 'I found a better plugin',
					'type'        => 'text',
					'placeholder' => 'Which plugin?'
				),
				array(
					'id'          => 'not-have-that-feature',
					'text'        => 'I need specific feature that you don\'t support',
					'type'        => 'textarea',
					'placeholder' => 'Could you tell us more about that feature?'
				),
				array(
					'id'          => 'is-not-working',
					'text'        => 'The plugin is not working',
					'type'        => 'textarea',
					'placeholder' => 'Could you tell us a bit more whats not working?'
				),
				array(
					'id'          => 'temporary-deactivation',
					'text'        => 'It\'s a temporary deactivation',
					'type'        => '',
					'placeholder' => ''
				),
				array(
					'id'          => 'other',
					'text'        => 'Other',
					'type'        => 'textarea',
					'placeholder' => 'Could you tell us a bit more?'
				),
			);

			return $reasons;
		}

		public function amazing_search_deactivate_reason_submission(){
			check_ajax_referer('amazing_search_nonce');
			global $wpdb;

			if ( ! isset( $_POST['reason_id'] ) ) { // WPCS: CSRF ok, Input var ok.
				wp_send_json_error();
			}

			$current_user = new WP_User(get_current_user_id());

			$data = array(
				'reason_id'     => sanitize_text_field( $_POST['reason_id'] ), // WPCS: CSRF ok, Input var ok.
				'plugin'        => "Amazing Search Free",
				'url'           => home_url(),
				'user_email'    => $current_user->data->user_email,
				'user_name'     => $current_user->data->display_name,
				'reason_info'   => isset( $_REQUEST['reason_info'] ) ? trim( stripslashes( $_REQUEST['reason_info'] ) ) : '',
				'software'      => $_SERVER['SERVER_SOFTWARE'],
				'date'			=> time(),
				'php_version'   => phpversion(),
				'mysql_version' => $wpdb->db_version(),
				'wp_version'    => get_bloginfo( 'version' ),
				'date'			=> time(),
			);


			$this->amazing_search_deactivate_send_request( $data);
			wp_send_json_success();

		}

		public function amazing_search_deactivate_send_request( $params) {
			$api_url = "https://coderockz.com/wp-json/coderockz-api/v1/deactivation-reason";
			return  wp_remote_post($api_url, array(
					'method'      => 'POST',
					'timeout'     => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => false,
					'headers'     => array( 'user-agent' => 'AmazingSearch/' . md5( esc_url( home_url() ) ) . ';' ),
					'body'        => $params,
					'cookies'     => array()
				)
			);
		}

		function amazing_search_deactivate_scripts() {

			global $pagenow;

			if ( 'plugins.php' != $pagenow ) {
				return;
			}

			$reasons = $this->amazing_search_get_deactivate_reasons();
			?>
			<!--pop up modal-->
			<div class="amazing_search_deactive_plugin-modal" id="amazing_search_deactive_plugin-modal">
				<div class="amazing_search_deactive_plugin-modal-wrap">
					<div class="amazing_search_deactive_plugin-modal-header">
						<h2 style="margin:0;"><span class="dashicons dashicons-testimonial"></span><?php _e( ' QUICK FEEDBACK' ); ?></h2>
					</div>

					<div class="amazing_search_deactive_plugin-modal-body">
						<p style="font-size:15px;font-weight:bold"><?php _e( 'If you have a moment, please share why you are deactivating Amazing Search', 'amazing-search' ); ?></p>
						<ul class="reasons">
							<?php foreach ($reasons as $reason) { ?>
								<li data-type="<?php echo esc_attr( $reason['type'] ); ?>" data-placeholder="<?php echo esc_attr( $reason['placeholder'] ); ?>">
									<label><input type="radio" name="selected-reason" value="<?php echo $reason['id']; ?>"> <?php echo $reason['text']; ?></label>
								</li>
							<?php } ?>
						</ul>
					</div>

					<div class="amazing_search_deactive_plugin-modal-footer">
						<a href="#" class="amazing-search-skip-deactivate"><?php _e( 'Skip & Deactivate', 'amazing-search' ); ?></a>
						<div style="float:left">
						<button class="amazing-search-deactivate-button button-primary"><?php _e( 'Submit & Deactivate', 'amazing-search' ); ?></button>
						<button class="amazing-search-cancel-button button-secondary"><?php _e( 'Cancel', 'amazing-search' ); ?></button>
						</div>
					</div>
				</div>
			</div>

			<?php
		}

	}
}