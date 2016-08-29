<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 7/1/2016
 * Time: 10:25 AM
 */
if (!class_exists('WPBooking_Inbox')) {
	class WPBooking_Inbox
	{
		static $_inst;

		function __construct()
		{
			add_action('init', array($this, '_handle_send_message'));

			/**
			 * Ajax Handle for load old inbox conversation
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wp_ajax_wpbooking_load_message', array($this, '_load_message'));


			/**
			 * Ajax handle for Load More User Message
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wp_ajax_wpbooking_load_reply', array($this, '_load_more_user_message'));
		}

		/**
		 * Handle Ajax Send Message
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _handle_send_message()
		{

			if (WPBooking_Input::post('wpbooking_action') == 'send_message' and is_user_logged_in()) {
				$is_validate = TRUE;
				$res = array('status' => 0);
				$to_user = FALSE;
				if ($post_id = WPBooking_Input::post('post_id')) {
					$to_user = get_post_field('post_author', $post_id);
				}
				if (WPBooking_Input::post('to_user')) {
					$to_user = WPBooking_Input::post('to_user');
				}


				$validator = new WPBooking_Form_Validator();
				$validator->set_rules('wb-message-input', esc_html__('Message', 'wpbooking'), 'required|max_length[500]|min_length[10]');

				if (!$validator->run()) {
					$is_validate = FALSE;
					wpbooking_set_message($validator->error_string(), 'danger');
				}

				if (!$to_user) {
					$is_validate = FALSE;
					wpbooking_set_message(esc_html__('Please specific User ID or Post ID', 'wpbooking'), 'danger');
				}

				$is_validate = apply_filters('wpbooking_send_message_validate', $is_validate, $post_id);

				if ($is_validate) {
					$model = WPBooking_Inbox_Model::inst();


					if ($message_id = $model->send($to_user, WPBooking_Input::post('wb-message-input'), $post_id)) {

						$res['status'] = 1;
						wpbooking_set_message(esc_html__('Your message has been sent', 'wpbooking'), 'success');

						do_action('wpbooking_before_sending_message', $message_id, $post_id);

						$message = $model->find($message_id);
						if ($message) {
							$res['messageHTML'] = wpbooking_load_view('account/inbox/loop-message', array('message' => $message));
						}

					} else {

						wpbooking_set_message(esc_html__('Can not send your message, please contact the admin', 'wpbooking'), 'danger');

					}
				}

				$res['message'] = wpbooking_get_message(TRUE);

				$res = apply_filters('wpbooking_send_message_result', $res, $post_id, $is_validate);


				echo json_encode($res);
				die;
			}
		}

		/**
		 * Get Latest Message Group by User
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $offset int
		 * @param $limit 20
		 * @return bool|array
		 */
		function get_latest_message($offset = 0, $limit = 10)
		{
			global $wpdb;
			$inbox=WPBooking_Inbox_Model::inst();


			$sql = "select SQL_CALC_FOUND_ROWS * from (
						select * from(
							(
								SELECT *,from_user as user_id FROM {$wpdb->prefix}wpbooking_message where to_user=%d
							)
							UNION
							(
								SELECT *,to_user as user_id FROM {$wpdb->prefix}wpbooking_message where from_user=%d
							)

							order by id desc
							)as table_desc
						group by user_id desc) as table_group
						where user_id!=%d
					order by id desc
					LIMIT %d,%d
				";
			$user_id = get_current_user_id();
			$sql = $wpdb->prepare($sql, array($user_id, $user_id, $user_id, $offset, $limit));

			$message=$wpdb->get_results($sql, ARRAY_A);


			return $message;
		}

		function filter_latest_message($message){
			$inbox=WPBooking_Inbox_Model::inst();;
			$user_id = get_current_user_id();
			if(!empty($message)){
				foreach($message as $key=>$mess){
					if($mess['to_user']==$user_id){
						$count=$inbox->select('count(id) as total')->where('(is_read=0 or is_read is NULL )',FALSE,TRUE)
							->where('to_user',$user_id)
							->where('from_user',$mess['from_user'])
							->get()->row();
						$message[$key]['unread_number']=$count['total'];
					}else{
						$message[$key]['unread_number']=0;
					}

				}
			}
			return $message;
		}


		/**
		 * Ajax Load More Conversation
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _load_message()
		{
			$limit = 10;
			$res = array(
				'status' => 1,
				'html'   => FALSE,
			);
			$offset = WPBooking_Input::post('offset');
			$offset += $limit;

			$messages = $this->get_latest_message($offset, $limit);
			$total=$this->count_total_message();
			$messages=$this->filter_latest_message($messages);

			if (!empty($messages)) {
				foreach ($messages as $key => $user) {
					if ($user['from_user'] != get_current_user_id()) $user_id = $user['from_user'];
					else $user_id = $user['to_user'];

					$myaccount_page = get_permalink(wpbooking_get_option('myaccount-page'));
					$url = $myaccount_page . 'tab/inbox/';
					$url = add_query_arg(array('user_id' => $user_id), $url);

					$user_info = get_userdata($user_id);
					@ob_start();
					?>
					<div class="inbox-user-item ">
						<a href="<?php echo esc_url($url) ?>">
							<div class="avatar"><?php echo get_avatar($user_id) ?></div>
							<div class="info">
								<h4 class="user-displayname"><?php echo esc_html($user_info->display_name) ?></h4>

								<div class="message"><?php echo wpbooking_cutnchar(stripcslashes($user['content']),60) ?></div>
								<p class="time"><?php printf(esc_html__('%s ago', 'wpbooking'), human_time_diff($user['created_at'], time())) ?></p>
								<?php if($user['unread_number']){
									printf('<p class="unread_number">%s</p>',sprintf(esc_html__('%d new message(s)','wpbooking'),$user['unread_number']));
								} ?>
							</div>
						</a>
					</div>
					<?php
					$res['html'] .= @ob_get_clean();
				}
			}
			if ($offset + $limit < $total) {
				$res['offset'] = $offset;
				$res['total']=$total;
			} else {
				$res['offset'] = 0;
			}

			echo json_encode($res);
			die;
		}

		/**
		 * Please only call right after method @get_latest_message
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return array|null|object|void
		 */
		function count_total_message()
		{
			global $wpdb;

			return $wpdb->get_row("SELECT FOUND_ROWS() as total")->total;
		}

		/**
		 * Count Total of message of Current User with Specific User ID
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $user_id
		 */
		function count_user_message($user_id)
		{
			$model = WPBooking_Inbox_Model::inst();
			$current = get_current_user_id();

			$res = $model->select('count(id) as total')
				->where(' (from_user=' . $current . ' and to_user=' . $user_id . ' )', FALSE, TRUE)
				->or_where(' (to_user=' . $current . ' and from_user=' . $user_id . ' )', FALSE, TRUE)
				->orderby('created_at', 'desc')
				->get()->row();

			return $res['total'];
		}

		/**
		 * Get Messages of Current User with Specific User ID, Only get Last 30 Messages
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @param $user_id
		 * @param $offset int
		 * @return mixed
		 */
		function get_user_message($user_id, $offset = 0, $limit = 10)
		{
			$model = WPBooking_Inbox_Model::inst();
			$current = get_current_user_id();

			$model
				->where(' ( (from_user=' . $current . ' and to_user=' . $user_id . ' ) or (to_user=' . $current . ' and from_user=' . $user_id . ' ) )', FALSE, TRUE)
				->where('(is_read=0 or is_read is NULL )',FALSE,TRUE)
				->update(array('is_read'=>1));

			return $model->select('SQL_CALC_FOUND_ROWS *')->limit($limit, $offset)
				->where(' (from_user=' . $current . ' and to_user=' . $user_id . ' )', FALSE, TRUE)
				->or_where(' (to_user=' . $current . ' and from_user=' . $user_id . ' )', FALSE, TRUE)
				->orderby('id', 'desc')
				->get()->result();
		}


		/**
		 * Hook Callback of Ajax Load More User Message
		 *
		 * @author dungdt
		 * @since 1.0
		 *
		 * @return string
		 */
		function _load_more_user_message()
		{
			$limit = 10;
			$res = array(
				'status'    => 1,
				'data'      => FALSE,
				'load_more' => FALSE
			);
			$user_id = WPBooking_Input::post('user_id');
			$offset = WPBooking_Input::post('offset');
			$offset += $limit;

			$messages = $this->get_user_message($user_id, $offset);

			if (!empty($messages)) {
				foreach ($messages as $message) {
					$res['html'] .= wpbooking_load_view('account/inbox/loop-message', array('message' => $message));
				}
			}

			if ($offset + $limit <= $this->count_user_message($user_id)) {
				$res['offset'] = $offset;
			} else {
				$res['offset'] = 0;
			}

			echo json_encode($res);
			die;
		}


		static function inst()
		{
			if (!self::$_inst) {
				self::$_inst = new self();
			}

			return self::$_inst;
		}


	}

	WPBooking_Inbox::inst();
}