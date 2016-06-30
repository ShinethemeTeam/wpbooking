<?php 
/**
*@since 1.0.0
**/


$class = ' wpbooking-form-group ';
$data_class = '';
if(!empty($data['condition'])){
    $class .= ' wpbooking-condition';
    $data_class .= ' data-condition='.$data['condition'].' ' ;
}
$name = isset( $data['custom_name'] ) ? esc_html( $data['custom_name'] ) : esc_html( $data['id'] );

$terms = get_object_taxonomies( 'wpbooking_service', 'objects' );

if( count( $terms ) ){
	unset( $terms['wpbooking_location'] );
}


?>
<div class="form-table wpbooking-settings <?php echo esc_html( $class ); ?>" <?php echo esc_html( $data_class ); ?>>
<div class="st-metabox-left" style="width: 100%;">
	<div class="st-metabox-content-wrapper">
		<div class="form-group">
			<div class="wpbooking-list-taxonomies clearfix">
			<?php 
				$item_term = array();
				if( !empty( $terms ) ):
					foreach( $terms as $key => $term ):
						$item_term = get_terms( $key, array('hide_empty' => false) );
			?>	
				<?php if( !empty( $item_term ) ) : ?>
				<h4><?php echo esc_html( $term->label ); ?></h4>
				<?php endif; ?>
				<div class="wpbooking-list-taxonomy clearfix">
					
					<?php 

						if( !empty( $item_term ) ):

							$old = array();
							$old_terms = wp_get_post_terms( $post_id, $key );
							if( !empty( $old_terms ) && is_array( $old_terms ) ){
								foreach( $old_terms as $term ){
									$old[] = (int) $term->term_id;
								}
							}

							foreach( $item_term as $item ):
					?>
						<div class="wpbooking-list-taxonomy-item">
							<label>
								<input <?php if( in_array( $item->term_id, $old ) ) echo 'checked'; ?> type="checkbox" value="<?php echo esc_html( $item->term_id ); ?>" name="<?php echo $name.'['. $key .'][]'; ?>">
								<span ><?php echo esc_html( $item->name ); ?></span>
							</label>
						</div>
					<?php endforeach; endif; ?>
				</div>
			<?php endforeach; endif; ?>
			</div>
		</div>
	</div>
	<i class="wpbooking-desc"><?php echo balanceTags( $data['desc'] ) ?></i>
</div>
</div>