<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 4/7/2016
 * Time: 11:16 AM
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Admin_Order')) {
	class WPBooking_Admin_Order extends WPBooking_Controller
	{
		static $_inst;

		function __construct()
		{
			add_action('init', array($this, '_register_post_type'));
			add_action('admin_menu', array($this, '_add_booking_menupage'));

			// Apply Changes to Order Items
			add_action('admin_init', array($this, '_apply_change_form'));

			add_action('add_meta_boxes', array($this, '_register_metabox'));

			add_filter('post_row_actions', array($this, '_add_post_row_actions'), 10, 2);
			add_action('admin_init', array($this, '_resend_email'));

			// Ajax for Jquery Fullcalendar
			add_action('wp_ajax_wpbooking_order_calendar', array($this, '_ajax_order_calendar'));

			add_action('admin_enqueue_scripts',array($this,'_add_script'));

		}

		function _apply_change_form()
		{

		}

		function _add_booking_menupage()
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


		/**
		 * @author dungdt
		 * @since 1.0
		 *
		 * @return mixed|void
		 */
		function get_menu_page()
		{
			$menu_page = WPBooking()->get_menu_page();
			$page = array(
				'parent_slug' => $menu_page['menu_slug'],
				'page_title'  => __('All Bookings', 'wpbooking'),
				'menu_title'  => __('All Bookings', 'wpbooking'),
				'capability'  => 'manage_options',
				'menu_slug'   => 'wpbooking_page_orders',
				'function'    => array($this, 'callback_menu_page')
			);

			return apply_filters('wpbooking_admin_order_menu_args', $page);

		}

		function callback_menu_page()
		{
			// Check Detail Page
			if ($id = WPBooking_Input::get('order_item_id')) {
				$order_item = WPBooking_Order_Model::inst()->find($id);

				if ($order_item) {

					$data['order_id'] = $order_item['order_id'];
					$data['order_item'] = $order_item;

					echo($this->admin_load_view('order/detail', $data));

					return;
				}


			}


			// Listing Page
			echo($this->admin_load_view('order/index'));

		}

		function _add_script()
		{
			if(WPBooking_Input::get('page')=='wpbooking_page_orders'){
				wp_enqueue_script('bootstrap');
				wp_enqueue_style('popover');
			}
		}
		function _ajax_order_calendar()
		{
			// Check Permission
			if(!current_user_can('publish_posts')){
				echo json_encode(array());
				die;
			}
			$order_model = WPBooking_Order_Model::inst();
			$wpbooking_booking_history=FALSE;

			// Filter
			if($filter_raw=WPBooking_Input::post('filter')){
				parse_str($filter_raw,$filter);

				if(!empty($filter['service_type'])){
					$order_model->where('service_type',$filter['service_type']);
				}
				if(!empty($filter['status'])){
					$order_model->where('status',$filter['status']);
				}
				if(!empty($filter['payment_status'])){
					$order_model->where('payment_status',$filter['payment_status']);
				}
				if(!empty($filter['keyword'])){
					$order_model->like('id',$filter['keyword']);
				}

				if(!empty($filter['wpbooking_booking_history'])){
					$order_model->where('customer_id',get_current_user_id());
					$wpbooking_booking_history=true;
				}
			}

			// Is Partner and not listing admin
			if(!current_user_can('manage_options') and !$wpbooking_booking_history ){
				$order_model->where('partner_id',get_current_user_id());
			}

			$result = $order_model->where('check_in_timestamp>=', WPBooking_Input::post('start'))
				->where('check_out_timestamp<=', WPBooking_Input::post('end'))
				->limit(500)
				->get()->result();

			$return = array();

			if (!empty($result)) {
				foreach ($result as $item) {
					$return[] = array(
						'id'              => $item['id'],
						'post_id'         => $item['post_id'],
						'start'           => date('Y-m-d', $item['check_in_timestamp']),
						'end'             => date('Y-m-d', $item['check_out_timestamp']),
						'status'          => $item['status'],
						'title'           => '#' . $item['id'] . ' - ' . get_the_title($item['post_id']),
						'backgroundColor' => wpbooking_order_item_status_color($item['status']),
						'borderColor' => wpbooking_order_item_status_color($item['status']),
						'tooltipContent'=>wpbooking_admin_load_view('order/calendar-popover',array('item'=>$item))
					);
				}
			}

			echo json_encode($return);
			die;
		}

		/**
		 * Check and resend email booking
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _resend_email()
		{
			if (WPBooking_Input::get('post_type') == 'wpbooking_order'
				and $order_id = WPBooking_Input::get('order_id')
				and WPBooking_Input::get('wpbooking_resend_email')
			) {
				WPBooking_Email::inst()->_send_order_email_success($order_id);
				add_action('admin_notices', array($this, '_show_notice_email_success'));
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
				<p><?php esc_html_e('Email Resend Success!', 'wpbooking'); ?></p>
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
			if ($post->post_type == 'wpbooking_order') {
				$url = add_query_arg(array(
					'post_type'          => 'wpbooking_order',
					'order_id'           => $post->ID,
					'wpbooking_resend_email' => 1,

				), admin_url('edit.php'));
				$actions['wpbooking_resend_email'] = '<a href="' . $url . '">' . esc_html__('Resend Booking Email', 'wpbooking') . '</a>';

				if (defined('WP_DEBUG') and WP_DEBUG) {
					$actions['wpbooking_test_email'] = '<a href="' . add_query_arg(array(
							'test_email' => '1',
							'post_id'    => $post->ID,

						), admin_url('edit.php')) . '">' . esc_html__('Test Partner Email', 'wpbooking') . '</a>';
				}

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
			add_meta_box('wpbooking_order_metabox', esc_html__('Order Information', 'wpbooking'), array($this, '_show_metabox'), 'wpbooking_order', 'normal', 'high');
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
				'name'               => _x('Booking', 'post type general name', 'wpbooking'),
				'singular_name'      => _x('Booking', 'post type singular name', 'wpbooking'),
				'menu_name'          => _x('Booking', 'admin menu', 'wpbooking'),
				'name_admin_bar'     => _x('Booking', 'add new on admin bar', 'wpbooking'),
				'add_new'            => _x('Add New', 'Booking', 'wpbooking'),
				'add_new_item'       => __('Add New Booking', 'wpbooking'),
				'new_item'           => __('New Booking', 'your-plugin-textdomain'),
				'edit_item'          => __('Edit Booking', 'wpbooking'),
				'view_item'          => __('View Booking', 'wpbooking'),
				'all_items'          => __('All Booking', 'wpbooking'),
				'search_items'       => __('Search Booking', 'wpbooking'),
				'parent_item_colon'  => __('Parent Booking:', 'wpbooking'),
				'not_found'          => __('No Booking found.', 'wpbooking'),
				'not_found_in_trash' => __('No Booking found in Trash.', 'wpbooking')
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __('Description.', 'wpbooking'),
				'public'             => TRUE,
				'publicly_queryable' => TRUE,
				'show_ui'            => FALSE,
				'show_in_menu'       => $menu_page['menu_slug'],
				'query_var'          => TRUE,
				'rewrite'            => array('slug' => 'booking'),
				'capability_type'    => 'post',
				'has_archive'        => TRUE,
				'hierarchical'       => FALSE,
				//'menu_position'      => '59.9',
				'supports'           => array('title', 'author')
			);

			register_post_type('wpbooking_order', $args);

			// Register The Order Status
			$all_status=WPBooking_Config::inst()->item('order_status');
			if(!empty($all_status)){
				foreach($all_status as $key=>$value){
					register_post_status( $key, array(
						'label'                     =>$value['label'],
						'public'                    => true,
						'exclude_from_search'       => true,
						'show_in_admin_all_list'    => false,
						'show_in_admin_status_list' => false,
					) );
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

	WPBooking_Admin_Order::inst();
}