<?php
$old_data=FALSE;
if(is_object($taxonomy) and property_exists($taxonomy,'term_id')){
	$old_data = get_term_meta($taxonomy->term_id,$field['id']);
}
$service_type=WPBooking_Service_Controller::inst()->get_service_types();
$array=array();
?>
<div class="small-input">
	<?php if(!empty($service_type)){
		foreach($service_type as $type_id => $type){
			$checked=FALSE;
			if(!empty($old_data) and is_array($old_data)){
				foreach($old_data as $key=>$val){
					if($val==$type_id) $checked='checked="checked"';
				}
			}
			?>
			<label class="type-<?php echo esc_attr($type_id)?>">
				<input <?php echo esc_attr($checked) ?> name="wb-<?php echo ($field['id']) ?>[]" class="form-control" value="<?php echo esc_html($type_id) ?>" type="checkbox"  >
				<span class="type-name"><?php echo esc_html($type->get_info('label')) ?></span>
			</label>
			<?php
		}
	} ?>
</div>
