<?php
$class = "wpbooking-col-md-12";
if(!empty($data['edit_field_class'])){
    $class = $data['edit_field_class'];
}
?>
<div class="<?php echo esc_html($class) ?>">
    <div class="wpbooking-build-group ">
        <label class="control-label">
            <input type="checkbox" data-type="is_required" class="item" data-name-shortcode="<?php echo esc_attr($parent) ?>" name="is_required" id="is_required">
            <?php echo esc_attr($data['title']) ?></label>
        <i class="desc"><?php echo esc_html($data['desc'])  ?></i>
    </div>
</div>