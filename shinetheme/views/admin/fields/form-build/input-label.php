<?php
$class = "wpbooking-col-md-6";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}
?>
<div class="<?php echo esc_html($class) ?>">
    <div class="wpbooking-build-group ">
        <label class="control-label"><?php echo esc_attr($data['title']) ?>:</label>
        <input type="text" disabled class="item" value="<?php echo esc_attr($data['value']) ?>">
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>