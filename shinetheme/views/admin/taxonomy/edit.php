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
	<h2><?php _e( 'Edit Attribute', 'wpbooking' ) ?></h2>
	<?php echo wpbooking_get_admin_message() ?>
	<form action="" method="post">
		<input type="hidden" name="taxonomy_name" value="<?php echo WPBooking_Input::get('taxonomy_name') ?>" class="hidden">
		<table class="form-table">
			<tbody>
			<tr class="form-field form-required">
				<th scope="row" valign="top">
					<label for="taxonomy_label"><?php _e( 'Name', 'wpbooking' ); ?></label>
				</th>
				<td>
					<input name="taxonomy_label" id="taxonomy_label" type="text" value="<?php echo esc_attr( $row['label'] ); ?>" />
					<p class="description"><?php _e( 'Name for the taxonomy (shown on the front-end).', 'wpbooking' ); ?></p>
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row" valign="top">
					<label for="taxonomy_slug"><?php _e( 'Slug', 'wpbooking' ); ?></label>
				</th>
				<td>
					<input name="taxonomy_slug" id="taxonomy_slug" type="text" value="<?php echo esc_attr( $row['slug'] ); ?>" maxlength="28" />
					<p class="description"><?php _e( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'wpbooking' ); ?></p>
				</td>
			</tr>

			<tr class="form-field form-required">
				<th scope="row" valign="top">
					<label for=""><?php _e( 'Service Type', 'wpbooking' ); ?></label>
				</th>
				<td>
					<?php
					$types=WPBooking_Service::inst()->get_service_types();
					if(!empty($types))
					{
						foreach($types as $key=>$value){
							printf('<label><input type="checkbox" name="%s" value="%s" %s>%s</label><br>','taxonomy_service_type[]',$key,(isset($row['service_type']) and is_array($row['service_type']) and in_array($key,$row['service_type']))?'checked="checked"':false,$value['label']);
						}
					}
					?>
					<p class="description"><?php _e( 'Choose which Service type that the Taxonomy supported', 'wpbooking' ); ?></p>
				</td>
			</tr>
			
			
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="wpbooking_create_taxonomy" id="submit" class="button-primary" value="<?php _e( 'Update', 'wpbooking' ); ?>"></p>
		<?php wp_nonce_field( 'wpbooking_create_taxonomy' ); ?>
	</form>
</div>
