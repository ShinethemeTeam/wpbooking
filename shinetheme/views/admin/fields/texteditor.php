<?php
$data=wp_parse_args($data,array(
	'editor_args'=>FALSE,
	'extra_html'=>FALSE
));
$data_value = wpbooking_get_option($data['id'],$data['std']);
$name = 'wpbooking_'.$data['id'];

if(!empty($data['element_list_item'])){
    $name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
    $data_value = $data['custom_value'];
}

$class = str_ireplace('[','_',$name);
$class = str_ireplace(']','_',$class);
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition wpbooking-form-group ';
    $data_class .= ' data-condition=wpbooking_'.$data['condition'].' ' ;
}
?>
<tr class="<?php echo esc_html($class) ?>" <?php echo esc_attr($data_class) ?>>
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
            <?php
            if(!empty($data['element_list_item'])){
                /*$content = $data_value;
                $editor_id = 'tmp_wpbooking_'.$data['id'];
                $settings = array('media_buttons' => false, 'textarea_name' => $name);
                wp_editor($content, $editor_id, $settings);*/
                ?>
                <textarea id="<?php echo esc_attr($name) ?>" name="<?php echo esc_html($name) ?>" class="form-control  min-width-500"><?php echo esc_html($data_value) ?></textarea>
            <?php
            }else{
                echo '<div class="min-width-500 width-800 content_editor">';
                wp_editor(stripslashes($data_value),$name,$data['editor_args']);
                echo '</div>';
            }

			if($data['extra_html']){
				printf('<div class="wpbooking-extra-html">%s</div>',do_shortcode($data['extra_html']));
			}
            ?>

        <i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
    </td>
</tr>