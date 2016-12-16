<?php
$data=wp_parse_args($data,array(
	'required'=>FALSE
));
$class = "wpbooking-col-md-6";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}

?>
<div class="<?php echo esc_html($class) ?>">
    <div class="wpbooking-build-group ">
        <label class="control-label"><?php echo esc_attr($data['title']) ?> <?php if($data['required']) echo '<span class="required">*</span>' ?>:</label>
        <input type="text" data-type="text" class="item" data-name-shortcode="<?php echo esc_attr($parent) ?>" name="<?php echo esc_attr($data['name']) ?>" id="<?php echo esc_attr($data['name']) ?>" value="<?php echo esc_attr($data['value']) ?>">
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>