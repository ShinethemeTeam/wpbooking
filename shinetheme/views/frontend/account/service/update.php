<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/27/2016
 * Time: 4:01 PM
 */
$service = FALSE;
if (WPBooking_Input::get('service_id')) {
	$service = get_post(WPBooking_Input::get('service_id'));
}
$meta = WPBooking_Metabox::inst()->get_metabox();

?>
<?php echo wpbooking_get_message() ?>
<div class="wpbooking-service-edit">
	<form action="" method="post">
		<input type="hidden" name="action" value="wpbooking_save_service">

		<div class="form-row">
			<input type="text" class="service_title" name="service_title"
				   placeholder="<?php esc_html_e('Service Name') ?>"
				   value="<?php echo WPBooking_Input::post('service_title', !empty($service->post_title) ? $service->post_title : FALSE) ?>">
		</div>
		<div class="service_content form-row">
			<?php wp_editor(WPBooking_Input::post('service_content', !empty($service->post_content) ? $service->post_content : FALSE), 'service_content') ?>
		</div>

		<?php if (!empty($meta['fields'])) : ?>
			<div class="wpbooking-service-metabox form-row">
				<div class="col-tab-nav"> <!-- required for floating -->
					<!-- Nav tabs -->
					<ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
						<?php
						$tabs=array();
						$last_tab_id=FALSE;
						foreach ($meta['fields'] as $k => $field) {
							$field = wp_parse_args($field, array(
								'id'    => '',
								'type'  => '',
								'label' => ''
							));

							// Only Generate Tabs
							if($field['type']!='tab'){
								if($last_tab_id){
									$tabs[$last_tab_id]['fields'][]=$field;
								}
								continue;
							}

							$tabs[$field['id']]=$field;
							$last_tab_id=$field['id'];

							$class = FALSE;
							if ($k === 0) $class = 'active';

							printf('<li class="%s"><a href="#wpbooking-section-%s" data-toggle="tab">%s</a></li>', $class, $field['id'],$field['label']);
						} ?>

					</ul>
				</div>
				<div class="col-tab-content">
					<!-- Tab panes -->
					<div class="tab-content">
						<?php
						if(!empty($tabs)){
							$i=0;
							foreach($tabs as $k=>$tab)
							{
								?>
								<div class="tab-pane <?php echo (!$i)?'active':FALSE ?>" id="wpbooking-section-<?php echo esc_attr($tab['id']) ?>">
									<?php
									if(!empty($tab['fields'])){
										foreach($tab['fields'] as $tab_field){
											$tab_field=wp_parse_args($tab_field,
												array(
													'id'          => '',
													'label'       => '',
													'type'        => '',
													'desc'        => '',
													'std'         => '',
													'class'       => '',
													'location'    => FALSE,
													'map_lat'     => '',
													'map_long'    => '',
													'map_zoom'    => 13,
													'server_type' => '',
													'width'       => ''
												));
											$class_extra = FALSE;
											if ($tab_field['location'] == 'hndle-tag') {
												$class_extra = 'wpbooking-hndle-tag-input';
											}

											$file = 'metabox-fields/' . $tab_field['type'];

											$field_html = apply_filters('wpbooking_metabox_field_html_' . $tab_field['type'], FALSE, $tab_field);
											if ($field_html)
												echo do_shortcode($field_html);
											else
												echo wpbooking_admin_load_view($file, array('data' => $tab_field, 'class_extra' => $class_extra));
										}
									}
									?>
								</div>
								<?php
								$i++;
							}
						}
						?>

						<div class="tab-pane" id="profile">Profile Tab.</div>
						<div class="tab-pane" id="messages">Messages Tab.</div>
						<div class="tab-pane" id="settings">Settings Tab.</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<div class="wpbooking-save form-row">
			<input type="submit" name="submit"
				   value="<?php echo (!WPBooking_Input::get('service_id')) ? esc_html__('Update', 'wpbooking') : esc_html__('Publish', 'wpbooking') ?>">
		</div>
	</form>
</div>

