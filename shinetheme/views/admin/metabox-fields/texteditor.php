<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );
if( !empty( $value ) ){
	$old_data = $value;
}

$class = ' traveler-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' traveler-condition ';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}

?>

<tr class="<?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
	<th scope="row">
		<label for="<?php echo esc_html( $data['id'] ); ?>"><?php echo esc_html( $data['label'] ); ?></label>
	</th>
	<td>
		<div class="st-metabox-content-wrapper"><div class="form-group">
			<div style="margin-bottom: 7px;">
			<?php
			wp_editor( stripslashes( $old_data ), esc_html( $data['id'] ) ); ?>
			<i class="traveler-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
			</div>
		</div></div>
	</td>
</tr>