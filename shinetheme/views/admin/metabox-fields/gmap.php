<?php 
/**
*@since 1.0.0
**/
$post_id = get_the_ID();

$map_lat = (float) get_post_meta( $post_id, 'map_lat', true );

$map_long = (float) get_post_meta( $post_id, 'map_long', true );

$map_zoom = (int) get_post_meta( $post_id, 'map_zoom', true );

if( !$map_zoom ){ $map_zoom = $data['map_zoom']; }

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition  ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$field = '<div class="st-metabox-content-wrapper"><div class="form-group">';


$field .= '<div style="margin-bottom: 7px;"><div class="gmap-container"><div id="'.esc_html( $data['id'] ).'" class="gmap-content"></div><input type="text" name="gmap-search" value="" placeholder="'.__('Enter a address...', 'traveler-booking').'" class="gmap-search"></div></div>
<input type="hidden" name="map_lat" value="'.esc_html( $map_lat ).'">
<input type="hidden" name="map_long" value="'.esc_html( $map_long ).'">
<input type="hidden" name="map_zoom" value="'.esc_html( $map_zoom ).'">
';

$field .= '</div></div>';

?>

<tr class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<th scope="row">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</th>
	<td>
		<?php echo $field; ?>
		<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
	</td>
</tr>