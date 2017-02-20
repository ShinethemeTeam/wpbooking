<?php
$old_data=FALSE;
if(is_object($taxonomy) and property_exists($taxonomy,'term_id')){
	$old_data=wpbooking_get_term_meta($taxonomy->term_id,$field['id']);
}
?>
<div class="small-input">
	<div class="input-group">
		<input data-placement="bottomRight" id="wb-field-<?php echo ($field['id']) ?>" name="wb-<?php echo ($field['id']) ?>" class="form-control icp icp-auto" value="<?php echo esc_html($old_data) ?>" type="text" />
		<span class="input-group-addon"></span>
	</div>
</div>
