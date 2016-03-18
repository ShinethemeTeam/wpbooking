<?php
/**
*@since 1.0.0
* Add metabox
**/
if( ! class_exists('Traveler_Admin_Metabox') ){
	class Traveler_Admin_Metabox extends Traveler_Controller{

		private $metabox;

		public function __construct(){
			if( ! is_admin() ){
				return;
			}
			parent::__construct();

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
           	 	wp_enqueue_script( 'traveler_admin.js ' , traveler_admin_assets_url( 'js/admin.js' ) , array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs') , null , true );
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
			$this->build_scripts();
		}

		public function build_scripts(){
			?>
			<style>
				  .st-metabox-nav li{ float: left; display: inline-block; }
				  .ui-tabs-vertical { width: 100%; }
				  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
				  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
				  .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
				  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; }
				  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 40em;}
				
				  .st-metabox-nav{ width: 160px; }
				  .st-metabox-nav li a{ border: 1px solid transparent; padding: 7px 10px; text-decoration: none;}
				  .ui-tabs-vertical .ui-tabs-nav li a:focus{
				  	-webkit-box-shadow: none;
				  	box-shadow: none;
				  }
				  .st-metabox-nav .ui-tabs-active a{ border: 1px solid #CCC; border-right: 1px solid #FFF; border-radius: 3px 0 0 3px; color: #000; font-weight: bold; }
				  .st-metabox-tabs-content{ width: auto !important; min-height: 200px; margin-left: 158px; float: none !important; border: 1px solid #ccc;}
				  @media( max-width: 1023px){
				  		.st-metabox-nav .ui-tabs-active a{ border-color: #CCC; border-bottom: 1px solid #FFF;}
				  		.st-metabox-tabs-content{ margin-top: 2px; padding: 15px; width: auto !important; margin-left: 0; float: none !important; padding-top: 10px; padding-bottom: 10px;}
				  }
				  .st-metabox-tabs-content .form-label{
				  	border-bottom: 1px dotted #CCC; margin-bottom: 20px; font-weight: bold; font-size: 16px; padding-bottom: 10px;
				  }
				  .st-metabox-content-left{ width: 70%; float: left; padding-right: 15px;}
				  .st-metabox-content-right{ margin-left: 70% }
				  @media( max-width: 1023px){
				  	.st-metabox-content-left{ width: 100%; float: none; }
				    .st-metabox-content-right{ width: 100%; margin: 10px 0; border-top: 1px dotted #CCC; padding-right: 0}
				  }
				  .st-metabox-content-wrapper{padding-bottom: 20px;}
				  .st-metabox-content-wrapper:after{
				  	display: table;
  					content: " ";
  					clear: both;
				  }
			</style>
			  	<script>
				  	jQuery(document).ready(function($) {
				  		var resize;
				  		$(window).resize(function(event) {
				  			clearTimeout( resize );

				  			resize = setTimeout(function(){
				  				if( $(window).width() < 1024 ){
				  					$( ".st-metabox-tabs" ).tabs({active: 0}).removeClass( "ui-tabs-vertical ui-helper-clearfix" );
				    				$( ".st-metabox-tabs li" ).addClass( "ui-corner-top" ).removeClass( "ui-corner-left" );
				  				}else{
				  					$( ".st-metabox-tabs" ).tabs({active: 0}).addClass( "ui-tabs-vertical ui-helper-clearfix" );
				    				$( ".st-metabox-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
				  				}
				  			}, 500);
				  		}).resize();;
				  		
				  	});
				</script>
			<?php
		}

	}
}