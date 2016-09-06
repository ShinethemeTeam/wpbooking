<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 12:44 PM
 */
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
			// Ajax save property

			// Merge Data
			add_action('admin_init', array($this, '_merge_data'));

            add_action('wp_ajax_wpbooking_autocomplete_post',array($this,'_autocomplete_post'));
		}

		function _autocomplete_post()
        {
            $res=array();

            $type=$this->post('type');
            $args['post_type']=$type;
            $args['s']=$this->post('q');
            $args['posts_per_page']=10;

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
				'name'     => __("Services", 'wpbooking'),
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
				'name'               => _x('Service', 'post type general name', 'wpbooking'),
				'singular_name'      => _x('Service', 'post type singular name', 'wpbooking'),
				'menu_name'          => _x('Services', 'admin menu', 'wpbooking'),
				'name_admin_bar'     => _x('Service', 'add new on admin bar', 'wpbooking'),
				'add_new'            => _x('Add New', 'service', 'wpbooking'),
				'add_new_item'       => __('Add New Service', 'wpbooking'),
				'new_item'           => __('New Service', 'wpbooking'),
				'edit_item'          => __('Edit Service', 'wpbooking'),
				'view_item'          => __('View Service', 'wpbooking'),
				'all_items'          => __('All Services', 'wpbooking'),
				'search_items'       => __('Search Services', 'wpbooking'),
				'parent_item_colon'  => __('Parent Services:', 'wpbooking'),
				'not_found'          => __('No services found.', 'wpbooking'),
				'not_found_in_trash' => __('No services found in Trash.', 'wpbooking')
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __('Description.', 'wpbooking'),
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
				'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
			);

			register_post_type('wpbooking_service', $args);


			// Default Taxonomy
			$labels = array(
				'name'              => _x('Amenities', 'taxonomy general name', 'wpbooking'),
				'singular_name'     => _x('Amenity', 'taxonomy singular name', 'wpbooking'),
				'search_items'      => __('Search Amenity', 'wpbooking'),
				'all_items'         => __('All Amenities', 'wpbooking'),
				'parent_item'       => __('Parent Amenity', 'wpbooking'),
				'parent_item_colon' => __('Parent Amenity:', 'wpbooking'),
				'edit_item'         => __('Edit Amenity', 'wpbooking'),
				'update_item'       => __('Update Amenity', 'wpbooking'),
				'add_new_item'      => __('Add New Amenity', 'wpbooking'),
				'new_item_name'     => __('New Amenity Name', 'wpbooking'),
				'menu_name'         => __('Amenity', 'wpbooking'),
			);

			$args = array(
				'hierarchical'      => TRUE,
				'labels'            => $labels,
				'show_ui'           => TRUE,
				'show_admin_column' => TRUE,
				'query_var'         => TRUE,
				'rewrite'           => array('slug' => 'amenities'),
			);
			$args = apply_filters('wpbooking_register_amenity_taxonomy', $args);

			register_taxonomy('wpbooking_amenity', array('wpbooking_service'), $args);

			WPBooking_Assets::add_css("#wpbooking_amenitydiv{display:none!important}");

			WPBooking_Taxonomy_Metabox::inst()->add_metabox(array(
				'id'       => 'amenity_information',
				'taxonomy' => array('wpbooking_amenity'),
				'fields'   => array(
					array(
						'type'  => 'icon',
						'id'    => 'icon',
						'label' => esc_html__('Icon', 'wpbooking')
					)
				)
			));

			// Extra Services
			$labels = array(
				'name'              => _x('Extra Services', 'taxonomy general name', 'wpbooking'),
				'singular_name'     => _x('Extra Service', 'taxonomy singular name', 'wpbooking'),
				'search_items'      => __('Search Extra Services', 'wpbooking'),
				'all_items'         => __('All Extra Services', 'wpbooking'),
				'parent_item'       => __('Parent Extra Service', 'wpbooking'),
				'parent_item_colon' => __('Parent Extra Service:', 'wpbooking'),
				'edit_item'         => __('Edit Extra Service', 'wpbooking'),
				'update_item'       => __('Update Extra Service', 'wpbooking'),
				'add_new_item'      => __('Add New Extra Service', 'wpbooking'),
				'new_item_name'     => __('New Extra Service Name', 'wpbooking'),
				'menu_name'         => __('Extra Service', 'wpbooking'),
			);

			$args = array(
				'hierarchical'      => TRUE,
				'labels'            => $labels,
				'show_ui'           => TRUE,
				'show_admin_column' => TRUE,
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
				'title'    => __('Information', 'wpbooking'),
				'desc'     => '',
				'pages'    => array('wpbooking_service'),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => array(
					array(
						'label' => __('1. About /', 'wpbooking'),
						'id'    => 'general_tab',
						'type'  => 'tab',
					),

					array(
						'label' => __("About Your Property", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'id'    => 'service_type',
						'label' => __("Service Type", 'wpbooking'),
						'type'  => 'service-type-select',
					),
					array(
						'id'    => 'enable_property',
						'label' => __("Enable Property", 'wpbooking'),
						'type'  => 'on-off',
						'std'   => 'on',
						'desc'  => esc_html__('Listing will appear in search results.', 'wpbooking'),
					),
					array(
						'label' => __('Bedrooms', 'wpbooking'),
						'id'    => 'bedroom',
						'type'  => 'dropdown',
						'value' => array(
							1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label' => __('Bathrooms', 'wpbooking'),
						'id'    => 'bathrooms',
						'type'  => 'dropdown',
						'value' => array(
							1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label' => __('Max Guests', 'wpbooking'),
						'id'    => 'max_guests',
						'type'  => 'dropdown',
						'value' => array(
							1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label' => esc_html__('External Booking URL', 'wpbooking'),
						'id'    => 'external_booking_url',
						'type'  => 'text'
					),
					array(
						'label' => __("Property Location", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'label' => __('Map Lat & Long', 'wpbooking'),
						'id'    => 'gmap',
						'type'  => 'gmap'
					),
					array(
						'label'           => __('Address', 'wpbooking'),
						'id'              => 'address',
						'type'            => 'address',
						'container_class' => 'mb35'
					),
					array(
						'label'        => __("Rate & Availability", 'wpbooking'),
						'type'         => 'title',
						'help_popover' => esc_html__('eg. If your nightly rate is 110USD, and your weekly rate is 700USD, a 3 night stay will cost 330USD, a 7 night stay will cost 700USD, and a 10 night stay will cost 1000USD (700 / 7 * 10).', 'wpbooking')
					),
					array(
						'label' => __("Nightly Rate", 'wpbooking'),
						'type'  => 'money_input',
						'id'    => 'price',
						'class' => 'small'
					),
					array(
						'label' => __("Weekly Rate", 'wpbooking'),
						'type'  => 'money_input',
						'id'    => 'weekly_rate',
						'class' => 'small'
					),
					array(
						'label'           => __("Monthly Rate", 'wpbooking'),
						'type'            => 'money_input',
						'id'              => 'monthly_rate',
						'class'           => 'small',
						'container_class' => 'mb35'
					),
					array(
						'label' => __("Additional Guests / Taxes/ Misc", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'label' => __('Allowed', 'wpbooking'),
						'type'  => 'on-off',
						'id'    => 'enable_additional_guest_tax',
						'std'   => 'off'
					),
					array(
						'label'       => __("Rates are based on occupancy of", 'wpbooking'),
						'type'        => 'text',
						'id'          => 'rate_based_on',
						'class'       => 'small',
						'help_inline' => esc_html__('guest(s)', 'wpbooking')
					),
					array(
						'label'       => __("Each additional guest will pay", 'wpbooking'),
						'type'        => 'money_input',
						'id'          => 'additional_guest_money',
						'class'       => 'small',
						'help_inline' => esc_html__('/night', 'wpbooking')
					),
					array(
						'label' => __("Tax (%)", 'wpbooking'),
						'type'  => 'text',
						'id'    => 'tax',
						'class' => 'small'
					),
					array(
						'type' => 'section_navigation',
						'prev' => FALSE
					),
					array(
						'label' => __('2. Details /', 'wpbooking'),
						'id'    => 'detail_tab',
						'type'  => 'tab',
					),
					array(
						'label' => __("More About Your Property", 'wpbooking'),
						'type'  => 'title',
					),

					array(
						'label' => __('Amenities', 'wpbooking'),
						'id'    => 'wpb_taxonomy',
						'type'  => 'taxonomies',
					),
					array(
						'label' => __('Double Bed', 'wpbooking'),
						'id'    => 'double_bed',
						'type'  => 'dropdown',
						'value' => array(
							1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label' => __('Single Bed', 'wpbooking'),
						'id'    => 'single_bed',
						'type'  => 'dropdown',
						'value' => array(
							0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label' => __('Sofa Bed', 'wpbooking'),
						'id'    => 'sofa_bed',
						'type'  => 'dropdown',
						'value' => array(
							0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label' => __('Property Floor', 'wpbooking'),
						'id'    => 'property_floor',
						'type'  => 'dropdown',
						'value' => array(
							1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20
						),
						'class' => 'small'
					),
					array(
						'label'           => __('Property Size', 'wpbooking'),
						'id'              => 'property_size',
						'type'            => 'property_size',
						'unit_id'         => 'property_unit',
						'container_class' => 'mb35'
					),
					array(
						'label' => __("Extra Services:", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'label' => __("Extra Services:", 'wpbooking'),
						'type'  => 'extra_services',
						'id'    => 'extra_services'
					),

					array(
						'type' => 'section_navigation',
					),
					array(
						'label' => __('3. Policies /', 'wpbooking'),
						'id'    => 'policies_tab',
						'type'  => 'tab',
					),
					array(
						'label' => __("Property Policies", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'label' => __('Deposit Type', 'wpbooking'),
						'id'    => 'deposit_type',
						'type'  => 'dropdown',
						'value' => array(
							'value'   => __('Value', 'wpbooking'),
							'percent' => __('Percent', 'wpbooking'),
						),
						'class' => 'small'
					),
					array(
						'label' => __('Deposit Amount', 'wpbooking'),
						'id'    => 'deposit_amount',
						'type'  => 'money_input',
						'class' => 'small'
					),
					array(
						'label' => __('Minimum Stay', 'wpbooking'),
						'id'    => 'minimum_stay',
						'type'  => 'dropdown',
						'value' => array(
							1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30
						),
						'class' => 'small'
					),
					array(
						'label'           => __('Cancellation Allowed', 'wpbooking'),
						'id'              => 'cancellation_allowed',
						'type'            => 'on-off',
						'std'             => 1,
						'container_class' => 'mb35'
					),
					array(
						'label' => __('Terms & Conditions', 'wpbooking'),
						'id'    => 'terms_conditions',
						'type'  => 'textarea',
					),
					array(
						'label' => __("Host's Regulations", 'wpbooking'),
						'id'    => 'host_regulations',
						'type'  => 'list-item',
						'value' => array(
//							array(
//								'id'=>'icon',
//								'label'=>esc_html__('Visual Icon','wpbooking'),
//								'type'=>'icon_select'
//							),
							array(
								'id'    => 'content',
								'label' => esc_html__('Content', 'wpbooking'),
								'type'  => 'textarea'
							),
						)
					),
					array(
						'label' => __("Check In & Check Out", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'label' => __('Instructions', 'wpbooking'),
						'id'    => 'check_in_out_instructions',
						'type'  => 'textarea',
					),
					array(
						'label' => __('Check In Time', 'wpbooking'),
						'id'    => 'check_in_time',
						'type'  => 'time_select',
					),
					array(
						'label' => __('Check Out Time', 'wpbooking'),
						'id'    => 'check_out_time',
						'type'  => 'time_select',
					),
					array(
						'label' => __("Cancellation Policies", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'type' => 'cancellation_policies_text',
					),
					array(
						'type' => 'section_navigation',
					),
					array(
						'label' => __('4. Photos /', 'wpbooking'),
						'id'    => 'photo_tab',
						'type'  => 'tab',
					),
					array(
						'label' => __("Pictures", 'wpbooking'),
						'type'  => 'title',
					),
					array(
						'label' => __("Gallery", 'wpbooking'),
						'id'    => 'gallery',
						'type'  => 'gallery',
						'desc'  => __('Picture recommendations

				We recommend having pictures in the following order (if available):

				Living area
				Bedroom(s)
				Kitchen
				View from the apartment/house
				Exterior of apartment/building
				Please no generic pictures of the city
				Pictures showing animals, people, watermarks, logos and images composed of multiple
				smaller images will be removed.', 'wpbooking')
					),

					array(
						'type' => 'section_navigation',
					),
					array(
						'label' => __('5. Calendar', 'wpbooking'),
						'id'    => 'calendar_tab',
						'type'  => 'tab',
					),
					array(
						'type'  => 'title',
						'label' => esc_html__('Availability Template', 'wpbooking')
					),
					array(
						'id'   => 'calendar',
						'type' => 'calendar'
					)
				)
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