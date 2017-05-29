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
	<h2><?php esc_html_e( 'Taxonomies', 'wpbooking' ) ?></h2>
	<br class="clear" />
	<?php echo wpbooking_get_admin_message() ?>
	<div id="col-container">
		<div id="col-right">
			<div class="col-wrap">
                <?php $full = WPBooking_Assets::build_css_class('width: 100%'); ?>
				<table class="widefat attributes-table wp-list-table ui-sortable <?php echo esc_attr($full); ?>">
					<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Name', 'wpbooking' ) ?></th>
						<th scope="col"><?php esc_html_e( 'Slug', 'wpbooking' ) ?></th>
						<th scope="col"><?php esc_html_e( 'Service Type(s)', 'wpbooking' ) ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					if ( !empty($rows) and is_array($rows) ) :
						foreach ($rows as $tax=>$value) :
							?><tr>
							
							<td><a href="<?php echo esc_url(add_query_arg(array(
									'taxonomy'=>$tax,
									'post_type'=>'wpbooking_service'
								),admin_url('edit-tags.php'))) ?>"><?php echo esc_html( $value['label'] ); ?></a>
								
								<div class="row-actions">
									<span class="edit">
										<a href="<?php echo esc_url( add_query_arg(array('taxonomy_name'=> $tax,'action'=>'wpbooking_edit_taxonomy'),$page_url) ); ?>"><?php esc_html_e( 'Edit', 'wpbooking' ); ?></a>
										| </span>
									<span class="delete">
										<a class="delete" href="<?php echo  wp_nonce_url( add_query_arg(array('action'=>'wpbooking_delete_taxonomy','tax_name'=>$tax),$page_url) ) ; ?>"><?php esc_html_e( 'Delete', 'wpbooking' ); ?>
										</a>
									</span>
								</div>
							</td>
							<td><?php echo esc_html( $value['slug'] ); ?></td>

							<td class="attribute-terms"><?php
								if(!empty($value['service_type']) and is_array($value['service_type']))
								{
									echo implode(',',$value['service_type']);
								}
								?></td>
							<?php
						endforeach;
					else :
						?><tr><td colspan="6"><?php esc_html_e( 'Currently, there are no custom taxonomies existing.', 'wpbooking' ) ?></td></tr><?php
					endif;
					?>
					</tbody>
				</table>
			</div>
		</div>
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">
					<h3><?php esc_html_e( 'Add New Taxonomy', 'wpbooking' ) ?></h3>
					<p><?php esc_html_e( 'Taxonomies let you define unlimited extra information for Hotel, Car ..etc', 'wpbooking' ) ?></p>
					<form action="" method="post">
						<div class="form-field">
							<label for="taxonomy_label"><?php esc_html_e( 'Name', 'wpbooking' ); ?></label>
							<input name="taxonomy_label" id="taxonomy_label" required type="text" value="" />
							<p class="description"><?php esc_html_e( 'Name for the attribute (shown on the front-end).', 'wpbooking' ); ?></p>
						</div>
						
						<div class="form-field">
							<label for="taxonomy_slug"><?php esc_html_e( 'Slug', 'wpbooking' ); ?></label>
							<input name="taxonomy_slug" id="taxonomy_slug" type="text" value="" maxlength="28" />
							<p class="description"><?php esc_html_e( 'Unique slug/reference for the attribute; must be shorter than 28 characters.', 'wpbooking' ); ?></p>
						</div>

						<div class="form-field">
							<label ><?php esc_html_e( 'Service Type', 'wpbooking' ); ?></label>
							<?php
							$types=WPBooking_Service_Controller::inst()->get_service_types();
							if(!empty($types))
							{
								foreach($types as $key=>$value){
									printf('<label><input type="checkbox" name="%s" value="%s">%s</label>','taxonomy_service_type[]',$key,$value->get_info('label'));
								}
							}
							?>
							<p class="description"><?php esc_html_e( 'Choose Types of Service that the Taxonomy supported', 'wpbooking' ); ?></p>

						</div>

						<p class="submit"><input type="submit" name="wpbooking_create_taxonomy" id="submit" class="button" value="<?php esc_html_e( 'Add Taxonomy', 'wpbooking' ); ?>"></p>
						<?php wp_nonce_field( 'wpbooking_create_taxonomy' ); ?>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		/* <![CDATA[ */
		
		jQuery('a.delete').click(function(){
			var answer = confirm ("<?php esc_html_e( 'Are you sure that you want to delete this attribute?', 'wpbooking' ); ?>");
			if (answer) return true;
			return false;
		});
		
		/* ]]> */
	</script>
</div>
