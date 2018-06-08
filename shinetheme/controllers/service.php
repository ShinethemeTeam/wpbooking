<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Service_Controller')) {
	class WPBooking_Service_Controller extends WPBooking_Controller
    {

        private static $_inst;
        private $service_types = array();
        private $all_services_instance=array();

        function __construct()
        {
            // Load Abstract Service Type class and Default Service Types
            $loader = WPBooking_Loader::inst();
            $loader->load_library(array(
                'service-types/abstract-service-type',
                'service-types/accommodation',
                'service-types/tour',
            ));

            add_filter('template_include', array($this, '_show_single_service'));

            // archive page
            add_filter('template_include', array($this, 'template_loader'));
            add_filter('body_class', array($this, '_add_body_class'));

            /**
             * Add More to Post Class
             *
             *
             * @since 1.0
             * @author dungdt
             *
             */
            add_filter('post_class',array($this,'_add_post_class'),10,2);

            /**
             * Ajax Add Favorite
             * @author dungdt
             * @since 1.0
             */
            add_action('wp_ajax_wpbooking_add_favorite', array($this, '_add_favorite'));

            /**
             * Redirect for Disable Property
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('template_redirect', array($this, '_redirect_disable_property'));

            /**
             * Filter To Add Class name to Body tag
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('body_class', array($this, '_body_class'));

            /**
             * Filter the Main Query
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('pre_get_posts',array($this,'_filter_main_query'));

            /**
             * Add latest booking and review score in loop service
             *
             * @since 1.0
             * @author tienhd
             */
            add_action('wpbooking_after_service_address', array($this,'_latest_booking_html'), 10 ,3);

            add_action('init', array($this, '_load_form_search_shortcodes'));
        }

        /**
         * Add More to Post Class
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $class
         * @return array
         */
        public function _add_post_class($class)
        {
            if(!is_admin()){
                global $post;
                $post_id=$post->ID;
                $service=wpbooking_get_service($post_id);
                if($service and $service->get_type()){
                    $class[]='service-type-'.$service->get_type();
                }
            }


            return $class;
        }

        /**
         * Filter the Main Query
         *
         * @since 1.0
         * @author dungdt
         */
        function _filter_main_query($q)
        {
            // We only want to affect the main query
            if ( ! $q->is_main_query() or is_admin()) {
                return;
            }

            // Fix for verbose page rules
            if ( $GLOBALS['wp_rewrite']->use_verbose_page_rules && isset( $q->queried_object->ID ) && $q->queried_object->ID === wpbooking_get_option( 'archive-page' ) ) {
                $q->set( 'post_type', 'wpbooking_service' );
                $q->set( 'page', '' );
                $q->set( 'pagename', '' );

                // Fix conditional Functions
                $q->is_archive           = true;
                $q->is_post_type_archive = true;
                $q->is_singular          = false;
                $q->is_page              = false;
            }

            if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) !== absint( $q->get( 'page_id' ) ) ) {
                $_query = wp_parse_args( $q->query );
                if ( ! empty( $_query ) and !empty( $this->query_vars ) && array_intersect( array_keys( $_query ), array_keys( $this->query_vars ) ) ) {
                    $q->is_page     = true;
                    $q->is_home     = false;
                    $q->is_singular = true;
                    $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                    add_filter( 'redirect_canonical', '__return_false' );
                }
            }

            // When orderby is set, WordPress shows posts. Get around that here.
            if ( $q->is_home() && 'page' === get_option( 'show_on_front' ) && absint( get_option( 'page_on_front' ) ) === wpbooking_get_option( 'archive-page' ) ) {
                $_query = wp_parse_args( $q->query );
                if ( empty( $_query ) || ! array_diff( array_keys( $_query ), array( 'preview', 'page', 'paged', 'cpage', 'orderby' ) ) ) {
                    $q->is_page = true;
                    $q->is_home = false;
                    $q->set( 'page_id', (int) get_option( 'page_on_front' ) );
                    $q->set( 'post_type', 'wpbooking_service' );
                }
            }

            /**
             * To allow archive page display in home page
             */
            if($q->is_page() && 'page' === get_option( 'show_on_front' ) && absint( $q->get( 'page_id' ) ) === wpbooking_get_option('archive-page')){
                $q->set( 'post_type', 'wpbooking_service' );
                $q->set( 'page_id', '' );

                if ( isset( $q->query['paged'] ) ) {
                    $q->set( 'paged', $q->query['paged'] );
                }

                // Fix conditional Functions like is_front_page
                $q->is_singular          = false;
                $q->is_post_type_archive = true;
                $q->is_archive           = true;
                $q->is_page              = true;
            }

        }

        /**
         * Redirect for Disable Property
         *
         * @since 1.0
         * @author dungdt
         */
        function _redirect_disable_property()
        {
            if (is_singular('wpbooking_service')) {
                $service = new WB_Service(get_the_ID());
                if (!$service->is_enable()) {
                    wp_safe_redirect(home_url('/'));
                }
            }
        }

        /**
         * Ajax Callback Add Favorite
         *
         * @since 1.0
         * @author dungdt
         */
        function _add_favorite()
        {
            $res = array('status' => 0);

            if (is_user_logged_in()) {
                if (!$post_id = WPBooking_Input::post('post_id')) {
                    $res['message'] = esc_html__('Post ID is required', 'wp-booking-management-system');
                } else {
                    $service = new WB_Service($post_id);
                    $res['status'] = 1;
                    $res['fav_status'] = $service->do_favorite();
                }
            } else {
                $res['not_logged_in'] = 1;
                $res['login_url'] = wp_login_url();
            }

            echo json_encode($res);
            die;
        }


        /**
         * Function Ajax Get Calendar Months
         *
         * @since 1.0
         * @author dungdt
         *
         * @return string json result
         */
        function _calendar_months()
        {
            $res = array();
            $post_id = WPBooking_Input::post('post_id');
            $currentMonth = WPBooking_Input::post('currentMonth');
            $currentYear = WPBooking_Input::post('currentYear');
            $today = new Datetime();
            $start_date = new DateTime($currentYear . '-' . $currentMonth . '-1');
            if ($start_date < $today) $start_date = $today;
            $start = $start_date->getTimestamp();
            $end = strtotime($start_date->format('Y-m-t'));
            $end_date = new DateTime();
            $end_date->setTimestamp($end);

            $max_service_number = 1;

            if ($max = WPBooking_Service_Model::inst()->find_by('post_id', $post_id)) {
                $max_service_number = $max['number'];
            }

            $raw_data = WPBooking_Calendar_Model::inst()->calendar_months($post_id, $start, $end);
            $calendar_dates = array();

            // All day data
            $all_days = array();

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));

            foreach ($period as $dt) {
                if (get_post_meta($post_id, 'property_available_for', TRUE) != 'specific_periods') {
                    $all_days[$dt->format('Y-m-d')] = array(
                        'date'            => $dt->format('Y-m-d'),
                        'status'          => 'available',
                        'price'           => WPBooking_Currency::format_money(get_post_meta($post_id, 'price', TRUE)),
                        'tooltip_content' => WPBooking_Currency::format_money(get_post_meta($post_id, 'price', TRUE)),
                        'can_check_in'    => 1,
                        'can_check_out'   => 1,
                        'number'          => $max_service_number
                    );
                }

            }

            if (!empty($raw_data)) {
                foreach ($raw_data as $k => $v) {
                    // Ignore Not Available Date
                    if ($v['status'] == 'not_available') {
                        // Ignore Allday not available
                        unset($all_days[date('Y-m-d', $v['start'])]);
                        continue;
                    }

                    $key = date('m', $v['start']) . '_' . date('Y', $v['start']);

                    $calendar_dates[date('Y-m-d', $v['start'])] = array(
                        'date'            => date('Y-m-d', $v['start']),
                        'price'           => WPBooking_Currency::format_money($v['price']),

                        'can_check_in'    => $v['can_check_in'],
                        'can_check_out'   => $v['can_check_out'],
                        'tooltip_content' => WPBooking_Currency::format_money($v['price']),
                        'number'          => $max_service_number
                    );
                }
            }

            // Foreach Data
            if (!empty($calendar_dates)) {
                foreach ($calendar_dates as $day) {
                    if (array_key_exists($day['date'], $all_days)) {
                        unset($all_days[$day['date']]);
                    }
                }
            }

            // Now append the exsits
            if (!empty($all_days)) {
                foreach ($all_days as $k => $day) {
                    $calendar_dates[$k] = $day;
                }
            }

            // Finally Check Booked Data
            $booked_data = WPBooking_Order_Model::inst()->get_calendar_booked($post_id, $start, $end);
            if (!empty($booked_data)) {
                foreach ($booked_data as $order_item) {
                    $check_in_temp = $order_item['check_in_timestamp'];
                    while ($check_in_temp <= $order_item['check_out_timestamp']) {

                        if (!empty($calendar_dates[date('Y-m-d', $check_in_temp)])) {
                            $calendar_dates[date('Y-m-d', $check_in_temp)]['number']--;
                        }
                        $check_in_temp = strtotime('+1 day', $check_in_temp);
                    }

                }

                foreach ($calendar_dates as $k => $day) {
                    if ($day['number'] < 1) unset($calendar_dates[$k]);
                }
            }

            $res['dates'] = $calendar_dates;

            echo json_encode($res);

            die;
        }

        function _add_body_class($class)
        {
            return $class;
        }

        function query($args = array(), $service_type = FALSE)
        {
            do_action('wpbooking_before_default_query_' . $service_type);

            $args = wp_parse_args($args, array(
                'post_type' => 'wpbooking_service',
            ));

            $query = wpbooking_query('default', $args);

            return $query;
        }

        /**
         * Filter to load template
         *
         * @param $template
         * @return string
         */
        public function template_loader($template)
        {
            if (is_post_type_archive('wpbooking_service') or is_tax(get_object_taxonomies('wpbooking_service'))) {
                $template = wpbooking_view_path('archive-service');
            }
            return $template;
        }

        /**
         * Get All Available Search fields
         *
         * @since 1.0
         * @author quandq
         *
         * @return array
         */
        function get_search_fields()
        {
            $all_types=$this->get_service_types();
            $list_filed=array();
            if(!empty($all_types)){
                foreach($all_types as $type_id=>$type_object){
                    $fields=$type_object->get_search_fields();
                    if(is_array($fields) and !empty($fields)){
                        $list_filed[$type_id]=$fields;
                    }
                }
            }
            $list_filed = apply_filters("wpbooking_list_fields_form_search", $list_filed);
            return $list_filed;
        }

        function _show_single_service($template)
        {

            if (get_post_type() == 'wpbooking_service' and is_single()) {
                $template = wpbooking_view_path('single-service');
            }

            return $template;
        }

        /**
         * Register Default Service Type Object
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $id
         * @param $object
         */
        function register_type($id, $object)
        {
            $this->service_types[$id] = $object;
        }

        /**
         * Get All Registered Service Types
         *
         * @author dungdt
         * @since 1.0
         *
         * @return WPBooking_Abstract_Service_Type[]
         */
        function get_service_types()
        {
            $default = $this->service_types;

            return apply_filters('wpbooking_service_types', $default);
        }


        /**
         * Get Service Type Object by Type ID
         * @since 1.0
         * @author dungdt
         *
         * @param bool|FALSE $type
         * @return WPBooking_Abstract_Service_Type
         */
        function get_service_type($type = FALSE)
        {
            $all = $this->get_service_types();

            if ($type and isset($all[$type])) return $all[$type];
        }


        /**
         * Filter to Add Class Name to Body Tag
         *
         * @since 1.0
         * @author dungdt
         *
         * @param array $class
         * @return array
         */
        function _body_class($class = array())
        {
            $template=get_template();
            $class[]=$template;
            return $class;
        }

        /**
         * Get Service Instance By ID, allow cached instance
         *
         * @author dungdt
         * @since 1.0
         *
         * @param bool $post_id
         * @return WB_Service
         */
        function get_service_instance($post_id=false){
            if(!$post_id)$post_id =get_the_ID();
            // Check Instance Exists
            if(!array_key_exists($post_id,$this->all_services_instance)){
                $this->all_services_instance[$post_id]=new WB_Service($post_id);
            }

            return $this->all_services_instance[$post_id];
        }

        /**
         * get latest booking html
         *
         * @since 1.0
         * @author tienhd
         *
         * @param $post_id
         * @param $service_type
         * @param $service
         */
        function _latest_booking_html($post_id, $service_type, $service){
            if(!empty($post_id)){
                $latest_time = WPBooking_Order_Model::inst()->get_latest_booking_date($post_id);
                if($latest_time) {
                    $current = new DateTime(date('Y-m-d h:i:s',strtotime('now')));
                    $latest = new DateTime(date('Y-m-d h:i:s',$latest_time));
                    if($latest->diff($current)->d > 3 || $latest->diff($current)->m > 0 || $latest->diff($current)->y > 0){
                        $latest_str = date(get_option('date_format'), $latest_time);
                    }elseif($latest->diff($current)->d > 0){
                        $latest_str = $latest->diff($current)->d.esc_html__(' day(s) ago','wp-booking-management-system');
                    }elseif($latest->diff($current)->h > 0){
                        $latest_str = $latest->diff($current)->h.esc_html__(' hour(s) ago','wp-booking-management-system');
                    }elseif($latest->diff($current)->i > 0){
                        $latest_str = $latest->diff($current)->i.esc_html__(' minute(s) ago','wp-booking-management-system');
                    }else{
                        $latest_str = esc_html__('just now','wp-booking-management-system');
                    }
                    $latest_booking = '<p class="wb-latest-booking">'.esc_html__('Latest booking: ','wp-booking-management-system').$latest_str.'</p>';

                    $latest_booking = apply_filters('wpbooking_latest_booking_html',$latest_booking,$service_type,$post_id,$latest_time,$current);
                    $latest_booking = apply_filters('wpbooking_latest_booking_html_'.$service_type,$latest_booking,$post_id,$latest_time,$current);

                    echo do_shortcode($latest_booking);
                }
            }
        }

        /**
         * Check user booking
         *
         * @param $post_id
         * @return bool|WPBooking_Order_Model
         */
        function check_user_booking($post_id){
            if(!empty($post_id)){
                $user_id = get_current_user_id();
                return WPBooking_Order_Model::inst()->check_user_booking($post_id,$user_id);
            }else{
                return false;
            }
        }
        /**
         * Load Shortcode
         *
         * @since 1.1
         * @author quandq
         */
        function _load_form_search_shortcodes(){
            WPBooking_Loader::inst()->load_library('shortcodes/form-search');
        }

		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}
	}
	WPBooking_Service_Controller::inst();
}