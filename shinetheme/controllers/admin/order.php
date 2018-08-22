<?php
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
            if($status = WPBooking_Input::get('action') and $order_ids = WPBooking_Input::get('wpbooking_order_item')) {
                $this->apply_status_changed($status,$order_ids);
            }
		}

        function apply_status_changed($status, $order_ids){
            $order = WPBooking_Order_Model::inst();
            if (is_array($order_ids)) {
                foreach($order_ids as $key => $val){
                    $this->apply_status_changed($status, $val);
                }
                return true;
            }

            switch ($status) {
                case 'onhold_booking':
                    $order->update_status($order_ids,'on_hold');
                    break;
                case 'complete_booking':
                    $order->update_status($order_ids,'completed');
                    break;
                case 'cancel_booking':
                    $order->update_status($order_ids,'cancelled');
                    break;
                case 'refunded_booking':
                    $order->update_status($order_ids,'refunded');
                    break;
                case 'cancel':
                    $order->update_status($order_ids,'cancel');
                    break;
                case 'permanently_delete':
					if(current_user_can('manage_options')){
						$order->delete_order($order_ids);
						do_action('wpbooking_delete_orders',$order_ids);
					}
                    break;
            }
            do_action('wpbooking_order_item_changed',$order_ids,$status);
        }

		/**
		 * Get Report Data for Report Tab
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return array
		 */
		function get_report_data(){
			$res=array();
			$date_from=$this->get('date_from');
			$date_to=$this->get('date_to');
			$res['chart_data']['labels']=array(esc_html__("January",'wp-booking-management-system'), esc_html__("February",'wp-booking-management-system'), esc_html__("March",'wp-booking-management-system'), esc_html__("April",'wp-booking-management-system'), esc_html__("May",'wp-booking-management-system'), esc_html__("June",'wp-booking-management-system'),esc_html__("July",'wp-booking-management-system'),esc_html__("August",'wp-booking-management-system'),esc_html__("September",'wp-booking-management-system'),esc_html__("October",'wp-booking-management-system'),esc_html__("November",'wp-booking-management-system'),esc_html__("December",'wp-booking-management-system'));

			if($date_from and $date_to){

			}

			$res['chart_data'];

			return $res;
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
				'page_title'  => esc_html__('All Bookings', 'wp-booking-management-system'),
				'menu_title'  => esc_html__('All Bookings', 'wp-booking-management-system'),
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
				wp_enqueue_script('wpbooking-bootstrap');
				wp_enqueue_style('wpbooking-popover');
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
			if (WPBooking_Input::get('page') == 'wpbooking_page_orders'
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
				<p><?php echo esc_html__('Email is resent successfully', 'wp-booking-management-system'); ?></p>
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
				$actions['wpbooking_resend_email'] = '<a href="' . esc_url($url) . '">' . esc_html__('Resend Booking Email', 'wp-booking-management-system') . '</a>';

				if (defined('WP_DEBUG') and WP_DEBUG) {
					$actions['wpbooking_test_email'] = '<a href="' . add_query_arg(array(
							'test_email' => '1',
							'post_id'    => $post->ID,

						), admin_url('edit.php')) . '">' . esc_html__('Test Partner Email', 'wp-booking-management-system') . '</a>';
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
			add_meta_box('wpbooking_order_metabox', esc_html__('Order Information', 'wp-booking-management-system'), array($this, '_show_metabox'), 'wpbooking_order', 'normal', 'high');
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
				'name'               => esc_html__('Booking', 'wp-booking-management-system'),
				'singular_name'      => esc_html__('Booking', 'wp-booking-management-system'),
				'menu_name'          => esc_html__('Booking', 'wp-booking-management-system'),
				'name_admin_bar'     => esc_html__('Booking', 'wp-booking-management-system'),
				'add_new'            => esc_html__('Add New', 'wp-booking-management-system'),
				'add_new_item'       => esc_html__('Add New Booking', 'wp-booking-management-system'),
				'new_item'           => esc_html__('New Booking', 'wp-booking-management-system'),
				'edit_item'          => esc_html__('Edit Booking', 'wp-booking-management-system'),
				'view_item'          => esc_html__('View Booking', 'wp-booking-management-system'),
				'all_items'          => esc_html__('All Bookings', 'wp-booking-management-system'),
				'search_items'       => esc_html__('Search for Booking', 'wp-booking-management-system'),
				'parent_item_colon'  => esc_html__('Parent Booking:', 'wp-booking-management-system'),
				'not_found'          => esc_html__('Not found Booking.', 'wp-booking-management-system'),
				'not_found_in_trash' => esc_html__('Not found Booking in Trash.', 'wp-booking-management-system')
			);

			$args = array(
				'labels'             => $labels,
				'description'        => esc_html__('Description.', 'wp-booking-management-system'),
				'public'             => TRUE,
				'publicly_queryable' => TRUE,
				'show_ui'            => FALSE,
				'show_in_menu'       => $menu_page['menu_slug'],
				'query_var'          => TRUE,
				'rewrite'            => array('slug' => 'booking'),
				'capability_type'    => 'post',
				'has_archive'        => TRUE,
				'hierarchical'       => FALSE,
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

        /**
         * Get time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param string $range
         * @param bool $start_date
         * @param bool $end_date
         * @return array
         */
        public function get_time_range($range = '',$start_date = false, $end_date = false){

            $data_range = array();
            switch($range){
                case 'this_year':
                    $current_date = strtotime('now');
                    $this_year = date('Y');
                    $start = strtotime($this_year.'-01-01');
                    while($start < $current_date){
                        $data_range['label'][] = date('F', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 month',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$current_date);
                    break;
                case 'last_year':
                    $last_year = date('Y',strtotime('-1 year'));
                    $start = strtotime($last_year.'-01-01');
                    $end = strtotime($last_year.'-12-31');
                    while($start < $end){
                        $data_range['label'][] = date('F', $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 month',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$end);
                    break;
                case 'today':
                    $start = strtotime('now midnight');
                    $end = strtotime('+1 day',$start);
                    $inti = 0;
                    while($start < $end){
                        $data_range['label'][] = $inti.':00';
                        $data_range['range'][] = $start;
                        $start = $start + 3600;
                        $inti++;
                    }
                    $data_range['range'][]= $end;
                    break;
                case 'yesterday':
                    $end = strtotime('now midnight');
                    $start = strtotime('-1 day',$end);
                    $inti = 0;
                    while($start < $end){
                        $data_range['label'][] = $inti.':00';
                        $data_range['range'][] = $start;
                        $start = $start + 3600;
                        $inti++;
                    }
                    $data_range['range'][]= $end;
                    break;
                case 'this_week':
                    $current_date = strtotime('now');
                    $start = strtotime('monday this week midnight',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$current_date);
                    break;
                case 'last_week':
                    $current_date = strtotime('now');
                    $monday_this_week = strtotime('monday this week midnight',$current_date);
                    $start = strtotime('-1 week', $monday_this_week);
                    $end = strtotime('+6 days', $start);
                    while($start <= $end){
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= $end;
                    break;
                case 'last_7days':
                    $current_date = strtotime('now');
                    $start = strtotime('-1 week midnight',$current_date);
                    $start = strtotime('+1 day',$start);
                    while($start <= $current_date){
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$current_date);
                    break;
                case 'last_30days':
                    $current_date = strtotime('now');
                    $start = strtotime('-30 days midnight',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$current_date);
                    break;
                case 'last_60days':
                    $current_date = strtotime('now');
                    $start = strtotime('-60 days midnight',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$current_date);
                    break;
                case 'last_90days':
                    $current_date = strtotime('now');
                    $start = strtotime('-90 days midnight',$current_date);
                    while($start <= $current_date){
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day',$start);
                    }
                    $data_range['range'][]= strtotime('+1 day midnight',$current_date);
                    break;
                default:
                    break;
            }
            if(!empty($start_date) && !empty($end_date)){
                $start = strtotime($start_date);
                $end = strtotime($end_date);
                if($start > $end){
                    $data_range['label'][] = date(get_option('date_format'), $start);
                    $data_range['range'][] = $start;
                    $data_range['range'][] = strtotime('+1 day', $start);
                }else {
                    while ($start <= $end) {
                        $data_range['label'][] = date(get_option('date_format'), $start);
                        $data_range['range'][] = $start;
                        $start = strtotime('+1 day', $start);
                    }
                    $data_range['range'][] = strtotime('+1 day midnight', $end);
                }
            }
            return $data_range;
        }

        /**
         * Get total data in time range
         *
         * @author tienhd
         * @since 1.0
         *
         * @param $service_type
         * @param $select
         * @param $range
         * @param bool $start_date
         * @param bool $end_date
         * @return string
         */
        public function total_in_time_range($service_type, $select ,$range, $start_date = false, $end_date = false , $author_id = false)
        {

            $time_range = $this->get_time_range($range, $start_date, $end_date);
            $res = '0';


            if (!empty($time_range['range'])) {
                switch($select){
                    case 'total_sale':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_sale($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1],$author_id);
                        break;
                    case 'net_profit':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_net_profit($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1],$author_id);
                        break;
                    case 'items':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_items($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1],$author_id);
                        break;
                    case 'total_bookings':
                        $res = WPBooking_Order_Model::inst()->get_rp_total_bookings($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1],$author_id);
                        break;
                    case 'completed':
                    case 'completed_a_part':
                    case 'on_hold':
                    case 'cancelled':
                    case 'refunded':
                        $res = WPBooking_Order_Model::inst()->get_rp_items_by_status($service_type,$time_range['range'][0],$time_range['range'][count($time_range['range'])-1],$select,$author_id);
                        break;
                }
            }

            return $res;
        }

        /**
         * Get total sale in time range
         *
         * @param $service_type
         * @param $range
         * @param bool $start_date
         * @param bool $end_date
         * @return array
         */
        public function get_total_sale_in_time_range($service_type ,$range, $start_date = false, $end_date = false , $author_id = false){
            $time_range = $this->get_time_range($range, $start_date, $end_date);
            $res = array();
            if(!empty($time_range['range'])){
                $res['label'] = $time_range['label'];
                foreach ($time_range['label'] as $key => $value) {
                    $res['data'][] = WPBooking_Order_Model::inst()->get_rp_total_sale($service_type ,$time_range['range'][$key],$time_range['range'][$key+1],$author_id);
                }
            }
            $decimal = WPBooking_Currency::get_current_currency('decimal');
            if(!empty($res['data'])){
                foreach($res['data'] as $key => $val){
                    $res['data'][$key] = number_format($val,$decimal,'.','');
                }
            }
            return $res;

        }
        /**
         * Get net profit in time range
         *
         * @param $service_type
         * @param $range
         * @param bool $start_date
         * @param bool $end_date
         * @return array
         */
        public function get_net_profit_in_time_range($service_type ,$range, $start_date = false, $end_date = false , $author_id = false){
            $time_range = $this->get_time_range($range, $start_date, $end_date);
            $res = array();
            if(!empty($time_range['range'])){
                foreach ($time_range['label'] as $key => $value) {
                    $res[] = WPBooking_Order_Model::inst()->get_rp_total_net_profit($service_type ,$time_range['range'][$key],$time_range['range'][$key+1],$author_id);
                }
            }
            $decimal = WPBooking_Currency::get_current_currency('decimal');
            if(!empty($res)){
                foreach($res as $key => $val){
                    $res[$key] = number_format($val,$decimal,'.','');
                }
            }
            return $res;
        }

        /**
         * Get total item booking by status order
         *
         * @param $service_type
         * @param $range
         * @param $status
         * @param bool $start_date
         * @param bool $end_date
         * @return array
         */
        public function get_items_booking_by_status($service_type ,$range, $status, $start_date = false, $end_date = false , $author_id = false){
            $time_range = $this->get_time_range($range, $start_date, $end_date);
            $res = array();
            if(!empty($time_range['range'])){
                foreach ($time_range['label'] as $key => $value) {
                    $res[] = WPBooking_Order_Model::inst()->get_rp_items_by_status($service_type ,$time_range['range'][$key],$time_range['range'][$key+1],$status,$author_id);
                }
            }
            if(!empty($res)){
                foreach($res as $key => $val){
                    $res[$key] = (float)$val;
                }
            }
            return $res;
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