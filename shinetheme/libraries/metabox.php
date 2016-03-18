<?php
/**
 *@since 1.0.0
 * Add metabox
 **/
if( ! class_exists('Traveler_Metabox') ){
	class Traveler_Metabox {

		private $metabox;

		public function __construct(){
			if( ! is_admin() ){
				return;
			}

			add_action( 'admin_enqueue_scripts', array( $this, '_add_scripts' ) );
		}

		public function _add_scripts(){
			wp_enqueue_media();
			global $wp_styles, $wp_scripts;

			$styles  = $wp_styles->queue;
			$scripts = $wp_scripts->queue;

			if( !in_array( 'traveler_admin.css', $styles ) ){
				wp_enqueue_style( 'traveler_admin.css ', traveler_admin_assets_url( 'css/admin.css' ) );
			}

			if( !in_array( 'traveler_admin.js', $scripts ) ){
				wp_enqueue_script( 'traveler_admin.js ' , traveler_admin_assets_url( 'js/traveler-admin.js' ) , array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs') , null , true );
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
							if( $fields[ $key ]['type'] === 'tab' ):
								?>
								<li><a href="#<?php echo 'st-metabox-tab-item-'.esc_html( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></a></li>
							<?php endif; endforeach; ?>
					</ul>
					<?php
					foreach( (array) $fields as $key => $field ):

						if( isset( $fields[ $key ]['type'] ) && $fields[ $key ]['type'] === 'tab' ):
							?>
							<div id="<?php echo 'st-metabox-tab-item-'.esc_html( $field['id'] ); ?>" class="st-metabox-tabs-content">
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
											'id'    => '',
											'label' => '',
											'type'  => '',
											'desc'  => '',
											'std'   => '',
											'class' => ''
										);

										$field_sub = wp_parse_args( $field_sub , $default );

										$file = 'metabox-fields/' . $field_sub['type'];

										echo traveler_admin_load_view( $file, array( 'data' => $field_sub ) );

										unset( $fields[ $key_sub ] );
										?>
									<?php endif; endforeach; ?>
							</div>
						<?php endif; unset( $fields[ $key ] ); endforeach; ?>
				</div>
			</div>
			<?php
		}


	}
}