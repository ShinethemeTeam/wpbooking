<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('WPBooking_Admin_Service')) {
	class WPBooking_Admin_Service extends WPBooking_Controller
	{
		private static $_inst;

		function __construct()
		{
			add_action('init', array($this, '_add_taxonomy'));
			add_action('init', array($this, '_add_post_type'), 5);
			add_action('init', array($this, '_add_metabox'));
			add_action('save_post', array($this, '_save_extra_field'));
			add_filter('wpbooking_settings', array($this, '_add_settings'));

			// Merge Data
			add_action('admin_init', array($this, '_merge_data'));

            add_action('wp_ajax_wpbooking_autocomplete_post',array($this,'_autocomplete_post'));

            /**
             * Get header email template
             *
             * @author: tienhd
             * @since: 1.0
             */
            add_filter('wpbooking_header_email_template_html',array($this,'_get_header_email_template'));

            /**
             * Get header email template
             *
             * @author: tienhd
             * @since: 1.0
             */
            add_filter('wpbooking_footer_email_template_html',array($this,'_get_footer_email_template'));

            /**
             * Add field filter in list service
             *
             * @author: tienhd
             * @since: 1.0
             */
            add_action( 'restrict_manage_posts', array($this, '_service_filter_field'), 15 );
            add_filter( 'parse_query', array($this, '_service_filter_meta') );

            /**
             * Add More Columns Head to Manage Service Screen
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('manage_posts_columns',array($this,'_add_service_columns'));

            /**
             * Add Columns Content to Manage Service Screen
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('manage_posts_custom_column',array($this,'_add_service_columns_content'),10,2);

		}


        /**
         * Callback to Add More Columns Head to Manage Service Screen
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $columns
         * @return array
         */
		public function _add_service_columns($columns){

		    if($this->get('post_type')=='wpbooking_service'){
                $new=array();
                $new['wpbooking_service_type']=esc_html__('Type','wpbooking');

                $columns=array_slice($columns, 0, 1, true) +
                    $new +
                array_slice($columns, 1, count($columns) - 1, true) ;
            }

		    return $columns;
        }


        /**
         * Callback Add Columns Content to Manage Service Screen
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $column_name
         * @param $post_ID
         */

        public function _add_service_columns_content($column_name, $post_ID)
        {
            switch ($column_name){
                case "wpbooking_service_type":
                    $service=new WB_Service($post_ID);
                    echo esc_html($service->get_type_name());

                break;
            }

        }

		function _autocomplete_post()
        {
            $res=array();

            $type=$this->post('type');
            $args['post_type']=$type;
            $args['post_status']='publish';
            $args['s']=$this->post('q');
            $args['posts_per_page']=10;
            $args['post__not_in']=$this->post('post__not_in');

            $query=new WP_Query($args);

            while ($query->have_posts()){
                $query->the_post();
                $res[]=array(
                    'id'=>get_the_ID(),
                    'text'=>get_the_title(),
                    'thumb'=>get_the_post_thumbnail(),
                    'address'=>get_post_meta(get_the_ID(),'address',true)
                );
            }

            wp_reset_postdata();

            echo json_encode($res);die;
        }

		function _save_extra_field($post_id = FALSE)
		{
			if (get_post_type($post_id) != 'wpbooking_service') return FALSE;

			WPBooking_Service_Model::inst()->save_extra($post_id);

		}

		function _add_settings($settings)
		{
			$settings['services'] = array(
				'name'     => esc_html__("Services", 'wpbooking'),
				'sections' => apply_filters('wpbooking_service_setting_sections', array())
			);

			return $settings;
		}

		function _add_taxonomy()
		{

		}

		function _add_post_type()
		{
			$labels = array(
				'name'               => esc_html__('Service','wpbooking'),
				'singular_name'      => esc_html__('Service','wpbooking'),
				'menu_name'          => esc_html__('Services','wpbooking'),
				'name_admin_bar'     => esc_html__('Service','wpbooking'),
				'add_new'            => esc_html__('Add New','wpbooking'),
				'add_new_item'       => esc_html__('Add New Service', 'wpbooking'),
				'new_item'           => esc_html__('New Service', 'wpbooking'),
				'edit_item'          => esc_html__('Edit Service', 'wpbooking'),
				'view_item'          => esc_html__('View Service', 'wpbooking'),
				'all_items'          => esc_html__('All Services', 'wpbooking'),
				'search_items'       => esc_html__('Search for Services', 'wpbooking'),
				'parent_item_colon'  => esc_html__('Parent Services:', 'wpbooking'),
				'not_found'          => esc_html__('Not found services.', 'wpbooking'),
				'not_found_in_trash' => esc_html__('Not found services in Trash.', 'wpbooking')
			);

			$args = array(
				'labels'             => $labels,
				'description'        => esc_html__('Description.', 'wpbooking'),
				'public'             => TRUE,
				'publicly_queryable' => TRUE,
				'show_ui'            => TRUE,
				'show_in_menu'       => TRUE,
				'query_var'          => TRUE,
				'rewrite'            => array('slug' => 'service'),
				'capability_type'    => 'post',
				'has_archive'        => ($page_id = wpbooking_get_option('archive-page')) && get_post($page_id) ? get_page_uri($page_id) : 'all-services',
				'hierarchical'       => FALSE,
				//'menu_position'      => '59.9',
                'menu_icon'=>'dashicons-tickets-alt',
				'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
			);

			register_post_type('wpbooking_service', $args);


			// Default Taxonomy
			$labels = array(
				'name'              => esc_html__('Amenities',  'wpbooking'),
				'singular_name'     => esc_html__('Amenity', 'wpbooking'),
				'search_items'      => esc_html__('Search for Amenity', 'wpbooking'),
				'all_items'         => esc_html__('All Amenities', 'wpbooking'),
				'parent_item'       => esc_html__('Parent Amenity', 'wpbooking'),
				'parent_item_colon' => esc_html__('Parent Amenity:', 'wpbooking'),
				'edit_item'         => esc_html__('Edit Amenity', 'wpbooking'),
				'update_item'       => esc_html__('Update Amenity', 'wpbooking'),
				'add_new_item'      => esc_html__('Add New Amenity', 'wpbooking'),
				'new_item_name'     => esc_html__('New Amenity Name', 'wpbooking'),
				'menu_name'         => esc_html__('Amenity', 'wpbooking'),
			);

			$args = array(
				'hierarchical'      => TRUE,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => false,
				'query_var'         => TRUE,
				'rewrite'           => array('slug' => 'amenities'),
                'meta_box_cb'=>false
			);
			$args = apply_filters('wpbooking_register_amenity_taxonomy', $args);

			register_taxonomy('wpbooking_amenity', array('wpbooking_service'), $args);

			WPBooking_Assets::add_css("#wpbooking_amenitydiv{display:none!important}");


			// Extra Services
			$labels = array(
				'name'              => esc_html__('Extra Services',  'wpbooking'),
				'singular_name'     => esc_html__('Extra Service',  'wpbooking'),
				'search_items'      => esc_html__('Search Extra Services', 'wpbooking'),
				'all_items'         => esc_html__('All Extra Services', 'wpbooking'),
				'parent_item'       => esc_html__('Parent Extra Service', 'wpbooking'),
				'parent_item_colon' => esc_html__('Parent Extra Service:', 'wpbooking'),
				'edit_item'         => esc_html__('Edit Extra Service', 'wpbooking'),
				'update_item'       => esc_html__('Update Extra Service', 'wpbooking'),
				'add_new_item'      => esc_html__('Add New Extra Service', 'wpbooking'),
				'new_item_name'     => esc_html__('New Extra Service Name', 'wpbooking'),
				'menu_name'         => esc_html__('Extra Service', 'wpbooking'),
			);

			$args = array(
				'hierarchical'      => TRUE,
				'labels'            => $labels,
				'show_ui'           => TRUE,
				'show_admin_column' => false,
				'query_var'         => TRUE,
			);
			$args = apply_filters('wpbooking_register_extra_services_taxonomy', $args);

			register_taxonomy('wpbooking_extra_service', array('wpbooking_service'), $args);

			WPBooking_Assets::add_css("#wpbooking_extra_servicediv{display:none!important}");

			WPBooking_Taxonomy_Metabox::inst()->add_metabox(array(
				'id'       => 'extra_services_info',
				'taxonomy' => array('wpbooking_extra_service'),
				'fields'   => array(
					array(
						'type'     => 'service-type-checkbox',
						'id'       => 'service_type',
						'label'    => esc_html__('Extra Service', 'wpbooking'),
						'add_meta' => TRUE // ,
					)
				)
			));

		}

		function _add_metabox()
		{
			$metabox = WPBooking_Metabox::inst();

			$settings = array(
				'id'       => 'st_post_metabox',
				'title'    => esc_html__('Information', 'wpbooking'),
				'desc'     => '',
				'pages'    => array('wpbooking_service'),
				'context'  => 'normal',
				'priority' => 'high',

			);

			$metabox->register_meta_box($settings);
		}

		function _merge_data()
		{
			if ($this->get('wb_merge_data')) {
				$query = new WP_Query(array(
					'post_type'      => 'wpbooking_service',
					'posts_per_page' => 1000
				));

				while ($query->have_posts()) {
					$query->the_post();
					WPBooking_Service_Model::inst()->save_extra(get_the_ID());
				}
				wp_reset_postdata();
				echo 'done';
				die;
			}

			if ($this->get('wb_setup_term')) {
				do_action('wpbooking_do_setup');
			}
		}

        /**
         * Get header email html
         *
         * @since: 1.0
         *
         * @return bool|mixed|void
         */
        public function _get_header_email_template(){
            return wpbooking_get_option('email_header','');
        }

        /**
         * Get header email html
         *
         * @since: 1.0
         *
         * @return bool|mixed|void
         */
        public function _get_footer_email_template(){
            return wpbooking_get_option('email_footer','');
        }

        /**
         * Add filter field service type
         *
         * @param $post_type
         */
        function _service_filter_field($post_type) {
            if ( $post_type == 'wpbooking_service' ) {
                $service_types = WPBooking_Service_Controller::inst()->get_service_types();

                echo '<select name="service_type">';
                echo '<option value="0">'.esc_html__('All services','wpbooking').'</option>';
                foreach($service_types as $key => $val){
                    echo '<option '.selected(WPBooking_Input::get('service_type'),$key,false).' value="'.esc_attr($key).'">'.esc_html($val->get_info('label')).'</option>';
                }

                echo '</select>';

            }
        }

        /**
         * Add meta query for filter service type
         *
         * @param $query
         */
        function _service_filter_meta($query){
            if( is_admin() AND $query->query['post_type'] == 'wpbooking_service' ) {
                $query_vars = &$query->query_vars;
                $query_vars['meta_query'] = array();
                if(WPBooking_Input::get('service_type')){
                    $query_vars['meta_query'][] = array(
                        'field' => 'service_type',
                        'value' => WPBooking_Input::get('service_type'),
                        'type' => 'char',
                        'compare' => '='
                    );
                }
            }
        }

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}

	}

	WPBooking_Admin_Service::inst();
}