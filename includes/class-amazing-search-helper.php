<?php

if( !class_exists( 'amazing_search_Helper' ) ) {

	class amazing_search_Helper {

		private $database_query;
		private $associate;

		/**
		 * Grab all attributes for a given shortcode in a post content
		 *
		 * @uses get_shortcode_regex()
		 * @uses shortcode_parse_atts()
		 * @param  string $tag   Shortcode tag
		 * @param  string $post_content  Text containing shortcodes
		 * @return array  $out   Array of attributes
		 */

		public function shortcode_get_all_attributes( $tag, $post_content )
		{
		    preg_match_all( '/' . get_shortcode_regex() . '/s', $post_content, $matches );
		    $out = array();
		    if( isset( $matches[2] ) )
		    {
		        foreach( (array) $matches[2] as $key => $value )
		        {
		            if( $tag === $value )
		                $out[] = shortcode_parse_atts( $matches[3][$key] );  
		        }
		    }
		    return $out;
		}

		public function identical_values( $arrayA , $arrayB ) {

		    sort( $arrayA );
		    sort( $arrayB );

		    return $arrayA == $arrayB;
		}

		public function site_url() {
		    if(isset($_SERVER['HTTPS'])){
		        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		    }
		    else{
		        $protocol = "http";
		    }
		    return filter_var($protocol . "://" . $_SERVER['HTTP_HOST'],
                FILTER_SANITIZE_URL);
		}

		public function array_push_assoc($array, $key, $value){
			$array[$key] = $value;
			return $array;
		}

		public function is_associative_array(array $array) {
            return array_keys($array) !== range(0, count($array) - 1);
        }

        public function get_string_between($string, $start, $end){
            $string = ' ' . $string;
            $ini = strpos($string, $start);
            if ($ini == 0) return '';
            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;
            return substr($string, $ini, $len);
        }

		public function reviews_n_rating($asin) {
			
			$associate = (array)get_option('amazing_search_associate_tab_option');

			if( !empty($associate) ) {
                array_key_exists('associate_country', $associate) ? $country = $associate['associate_country'] : $country = 'com';
            }

            switch ($country) {
			    case "com":
			        $url = "https://www.amazon.com/product-reviews/".$asin;
			        break;
			    case "co.uk":
			        $url = "https://www.amazon.co.uk/product-reviews/".$asin;
			        break;
			    case "ca":
			        $url = "https://www.amazon.ca/product-reviews/".$asin;
			        break;
			    case "es":
			        $url = "https://www.amazon.es/product-reviews/".$asin;
			        break;
			    case "fr":
			        $url = "https://www.amazon.fr/product-reviews/".$asin;
			        break;
			    case "it":
			        $url = "https://www.amazon.it/product-reviews/".$asin;
			        break;
			    case "de":
			        $url = "https://www.amazon.de/product-reviews/".$asin;
			        break;
			    case "com.mx":
			        $url = "https://www.amazon.com.mx/product-reviews/".$asin;
			        break;
			    case "cn":
			        $url = "https://www.amazon.cn/product-reviews/".$asin;
			        break;
			    case "co.jp":
			        $url = "https://www.amazon.co.jp/product-reviews/".$asin;
			        break;
			    case "com.br":
			        $url = "https://www.amazon.com.br/product-reviews/".$asin;
			        break;
			    case "in":
			        $url = "https://www.amazon.in/product-reviews/".$asin;
			        break;
		        case "in":
		        	$url = "https://www.amazon.com.au/product-reviews/".$asin;
		        	break;
			    default:
			        $url = "https://www.amazon.com/product-reviews/".$asin;
			}

			/*$html = AmazingSearch\Htmldom\file_get_html($url);
			$item_review = $html->find('.totalReviewCount',0)->plaintext;
    		$item_rating = $html->find('.arp-rating-out-of-text',0)->plaintext;*/

    		$response = wp_remote_get( $url );
            $item_rating = $this->get_string_between($response['body'],'<span data-hook="rating-out-of-text" class="arp-rating-out-of-text">','</span>');

            $item_review = $this->get_string_between($response['body'],'<span data-hook="total-review-count" class="a-size-medium totalReviewCount">','</span>');

    		$review_rating = [];
    		if($item_review)
    		   $review_rating = $this->array_push_assoc($review_rating, 'review', $item_review);
    		if($item_rating) {

				if (preg_match_all('/\-?\d+\.\d+/', $item_rating, $rating) > 0) {
			        $item_rating = $rating[0][0];
			    }

				$review_rating = $this->array_push_assoc($review_rating, 'rating', $item_rating);
    		}

			/*$html->clear(); 
			unset($html);*/

    		return $review_rating;
		}

		public function amazing_search_array_sanitize($array) {
		    $newArray = array();
		    if (count($array)) {
		        foreach ($array as $key => $value) {
		            if (is_array($value)) {
		                foreach ($value as $key2 => $value2) {
		                    if (is_array($value2)) {
		                        foreach ($value2 as $key3 => $value3) {
		                            $newArray[$key][$key2][$key3] = sanitize_text_field($value3);
		                        }
		                    } else {
		                        $newArray[$key][$key2] = sanitize_text_field($value2);
		                    }
		                }
		            } else {
		                $newArray[$key] = sanitize_text_field($value);
		            }
		        }
		    }
		    return $newArray;
		}

		public function image_grabber($images,$image_set) {

			if(!empty($images)) {

				
				if(isset($images['LargeImage']['URL']))
					$image_url = $images['LargeImage']['URL'];
				elseif(isset($images['MediumImage']['URL']))
					$image_url = $images['MediumImage']['URL'];
				else
					$image_url = $images['SmallImage']['URL'];

			} else {

			    if(!empty($image_set)) {

			    	reset($image_set);
					$first_key = key($image_set);

			    	if($first_key == '0') {
			    		if (isset($image_set[0]['HiResImage']['URL'])) {
				    		$image_url = $image_set[0]['HiResImage']['URL'];
				    	} elseif(isset($image_set[0]['LargeImage']['URL'])) {
				    		$image_url = $image_set[0]['LargeImage']['URL'];
				    	} elseif(isset($image_set[0]['MediumImage']['URL'])){
				    		$image_url = $image_set[0]['MediumImage']['URL'];
				    	} elseif (isset($image_set[0]['TinyImage']['URL'])) {
				    		$image_url = $image_set[0]['TinyImage']['URL'];
				    	} elseif (isset($image_set[0]['SmallImage']['URL'])) {
				    		$image_url = $image_set[0]['SmallImage']['URL'];
				    	} elseif (isset($image_set[0]['ThumbnailImage']['URL'])) {
				    		$image_url = $image_set[0]['ThumbnailImage']['URL'];
				    	} elseif (isset($image_set[0]['SwatchImage']['URL'])) {
				    		$image_url = $image_set[0]['SwatchImage']['URL'];
				    	} else {
				    		$image_url = AMAZING_SEARCH_PLUGIN_URL.'public/images/unavailable.png';
				    	}
			    	} else {

				    	if (isset($image_set['HiResImage']['URL'])) {
				    		$image_url = $image_set['HiResImage']['URL'];
				    	} elseif(isset($image_set['LargeImage']['URL'])) {
				    		$image_url = $image_set['LargeImage']['URL'];
				    	} elseif(isset($image_set['MediumImage']['URL'])){
				    		$image_url = $image_set['MediumImage']['URL'];
				    	} elseif (isset($image_set['TinyImage']['URL'])) {
				    		$image_url = $image_set['TinyImage']['URL'];
				    	} elseif (isset($image_set['SmallImage']['URL'])) {
				    		$image_url = $image_set['SmallImage']['URL'];
				    	} elseif (isset($image_set['ThumbnailImage']['URL'])) {
				    		$image_url = $image_set['ThumbnailImage']['URL'];
				    	} elseif (isset($image_set['SwatchImage']['URL'])) {
				    		$image_url = $image_set['SwatchImage']['URL'];
				    	} else {
				    		$image_url = AMAZING_SEARCH_PLUGIN_URL.'public/images/unavailable.png';
				    	}
			    	}

		    	} else {
		    		$image_url = AMAZING_SEARCH_PLUGIN_URL.'public/images/unavailable.png';
		    	}
			}
			
			return $image_url;

		}

		public function price_grabber_database ($item) {
			
			if((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['ItemAttributes']['ListPrice']) && !isset($item['Variations']['Item']) && !isset($item['VariationSummary']) && (!isset($item['OfferSummary']) || $item['OfferSummary']['TotalNew'] == 0)) {
			    
			    return $price = 'Unavailable';

	    	} elseif(isset($item['Offers']['Offer']) && ($item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New' && strpos(strtolower($item['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false )) {
	    		
	    		if(isset($item['Offers']['Offer']['OfferListing']['SalePrice'])) {
	    			return $price = $item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'];
	    		} else {
	    			return $price = $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
	    		}

	    	} elseif(isset($item['Offers']['Offer']) && $item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

	    		if(isset($item['Offers']['Offer']['OfferListing']['SalePrice'])) {
	    			return $price = $item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'];
	    		} else {
	    			return $price = $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
	    		}

	    	} elseif(((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['ItemAttributes']['ListPrice'])) && isset($item['Variations']['Item'])) {

	    		
	    		if($item['Variations']['TotalVariations'] > 1) {
		    		$price = [];

		    		foreach ($item['Variations']['Item'] as $variation_item) {

		    			if(isset($variation_item['Offers']) && strpos(strtolower($variation_item['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false && $variation_item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			
			    			if(isset($variation_item['Offers']['Offer']['OfferListing']['SalePrice'])) {
			    				$variation_price = $variation_item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'];
			    				array_push($price, $variation_price);
			    			} else {
			    				$variation_price = $variation_item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
			    				array_push($price, $variation_price);
			    			}		
			    		
			    		} elseif (isset($variation_item['Offers']) && $variation_item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

			    			if(isset($variation_item['Offers']['Offer']['OfferListing']['SalePrice'])) {
			    				$variation_price = $variation_item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'];
			    				array_push($price, $variation_price);
			    			} else {
			    				$variation_price = $variation_item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
			    				array_push($price, $variation_price);
			    			}
			    		}

		    		}

		    		$price = min($price).'-'.max($price);

		    		return $price;
	    		
	    		} else {

	    			if(strpos(strtolower($item['Variations']['Item']['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false && $item['Variations']['Item']['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			
		    			if(isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice'])) {
		    				return $price = $item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'];
		    			} else {
		    				return $price = $item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
		    			}		
		    		
		    		} elseif ($item['Variations']['Item']['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			if(isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice'])) {
		    				return $price = $item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'];
		    			} else {
		    				return $price = $item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
		    			}
		    		}
	    		}

	    		

	    	} elseif(((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['ItemAttributes']['ListPrice'])) && isset($item['VariationSummary'])) {

	    		if(isset($item['VariationSummary']['LowestSalePrice'])) {
	    			return $price = $item['VariationSummary']['LowestSalePrice']['FormattedPrice'];
	    		} elseif (isset($item['VariationSummary']['LowestPrice'])) {
	    			return $price = $item['VariationSummary']['LowestPrice']['FormattedPrice'];
	    		}

	    	} elseif((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['VariationSummary']) && isset($item['OfferSummary']['LowestNewPrice']) && !isset($item['OfferSummary']['LowestSalePrice'])) {
	    		
	    		return $price = $item['OfferSummary']['LowestNewPrice']['FormattedPrice'];

	    	} elseif((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['VariationSummary']) && isset($item['OfferSummary']['LowestSalePrice']) && !isset($item['OfferSummary']['LowestNewPrice'])) {
	    		
	    		return $price = $item['OfferSummary']['LowestSalePrice']['FormattedPrice'];

	    	} else {
	    		
	    		return $price = $item['ItemAttributes']['ListPrice']['FormattedPrice'];
	    	}

		}

		public function regular_price_for_sale_item_database($item) {

			if(isset($item['Offers']['Offer']) && ($item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New' && strpos(strtolower($item['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false)) {
	    		
	    		if(isset($item['Offers']['Offer']['OfferListing']['AmountSaved'])) {

		    		if(isset($item['Offers']['Offer']['OfferListing']['SalePrice'])) {

		    			$position = strcspn( $item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'] , '0123456789' );

						$currency_symbol = substr($item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],0,$position);
						
						$sale_price = substr($item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],$position);
						$sale_price = (float)str_replace(',', '', $sale_price);

						$amount_save = substr($item['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
						$amount_save = (float)str_replace(',', '', $amount_save);

						$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

		    			return $regular_price_for_sale_item;

		    		} elseif(isset($item['Offers']['Offer']['OfferListing']['Price'])) {

		    			$position = strcspn( $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] , '0123456789' );

						$currency_symbol = substr($item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],0,$position);
						
						$sale_price = substr($item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],$position);
						$sale_price = (float)str_replace(',', '', $sale_price);

						$amount_save = substr($item['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
						$amount_save = (float)str_replace(',', '', $amount_save);

						$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

		    			return $regular_price_for_sale_item;

		    		}

	    		}



	    	} elseif(isset($item['Offers']['Offer']) && $item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

	    		if(isset($item['Offers']['Offer']['OfferListing']['AmountSaved'])) {

		    		if(isset($item['Offers']['Offer']['OfferListing']['SalePrice'])) {

		    			$position = strcspn( $item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'] , '0123456789' );

						$currency_symbol = substr($item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],0,$position);
						
						$sale_price = substr($item['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],$position);
						$sale_price = (float)str_replace(',', '', $sale_price);

						$amount_save = substr($item['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
						$amount_save = (float)str_replace(',', '', $amount_save);

						$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

		    			return $regular_price_for_sale_item;

		    		} elseif(isset($item['Offers']['Offer']['OfferListing']['Price'])) {

		    			$position = strcspn( $item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] , '0123456789' );

						$currency_symbol = substr($item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],0,$position);
						
						$sale_price = substr($item['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],$position);
						$sale_price = (float)str_replace(',', '', $sale_price);

						$amount_save = substr($item['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
						$amount_save = (float)str_replace(',', '', $amount_save);

						$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

		    			return $regular_price_for_sale_item;

		    		}

	    		}

	    	} elseif((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && isset($item['Variations']['Item'])) {


	    		if($item['Variations']['TotalVariations'] > 1) {

		    		return null;
	    		
	    		} else {

	    			if(strpos(strtolower($item['Variations']['Item']['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false && $item['Variations']['Item']['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			if(isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']) && isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice'])) {

			    			$position = strcspn( $item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'] , '0123456789' );

							$currency_symbol = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],0,$position);
							
							$sale_price = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],$position);
							$sale_price = (float)str_replace(',', '', $sale_price);

							$amount_save = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
							$amount_save = (float)str_replace(',', '', $amount_save);

							$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

			    			return $regular_price_for_sale_item;


			    		} elseif (isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']) && isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price'])) {

			    			$position = strcspn( $item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] , '0123456789' );

							$currency_symbol = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],0,$position);
							
							$sale_price = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],$position);
							$sale_price = (float)str_replace(',', '', $sale_price);

							$amount_save = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
							$amount_save = (float)str_replace(',', '', $amount_save);

							$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

			    			return $regular_price_for_sale_item;

			    		} else {
			    			return null;
			    		}


		    		
		    		} elseif ($item['Variations']['Item']['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			if(isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']) && isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice'])) {

			    			$position = strcspn( $item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'] , '0123456789' );

							$currency_symbol = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],0,$position);
							
							$sale_price = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['SalePrice']['FormattedPrice'],$position);
							$sale_price = (float)str_replace(',', '', $sale_price);

							$amount_save = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
							$amount_save = (float)str_replace(',', '', $amount_save);

							$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

			    			return $regular_price_for_sale_item;


			    		} elseif (isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']) && isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price'])) {

			    			$position = strcspn( $item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] , '0123456789' );

							$currency_symbol = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],0,$position);
							
							$sale_price = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'],$position);
							$sale_price = (float)str_replace(',', '', $sale_price);

							$amount_save = substr($item['Variations']['Item']['Offers']['Offer']['OfferListing']['AmountSaved']['FormattedPrice'],$position);
							$amount_save = (float)str_replace(',', '', $amount_save);

							$regular_price_for_sale_item = $currency_symbol . number_format(($sale_price + $amount_save),2);

			    			return $regular_price_for_sale_item;

			    		} else {
		    				return null;
			    		}

		    		}


	    		}

	    	} else {	    		
	    		return null;
	    	}

		}

		public function sale_item_finder($item) {
			
			if(isset($item['Offers']['Offer']) && ($item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New' && strpos(strtolower($item['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false)) {
	    		
	    		if(isset($item['Offers']['Offer']['OfferListing']['AmountSaved'])) {

		    		if(isset($item['Offers']['Offer']['OfferListing']['SalePrice'])) {

		    			return "Y";

		    		} else {
		    			return null;
		    		}

	    		}

	    	} elseif(isset($item['Offers']['Offer']) && $item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

	    		if(isset($item['Offers']['Offer']['OfferListing']['AmountSaved'])) {

		    		if(isset($item['Offers']['Offer']['OfferListing']['SalePrice'])) {

		    			return "Y";

		    		} else {
		    			return null;
		    		}

	    		}

	    	} elseif(((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['ItemAttributes']['ListPrice'])) && isset($item['VariationSummary'])) {

	    		if(isset($item['VariationSummary']['LowestSalePrice'])) {
	    			return "Y";
	    		} elseif (isset($item['VariationSummary']['LowestPrice'])) {
	    			return null;
	    		}

	    	} elseif((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['VariationSummary']) && isset($item['OfferSummary']['LowestNewPrice']) && !isset($item['OfferSummary']['LowestSalePrice'])) {
	    		
	    		return null;

	    	} elseif((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && !isset($item['VariationSummary']) && isset($item['OfferSummary']['LowestSalePrice']) && !isset($item['OfferSummary']['LowestNewPrice'])) {
	    		
	    		return "Y";

	    	} else {	    		
	    		return null;
	    	}

		}


		#Here in prime eligible function, OfferSummary & VariationSummary covered by Offers and Variations
		public function prime_eligible_grabber_database($item) {
			
			if(isset($item['Offers']['Offer']) && ($item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New' && strpos(strtolower($item['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false) && isset($item['Offers']['Offer']['OfferListing']['IsEligibleForPrime'])) {
	    		
	    		$eligible_prime = $item['Offers']['Offer']['OfferListing']['IsEligibleForPrime'];

	    		return $eligible_prime;

	    	} elseif(isset($item['Offers']['Offer']) && $item['Offers']['Offer']['OfferAttributes']['Condition'] == 'New' && isset($item['Offers']['Offer']['OfferListing']['IsEligibleForPrime'])) {

	    		$eligible_prime = $item['Offers']['Offer']['OfferListing']['IsEligibleForPrime'];

	    		return $eligible_prime;

	    	} elseif((!isset($item['Offers']['TotalOffers']) || $item['Offers']['TotalOffers'] == 0) && isset($item['Variations']['Item'])) {


	    		if($item['Variations']['TotalVariations'] > 1) {
		    		
		    		$eligible_prime = '0';
		    		return $eligible_prime;
	    		
	    		} else {

	    			if(strpos(strtolower($item['Variations']['Item']['Offers']['Offer']['Merchant']['Name']), 'amazon') !== false && $item['Variations']['Item']['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			if(isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['IsEligibleForPrime'])) {
	    					$eligible_prime = $item['Variations']['Item']['Offers']['Offer']['OfferListing']['IsEligibleForPrime'];
	    					return $eligible_prime;
	    				} else {
	    					$eligible_prime = '0';
	    					return $eligible_prime;
	    				}


		    		
		    		} elseif ($item['Variations']['Item']['Offers']['Offer']['OfferAttributes']['Condition'] == 'New') {

		    			if(isset($item['Variations']['Item']['Offers']['Offer']['OfferListing']['IsEligibleForPrime'])) {
	    					$eligible_prime = $item['Variations']['Item']['Offers']['Offer']['OfferListing']['IsEligibleForPrime'];
	    					return $eligible_prime;
	    				} else {
	    					$eligible_prime = '0';
	    					return $eligible_prime;
	    				}

		    		}


	    		}

	    	} else {
	    		
	    		$eligible_prime = '0';
	    		return $eligible_prime;
	    	}
		
		}

	}

}