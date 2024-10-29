<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Amazing_Search
 * @subpackage Amazing_Search/public
 * @author     CodeRockz
 */

if( !class_exists( 'Amazing_Search_Public' ) ) {

	class Amazing_Search_Public {

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
		 * @param      string    $plugin_name       The name of the plugin.
		 * @param      string    $version    The version of this plugin.
		 */

		public $amazing_search_shortcode;

		private $helper;

		private $api;

		private $database_query;

		private $settings;

		private $new_tab;

		private $no_follow;

		private $show_review;

		private $show_rating;
		
		private $show_sale_label;

		private $button_text;

		private $button_color;

		private $button_text_color;

		private $credential;

		private $associate;

		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version = $version;

			require_once AMAZING_SEARCH_PLUGIN_DIR . 'includes/class-amazing-search-helper.php';
    		$this->helper = new Amazing_Search_Helper();

    		require_once AMAZING_SEARCH_PLUGIN_DIR . 'admin/includes/class-amazing-search-api.php';
			$this->api = new Amazing_Search_Api();
			$this->api->set_credentials();

    		$this->settings = (array)get_option('amazing_search_settings_tab_option');
    		$this->associate = (array)get_option('amazing_search_associate_tab_option');
    		$this->credential = (array)get_option('amazing_search_credential_tab_option');

            $this->button_text = "SEE DETAILS";
            $this->button_color = "#222f3d";
            $this->button_text_color = "#ffffff";
            $this->no_key = false;
            $this->show_categories = false;
            $this->show_page = false;
            $this->show_condition = false;
            $this->show_maximum_price = false;
            $this->show_minimum_price = false;
            $this->show_sort = false;

            if( $this->settings != false ) {
                if(array_key_exists('new_tab_open', $this->settings)) {
                	$this->settings['new_tab_open'] ? $this->new_tab = '_blank' : $this->new_tab = '';
                } 
                if(array_key_exists('nofollow_attribute', $this->settings)) {
                	$this->settings['nofollow_attribute'] ? $this->no_follow = 'nofollow' : $this->no_follow = '';
                }
                if(array_key_exists('show_review', $this->settings)) {
                	$this->settings['show_review'] ? $this->show_review = true : $this->show_review = false;
                }
                if(array_key_exists('show_rating', $this->settings)) {
                	$this->settings['show_rating'] ? $this->show_rating = true : $this->show_rating = false;
                }

                if(array_key_exists('show_sale_label', $this->settings)) {
                	$this->settings['show_sale_label'] ? $this->show_sale_label = true : $this->show_sale_label = false;
                }
                
                if(array_key_exists('button_text', $this->settings)) {
                	$this->button_text = sanitize_text_field($this->settings['button_text']);
                }
                
                if(array_key_exists('button_color', $this->settings)) {
                	$this->button_color = sanitize_text_field($this->settings['button_color']);
                }
                
                if(array_key_exists('button_text_color', $this->settings)) {
                	$this->button_text_color = sanitize_text_field($this->settings['button_text_color']);
                }
                 
            }

		}

		/**
		 * Register the stylesheets for the public-facing side of the site.
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

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/amazing-search-public.css', array(), $this->version, 'all' );

		}

		/**
		 * Register the JavaScript for the public-facing side of the site.
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


			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/amazing-search-public.js', array( 'jquery' ), $this->version, true );

			$amazing_search_nonce = wp_create_nonce('amazing_search_nonce');
	        wp_localize_script($this->plugin_name, 'amazing_search_ajax_obj', array(
	            'amazing_search_ajax_url' => admin_url('admin-ajax.php'),
	            'nonce' => $amazing_search_nonce,
	        ));

        	wp_enqueue_script($this->plugin_name);

		}

		public function amazing_search_register_shortcodes() {
			add_shortcode( 'amazing_search', array( $this, 'amazing_search_shortcode_display') );
		}

		public function amazing_search_shortcode_display($atts) {
		    if(array_key_exists('associate_country', $this->associate)) {
		    	$country = $this->associate['associate_country'];
		    } else {
		    	$country = 'com';
		    }
		    
		    $default = array(
		        "width" => null		        
		    );
		    $shortcode_data = shortcode_atts($default, $atts);
		    $content_search = 
		      '<div class="amsearch-search-field">
			      <form>
			        <div class="amsearch-inner-form">
			          <div class="input-field first-wrap"> 
			            <input id="amsearch-content-search" type="text" placeholder="Keyword" required/>
			          </div>
			          <div class="input-field second-wrap">
			            <button class="amsearch-content-btn-search" type="button">
			            	<img src="'.AMAZING_SEARCH_PLUGIN_URL.'public/images/search.png" alt="search.png">
			            </button>
			          </div>
			        </div>
			        <img class="amazing-search-loading" src="'.AMAZING_SEARCH_PLUGIN_URL.'public/images/loading.gif" alt="Loading">
			        </form>
			      <div class="content-error-notice"><p></p></div>
			      <div class="amsearch-content-search-result-block">
			      <img class="amazing-search-item-loading" src="'.AMAZING_SEARCH_PLUGIN_URL.'public/images/loading.gif" alt="Loading">
			      <div class="amsearch-content-search-result"></div>
			      </div>
			   </div>';

			return $content_search;

		}


		public function amazing_search_get_content_search_items() {
			check_ajax_referer('amazing_search_nonce');
			$keyword = sanitize_text_field($_POST['keyword']);
			$keyword = str_replace(' ','_',$keyword);

			$this->api->set_credentials();
			$items = $this->api->item_search($keyword);
			if(is_array($items)) {
				$list = '';
				$list .= '<div class="amazing-search-hr-list-wrapper">'; 
			    foreach($items as $item) {

					if(isset($item['SmallImage']))
							$images = $this->helper->array_push_assoc($images, 'SmallImage', $item['SmallImage']);
					if(isset($item['MediumImage']))
						$images = $this->helper->array_push_assoc($images, 'MediumImage', $item['MediumImage']);
					if(isset($item['LargeImage']))
						$images = $this->helper->array_push_assoc($images, 'LargeImage', $item['LargeImage']);
					if(isset($item['ImageSets']['ImageSet'])){
	                	$image_set = $item['ImageSets']['ImageSet'];
	                }
		                				
					$image_url = $this->helper->image_grabber($images,$image_set);
					$sale_label = $this->helper->sale_item_finder($item);
					$list .= '<div class="amazing-search-hr-list-item">';
					if($this->show_sale_label) { 
						if(!is_null($sale_label)) {
							$list .= '<p class="amazing-search-sale-text">Sale</p>';
						}
					}
			    	
			    	$list .= '<div class="amazing-search-hr-list-item-image"><img src="'.sanitize_text_field(urldecode($image_url)).'" alt="'.sanitize_text_field($item['ItemAttributes']['Title']).'"/></div>';		    	
			    	$list .='<div class="amazing-search-hr-list-item-title"><a href="'.urldecode($item['DetailPageURL']).'" rel="'.$this->no_follow.'" target="'.$this->new_tab.'">'.sanitize_text_field($item['ItemAttributes']['Title']).'</a></div>';
			    	$list .= '<div class="hr"></div>';
			    	$list .='<ul class="amazing-search-hr-list-item-features">';

			    	$review_rating = $this->helper->reviews_n_rating($item['ASIN']);

			    	if(!is_null($review_rating)) {
	                	if(array_key_exists('review', $review_rating)) {
	                		$review = $review_rating['review'];
	                	}
	                	if(array_key_exists('rating', $review_rating)) {
	                		$rating = $review_rating['rating'];
	                	}
	                }
			    	
			    	if($this->show_review) {
						(!is_null($review) ? $list .='<li>'.sanitize_text_field($review).' Reviews</li>' : $list .= '<li>No Review</li>');
						$list .= '<div class="hr"></div>';
					}
			    	
					if($this->show_rating) {
						(!is_null($rating) ? $list .='<li><div class="amazing-search-al-stars">'.sanitize_text_field($rating).'</div><span class="amazing-search-rating-text">'.sanitize_text_field($rating).' out of 5 stars</span></li>' : '');
			    		$list .= '<div class="hr"></div>';
					}

					$price = $this->helper->price_grabber_database($item);

			    	if(sanitize_text_field($price) == 'Unavailable'){
			    		$list .='<li class="amazing-search-hr-list-price-block" style="color:#B12704!important;"><section class="amazing-search-verticaly-middle">'.sanitize_text_field($price).'</section></li>';
			    	} elseif(is_null($price)) {
			    		$list .='<li class="amazing-search-hr-list-price-block" style="color:#B12704!important;"><section class="amazing-search-verticaly-middle">Unavailable</section></li>';
			    	} else {

			    		if(strpos($price, '-')!==false) { 

			    			$list .='<li class="amazing-search-hr-list-price-block"><section class="amazing-search-verticaly-middle">';
			    				$list .= '<div class="amazing-search-single-price-block">';
			    				$list .= '<span>'.sanitize_text_field($price) . '</span>';
					    		$list .= '</div>';
			    			
			    			$list .='</section></li>';

			    		} else {

			    			$reg_price = $this->helper->regular_price_for_sale_item_database($item);
			    			$eligible_prime = $this->helper->prime_eligible_grabber_database($item);

			    			if(!is_null($reg_price)) {
			    				$list .='<li class="amazing-search-hr-list-price-block"><section class="amazing-search-verticaly-middle"><span style="text-decoration: line-through;color:#ccb3c1!important">'.sanitize_text_field($reg_price).'</span>&nbsp;';
			    			} else {
			    				$list .='<li class="amazing-search-hr-list-price-block"><section class="amazing-search-verticaly-middle">';
			    			}

			    			$list .= '<span>'.sanitize_text_field($price).'</span>';
				    		if(sanitize_text_field($eligible_prime) == '1') {
				    			$list .='&nbsp;<img src="'.AMAZING_SEARCH_PLUGIN_URL.'/public/images/prime-logo.png" class="amazing-search-prime-logo" alt="prime-logo">';
				    		}
				    		$list .='</section></li>';
			    		}
			    		
			    	}

			    	$list .= '<div class="hr"></div>';
			    	
			    	$list .= '</ul>';
			    	$list .= '<div class="amazing-search-hr-list-item-select"><a style="background:'.$this->button_color.'!important;color:'.$this->button_text_color.'!important;" href="'.urldecode($item['DetailPageURL']).'" rel="'.$this->no_follow.'" target="'.$this->new_tab.'">'.$this->button_text.'</a></div>';
			    	$list .= '</div>';
		
				    
				}
				
				$list .= '<div style="clear: both;"></div>';			
				$list .= '</div>';

				$data=array(
		            "success"=>true,
		            "list"=>$list,
		            "error" => "No Error"
		        );
			} else {
				$data=array(
		            "success"=>true,
		            "list"=>$items,
		            "error"=>"Error",
		        );
			}						
			
	        wp_send_json_success($data);
		}

		public function load_onelink_script() {
			$options = (array)get_option( 'amazing_search_associate_tab_option' );
			if($options !=false && array_key_exists('onelink_ad_instance_id', $options) && !isset($options['onelink_ad_instance_id'])) {
				$onelink_ad_instance_id = sanitize_text_field($options['onelink_ad_instance_id']);
				echo '<div id="amzn-assoc-ad-'.$onelink_ad_instance_id.'"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId='.$onelink_ad_instance_id.'"></script>';
			}
		}

		public function load_custom_css() {
			$options = (array)get_option( 'amazing_search_settings_tab_option' );
			if($options !=false && array_key_exists('custom_css', $options)) { 
				$custom_css = wp_unslash($options['custom_css']);
				echo '<style>' . $custom_css . '</style>';
			}
		}

	}

}