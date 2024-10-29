<?php
/*require_once AMAZING_SEARCH_PLUGIN_DIR . 'admin/includes/class-amazing-search-api.php';
$api = new Amazing_Search_Api();
$api->set_credentials();
$response = $api->item_search('cat litter','PetSupplies','2975298011');
echo "<pre>";
print_r($response);


die();*/

?>


<div class="amazing-search-wrap">

	<img src="<?php echo AMAZING_SEARCH_PLUGIN_URL ?>admin/images/amazing-search-logo.png" alt="amazing-search-logo.png" style="width: 90px;margin-top: 25px;">
	
	<div><?php settings_errors(); ?></div>

	<div class="amazing-search-tab">

		<ul class="amazing-search-tabs">
			<li><a href="#"><span class="dashicons dashicons-id" style="vertical-align: sub;"></span>Credentials</a></li>
			<li><a href="#"><div class="amazing-search-amazon-icon"></div> Amazon Associates</a></li>
			<li><a href="#"><span class="dashicons dashicons-admin-generic" style="vertical-align: sub;"></span> Settings</a></li>
			<li><a href="#"><span class="dashicons dashicons-clipboard" style="vertical-align: sub;"></span> Free VS Pro</a></li></li>
		</ul> <!-- / tabs -->

		<div class="amazing-search-tab-content">

			<div class="amazing-search-tabs-item">
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php 
						settings_fields( 'amazing_search_credential_tab_section' );
						do_settings_sections( 'amazing_search_credential_tab' );
						submit_button();
					?>
				</form>
			</div> <!-- / tabs_item -->

			<div class="amazing-search-tabs-item">
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php 
						settings_fields( 'amazing_search_associate_tab_section' );
						do_settings_sections( 'amazing_search_associate_tab' );
						submit_button();
					?>
				</form>
			</div> <!-- / tabs_item -->

			<div class="amazing-search-tabs-item">
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php 
						settings_fields( 'amazing_search_settings_tab_section' );
						do_settings_sections( 'amazing_search_settings_tab' );
						submit_button();
					?>
				</form>
			</div> <!-- / tabs_item -->
			<div class="amazing-search-tabs-item">
				<table width="100%">
                    <tr >
                        <th style="padding: 20px 20px 20px 10px;font-size: 18px;text-align: left;" width="50%">Features</th>
                        <th width="25%" style="text-align: center;font-size:18px">Free</th>
                        <th width="25%" style="text-align: center;font-size:18px">PRO</th>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Searchbar for content</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-yes"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Working Without Amazon API key</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Browse Categories for Searchbar</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Page Selection for Searchbar</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Condition for Searchbar</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Maximum price for Searchbar</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Minimum price for Searchbar</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tr>
                        <td class="amazing-search-proFree-feature">Sort functionality for Searchbar</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>                                       
                    <tr>
                        <td class="amazing-search-proFree-feature">Advanced Search Widget</td>
                        <td class="amazing-search-proFree-free"><span class="dashicons dashicons-no-alt"></span></td>
                        <td class="amazing-search-proFree-pro"><span class="dashicons dashicons-yes"></span></td>
                    </tr>
                    <tfoot>
                        <tr>
                            <td class="amazing-search-proFree-feature"></td>
                            <td class="amazing-search-proFree-free"></td>
                            <td class="amazing-search-proFree-pro"><a href="https://coderockz.com/downloads/amazing-search/" target="_blank"><button class="amazing-search-buy-now-btn">Buy Now</button></a></td>
                        </tr>
                    </tfoot>
                </table>
			</div> <!-- / tabs_item -->
		</div> <!-- / tab_content -->
	</div> <!-- / tab -->

</div>