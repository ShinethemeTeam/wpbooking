<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 3/14/2016
 * Time: 9:32 AM
 */
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
                'service-types/room',
                'service-types/car',
            ));

            //add_filter('comment_form_field_comment', array($this, 'add_review_field'));
            add_action('comment_post', array($this, '_save_review_stats'));

            add_filter('template_include', array($this, '_show_single_service'));

            // archive page
            add_filter('template_include', array($this, 'template_loader'));
            add_filter('body_class', array($this, '_add_body_class'));

            /**
             *
             * Ajax Get Calendar Months
             * @author dungdt
             * @since 1.0
             */
            //add_action('wp_ajax_wpbooking_calendar_months', array($this, '_calendar_months'));
            //add_action('wp_ajax_nopriv_wpbooking_calendar_months', array($this, '_calendar_months'));


            /**
             * Ajax Add Favorite
             * @author dungdt
             * @since 1.0
             */
            add_action('wp_ajax_wpbooking_add_favorite', array($this, '_add_favorite'));

            /**
             * Filter to load specific comment template file
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('comments_template', array($this, '_comments_template'));

            /**
             * Ajax Vote Review Handler
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_vote_review', array($this, '_wpbooking_vote_review'));

            /**
             * Ajax Reply a Review
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_write_reply', array($this, '_wpbooking_write_reply'));

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
            add_action('wpbooking_after_service_address', array($this,'_review_score_html'), 15 ,3);
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
                if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->query_vars ) ) ) {
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
                    $res['message'] = esc_html__('Post ID is required', 'wpbooking');
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
            $calendar_months = array();
            $calendar_dates = array();

            // Default Months
//			for ($i = 0; $i < 3; $i++) {
//				$date = new DateTime($currentYear . '-' . $currentMonth . '-1');
//				if (!$i) {
//					$calendar_months[$date->format('m_Y')] = array();
//				} else {
//					$date->modify('+' . $i . ' months');
//					$calendar_months[$date->format('m_Y')] = array();
//				}
//			}
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
//					$calendar_months[$key][] = array(
//						'date'            => date('Y-m-d', $v['start']),
//						'price'           => WPBooking_Currency::format_money($v['price']),
//						//'tooltip_content' => sprintf(esc_html__('%s - %d available', 'wpbooking'), WPBooking_Currency::format_money($v['price']), $v['number'] - $v['total_booked']),
//						'tooltip_content' => WPBooking_Currency::format_money($v['price']),
//						'can_check_in'    => $v['can_check_in'],
//						'can_check_out'   => $v['can_check_out'],
//					);

                    $calendar_dates[date('Y-m-d', $v['start'])] = array(
                        'date'            => date('Y-m-d', $v['start']),
                        'price'           => WPBooking_Currency::format_money($v['price']),
                        //'tooltip_content' => sprintf(esc_html__('%s - %d available', 'wpbooking'), WPBooking_Currency::format_money($v['price']), $v['number'] - $v['total_booked']),
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

        function get_days_from_range($start, $end)
        {

        }

        function _add_body_class($class)
        {
//			if (is_singular()) {
//				$is_page = get_the_ID();
//				$list_page_search = apply_filters("wpbooking_add_page_archive_search", array());
//				if (!empty($list_page_search[$is_page])) {
//					$class[] = 'wpbooking-archive-page';
//				}
//			}

            return $class;
        }

        function query($args = array(), $service_type = FALSE)
        {
            do_action('wpbooking_before_default_query_' . $service_type);

            $args = wp_parse_args($args, array(
                'post_type' => 'wpbooking_service'
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
            // check tax
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


        /**
         *
         */
        function _show_single_service($template)
        {

            if (get_post_type() == 'wpbooking_service' and is_single()) {
                $template = wpbooking_view_path('single-service');
            }

            return $template;
        }


        /**
         * Save Comment Stats Data
         * @param $comment_id
         * @return bool
         */
        function _save_review_stats($comment_id)
        {
            $comemntObj = get_comment($comment_id);
            $post_id = $comemntObj->comment_post_ID;

            if (get_post_type($post_id) != 'wpbooking_service') return FALSE;

            $validate = apply_filters('wpbooking_save_review_stats_validate', TRUE, $post_id, $comment_id);

            if ($validate) {
                $wpbooking_review = $this->post('wpbooking_review');
                $details = $this->post('wpbooking_review_detail');
                if (!empty($details) and is_array($details)) {
                    $total = 0;
                    foreach ($details as $key=>$val) {
                        $total += $val['rate'];
                        update_comment_meta($comment_id, 'wpbooking_review_stats_'.$key, $val['rate']);
                    }
                    $wpbooking_review = ($total) / count($details);
                }
                if($wpbooking_review < 1) $wpbooking_review = 1;
                update_comment_meta($comment_id, 'wpbooking_review', $wpbooking_review);
                update_comment_meta($comment_id, 'wpbooking_review_detail', $details);
                update_comment_meta($comment_id, 'wpbooking_title', $this->post('wpbooking_title'));
            }

            do_action('after_wpbooking_update_review_stats', $validate, $comment_id, $post_id);
        }

        function add_review_field($fields)
        {
//			if (get_post_type() != 'wpbooking_service') return $fields;
//
//			$field_review = apply_filters('wpbooking_review_field', wpbooking_load_view('single/review/review-field'));
//
//			return $field_review . $fields;
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
         * Filter to load our specific reviews template
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $template
         * @return string
         */
        function _comments_template($template)
        {
            if (get_post_type() != 'wpbooking_service') return $template;

            $template = wpbooking_view_path('reviews');

            return $template;
        }

        /**
         * Ajax Vote for Review handler
         *
         * @since 1.0
         * @author dungdt
         */
        function _wpbooking_vote_review()
        {
            $res = array(
                'status' => FALSE
            );
            $review_id = WPBooking_Input::post('review_id');
            if (!is_user_logged_in()) {
                $res['status'] = FALSE;
                $res['not_logged_in'] = 1;
            } else {
                $model = WPBooking_Review_Helpful_Model::inst();

                $res['voted'] = (int)$model->vote($review_id, get_current_user_id());
                $res['status'] = 1;
                if ($count = $model->count($review_id)) {
                    $res['vote_count'] = sprintf(esc_html__('%d like this', 'wpbooking'), $count);
                    if ($count > 1)
                        $res['vote_count_2'] = sprintf(esc_html__('%d likes', 'wpbooking'), $count);
                    else
                        $res['vote_count_2'] = sprintf(esc_html__('%d like', 'wpbooking'), $count);
                    $res['count'] = $count;
                } else {
                    $res['vote_count'] = '';
                    $res['vote_count_2'] = '';
                    $res['count'] = 0;
                }


            }

            echo json_encode($res);
            die;
        }

        /**
         * Ajax Reply for Review
         *
         * @since 1.0
         * @author dungdt
         */
        function _wpbooking_write_reply()
        {
            $res = array(
                'status' => FALSE
            );
            $review_id = $this->post('review_id');
            $message = $this->post('message');
            if ($review_id and $message and is_user_logged_in()) {

                $review = get_comment($review_id);
                $post_id = $review->comment_post_ID;
                $service = new WB_Service($post_id);

                // Only Level 1 and check current user permission
                if (wpbooking_review_allow_reply($review_id)) {
                    $current_user = wp_get_current_user();
                    $data = array(
                        'comment_content'      => $message,
                        'comment_parent'       => $review_id,
                        'user_id'              => get_current_user_id(),
                        'comment_author_IP'    => $this->ip_address(),
                        'comment_author_email' => $current_user->user_email,
                        'comment_post_ID'      => $post_id
                    );
                    $reply_id = wp_insert_comment($data);
                    $count = WPBooking_User::inst()->count_reviews($service->get_author('email'));
                    $html_count = FALSE;
                    if ($count) $html_count = sprintf('<span class="review-count">' . _n('1 review', '%d reviews', $count, 'wpbooking') . '</span>', $count);

                    $res['status'] = 1;
                    $res['html'] = '<li>
										<div class="comment_container">
											<footer class="comment-meta">
												<div class="comment-author vcard">
													' . $service->get_author('avatar')
                        . sprintf('<b class="review-author-name">%s</b>', $service->get_author('name'))
                        . $html_count . '
												</div><!-- .comment-author -->
											</footer><!-- .comment-meta -->

											<div class="comment-content-wrap">
												<div class="comment-text">
													<p>' . $message . '</p>
												</div>
											</div><!-- .comment-content -->
										</div>
									</li>';

                    $res['html'] = apply_filters('wpbooking_write_reply_html_result', $res['html'], $reply_id);

                }
            }

            $res = apply_filters('wpbooking_write_reply_result', $res);

            echo json_encode($res);
            die;
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
                        $latest_str = $latest->diff($current)->d.esc_html__(' day(s) ago','wpbooking');
                    }elseif($latest->diff($current)->h > 0){
                        $latest_str = $latest->diff($current)->h.esc_html__(' hour(s) ago','wpbooking');
                    }elseif($latest->diff($current)->i > 0){
                        $latest_str = $latest->diff($current)->i.esc_html__(' minute(s) ago','wpbooking');
                    }else{
                        $latest_str = esc_html__('just now','wpbooking');
                    }
                    $latest_booking = '<p class="wb-latest-booking">'.esc_html__('Latest booking: ','wpbooking').$latest_str.'</p>';

                    echo apply_filters('wpbooking_latest_booking_html_'.$service_type,$latest_booking,$post_id,$latest_time,$current);
                }
            }
        }

        function _review_score_html($post_id, $service_type, $service){
            if(!empty($post_id)){
                ?>
                <div class="wb-score-review">
                    <?php echo $service->get_review_score(); ?>
                </div>
            <?php
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

	WPBooking_Service_Controller::inst();
}