<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/13/2016
 * Time: 2:38 PM
 */
$old_data = (isset($data['custom_data'])) ? esc_html($data['custom_data']) : get_post_meta($post_id, esc_html($data['id']), TRUE);

$class = ' wpbooking-form-group ';
$data_class = '';
if (!empty($data['condition'])) {
	$class .= ' wpbooking-condition ';
	$data_class .= ' data-condition=' . $data['condition'] . ' ';
}
if (!empty($data['container_class'])) $class .= ' ' . $data['container_class'];

$class .= ' width-' . $data['width'];
$name = isset($data['custom_name']) ? esc_html($data['custom_name']) : esc_html($data['id']);


?>
<div class="form-table wpbooking-settings <?php echo esc_html($class); ?>" <?php echo esc_html($data_class); ?>>
	<div class="st-metabox-left">
		<label for="<?php echo esc_html($data['id']); ?>"><?php echo esc_html($data['label']); ?></label>
	</div>
	<div class="st-metabox-right">
		<div class="st-metabox-content-wrapper">
			<div class="form-group">
				<div class="wpbooking-row">
					<div class="wpbooking-col-sm-12">
						<?php wp_dropdown_categories(array(
							'show_option_all' => esc_html__('Please Select', 'wpbooking'),
							'taxonomy'        => 'wpbooking_location',
							'class'           => 'widefat form-control',
							'name'            => 'location_id',
							'selected'        => get_post_meta(get_the_ID(), 'location_id', TRUE),
							'hide_empty'      => FALSE,
							'hierarchical'    => 1
						)) ?>
						<p class="help-block"><?php esc_html_e('Place, location, spot, site, locality','wpbooking') ?></p>
					</div>
					<div class="wpbooking-col-sm-12">
						<input type="text" name="zip_code"
							   placeholder="<?php esc_html_e('Zip/Postcode', 'wpbooking') ?>"
							   value="<?php echo get_post_meta(get_the_ID(), 'zip_code', TRUE) ?>"
							   class="widefat form-control">
						<p class="help-block"><?php esc_html_e('Zip/ postcode','wpbooking') ?></p>
					</div>
					<div class="wpbooking-col-sm-12">
						<input type="text" name="address" placeholder="<?php esc_html_e('Address', 'wpbooking') ?>"
							   value="<?php echo get_post_meta(get_the_ID(), 'address', TRUE) ?>"
							   class="widefat form-control">
						<p class="help-block"><?php esc_html_e('Address neighborhood, organization and clusters','wpbooking') ?></p>
					</div>
					<div class="wpbooking-col-sm-12">
						<input type="text" name="apt_unit" placeholder="<?php esc_html_e('Apt/Unit #', 'wpbooking') ?>"
							   value="<?php echo get_post_meta(get_the_ID(), 'apt_unit', TRUE) ?>"
							   class="widefat form-control">
						<p class="help-block"><?php esc_html_e('House number, floor, building','wpbooking') ?></p>
					</div>
				</div>
			</div>
		</div>
		<i class="wpbooking-desc"><?php echo balanceTags($data['desc']) ?></i>
	</div>
</div>
