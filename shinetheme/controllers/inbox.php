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
			 * Ajax Handle for reload old messages
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wp_ajax_wpbooking_reload_old_message', array($this, '_reload_old_message'));

			/**
			 * Ajax handle for Load More User Message
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wp_ajax_wpbooking_load_more_user_message', array($this, '_load_more_user_message'));
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
				$validator->set_rules('wb-message-input', esc_html__('Message', 'wpbooking'), 'required');

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
		 * @return bool|array
		 */
		function get_latest_message()
		{
			$model = WPBooking_Inbox_Model::inst();
			$current_user = get_current_user_id();

			return $model->limit(5)
				->where('from_user', $current_user)
				->or_where('to_user', $current_user)
				->where('from_user!=to_user', FALSE,true)
				->orderby('created_at', 'desc')
				->groupby('from_user')
				->having('from_user!=to_user')
				->get()->result();
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
		function get_user_message($user_id, $offset = 0)
		{
			$model = WPBooking_Inbox_Model::inst();
			$current = get_current_user_id();

			return $model->limit(30, $offset)
				->where(' (from_user=' . $current . ' and to_user=' . $user_id . ' )', FALSE, TRUE)
				->or_where(' (to_user=' . $current . ' and from_user=' . $user_id . ' )', FALSE, TRUE)
				->orderby('created_at', 'desc')
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
			$res = array(
				'status' => 1,
				'data'   => FALSE,
				'load_more'=>FALSE
			);
			$user_id = WPBooking_Input::post('user_id');
			$offset = WPBooking_Input::post('offset');

			$messages = $this->get_user_message($user_id, $offset);

			if (!empty($messages)) {
				foreach($messages as $message){
					$res['data'].= wpbooking_load_view('account/inbox/loop-message', array('message' => $message));
				}
			}

			if($offset+30<=$this->count_user_message($user_id)){
				$res['load_more']=true;
			}

			echo json_encode($res);
			die;
		}

		/**
		 * Hook Callback for Ajax Get Old Messages
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _reload_old_message()
		{
			if (!is_user_logged_in()) return;

			if (!$user_id = WPBooking_Input::post('user_id')) {
				echo esc_html__('User ID must be not empty', 'wpbooking');
				die;
			}

			$old_messages = $this->get_user_message($user_id);

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