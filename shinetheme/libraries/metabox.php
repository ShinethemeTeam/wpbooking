<?php
/**
 *@since 1.0.0
 * Add metabox
 **/
if( ! class_exists('WPBooking_Metabox') ){
	class WPBooking_Metabox {

		static $_inst;

		private $metabox;

		public function __construct(){
			if( ! is_admin() ){
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, '_add_scripts' ) );

			add_action( 'save_post', array( $this, 'save_meta_box'), 10, 2 );

			add_action( 'wpbooking_save_metabox', array( $this, 'wpbooking_save_list_item'), 20, 2);
			add_action( 'wpbooking_save_metabox', array( $this, 'wpbooking_save_gmap'), 20, 2);
			add_action( 'wpbooking_save_metabox', array( $this, 'wpbooking_save_location'), 20, 2);
			add_action( 'wpbooking_save_metabox', array( $this, 'wpbooking_save_taxonomies'), 20, 2);
			
		}

		public function _add_scripts(){
			wp_enqueue_media();
			global $wp_styles, $wp_scripts;

			$styles  = $wp_styles->queue;
			$scripts = $wp_scripts->queue;

			if( !in_array( 'gmap3.js', $scripts ) ){

				wp_enqueue_script( 'maps.googleapis.js ' , 'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places', array( 'jquery') , null , true );

				wp_enqueue_script( 'gmap3.js ' , wpbooking_admin_assets_url( 'js/gmap3.min.js' ) , array( 'jquery') , null , true );
			}
		}

		public function register_meta_box( $metabox = array() ){

			$this->metabox = $metabox;

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		}

		public function add_meta_boxes(){
			foreach ( (array) $this->metabox['pages'] as $page ) {
				add_meta_box( $this->metabox['id'], $this->metabox['title'], array( $this, 'build_metabox' ), $page, $this->metabox['context'], $this->metabox['priority']);
			}
		}

		public function build_metabox( $post, $metabox ){
			$fields = $this->metabox['fields'];
			?>
			<div class="st-metabox-wrapper">
				<input type="hidden" name="<?php echo $this->metabox['id'].'_nonce'; ?>" value="<?php echo wp_create_nonce( $this->metabox['id'] ); ?>">
				<div id="<?php echo 'st-metabox-tabs-'.$this->metabox['id']; ?>" class="st-metabox-tabs">
					<ul class="st-metabox-nav">
						<?php
						foreach( (array) $fields as $key => $field ):
							if( $field['type'] === 'tab' ):

								$class = '';
								$data_class = '';
								if(!empty($field['condition'])){
									$class .= ' wpbooking-condition ';
									$data_class .= ' data-condition='.$field['condition'].' ' ;
								}
								?>
								<li class=""><a class="<?php echo esc_attr($class)?>" <?php echo esc_attr($data_class) ?> href="#<?php echo 'st-metabox-tab-item-'.esc_html( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></a></li>
							<?php endif; endforeach; ?>
					</ul>
					<?php
					foreach( (array) $fields as $key => $field ):

						if( isset( $fields[ $key ]['type'] ) && $fields[ $key ]['type'] === 'tab' ):
							$class = '';
							$data_class = '';
							if(!empty($field['condition'])){
								$class .= ' wpbooking-condition ';
								$data_class .= ' data-condition='.$field['condition'].' ' ;
							}
							?>
							<div id="<?php echo 'st-metabox-tab-item-'.esc_html( $field['id'] ); ?>" class="st-metabox-tabs-content ">
								<div class="st-metabox-tab-content-wrap <?php echo esc_attr($class)?>" <?php echo esc_attr($data_class) ?>>
									
									<?php

									$current_tab = (int) $key;
									foreach( (array) $fields as $key_sub => $field_sub ):
										if( $fields[ $key_sub ]['type'] === 'tab' ){

											if( (int) $current_tab != (int) $key_sub ){
												break;
											}
										}

										if( $fields[ $key_sub ]['type'] !== 'tab' ):

											$default = array(
												'id'       => '',
												'label'    => '',
												'type'     => '',
												'desc'     => '',
												'std'      => '',
												'class'    => '',
												'location' => FALSE,
												'map_lat' => '',
												'map_long' => '',
												'map_zoom' => 13,
												'server_type' => ''
											);

											$field_sub = wp_parse_args( $field_sub , $default );

											$class_extra=FALSE;
											if($field_sub['location']=='hndle-tag'){
												$class_extra='wpbooking-hndle-tag-input';
											}
											$file = 'metabox-fields/' . $field_sub['type'];

											$field_html=apply_filters('wpbooking_metabox_field_html_'.$field_sub['type'],FALSE,$field_sub);
											if($field_html) echo $field_html;
											else
											echo wpbooking_admin_load_view( $file, array( 'data' => $field_sub,'class_extra' => $class_extra ) );

											unset( $fields[ $key_sub ] );
											?>
										<?php endif; endforeach; ?>
								</div>
							</div>
						<?php endif; unset( $fields[ $key ] ); endforeach; ?>
				</div>
			</div>
			<?php
		}

		function save_meta_box( $post_id, $post_object ) {
	      global $pagenow;

	      /* don't save if $_POST is empty */
	      if ( empty( $_POST ) )
	        return $post_id;
	      
	      /* don't save during quick edi
	      t */
	      if ( $pagenow == 'admin-ajax.php' )
	        return $post_id;
	        
	      /* don't save during autosave */
	      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	        return $post_id;

	      /* don't save if viewing a revision */
	      if ( $post_object->post_type == 'revision' || $pagenow == 'revision.php' )
	        return $post_id;
	  	
	      /* verify nonce */
	      if ( isset( $_POST[ $this->metabox['id'] . '_nonce'] ) && ! wp_verify_nonce( $_POST[ $this->metabox['id'] . '_nonce'], $this->metabox['id'] ) )
	        return $post_id;

	      /* check permissions */
	      if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
	        if ( ! current_user_can( 'edit_page', $post_id ) )
	          return $post_id;
	      } else {
	        if ( ! current_user_can( 'edit_post', $post_id ) )
	          return $post_id;
	      }


	      foreach ( $this->metabox['fields'] as $field ) {
	      	if ( $field[ 'type' ] == 'list-item' ) {
	      		continue;
	      	}
	        $old = get_post_meta( $post_id, $field['id'], true );
	        $new = '';
	        
	        /* there is data to validate */
	        if ( isset( $_POST[$field['id']] ) ) {
	            
	            /* set up new data with validated data */
	            $new = $_POST[$field['id']];
	        
	        }
	        
	        if ( isset( $new ) && $new !== $old ) {
	          update_post_meta( $post_id, $field['id'], $new );
	          
	        } else if ( '' == $new && $old ) {
	          delete_post_meta( $post_id, $field['id'], $old );
	        }
	      }

	      do_action('wpbooking_save_metabox', $post_id, $post_object);
	  
	    }

	    public function wpbooking_save_gmap( $post_id, $post_object ){
	    	if( isset( $_POST['map_lat'] ) && isset( $_POST['map_long'] ) && isset($_POST['map_zoom'] ) ){
	    		$map_lat = (float)WPBooking_Input::post('map_lat', 0);
		    	$map_long = (float)WPBooking_Input::post('map_long', 0);
		    	$map_zoom = (int)WPBooking_Input::post('map_zoom', 0);

		    	update_post_meta( $post_id, 'map_lat', $map_lat );
		    	update_post_meta( $post_id, 'map_long', $map_long );
		    	update_post_meta( $post_id, 'map_zoom', $map_zoom );
	    	}

	    	return $post_id;
	    	
	    }

	    public function wpbooking_save_location( $post_id, $post_object ){
	    	foreach ( $this->metabox[ 'fields' ] as $field ) {
	    		if ( $field[ 'type' ] == 'location' ) {
	    			$new = WPBooking_Input::post( $field[ 'id' ] , '');

	    			if( !empty( $new ) && is_array( $new ) ){
	    				wp_set_post_terms( $post_id, $new, 'wpbooking_location' );
	    			}else{

	    				wp_set_post_terms( $post_id, array(0), 'wpbooking_location' );
	    			}

	    		}
	    	}

	    	return $post_id;
	    }

	    public function wpbooking_save_taxonomies( $post_id, $post_object ){
	    	foreach ( $this->metabox[ 'fields' ] as $field ) {
	    		if ( $field[ 'type' ] == 'taxonomies' ) {
	    			
	    			$terms = WPBooking_Input::post( $field[ 'id' ] , '');


	    			$service = get_post_meta( $post_id, 'service_type', true );
	    			if( !$service ) $service = 'room';

	    			$term_service = get_option('wpbooking_taxonomies', array() );
	    			if( !empty( $term_service ) && is_array( $term_service ) ){
	    				foreach( $term_service as $key => $term ){
	    					if( in_array( $service, $term['service_type'] ) ){
	    						wp_set_post_terms( $post_id, array(0), $key );
	    					}
	    				}
	    			}

	    			if( !empty( $terms ) && is_array( $terms ) ){
	    				foreach( $terms as $key => $val ){

	    					if( !empty( $val ) && is_array( $val ) ){
			    				wp_set_post_terms( $post_id, $val, $key );
			    			}
	    				}
	    			}else{
	    				wp_set_post_terms( $post_id, array(0) , $key );
	    			}
	    		}
	    	}

	    	return $post_id;
	    }

	    public function wpbooking_save_list_item( $post_id, $post_object ){
	    	foreach ( $this->metabox[ 'fields' ] as $field ) {
	    		
	    		if ( $field[ 'type' ] == 'list-item' ) {
	    			if ( isset( $_POST[ $field['id'] ] ) && is_array( $_POST[ $field['id'] ]) ) {
	    				$new_list = array();
	    				$list = $_POST[ $field['id'] ];
	    				
	    				$i = 0;
    					for( $j = 0; $j < count( $list['title']) - 1; $j ++ ){
    						foreach( $list as $key1 => $val1 ){
	    						$new_list[ $i ][ $key1 ] = $list[ $key1 ][ $i];
	    					}
	    					$i ++;
    					}
    					
    					update_post_meta( $post_id, $field['id'], $new_list ); 
	    			}else{
		    			continue;
		    		}
	    		}
	    	}
	    	return $post_id;
	    }

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}


	}

	WPBooking_Metabox::inst();
}