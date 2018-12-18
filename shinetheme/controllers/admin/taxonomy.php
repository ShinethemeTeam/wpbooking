<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!class_exists('WPBooking_Admin_Taxonomy_Controller')) {
	class WPBooking_Admin_Taxonomy_Controller extends WPBooking_Controller
	{
		static $_inst;

		protected $_option_name = 'wpbooking_taxonomies';

		function __construct()
		{
			parent::__construct();

			if (WPBooking_Input::post('wpbooking_create_taxonomy')) {
				add_action('init', array($this, '_wpbooking_create_taxonomy'));
			}
			if (WPBooking_Input::get('action') == 'wpbooking_delete_taxonomy') {
				add_action('init', array($this, '_delete_taxonomy'));
			}
			add_action('admin_menu', array($this, '_add_taxonomy_page'));

			add_action('init', array($this, '_register_taxonomy'));

			add_action('wp_ajax_wpbooking_add_term',array($this,'_add_term'));

			add_action('wp_ajax_wpbooking_add_extra_service',array($this,'_ajax_add_extra_service'));

            add_action( 'wb_tour_type_edit_form_fields', [ $this, '_edit_custom_fields_tour_type' ] );
            add_action( 'wb_tour_type_add_form_fields', [ $this, '_edit_custom_fields_tour_type' ] );

            add_action( 'edited_wb_tour_type', [ $this, '_save_custom_fields_tour_type' ] );
            add_action( 'created_wb_tour_type', [ $this, '_save_custom_fields_tour_type' ], 10, 2 );

            /* room type */
            add_action( 'wb_hotel_room_type_edit_form_fields', [ $this, '_edit_custom_fields_room_type' ] );
            add_action( 'wb_hotel_room_type_add_form_fields', [ $this, '_edit_custom_fields_room_type' ] );

            add_action( 'edited_wb_hotel_room_type', [ $this, '_save_custom_fields_room_type' ] );
            add_action( 'created_wb_hotel_room_type', [ $this, '_save_custom_fields_room_type' ], 10, 2 );

            add_shortcode( 'wb_tour_type', [ $this, 'add_tour_type_shortcode' ] );
            add_shortcode( 'wb_hotel_room_type', [ $this, 'add_room_type_shortcode' ] );

            $attr_list = $this->get_taxonomies();
            $attr_list['wpbooking_amenity'] = array();
            $attr_list['wb_hotel_room_facilities'] = array();
            if (!empty($attr_list)) {
                foreach ($attr_list as $key => $value) {
                    add_filter('manage_edit-' . $key . '_columns', array(
                        $this,
                        'product_cat_columns'
                    ));
                    add_filter('manage_' . $key . '_custom_column', array(
                        $this,
                        'product_cat_column'
                    ), 10, 3);
                }
            }
            $this->add_meta_field();

		}

        public function add_tour_type_shortcode( $atts )
        {
            $atts = shortcode_atts( [
                'tour_type_id' => 0,
                'unit'        => 'c',
                'image_size'  => 'thumbnail'
            ], $atts, 'wb_tour_type' );

            extract( $atts );
            $image_id = get_tax_meta( $tour_type_id, 'featured_image', true );
            $tour_type = get_term( $tour_type_id, 'wb_tour_type' );
            if ( strpos( $image_size, 'x' ) ) {
                $image_size = explode( 'x', $image_size );
            }
            $image = wp_get_attachment_image_url( $image_id, $image_size );
            wp_enqueue_script( 'wpbooking-simpleWeather' );

            return '
                    <div class="wpbooking-tour-type-item" data-address="' . esc_attr( $tour_type->name ) . '" data-unit="' . esc_attr( $unit ) . '">
                        <div class="wpbooking-tour-type-image">
                            <img src="' . esc_url( $image ) . '" alt="' . esc_attr( $tour_type->name ) . '" class="img-responsive">
                        </div>
                        <h4 class="wpbooking-tour-type-temp"></h4>
                        <h2 class="wpbooking-tour-type-title"><a href="'.get_term_link($tour_type).'" target="_blank">' . esc_html( $tour_type->name ) . '</a></h2>
                    </div>
                ';

        }


        public function add_room_type_shortcode( $atts )
        {
            $atts = shortcode_atts( [
                'tag_id' => '',
                'image_size'  => 'thumbnail'
            ], $atts, 'wb_hotel_room_type' );

            extract( $atts );
            $image_id = get_tax_meta( $room_type_id, 'featured_image_room_type', true );
            $room_type = get_term( $room_type_id, 'wb_hotel_room_type' );
            if ( strpos( $image_size, 'x' ) ) {
                $image_size = explode( 'x', $image_size );
            }
            $image = wp_get_attachment_image_url( $image_id, $image_size );
            wp_enqueue_script( 'wpbooking-simpleWeather' );

            return '
                    <div class="wpbooking-tour-type-item" data-address="' . esc_attr( $room_type->name ) . '" >
                        <div class="wpbooking-tour-type-image">
                            <img src="' . esc_url( $image ) . '" alt="' . esc_attr( $room_type->name ) . '" class="img-responsive">
                        </div>
                        <h4 class="wpbooking-tour-type-temp"></h4>
                        <h2 class="wpbooking-tour-type-title"><a href="'.get_term_link($room_type).'" target="_blank">' . esc_html( $room_type->name ) . '</a></h2>
                    </div>
                ';

        }

        function _save_custom_fields_tour_type( $tour_type_id )
        {
            $featured_image = WPBooking_Input::post( 'featured_image_tour_type' );
            update_tax_meta( $tour_type_id, 'featured_image_tour_type', $featured_image );
        }

        function _save_custom_fields_room_type( $tour_type_id )
        {
            $featured_image = WPBooking_Input::post( 'featured_image_room_type' );
            update_tax_meta( $tour_type_id, 'featured_image_room_type', $featured_image );
        }

        function _edit_custom_fields_tour_type($term_object){

            if ( empty( $term_object->term_id ) ) $tour_type_id = 0; else $tour_type_id = $term_object->term_id;

            $wpbooking_featured_image = get_tax_meta( $tour_type_id, 'featured_image_tour_type' );
            $thumbnail_url            = wp_get_attachment_url( $wpbooking_featured_image );
            ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php echo esc_html__( 'Featured Image', 'wp-booking-management-system' ); ?></label>
                </th>
                <td>
                    <div class="upload-wrapper">
                        <div class="upload-items">
                            <?php
                            if ( !empty( $thumbnail_url ) ):
                                ?>
                                <div class="upload-item">
                                    <img src="<?php echo esc_url( $thumbnail_url ); ?>"
                                         alt="<?php echo esc_html__( 'Featured Thumb', 'wp-booking-management-system' ) ?>"
                                         class="frontend-image img-responsive">
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" class="save-image-id" name="featured_image_tour_type"
                               value="<?php echo esc_attr( $wpbooking_featured_image ); ?>">
                        <button type="button"
                                class="upload-button <?php if ( empty( $thumbnail_url ) ) echo 'no_image'; ?> button"
                                data-uploader_title="<?php esc_html_e( 'Select an image to upload', 'wp-booking-management-system' ); ?>"
                                data-uploader_button_text="<?php esc_html_e( 'Use this image', 'wp-booking-management-system' ); ?>"><?php echo esc_html__( 'Upload', 'wp-booking-management-system' ); ?></button>
                        <button type="button"
                                class="delete-button <?php if ( empty( $thumbnail_url ) ) echo 'none'; ?>"
                                data-delete-title="<?php echo esc_html__( 'Do you want delete this image?', 'wp-booking-management-system' ) ?>"><?php echo esc_html__( 'Delete', 'wp-booking-management-system' ); ?></button>
                    </div>
                </td>
            </tr>

        <?php }

        function _edit_custom_fields_room_type($term_object){

            if ( empty( $term_object->term_id ) ) $room_type_id = 0; else $room_type_id = $term_object->term_id;

            $wpbooking_featured_image = get_tax_meta( $room_type_id, 'featured_image_room_type' );
            $thumbnail_url            = wp_get_attachment_url( $wpbooking_featured_image );
            ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php echo esc_html__( 'Featured Image', 'wp-booking-management-system' ); ?></label>
                </th>
                <td>
                    <div class="upload-wrapper">
                        <div class="upload-items">
                            <?php
                            if ( !empty( $thumbnail_url ) ):
                                ?>
                                <div class="upload-item">
                                    <img src="<?php echo esc_url( $thumbnail_url ); ?>"
                                         alt="<?php echo esc_html__( 'Featured Thumb', 'wp-booking-management-system' ) ?>"
                                         class="frontend-image img-responsive">
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" class="save-image-id" name="featured_image_room_type"
                               value="<?php echo esc_attr( $wpbooking_featured_image ); ?>">
                        <button type="button"
                                class="upload-button <?php if ( empty( $thumbnail_url ) ) echo 'no_image'; ?> button"
                                data-uploader_title="<?php esc_html_e( 'Select an image to upload', 'wp-booking-management-system' ); ?>"
                                data-uploader_button_text="<?php esc_html_e( 'Use this image', 'wp-booking-management-system' ); ?>"><?php echo esc_html__( 'Upload', 'wp-booking-management-system' ); ?></button>
                        <button type="button"
                                class="delete-button <?php if ( empty( $thumbnail_url ) ) echo 'none'; ?> button"
                                data-delete-title="<?php echo esc_html__( 'Do you want delete this image?', 'wp-booking-management-system' ) ?>"><?php echo esc_html__( 'Delete', 'wp-booking-management-system' ); ?></button>
                    </div>
                </td>
            </tr>

        <?php }


        function add_meta_field()
        {
            if (is_admin()) {
                $attr_list = $this->get_taxonomies();
                $attr_list['wpbooking_amenity'] = array();
                $attr_list['wb_hotel_room_facilities'] = array();
                $pages = array();
                if (!empty($attr_list)) {
                    foreach ($attr_list as $key => $value) {
                        $pages[] = $key;
                    }
                }
                $config = array(
                    'id' => 'wpbooking_extra_infomation', // meta box id, unique per meta box
                    'title' => esc_html__('Extra Information', 'wp-booking-management-system'), // meta box title
                    'pages' => $pages, // taxonomy name, accept categories, post_tag and custom taxonomies
                    'context' => 'normal', // where the meta box appear: normal (default), advanced, side; optional
                    'fields' => array(), // list of meta fields (can be added by field arrays)
                    'local_images' => false, // Use local or hosted images (meta box images for add/remove)
                    'use_with_theme' => false //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
                );
                $my_meta = new Tax_Meta_Class($config);
                $my_meta->addText('wpbooking_icon', array(
                    'name' => esc_html__('Icon Picker', 'wp-booking-management-system'),
                    'desc' => ''
                ));
                $my_meta->Finish();
            }
        }
        function product_cat_columns($columns)
        {
            $new_columns         = array();
            if(!empty($columns['cb'])){
                $new_columns['cb']   = $columns['cb'];
                $new_columns['icon'] = esc_html__('Icon', 'wp-booking-management-system');

                unset($columns['cb']);
            }
            return array_merge($new_columns, $columns);
        }
        function product_cat_column($columns, $column, $id)
        {
            if ($column == 'icon') {
                $icon = get_tax_meta($id, 'wpbooking_icon');
                $columns .= '<i style="font-size:24px" class="'.wpbooking_handle_icon($icon).'"></i>';
            }
            return $columns;
        }
		/**
		 * Ajax create new extra service item for
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _ajax_add_extra_service()
		{
			$res=array('status'=>0);
			if(current_user_can('manage_options') and $service_type=WPBooking_Input::post('service_type') and $service_name=WPBooking_Input::post('service_name')){
				$tax='wpbooking_extra_service';
				$parent_term = term_exists( $service_name, $tax ); // array is returned if taxonomy is given
				$args = array();
				$service_desc=WPBooking_Input::post('service_desc');
				if(!empty($service_desc)){
					$args['description'] = $service_desc;
				}
				if($parent_term){
                    $check=get_term_meta($parent_term['term_id'],'service_type',true);
					if(!$check){
						$res['status']=1;
                        update_term_meta($parent_term['term_id'],'service_type',$service_type);
					}else{
						$res['message']=esc_html__('Term exists','wp-booking-management-system');
					}
				}else{
					$q=wp_insert_term($service_name,$tax,$args);
					if(!is_wp_error($q)){
						$res['status']=1;
						$res['data']=array(
							'term_id'=>$q['term_id'],
							'title'=>$service_name);
                        update_term_meta($q['term_id'],'service_type',$service_type);
					}
				}

			}

			echo json_encode($res);
			die;
		}

		/**
		 * Ajax Add Term for metabox
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _add_term()
		{
			$res=array(
				'status'=>0
			);

			if(current_user_can('manage_options') and $term_name=WPBooking_Input::post('term_name') and $tax=WPBooking_Input::post('taxonomy')){

				$parent_term = term_exists( $term_name, $tax ); // array is returned if taxonomy is given
				if($parent_term){
				    $res['status']=1;
                    $res['data']=array(
                        'term_id'=>$parent_term['term_id'],
                        'name'=>$term_name);
				}else{
					$q=wp_insert_term($term_name,$tax);
					if(!is_wp_error($q)){
						$res['status']=1;
						$res['data']=array(
							'term_id'=>$q['term_id'],
							'name'=>$term_name);

						// Extra Fields
						$other_data_raw=WPBooking_Input::post('other_data');
						parse_str(urldecode($other_data_raw),$other_data);

						$fields=array('icon');
						if(!empty($other_data['_add_term']) and is_array($other_data['_add_term'])){
							foreach($fields as $field){
								if(array_key_exists($field,$other_data['_add_term'])){

                                    update_term_meta($q['term_id'],$field,$other_data['_add_term'][$field]);

									switch($field){
										case "icon":
											$res['extra_fields'][$field]=wpbooking_icon_class_handler($other_data['_add_term'][$field]);
											break;
										default:
											$res['extra_fields'][$field]=$other_data['_add_term'][$field];
											break;
									}
								}
							}
						}

					}
				}

			}

			echo json_encode($res);
			die;
		}

		function _register_taxonomy()
		{
			$all = $this->get_taxonomies();
			if (!empty($all) and is_array($all)) {
				foreach ($all as $key => $value) {
					$labels = array(
						'name' => $value['label'],
						'add_new_item'      => sprintf(esc_html__('Add New %s','wp-booking-management-system'),$value['label']),
						'new_item_name'     => sprintf(esc_html__('New %s Name','wp-booking-management-system'),$value['label']),
					);

					$args = array(
						'hierarchical'       => TRUE,
						'labels'             => $labels,
						'show_ui'            => TRUE,
						'show_tagcloud' 	 => FALSE,
						'show_admin_column'  => FALSE,
						'query_var'          => TRUE,
                        'meta_box_cb'=>false,
						'rewrite'            => array('slug' => $value['slug']),
					);

					register_taxonomy($value['name'], array('wpbooking_service'), $args);

					$hide=apply_filters('wpbooking_hide_taxonomy_select_box',true);
					$hide=apply_filters('wpbooking_hide_taxonomy_select_box_'.$value['name'],$hide);
					if($hide)
					WPBooking_Assets::add_css("#".$value['name'].'div{display:none!important}');
				}
			}

		}

		function _delete_taxonomy()
		{
			if ($tax_name = WPBooking_Input::get('tax_name')) {
				$all = $this->get_taxonomies();
				unset($all[$tax_name]);
				update_option($this->_option_name, $all);
                wpbooking_set_admin_message(sprintf('<p>%s</p>',esc_html__('Delete Success','wp-booking-management-system')), 'success');

			} else {
                wpbooking_set_admin_message(sprintf('<p>%s</p>',esc_html__('Please select a Taxonomy','wp-booking-management-system')), 'error');
			}
			wp_redirect($this->get_page_url());
			die;
		}
		
		function _wpbooking_create_taxonomy()
		{
			$error = FALSE;
			$validate = TRUE;

			check_admin_referer('wpbooking_create_taxonomy');
			$taxonomy_label = stripslashes(WPBooking_Input::post('taxonomy_label'));
			$taxonomy_slug = stripslashes(WPBooking_Input::post('taxonomy_slug'));// for rewrite url

			$action = WPBooking_Input::post('taxonomy_name') ? 'edit' : 'add';

			if ($action == 'add') {
				$taxonomy_name = mb_strtolower($taxonomy_label);
				$taxonomy_name = $this->convert_vi_to_en($taxonomy_name);
				$taxonomy_name = 'wb_tax_' . str_replace('-', '_', $taxonomy_name);
			} else {
				$taxonomy_name = WPBooking_Input::post('taxonomy_name');
			}

			// Forbidden attribute names
			// http://codex.wordpress.org/Function_Reference/register_taxonomy#Reserved_Terms
			$reserved_terms = array(
				'attachment',
				'attachment_id',
				'author',
				'author_name',
				'calendar',
				'cat',
				'category',
				'category__and',
				'category__in',
				'category__not_in',
				'category_name',
				'comments_per_page',
				'comments_popup',
				'cpage',
				'day',
				'debug',
				'error',
				'exact',
				'feed',
				'hour',
				'link_category',
				'm',
				'minute',
				'monthnum',
				'more',
				'name',
				'nav_menu',
				'nopaging',
				'offset',
				'order',
				'orderby',
				'p',
				'page',
				'page_id',
				'paged',
				'pagename',
				'pb',
				'perm',
				'post',
				'post__in',
				'post__not_in',
				'post_format',
				'post_mime_type',
				'post_status',
				'post_tag',
				'post_type',
				'posts',
				'posts_per_archive_page',
				'posts_per_page',
				'preview',
				'robots',
				's',
				'search',
				'second',
				'sentence',
				'showposts',
				'static',
				'subpost',
				'subpost_id',
				'tag',
				'tag__and',
				'tag__in',
				'tag__not_in',
				'tag_id',
				'tag_slug__and',
				'tag_slug__in',
				'taxonomy',
				'tb',
				'term',
				'type',
				'w',
				'withcomments',
				'withoutcomments',
				'year',
				'wpbooking_service_type'
			);


			if (!$taxonomy_slug) {
				$taxonomy_slug = mb_strtolower($taxonomy_label);
                $taxonomy_slug = $this->convert_vi_to_en($taxonomy_slug);
				$taxonomy_slug = sanitize_title_with_dashes(stripslashes($taxonomy_slug));
			}

			// Error checking
			if (!$taxonomy_label || !$taxonomy_name) {
				$error = esc_html__('Please provide a taxonomy name', 'wp-booking-management-system');
			} elseif (strlen($taxonomy_name) >= 35) {
				$error = sprintf(esc_html__('Slug "%s" is too long (35 characters max). Shorten it, please.', 'wp-booking-management-system'), ($taxonomy_name));
			} elseif (in_array($taxonomy_name, $reserved_terms)) {
				$error = sprintf(esc_html__('Slug "%s" is not allowed because it is a reserved term. Change it, please.', 'wp-booking-management-system'), ($taxonomy_name));
			} else {
				
				$taxonomy_exists = taxonomy_exists($taxonomy_name);
				
				if ('add' === $action && $taxonomy_exists) {
					$error = sprintf(esc_html__('Slug "%s" is already in use. Change it, please.', 'wp-booking-management-system'), sanitize_title($taxonomy_name));
				}
			}
			if ($error) {
				$validate = FALSE;
				wpbooking_set_admin_message($error, 'error');
			}
			
			$validate = apply_filters('wpbooking_admin_save_taxonomy_validate', $validate);

			if (!$validate) return;

			$all = $this->get_taxonomies();
			if (!is_array($all)) $all = array();
			$all[$taxonomy_name] = array(
				'label'        => $taxonomy_label,
				'name'         => $taxonomy_name,
				'hierarchical' => 1,
				'service_type' => WPBooking_Input::post('taxonomy_service_type'),
				'slug'         => $taxonomy_slug
			);
			update_option($this->_option_name, $all);
			flush_rewrite_rules();
			if ($action == 'add') {
				wpbooking_set_admin_message(sprintf('<p>%s</p>',esc_html__('Create Successfully','wp-booking-management-system')), 'success');
			} else {
                wpbooking_set_admin_message(sprintf('<p>%s</p>',esc_html__('Save Successfully','wp-booking-management-system')), 'success');
			}
		}

		function _show_taxonomy_page()
		{
			$tax = $this->get_taxonomies();
			if (WPBooking_Input::get('action') == 'wpbooking_edit_taxonomy') {
				$single = WPBooking_Input::get('taxonomy_name');
				echo ($this->admin_load_view('taxonomy/edit', array('row' => $tax[$single])));

				return;
			}


			echo ($this->admin_load_view('taxonomy/index', array(
				'rows'     => $tax,
				'page_url' => $this->get_page_url()
			)));
		}

		function get_taxonomies()
		{
			return get_option($this->_option_name);
		}

		function _add_taxonomy_page()
		{
			$menu_page = $this->get_menu_page();
			add_submenu_page(
				$menu_page['parent_slug'],
				$menu_page['page_title'],
				$menu_page['menu_title'],
				$menu_page['capability'],
				$menu_page['menu_slug'],
				$menu_page['function']
			);

		}

		function get_menu_page()
		{
			$menu_page = WPBooking()->get_menu_page();
			$page = array(
				'parent_slug' => $menu_page['menu_slug'],
				'page_title'  => esc_html__('Taxonomies', 'wp-booking-management-system'),
				'menu_title'  => esc_html__('Taxonomies', 'wp-booking-management-system'),
				'capability'  => 'manage_options',
				'menu_slug'   => 'wpbooking_page_taxonomy',
				'function'    => array($this, '_show_taxonomy_page')
			);

			return apply_filters('wpbooking_setting_menu_args', $page);
		}

		function get_page_url()
		{
			$menu_page = $this->get_menu_page();

			return esc_url(add_query_arg(
				array(
					'page' => $menu_page['menu_slug']
				)
				, admin_url('admin.php')
			));
		}
		function set_tax_service_type(){

		}

        /**
         * Get all tax by service type
         *
         * @since 1.0
         * @author dungdt
         *
         * @param string|bool $service_type
         * @return array
         */
		function get_tax_service_type($service_type=false)
		{
            $all=$this->get_taxonomies();
            if(!empty($all) and $service_type){
                $res=array();

                foreach($all as $key=>$v){
                    if(!empty($v['service_type']) and in_array($service_type,$v['service_type'])){
                        $res[]=$v;
                    }
                }

                return $res;
            }
		}

        /**
         * Convert Title to EN
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $str
         * @return mixed
         */
        function convert_vi_to_en($str) {
            $str = preg_replace( '/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/' , 'a' , $str );
            $str = preg_replace( '/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/' , 'e' , $str );
            $str = preg_replace( '/(ì|í|ị|ỉ|ĩ)/' , 'i' , $str );
            $str = preg_replace( '/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/' , 'o' , $str );
            $str = preg_replace( '/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/' , 'u' , $str );
            $str = preg_replace( '/(ỳ|ý|ỵ|ỷ|ỹ)/' , 'y' , $str );
            $str = preg_replace( '/(đ)/' , 'd' , $str );
            $str = preg_replace( '/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/' , 'A' , $str );
            $str = preg_replace( '/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/' , 'E' , $str );
            $str = preg_replace( '/(Ì|Í|Ị|Ỉ|Ĩ)/' , 'I' , $str );
            $str = preg_replace( '/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/' , 'O' , $str );
            $str = preg_replace( '/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/' , 'U' , $str );
            $str = preg_replace( '/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/' , 'Y' , $str );
            $str = preg_replace( '/(Đ)/' , 'D' , $str );

            $str = str_replace( ' ' , '-' , $str ); // Replaces all spaces with hyphens.
            $str = preg_replace( '/[^A-Za-z0-9\-]/' , '' , $str ); // Removes special chars.

            $str = preg_replace( '/-+/' , '-' , $str ); // Replaces multiple hyphens with single one.

            return $str;
        }
		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}


	}

	WPBooking_Admin_Taxonomy_Controller::inst();
}