<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/7/2016
 * Time: 11:16 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if(!class_exists('WPBooking_Admin_Order'))
{
	class WPBooking_Admin_Order extends WPBooking_Controller
	{
		static $_inst;

		function __construct()
		{
			add_action('init',array($this,'_register_post_type'));
			add_action('add_meta_boxes',array($this,'_register_metabox'));

			add_filter('post_row_actions',array($this,'_add_post_row_actions'),10,2);
			add_action('admin_init',array($this,'_resend_email'));
		}

		/**
		 * Check and resend email booking
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _resend_email()
		{
			if(WPBooking_Input::get('post_type')=='wpbooking_order'
			and $order_id=WPBooking_Input::get('order_id')
			and WPBooking_Input::get('bravo_resend_email')
			){
				WPBooking_Email::inst()->_send_order_email_success($order_id);
				add_action('admin_notices',array($this,'_show_notice_email_success'));
			}
		}

		/**
		 * Show Admin Notice: Email send success
		 * @since 1.0
		 * @author dungdt
		 */
		function _show_notice_email_success()
		{
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Email Resend Success!', 'wpbooking' ); ?></p>
			</div>
			<?php
		}
		/**
		 * Filer to add row actions to order
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _add_post_row_actions($actions, $post)
		{
			if($post->post_type=='wpbooking_order'){
				$url=add_query_arg(array(
					'post_type'=>'wpbooking_order',
					'order_id'=>$post->ID,
					'bravo_resend_email'=>1,

				),admin_url('edit.php'));
				$actions['bravo_resend_email']='<a href="'.$url.'">'.esc_html__('Resend Booking Email','wpbooking').'</a>';
			}
			return $actions;
		}
		/**
		 * Register Metabox to show Order Information
		 * @author dungdt
		 * @since 1.0
		 */
		function _register_metabox()
		{
			add_meta_box('wpbooking_order_metabox',esc_html__('Order Information','wpbooking'),array($this,'_show_metabox'),'wpbooking_order','normal','high');
		}

		/**
		 * Callback function to show Order Metabox HTML
		 * @since 1.0
		 * @author dungdt
		 */
		function _show_metabox()
		{
			echo wpbooking_admin_load_view('order/detail');
		}

		function _register_post_type()
		{
			$menu_page = WPBooking()->get_menu_page();
			$labels = array(
				'name'               => _x( 'Booking', 'post type general name', 'wpbooking' ),
				'singular_name'      => _x( 'Booking', 'post type singular name', 'wpbooking' ),
				'menu_name'          => _x( 'Booking', 'admin menu', 'wpbooking' ),
				'name_admin_bar'     => _x( 'Booking', 'add new on admin bar', 'wpbooking' ),
				'add_new'            => _x( 'Add New', 'Booking', 'wpbooking' ),
				'add_new_item'       => __( 'Add New Booking', 'wpbooking' ),
				'new_item'           => __( 'New Booking', 'your-plugin-textdomain' ),
				'edit_item'          => __( 'Edit Booking', 'wpbooking' ),
				'view_item'          => __( 'View Booking', 'wpbooking' ),
				'all_items'          => __( 'All Booking', 'wpbooking' ),
				'search_items'       => __( 'Search Booking', 'wpbooking' ),
				'parent_item_colon'  => __( 'Parent Booking:', 'wpbooking' ),
				'not_found'          => __( 'No Booking found.', 'wpbooking' ),
				'not_found_in_trash' => __( 'No Booking found in Trash.', 'wpbooking' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Description.', 'wpbooking' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => $menu_page['menu_slug'],
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'booking' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				//'menu_position'      => '59.9',
				'supports'           => array( 'title',  'author' )
			);

			register_post_type( 'wpbooking_order', $args );
		}

		static function inst()
		{
			if(!self::$_inst)
			{
				self::$_inst=new self();
			}
			return self::$_inst;
		}
	}

	WPBooking_Admin_Order::inst();
}