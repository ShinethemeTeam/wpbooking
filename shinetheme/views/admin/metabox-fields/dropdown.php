<?php 
/**
*@since 1.0.0
**/

$old_data = get_post_meta( get_the_ID(), esc_html( $data['id'] ), true );

$field = '<div class="form-group">';

if( !empty( $data['label'] ) )
	echo '<div class="form-label"><label for="'.esc_html( $data['id'] ).'">'. esc_html( $data['label'] ) .'</label></div>';

if( is_array( $data['value'] ) && !empty( $data['value'] ) ){
	$field .= '<div style="margin-bottom: 7px;"><select name="'. esc_html( $data['id'] ).'" id="'. esc_html( $data['id'] ) .'" class="widefat form-control '. esc_html( $data['class'] ).'">';
	foreach( $data['value'] as $key => $value ){
		$checked = '';
		if( !empty( $data['std'] ) && ( esc_html( $key ) == esc_html( $data['std'] ) ) ){
			$checked = ' selected ';
		}
		if( $old_data && is_array( $old_data ) ){
			if( in_array( esc_html( $key ), $old_data ) ){
				$checked = ' selected ';
			}else{
				$checked = '';
			}
		}
		
		$field .= '<option value="'. esc_html( $key ).'" '. $checked .'>'. esc_html( $value ).'</option>';
	}
	$field .= '</select></div>';
}

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