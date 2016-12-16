<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/6/2016
 * Time: 2:35 PM
 */
wp_enqueue_script('acejs');
$data_value = wpbooking_get_option($data['id'],$data['std']);
$name = 'wpbooking_'.$data['id'];

if(!empty($data['element_list_item'])){
	$name = $data['custom_name'];
}
if(!empty($data['element_list_item'])){
	$data_value = $data['custom_value'];
}
$class = $name;
$data_class = '';
if(!empty($data['condition'])){
	$class .= ' wpbooking-condition wpbooking-form-group ';
	$data_class .= ' data-condition=wpbooking_'.$data['condition'].' ' ;
}
?>
<tr class="<?php echo esc_html($class) ?>" <?php echo esc_attr($data_class) ?>>
	<th scope="row">
		<label for="<?php echo esc_html($name) ?>"><?php echo esc_html($data['label']) ?>:</label>
	</th>
	<td>
		<div  id="<?php echo esc_attr($name) ?>" style="height: 400px" class="width-800 ace-editor" ></div>
		<textarea name="<?php echo esc_html($name) ?>" class="form-control  min-width-800 hidden" data-type="css"><?php echo esc_html($data_value) ?></textarea>
		<i class="wpbooking-desc"><?php echo do_shortcode($data['desc']) ?></i>
	</td>

</tr>