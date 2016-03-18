<?php 
/**
*@since 1.0.0
**/

$old_data = esc_html( $data['std'] );

$value = get_post_meta( get_the_ID(), esc_html( $data['id'] ) );
if( !empty( $value ) ){
	$old_data = $value;
}
$field = '<div class="form-group">';

if( !empty( $data['label'] ) )
	echo '<div class="form-label"><label for="'.esc_html( $data['id'] ).'">'. esc_html( $data['label'] ) .'</label></div>';

$field .= '<div style="margin-bottom: 7px;"><textarea rows="10" id="'. esc_html( $data['id'] ).'" name="'. esc_html( $data['id'] ).'" class=" form-control widefat '. esc_html( $data['class'] ).'">'. esc_html( $old_data ).'</textarea></div>';

$field .= '</div>';
if( !empty( $data['desc'] ) ): ?>
<div class="st-metabox-content-wrapper">
	<div class="st-metabox-content-left">
		<?php echo $field;  ?>
	</div>
	<div class="st-metabox-content-right">
		<div class="description"><?php echo esc_html( $data['desc'] ); ?></div>
	</div>
</div>	
<?php else: 
echo '<div class="st-metabox-content-wrapper">';
	echo $field; 
echo '</div>';	
endif; ?>