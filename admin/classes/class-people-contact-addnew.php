<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * People Contact AddNew
 *
 * Table Of Contents
 *
 * admin_screen_add_edit()
 */

namespace A3Rev\ContactPeople\Admin;

use A3Rev\ContactPeople\Data as Data;

class AddNew
{

	public static function profile_form_action() {
		if ( !is_admin() ) return ;

		$correct_address = false;
		if ( isset( $_POST['update_contact'] ) || isset( $_POST['add_new_contact'] ) ) {
			if ( isset( $_REQUEST['contact_arr']['c_address'] ) && trim( $_REQUEST['contact_arr']['c_address'] ) != '' ) {
				$correct_address = true;
			}
		}

		if ( isset( $_POST['update_contact'] ) ) {

			if ( ! $correct_address ) {
				update_option( 'a3_people_profile_save_failure', 1 );
				return;
			}

			$_REQUEST['contact_arr']['c_avatar'] = $_REQUEST['c_avatar'];
			$_REQUEST['contact_arr']['c_attachment_id'] = $_REQUEST['c_avatar_attachment_id'];

			if ( isset($_REQUEST['contact_arr']['c_website']) && trim( $_REQUEST['contact_arr']['c_website'] ) == 'http://' ) $_REQUEST['contact_arr']['c_website'] = '';

			if ( isset($_REQUEST['contact_arr']['show_on_main_page']) ) {
				$_REQUEST['contact_arr']['show_on_main_page'] = 1;
			} else {
				$_REQUEST['contact_arr']['show_on_main_page'] = 0;
			}

			if ( isset($_REQUEST['contact_arr']['enable_map_marker']) ) {
				$_REQUEST['contact_arr']['enable_map_marker'] = 1;
			} else {
				$_REQUEST['contact_arr']['enable_map_marker'] = 0;
			}

			Data\Profile::update_row($_REQUEST['contact_arr']);

			wp_redirect( 'admin.php?page=people-contact-manager&edited_profile=true', 301 );
			exit();

		} elseif ( isset( $_POST['add_new_contact'] ) ) {
			if ( ! $correct_address ) {
				update_option( 'a3_people_profile_save_failure', 1 );
				return;
			}

			$_REQUEST['contact_arr']['c_avatar'] = $_REQUEST['c_avatar'];
			$_REQUEST['contact_arr']['c_attachment_id'] = $_REQUEST['c_avatar_attachment_id'];

			if ( isset($_REQUEST['contact_arr']['c_website']) && trim( $_REQUEST['contact_arr']['c_website'] ) == 'http://' ) $_REQUEST['contact_arr']['c_website'] = '';

			if ( isset($_REQUEST['contact_arr']['show_on_main_page']) ) {
				$_REQUEST['contact_arr']['show_on_main_page'] = 1;
			} else {
				$_REQUEST['contact_arr']['show_on_main_page'] = 0;
			}

			if ( isset($_REQUEST['contact_arr']['enable_map_marker']) ) {
				$_REQUEST['contact_arr']['enable_map_marker'] = 1;
			} else {
				$_REQUEST['contact_arr']['enable_map_marker'] = 0;
			}

			$profile_id = Data\Profile::insert_row($_REQUEST['contact_arr']);

			wp_redirect( 'admin.php?page=people-contact-manager&created_profile=true', 301 );
			exit();
		}
	}

	public static function admin_screen_add_edit() {
		global $people_contact_location_map_settings;

		global $people_contact_admin_interface;

		$address_error_class = '';
		$message             = '';
		if ( get_option('a3_people_profile_save_failure', 0 ) == 1 ) {
			$address_error_class = 'input_error';
			$message             = '<div class="message error"><p>' . __( 'ERROR: A location address must be entered. Please enter a Location Address for this profile.' , 'contact-us-page-contact-people' ) .'</p></div>';
			delete_option( 'a3_people_profile_save_failure' );
		}

		$bt_type        = 'add_new_contact';
		$bt_value       = __('Create', 'contact-us-page-contact-people' );
		$title          = __('Add New Profile', 'contact-us-page-contact-people' );

		$center_address = 'Australia';
		$center_lat     = -25.155123773636443;
		$center_lng     = 133.77513599999997;
		$latlng_center  = $latlng = $center_lat.','.$center_lng;

		$bt_cancel      = '<input type="button" class="button" onclick="window.location=\'admin.php?page=people-contact-manager\'" value="'.__('Cancel', 'contact-us-page-contact-people' ).'" />';

		$data           = array('c_title' => '', 'c_name' => '', 'c_email' => '', 'c_phone' => '', 'c_fax' => '', 'c_mobile' => '', 'c_website' => '', 'c_address' => '', 'c_latitude' => '', 'c_longitude' => '', 'c_shortcode' => '', 'c_avatar' => '', 'c_attachment_id' => 0, 'c_about' => '');

		if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && $_GET['id'] >= 0) {
			$bt_type = 'update_contact';
			$data    = Data\Profile::get_row( $_GET['id'], '', 'ARRAY_A' );
			$title   = __('Edit Profile', 'contact-us-page-contact-people' );
			
			if ( trim($data['c_latitude']) != '' && trim($data['c_longitude']) != '' ) {
				$latlng_center = $latlng = $data['c_latitude'].','.$data['c_longitude'];
			}

			$bt_value = __('Update', 'contact-us-page-contact-people' );

		}

		if ( isset( $_POST['update_contact'] ) || isset( $_POST['add_new_contact'] ) ) {
			$data = array_merge( $data, $_REQUEST['contact_arr'] );
		}
		?>
        <div id="htmlForm">
        <div style="clear:both"></div>
		<div class="wrap">
		<style>
		.input_error {
			border-color: #cc0000 !important;
		}
		</style>
		<?php echo $message; ?>
        <div class="icon32 icon32-a3rev-ui-settings icon32-a3revpeople-contact-settings" id="icon32-a3revpeople-contact-addnew"><br></div><h1><?php echo $title;?></h1>
          <div style="clear:both;"></div>
		  <div class="contact_manager a3rev_panel_container a3rev_custom_panel_container">
			<form action="" name="add_conact" id="add_contact" method="post">
			<input type="hidden" value="<?php if ( isset( $_GET['id'] ) ) echo $_GET['id']; ?>" id="profile_id" name="contact_arr[profile_id]">
            <div class="col-left">
	            <?php ob_start(); ?>
				<table class="form-table" style="margin-bottom:0;">
				  <tbody>
					<tr valign="top">
					  <th scope="row"><label for="c_title"><?php _e('Title / Position', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if(isset($data['c_title'])){ esc_attr_e( stripslashes( $data['c_title'] ) ) ;}?>" id="c_title" name="contact_arr[c_title]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_name"><?php _e('Name', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if(isset($data['c_name'])){ esc_attr_e( stripslashes( $data['c_name']));}?>" id="c_name" name="contact_arr[c_name]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_avatar"><?php _e('Profile Image', 'contact-us-page-contact-people' ) ?></label></th>
					  <td class="profileavatar">
	                  <?php
					  global $people_contact_uploader;
					  ?>
	                  <?php echo $people_contact_uploader->upload_input( 'c_avatar', 'c_avatar', $data['c_avatar'], $data['c_attachment_id'], '', __('Profile Image', 'contact-us-page-contact-people' ), '', 'width:100%;', '<div class="description">'.__("Image format .jpg, .png", 'contact-us-page-contact-people' ).'</div>' ); ?>
	                  </td>
					</tr>
	        	  </tbody>
				</table>
				<?php
		        $settings_html = ob_get_clean();
		        $people_contact_admin_interface->panel_box( $settings_html, array(
		        	'name' 		=> __( 'Profile Details', 'contact-us-page-contact-people' ),
		        	'desc'		=> __("Fields left empty will not show on the front end.", 'contact-us-page-contact-people' ),
		        	'css'		=> 'margin-top: 5px',
		        	'id'		=> 'a3_people_profile_details_box',
					'is_box'	=> true,
				) );
		        ?>

		        <?php ob_start(); ?>
				<table class="form-table" style="margin-bottom:0;" width="100%">
				  <tbody>
					<tr valign="top">
					  <th scope="row"><label for="c_email"><?php _e('Email', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if(isset($data['c_email'])){esc_attr_e( stripslashes( $data['c_email'] ));}?>" id="c_email" name="contact_arr[c_email]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_phone"><?php _e('Phone', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if(isset($data['c_phone'])){esc_attr_e( stripslashes( $data['c_phone'] ) );}?>" id="c_phone" name="contact_arr[c_phone]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_fax"><?php _e('Fax', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if(isset($data['c_fax'])){esc_attr_e( stripslashes( $data['c_fax'] ));}?>" id="c_fax" name="contact_arr[c_fax]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_mobile"><?php _e('Mobile', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if(isset($data['c_mobile'])){esc_attr_e( stripslashes( $data['c_mobile'] ));}?>" id="c_mobile" name="contact_arr[c_mobile]"></td>
					</tr>
	                <tr valign="top">
					  <th scope="row"><label for="c_website"><?php _e('Website', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text" value="<?php if( isset( $data['c_website']) && trim($data['c_website']) != '' ) { echo esc_url( stripslashes( $data['c_website'] ) ); } else { echo 'http://'; } ?>" id="c_website" name="contact_arr[c_website]" onfocus="if (this.value == 'http://') {this.value = '';}" onblur="if (this.value == '') {this.value = 'http://';}" /></td>
					</tr>
	                <tr valign="top">
					  <td class="forminp" colspan="2" style="padding-left:0;">
					   <div class="row_mod" ><label for="c_website"><?php _e('About', 'contact-us-page-contact-people' ) ?></label></div>
	                  	<?php wp_editor(stripslashes( $data['c_about'] ), 'c_about', array('textarea_name' => 'contact_arr[c_about]', 'wpautop' => true, 'textarea_rows' => 8 ) ); ?>
	                  </td>
					</tr>
				  </tbody>
				</table>
				<?php
		        $settings_html = ob_get_clean();
		        $people_contact_admin_interface->panel_box( $settings_html, array(
		        	'name' 		=> __( 'Contact Details', 'contact-us-page-contact-people' ),
		        	'desc'		=> __("Fields left empty will not show on the front end.", 'contact-us-page-contact-people' ),
		        	'id'		=> 'a3_people_contact_details_box',
					'is_box'	=> true,
				) );
		        ?>

	        	<?php ob_start(); ?>
				<table class="form-table" style="margin-bottom:0;">
				  <tbody>
					<tr valign="top">
					  <th scope="row"><label for="show_on_main_page"><?php _e('Contact Page Display', 'contact-us-page-contact-people' ) ?></label></th>
					  <td>
					  	<?php
					  		if ( ! isset( $data['show_on_main_page'] ) ) {
					  			$data['show_on_main_page'] = 1;
					  		}
					  	?>
					  	<input
					  		id="show_on_main_page"
							name="contact_arr[show_on_main_page]"
							class="a3rev-ui-onoff_checkbox show_on_main_page"
	                        checked_label="<?php _e( 'ON', 'contact-us-page-contact-people' ); ?>"
	                        unchecked_label="<?php _e( 'OFF', 'contact-us-page-contact-people' ); ?>"
	                        type="checkbox"
							value="1"
							<?php checked( 1, $data['show_on_main_page'], true ); ?>
							/>
					  </td>
					</tr>
				  </tbody>
				</table>
				<div class="show_marker_on_main_map_container" style="<?php if ( 1 != $data['show_on_main_page'] ) { echo 'visibility:hidden; height: 0px; overflow: hidden; margin-bottom: 0px;'; } ?>" >
					<table class="form-table" style="margin-bottom:0;">
					  <tbody>
						<tr valign="top">
						  <th scope="row"><label for="enable_map_marker"><?php _e('Show on Contact Page map', 'contact-us-page-contact-people' ) ?></label></th>
						  <td>
						  	<?php
						  		if ( ! isset( $data['enable_map_marker'] ) ) {
						  			$data['enable_map_marker'] = 1;
						  		}
						  	?>
						  	<input
						  		id="enable_map_marker"
								name="contact_arr[enable_map_marker]"
								class="a3rev-ui-onoff_checkbox enable_map_marker"
		                        checked_label="<?php _e( 'ON', 'contact-us-page-contact-people' ); ?>"
		                        unchecked_label="<?php _e( 'OFF', 'contact-us-page-contact-people' ); ?>"
		                        type="checkbox"
								value="1"
								<?php checked( 1, $data['enable_map_marker'], true ); ?>
								/>
								<span class="description"><?php echo __( 'ON to show this Profile on the Contact Page map', 'contact-us-page-contact-people' ); ?></span>
						  </td>
						</tr>
					  </tbody>
					</table>
				</div>
				<?php
		        $settings_html = ob_get_clean();
		        $people_contact_admin_interface->panel_box( $settings_html, array(
		        	'name' 		=> __( 'Contact Page Profile', 'contact-us-page-contact-people' ),
		        	'desc'		=> __( 'Switch OFF and this profile will not show on the main Contact Us Page but still can be inserted by shortcode (Pro and Ultimate Versions) and assigned to groups (Ultimate Version) that are inserted by shortcode.', 'contact-us-page-contact-people' ),
		        	'id'		=> 'a3_people_contact_page_profile_box',
					'is_box'	=> true,
				) );
		        ?>

				<div>
					<?php ob_start(); ?>
		            <table class="form-table" style="margin-bottom:0;">
		            <tr valign="top">
						  <th scope="row"><label for="c_shortcode"><?php _e('Enter Form Shortcode', 'contact-us-page-contact-people' ) ?></label></th>
						  <td>
		                  <input type="text" class="regular-text" value="" id="c_shortcode" name="contact_arr[c_shortcode]"></td>
						</tr>
					  </tbody>
					</table>
					<?php
			        $settings_html = ob_get_clean();
			        $people_contact_admin_interface->panel_box( $settings_html, array(
			        	'name' 		=> __( 'Contact Form by Shortcode', 'contact-us-page-contact-people' ),
			        	'desc'		=> sprintf( __( 'Add a unique Contact Form for this profile. Supports Contact Form 7 or Gravity Forms plugin shortcodes. Feature must be switched <a href="%s" target="_blank">ON here</a> + Contact Form Type > Create Form by Shortcode switch.', 'contact-us-page-contact-people' ), admin_url( 'admin.php?page=people-contact-settings&tab=email-inquiry' ) ),
			        	'class'		=> 'pro_feature_fields',
			        	'id'		=> 'a3_people_contact_form_shortcode_box',
						'is_box'	=> true,
					) );
			        ?>
				</div>

				<div class="categories_selection_container">
              		<?php ob_start(); ?>
					<div class="categories_selection">
		              <?php
					  	$all_categories = array ( array('id' => 1, 'category_name' => __('Profile Group', 'contact-us-page-contact-people' ) ) );
						if ( is_array($all_categories) && count($all_categories) > 0 ) {
							foreach ( $all_categories as $category_data ) {
						?>
		                	<div style="float:left; width:100%; margin-bottom:10px;">
		                    	<input
										name="categories_assign[]"
										class="a3rev-ui-onoff_checkbox"
		                                checked_label="<?php _e( 'ON', 'contact-us-page-contact-people' ); ?>"
		                                unchecked_label="<?php _e( 'OFF', 'contact-us-page-contact-people' ); ?>"
		                                type="checkbox"
										value="<?php echo $category_data['id']; ?>"
										/>
		                    	<?php esc_attr_e( stripslashes( $category_data['category_name'] ) ); ?>
		                	</div>
		                    <div style="clear:both"></div>
		                <?php
							}
						} else {
							_e('No Groups', 'contact-us-page-contact-people' );
						}
					  ?>
              		</div>
              		<?php
			        $settings_html = ob_get_clean();
			        $people_contact_admin_interface->panel_box( $settings_html, array(
			        	'name' 		=> __( 'Assign Profile to Groups', 'contact-us-page-contact-people' ),
			        	'desc'		=> __( 'Use the ON | OFF switches to assign this profile to any number of Groups', 'contact-us-page-contact-people' ),
			        	'class'		=> 'pro_feature_fields',
			        	'id'		=> 'a3_people_groupes_box',
						'is_box'	=> true,
					) );
			        ?>
              	</div>

            </div>
			<div class="col-right">
				<?php ob_start(); ?>
				<table class="form-table" style="margin-bottom:0;">
				  <tbody>
					<tr valign="top">
					  <th scope="row"><label for="c_address"><?php _e('Address', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" class="regular-text <?php echo $address_error_class; ?>" value="<?php if(isset($data['c_address'])){esc_attr_e( stripslashes( $data['c_address']));}?>" id="c_address" name="contact_arr[c_address]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_latitude"><?php _e('Latitude', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" readonly="readonly" class="regular-text" value="<?php if(isset($data['c_latitude'])){echo $data['c_latitude'];}?>" id="c_latitude" name="contact_arr[c_latitude]"></td>
					</tr>
					<tr valign="top">
					  <th scope="row"><label for="c_longitude"><?php _e('Longitude', 'contact-us-page-contact-people' ) ?></label></th>
					  <td><input type="text" readonly="readonly" class="regular-text" value="<?php if(isset($data['c_longitude'])){echo $data['c_longitude'];}?>" id="c_longitude" name="contact_arr[c_longitude]"></td>
					</tr>
					<tr>
						<th></th>
						<td>
							<div style="font-size: 13px;"><?php echo __( '* <strong>Tip</strong> - drag and drop the map marker to the required location', 'contact-us-page-contact-people' ); ?></div>
							<div class="maps_content" style="padding:10px 0px;">
					    		<div id="map_canvas"></div>
		              		</div>
		              	</td>
					</tr>
				  </tbody>
				</table>
				<?php
		        $settings_html = ob_get_clean();
		        $people_contact_admin_interface->panel_box( $settings_html, array(
		        	'name' 		=> __( 'Profile Location Address', 'contact-us-page-contact-people' ),
		        	'desc'		=> __( '<strong>REQUIRED</strong>: All profiles must have a map location set. The location set here is used for this profile on the Contact Us Page map (optional) and any Group map (optional) that the profile has been assigned to.', 'contact-us-page-contact-people' ),
		        	'id'		=> 'a3_people_location_address_box',
					'is_box'	=> true,
					'alway_open'=> true,
				) );
		        ?>

              	<div style="clear:both"></div>
			</div>
            <div style="clear:both"></div>
			<script type="text/javascript" >
			<?php
			$map_type = $people_contact_location_map_settings['map_type'];
			if($map_type == ''){
				$map_type = 'ROADMAP';
			}

			$zoom_level = 1;
			if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && $_GET['id'] >= 0) {
				$zoom_level = $people_contact_location_map_settings['zoom_level'];
				if($zoom_level <= 0){
					$zoom_level = 1;
				}
			}
			?>

			jQuery(document).ready(function ($) {
				var geocoder;
				var map;
				var marker;
				//MAP
				var mapType = '<?php echo $map_type; ?>';
				var zoom = <?php echo $zoom_level; ?>;
				var latlng = new SMap.Coords(<?php echo $latlng; ?>);
				var latlng_center = new SMap.Coords(<?php echo $latlng_center;?>);

				map = new SMap(JAK.gel("map_canvas"), null, zoom);
				map.setCenter(latlng)
				map.addDefaultControls(); 
				
				var layer = SMap.DEF_BASE;
				if('SATELLITE' == mapType) {
					layer = SMap.DEF_OPHOTO;
				}
				map.addDefaultLayer(layer).enable();

				var markerLayer = new SMap.Layer.Marker();
				map.addLayer(markerLayer).enable();

				marker = new SMap.Marker(latlng,'marker', {});
				marker.decorate(SMap.Marker.Feature.Draggable);
				markerLayer.addMarker(marker);

				var signals = map.getSignals();
				signals.addListener(window, 'marker-drag-stop', function(e) {
					var coords = e.target.getCoords();
					
					new SMap.Geocoder.Reverse(coords, function(geocoder) {
						if (!geocoder.getResults()) {
							return
						}

						var item = geocoder.getResults();
						jQuery('#c_address').removeClass('input_error');
						jQuery('#c_latitude').val(item.coords.x);
						jQuery('#c_longitude').val(item.coords.y);
					});
				})

				$(document).on( "a3rev-ui-onoff_checkbox-switch", '.show_on_main_page', function( event, value, status ) {
					$(".show_marker_on_main_map_container").attr('style','display:none;');
					if ( status == 'true' ) {
						$(".show_marker_on_main_map_container").slideDown();
					} else {
						$(".show_marker_on_main_map_container").slideUp();
					}
				});

				$("#c_address").focus(function() {
					$(this).removeClass('input_error');
				});

				$(function() {
					$("#c_address").autocomplete({
					  //This bit uses the geocoder to fetch address values
					  source: function(request, response) {
						new SMap.Geocoder(request.term, function(geocoder) {
							if (!geocoder.getResults()[0].results.length) {
								response([]);
							}

							response($.map(geocoder.getResults()[0].results, function(item) {
								return {
									label: item.label,
									value: request.term,
									latitude: item.coords.x,
									longitude: item.coords.y
								}
							}));
						});
					  },
					  //This bit is executed upon selection of an address
					  select: function(event, ui) {
						$("#c_latitude").val(ui.item.latitude);
						$("#c_longitude").val(ui.item.longitude);
						var location = new SMap.Coords(ui.item.latitude, ui.item.longitude);
						marker.setCoords(location);
						map.setCenter(location);
					  }
					});
			  	});
			});
			</script>
			<div style="clear:both"></div>
			<p class="submit" style="margin-bottom:0;padding-bottom:0;">
            <input type="hidden" value="<?php echo $bt_type;?>" name="<?php echo $bt_type;?>" />
            <input type="submit" value="<?php echo $bt_value;?>" class="button button-primary" id="add_edit_buttom" name="add_edit_buttom"> <?php echo $bt_cancel;?></p>
			</form>
		  </div>
		  <div style="clear:both"></div>
		</div>
        <div style="clear:both"></div>
		</div>
		<?php
	}
}
