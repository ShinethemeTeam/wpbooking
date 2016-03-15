<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/15/2016
 * Time: 3:18 PM
 */
?>
<div class="wrap">
	<div class="icon32 icon32-attributes" id="icon-woocommerce"><br/></div>
	<h2><?php _e( 'Taxonomies', 'traveler-booking' ) ?></h2>
	<br class="clear" />
	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
				<table class="widefat attributes-table wp-list-table ui-sortable" style="width:100%">
					<thead>
					<tr>
						<th scope="col"><?php _e( 'Name', 'traveler-booking' ) ?></th>
						<th scope="col"><?php _e( 'Slug', 'traveler-booking' ) ?></th>
						<th scope="col"><?php _e( 'Hierarchical', 'traveler-booking' ) ?></th>
						<th scope="col"><?php _e( 'Service Type(s)', 'traveler-booking' ) ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					if ( !empty($rows) and is_array($rows) ) :
						foreach ($rows as $tax=>$value) :
							?><tr>
							
							<td><a href="edit-tags.php?taxonomy=<?php echo esc_html($tax); ?>"><?php echo esc_html( $value['name'] ); ?></a>
								
								<div class="row-actions"><span class="edit"><a href="<?php echo esc_url( add_query_arg('edit', $tax) ); ?>"><?php _e( 'Edit', 'traveler-booking' ); ?></a> | </span><span class="delete"><a class="delete" href="<?php echo esc_url( wp_nonce_url( add_query_arg('delete', $tax), 'st_delete_attribute' ) ); ?>"><?php _e( 'Delete', 'traveler-booking' ); ?></a></span></div>
							</td>
							<td><?php echo esc_html( $tax ); ?></td>
							<td><?php if($value['hierarchical']) echo __('Yes','traveler-booking'); else echo __('No','traveler-booking'); ?></td>
							
							<td class="attribute-terms"><?php
								if(!empty($value['post_type']) and is_array($value['post_type']))
								{
									foreach($value['post_type'] as $k=>$v){
										$obj=get_post_type_object($v);
										if($obj){
											if($k)
											{
												echo ', ';
											}
											echo ($obj->labels->name);
										}
									}
								}
								?></td>
							<?php
						endforeach;
					else :
						?><tr><td colspan="6"><?php _e( 'No custom taxonomies currently exist.', 'traveler-booking' ) ?></td></tr><?php
					endif;
					?>
					</tbody>
				</table>
			</div>
		</div>
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">
					<h3><?php _e( 'Add New Taxonomy', 'traveler-booking' ) ?></h3>
					<p><?php _e( 'Taxonomies let you define unlimited extra information for Hotel, Car ..etc', 'traveler-booking' ) ?></p>
					<form action="" method="post">
						<div class="form-field">
							<label for="taxonomy_name"><?php _e( 'Name', 'traveler-booking' ); ?></label>
							<input name="taxonomy_name" id="taxonomy_name" required type="text" value="" />
							<p class="description"><?php _e( 'Name for the attribute (shown on the front-end).', 'traveler-booking' ); ?></p>
						</div>
						
						<div class="form-field">
							<label for="taxonomy_slug"><?php _e( 'Slug', 'traveler-booking' ); ?></label>
							<input name="taxonomy_slug" id="taxonomy_slug" type="text" value="" maxlength="28" />
							<p class="description"><?php _e( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'traveler-booking' ); ?></p>
						</div>
						
						<div class="form-field">
							<label for="attribute_type"><?php _e( 'Hierarchy', 'traveler-booking' ); ?></label>
							<select name="attribute_type" id="attribute_type">
								<option value="1"><?php _e( 'Yes', 'traveler-booking' ) ?></option>
								<option value="0"><?php _e( 'No', 'traveler-booking' ) ?></option>
							</select>
						</div>

						<p class="submit"><input type="submit" name="traveler_create_taxonomy" id="submit" class="button" value="<?php _e( 'Add Taxonomy', 'traveler-booking' ); ?>"></p>
						<?php wp_nonce_field( 'traveler_create_taxonomy' ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		/* <![CDATA[ */
		
		jQuery('a.delete').click(function(){
			var answer = confirm ("<?php _e( 'Are you sure you want to delete this attribute?', 'traveler-booking' ); ?>");
			if (answer) return true;
			return false;
		});
		
		/* ]]> */
	</script>
</div>
