<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/17/2016
 * Time: 2:01 PM
 */
?>
<div class="wrap">
	<div class="icon32 icon32-attributes"><br/></div>
	<h2><?php echo esc_html__( 'Edit Attribute', 'wp-booking-management-system' ) ?></h2>
	<?php echo wpbooking_get_admin_message() ?>
	<form action="" method="post">
		<input type="hidden" name="taxonomy_name" value="<?php echo esc_attr(WPBooking_Input::get('taxonomy_name')) ?>" class="hidden">
		<table class="form-table">
			<tbody>
			<tr class="form-field form-required">
				<th scope="row" valign="top">
					<label for="taxonomy_label"><?php echo esc_html__( 'Name', 'wp-booking-management-system' ); ?></label>
				</th>
				<td>
					<input name="taxonomy_label" id="taxonomy_label" type="text" value="<?php echo esc_attr( $row['label'] ); ?>" />
					<p class="description"><?php echo esc_html__( 'Name for the taxonomy (shown on the front-end).', 'wp-booking-management-system' ); ?></p>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top">
					<label for="taxonomy_slug"><?php echo esc_html__( 'Slug', 'wp-booking-management-system' ); ?></label>
				</th>
				<td>
					<input name="taxonomy_slug" id="taxonomy_slug" type="text" value="<?php echo esc_attr( $row['slug'] ); ?>" maxlength="28" />
					<p class="description"><?php echo esc_html__( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'wp-booking-management-system' ); ?></p>
				</td>
			</tr>

			<tr class="form-field form-required">
				<th scope="row" valign="top">
					<label for=""><?php echo esc_html__( 'Service Type', 'wp-booking-management-system' ); ?></label>
				</th>
				<td>
					<?php
					$types=WPBooking_Service_Controller::inst()->get_service_types();
					if(!empty($types))
					{
						foreach($types as $key=>$value){
							printf('<label><input type="checkbox" name="%s" value="%s" %s>%s</label><br>','taxonomy_service_type[]',$key,(isset($row['service_type']) and is_array($row['service_type']) and in_array($key,$row['service_type']))?'checked="checked"':false,$value->get_info('label'));
						}
					}
					?>
					<p class="description"><?php echo esc_html__( 'Choose Types of Service that the Taxonomy supported', 'wp-booking-management-system' ); ?></p>
				</td>
			</tr>
			
			
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="wpbooking_create_taxonomy" id="submit" class="button-primary" value="<?php echo esc_html__( 'Update', 'wp-booking-management-system' ); ?>"></p>
		<?php wp_nonce_field( 'wpbooking_create_taxonomy' ); ?>
	</form>
</div>
