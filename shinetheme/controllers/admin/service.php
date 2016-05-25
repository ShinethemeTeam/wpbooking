<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/11/2016
 * Time: 12:44 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(!class_exists('Traveler_Admin_Service'))
{
	class Traveler_Admin_Service extends Traveler_Controller
	{
		private static $_inst;

		function __construct()
		{
			add_action('init',array($this,'_add_taxonomy'));
			add_action('init',array($this,'_add_post_type'),5);
			add_action('init',array($this,'_add_metabox'));
			add_action('save_post',array($this,'_save_extra_field'));
			add_filter('traveler_booking_settings',array($this,'_add_settings'));
		}


		function _save_extra_field($post_id=FALSE)
		{
			if(get_post_type($post_id)!='traveler_service') return false;

			$service_model=Traveler_Service_Model::inst();

			$data=array(
				'map_lat'=>get_post_meta($post_id,'map_lat',true)
			);

			if(!$service_model->find_by('post_id',$post_id)){
				$service_model->insert($data);
			}else{
				$service_model->where('post_id',$post_id)->update($data);
			}
		}

		function _add_settings($settings)
		{
			$settings['services']=array(
				'name'=>__("Services",'wpbooking'),
				'sections'=>apply_filters('traveler_service_setting_sections',array())
			);
			return $settings;
		}

		function _add_taxonomy(){

		}
		function _add_post_type()
		{
			$labels = array(
				'name'               => _x( 'Service', 'post type general name', 'wpbooking' ),
				'singular_name'      => _x( 'Service', 'post type singular name', 'wpbooking' ),
				'menu_name'          => _x( 'Services', 'admin menu', 'wpbooking' ),
				'name_admin_bar'     => _x( 'Service', 'add new on admin bar', 'wpbooking' ),
				'add_new'            => _x( 'Add New', 'service', 'wpbooking' ),
				'add_new_item'       => __( 'Add New Service', 'wpbooking' ),
				'new_item'           => __( 'New Service', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Service', 'wpbooking' ),
				'view_item'          => __( 'View Service', 'wpbooking' ),
				'all_items'          => __( 'All Services', 'wpbooking' ),
				'search_items'       => __( 'Search Services', 'wpbooking' ),
				'parent_item_colon'  => __( 'Parent Services:', 'wpbooking' ),
				'not_found'          => __( 'No services found.', 'wpbooking' ),
				'not_found_in_trash' => __( 'No services found in Trash.', 'wpbooking' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'wpbooking' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'service' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				//'menu_position'      => '59.9',
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			);

			register_post_type( 'traveler_service', $args );
		}

		function _add_metabox()
		{
			$metabox = Traveler_Metabox::inst();

			$settings = array(
				'id'       => 'st_post_metabox',
				'title'    => __('Information', 'wpbooking'),
				'desc'     => '',
				'pages'    => array('post','traveler_service'),
				'context'  => 'normal',
				'priority' => 'high',
				'fields'   => array(
					array(
						'label' => __('General', 'wpbooking'),
						'id' => 'general_tab',
						'type' => 'tab',
					),array(
						'id'=>'service_type',
						'label'=>__("Service Type",'wpbooking'),
						'location'=>'hndle-tag',
						'type'=>'service-type-select',
					),

					array(
						'label' => __('Location', 'wpbooking'),
						'id' => 'location',
						'type' => 'location'
					),
					array(
						'label' => __('Map', 'wpbooking'),
						'id' => 'gmap',
						'type' => 'gmap'
					),
					array(
						'label' => __('Gallery', 'wpbooking'),
						'id' => 'gallery',
						'type' => 'gallery'
					),
					array(
						'label' => __('Accommodates', 'wpbooking'),
						'id' => 'accommodates',
						'type' => 'text'
					),
					array(
						'label' => __('Bathrooms', 'wpbooking'),
						'id' => 'bathrooms',
						'type' => 'text'
					),
					array(
						'label' => __('Check-in Time', 'wpbooking'),
						'id' => 'check_in_time',
						'type' => 'text',
						'class' => 'time-picker'
					),
					array(
						'label' => __('Check-out Time', 'wpbooking'),
						'id' => 'check_out_time',
						'type' => 'text',
						'class' => 'time-picker'
					),
					array(
						'label' => __('No. Adult', 'wpbooking'),
						'id' => 'number_adult',
						'type' => 'number',
					),
					array(
						'label' => __('No. Children', 'wpbooking'),
						'id' => 'number_children',
						'type' => 'number',
					),
					array(
						'label' => __('External Booking?', 'wpbooking'),
						'id' => 'external_booking',
						'type' => 'checkbox',
						'value' => array(
							'yes' => __('Yes', 'wpbooking')
						),
					),
					array(
						'label' => __('External URL', 'wpbooking'),
						'id' => 'external_url',
						'type' => 'text',
						'condition' => 'external_booking:is(yes)'
					),
//                array(
//                    'label' => __('Instant Booking?', 'wpbooking'),
//                    'id' => 'instant_booking',
//                    'type' => 'checkbox',
//                    'value' => array(
//                        'yes' => __('Yes', 'wpbooking')
//                    ),
//                ),
					array(
						'label' => __('Day not available from - to days', 'wpbooking'),
						'id' => 'day_not_available',
						'type' => 'text',
					),
					array(
						'label' => __('Preparations', 'wpbooking'),
						'id' => 'preparations',
						'type' => 'text',
					),
					array(
						'label' => __('Amenities', 'wpbooking'),
						'id'    => 'amelities_tab',
						'type'  => 'tab',
					),
					array(
						'label' => __('Taxonomy', 'wpbooking'),
						'id' => 'taxonomy',
						'type' => 'taxonomies',
					),
					array(
						'label' => __('Pricing', 'wpbooking'),
						'id'    => 'price_tab',
						'type'  => 'tab',
					),

					array(
						'label' => __('Base Price', 'wpbooking'),
						'id' => 'price',
						'type' => 'text',
					),
//					array(
//						'label' => __('Currency', 'wpbooking'),
//						'id' => 'currency',
//						'type' => 'dropdown',
//						'std' => 'usd',
//						'value' =>Traveler_Currency::get_added_currency_array()
//					),
					array(
						'label' => __('Price Type', 'wpbooking'),
						'id' => 'price_type',
						'type' => 'dropdown',
						'value' => array(
							'fixed' => __('Fixed', 'wpbooking'),
							'per_night' => __('Per Night', 'wpbooking'),
						),
					),
//					array(
//						'label' => __('Long Terms?', 'wpbooking'),
//						'id' => 'long_terms',
//						'type' => 'checkbox',
//						'value' => array(
//							'yes' => __('Yes', 'wpbooking')
//						),
//					),
//					array(
//						'label' => __('Weekly Discount', 'wpbooking'),
//						'id' => 'weekly_discount',
//						'type' => 'text',
//						'condition' => 'long_terms:is(yes)'
//					),
//					array(
//						'label' => __('Monthly Discount', 'wpbooking'),
//						'id' => 'monthly_discount',
//						'type' => 'text',
//						'condition' => 'long_terms:is(yes)'
//					),
					array(
						'label' => __('Extra Price', 'wpbooking'),
						'id' => 'extra_price',
						'type' => 'list-item',
						'value' => array(
							array(
								'id'    => 'price',
								'label' => __('Price', 'wpbooking'),
								'type'  => 'text',
							),
							array(
								'label' => __('Type', 'wpbooking'),
								'id' => 'type',
								'type' => 'dropdown',
								'value' => array(
									'fixed' => __('Fixed', 'wpbooking'),
									'per_night' => __('Per Night', 'wpbooking'),
								),
							),
						)
					),
					array(
						'label' => __('Allow Deposit?', 'wpbooking'),
						'id' => 'deposit',
						'type' => 'dropdown',
						'value' => array(
							'none' => __('None', 'wpbooking'),
							'percent' => __('Percent', 'wpbooking'),
							'fixed' => __('Fixed', 'wpbooking'),
						),
					),
					array(
						'label' => __('Deposit Amount', 'wpbooking'),
						'id' => 'deposit_amount',
						'type' => 'text',
						''
					),
					array(
						'label' => __('Calendar', 'wpbooking'),
						'id'    => 'calendar_tab',
						'type'  => 'tab',
					),
					array(
						'label' => __('Availability', 'wpbooking'),
						'id'    => 'availability',
						'type'  => 'calendar',
						'service_type' => 'room'
					),

				)
			);

			$metabox->register_meta_box( $settings );
		}

		static function inst()
		{
			if(!self::$_inst){
				self::$_inst=new self();
			}
			return self::$_inst;
		}


	}

	Traveler_Admin_Service::inst();
}