<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:28 PM
 */
if (!class_exists('WPBooking_User')) {
	class WPBooking_User
	{
		static $_inst;

		function __construct()
		{
			add_action('init', array($this, '_add_shortcode'));
			/**
			 * Login & Register handler
			 *
			 * @author dungdt
			 * @since 1.0
			 */
			add_action('init', array($this, '_login_register_handler'));

			/**
			 * Ajax Handler Upload Certificate before Register
			 * @author dungdt
			 * @since 1.0
			 */
			add_action('wp_ajax_nopriv_wpbooking_upload_certificate', array($this, '_ajax_upload_certificate'));


			/**
			 * Send Email to User after Registration
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_action('wpbooking_register_success', array($this, '_send_registration_email'));
			add_action('wpbooking_partner_register_success', array($this, '_send_partner_registration_email'));

			/**
			 * Get Email Shortcode Content
			 *
			 * @since 1.0
			 * @author dungdt
			 */
			add_filter('wpbooking_registration_email_shortcode', array($this, '_get_shortcode_content'), 10, 3);
		}

		/**
		 * Upload Certificate Ajax Handler
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _ajax_upload_certificate()
		{
			$res = array(
				'status' => 1

			);
			if (!function_exists('wp_handle_upload')) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');
			}

			if (empty($_FILES['image'])) {
				echo json_encode(array(
					'status'  => 0,
					'message' => esc_html__('You did not select any file', 'wpbooking')
				));
				die;
			}
			$uploadedfile = $_FILES['image'];

			$size_file = $uploadedfile["size"];

			if ($size_file > (1024 * 1024 * 2)) {
				$res['status'] = 0;
				$res['message'] = esc_html__('Max upload size is 2mb', 'wpbooking');
			} else {
				$allowed_file_types = array('jpg' => 'image/jpg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');
				$overrides = array('test_form' => FALSE, 'mimes' => $allowed_file_types);

				$movefile = wp_handle_upload($uploadedfile, $overrides);

				if ($movefile && !isset($movefile['error'])) {
					$res['image'] = $movefile;

				} else {
					$res['status'] = FALSE;
					$res['message'] = $movefile['error'];
				}
			}

			echo json_encode($res);
			die;
		}

		/**
		 * Login & Register handler
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _login_register_handler()
		{

			if (is_user_logged_in()) return FALSE;
			// Login
			if (WPBooking_Input::post('action') == 'wpbooking_do_login') {

				$creds['user_login'] = WPBooking_Input::post('login');
				$creds['user_password'] = WPBooking_Input::post('password');
				$creds['remember'] = WPBooking_Input::post('remember');

				$user = wp_signon($creds, FALSE);

				if (is_wp_error($user)) {
					wpbooking_set_message(esc_html__('Your Username or Password is not correct! Please try again', 'wpbooking'), 'danger');

				} else {
					// Login Success
					// Redirect if url is exists
					if ($redirect = WPBooking_Input::post('url')) {
						wp_redirect($redirect);
						die;
					}
				}

			}


			// Register
			if (WPBooking_Input::post('action') == 'wpbooking_do_register') {


			}

			// Partner Register
			if (WPBooking_Input::post('action') == 'wpbooking_do_partner_register') {
				$this->_do_partner_register();

			}
		}

		/**
		 * Register for Normal User
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 */
		function _do_register()
		{
			$validate = new WPBooking_Form_Validator();
			$validate->set_rules('login', esc_html__('Username', 'wpbooking'), 'required|max_length[100]');
			$validate->set_rules('email', esc_html__('Email', 'wpbooking'), 'required|max_length[100]|valid_email');
			$validate->set_rules('password', esc_html__('Password', 'wpbooking'), 'required|min_length[6]|max_length[100]');
			$validate->set_rules('repassword', esc_html__('Re-Type Password', 'wpbooking'), 'required|min_length[6]|max_length[100]|matches[password]');
			$validate->set_rules('term_condition', esc_html__('Term & Condition', 'wpbooking'), 'required');

			$is_validated = TRUE;

			if (!$validate->run()) {
				wpbooking_set_message($validate->error_string(), 'danger');
				$is_validated = FALSE;
			}

			// Validate Username and Email exists
			if ($is_validated) {
				$user_id = username_exists(WPBooking_Input::post('login'));
				$user_email = WPBooking_Input::post('email');
				if ($user_id or email_exists($user_email)) {
					wpbooking_set_message(esc_html__('User already exists.  Password inherited.', 'wpbooking'), 'danger');
					$is_validated = FALSE;
				}
			}

			// Allow to add filter before register
			if ($is_validated) {
				$is_validated = apply_filters('wpbooking_register_validate', $is_validated);
			}


			if ($is_validated) {
				// Start Create User
				$user_email = WPBooking_Input::post('email');
				$user_name = WPBooking_Input::post('login');
				$password = WPBooking_Input::post('password');
				$user_id = wp_insert_user(array(
					'user_login' => $user_name,
					'user_pass'  => $password,
					'user_email' => $user_email
				));
				if (is_wp_error($user_id)) {

					wpbooking_set_message(esc_html__('Can not create user. Please try it again later', 'wpbooking'), 'danger');
					do_action('wpbooking_register_failed', $user_id);

				} else {

					wpbooking_set_message(esc_html__('Your account is registered successfully. You can login now', 'wpbooking'), 'success');

					// Hook after Register Success, maybe sending some email...etc
					do_action('wpbooking_register_success', $user_id);
				}
			}
		}

		/**
		 * Do Register for Partner
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _do_partner_register()
		{
			$validate = new WPBooking_Form_Validator();
			$validate->set_rules('login', esc_html__('Username', 'wpbooking'), 'required|max_length[100]');
			$validate->set_rules('email', esc_html__('Email', 'wpbooking'), 'required|max_length[100]|valid_email');
			$validate->set_rules('password', esc_html__('Password', 'wpbooking'), 'required|min_length[6]|max_length[100]');
			$validate->set_rules('repassword', esc_html__('Re-Type Password', 'wpbooking'), 'required|min_length[6]|max_length[100]|matches[password]');
			$validate->set_rules('service_type', esc_html__('Certificate', 'wpbooking'), 'required');
			$validate->set_rules('term_condition', esc_html__('Term & Condition', 'wpbooking'), 'required');

			$is_validated = TRUE;

			if (!$validate->run()) {
				wpbooking_set_message($validate->error_string(), 'danger');
				$is_validated = FALSE;

				return FALSE;
			}

			// Validate Username and Email exists
			if ($is_validated) {
				$user_id = username_exists(WPBooking_Input::post('login'));
				$user_email = WPBooking_Input::post('email');
				if ($user_id or email_exists($user_email)) {
					wpbooking_set_message(esc_html__('User already exists.  Password inherited.', 'wpbooking'), 'danger');
					$is_validated = FALSE;

					return FALSE;
				}
			}


			// Validate Certificate Upload
			if ($is_validated) {
				$is_select_service = FALSE;
				$service_type = WPBooking_Input::post('service_type');
				if (is_array($service_type) and !empty($service_type)) {
					foreach ($service_type as $k => $v) {
						if (!empty($v[$k]['name'])) $is_select_service = TRUE;
					}
				}

				if (!$is_select_service) {
					$is_validated = FALSE;
					wpbooking_set_message(esc_html__('Please select at lease one Service Type!', 'wpbooking'), 'danger');

					return FALSE;
				}
			}


			// Allow to add filter before register
			if ($is_validated) {
				$is_validated = apply_filters('wpbooking_partner_register_validate', $is_validated);
			}


			if ($is_validated) {
				// Start Create User
				$user_email = WPBooking_Input::post('email');
				$user_name = WPBooking_Input::post('login');
				$password = WPBooking_Input::post('password');
				$user_id = wp_insert_user(array(
					'user_login' => $user_name,
					'user_pass'  => $password,
					'user_email' => $user_email,
					'role'       => 'author'
				));
				if (is_wp_error($user_id)) {

					wpbooking_set_message(esc_html__('Can not create user. Please try it again later', 'wpbooking'), 'danger');
					do_action('wpbooking_partner_register_failed', $user_id);

				} else {
					// Update Status
					update_user_meta($user_id, 'wpbooking_register_as_partner', 1);
					// Service Access
					$service_type = WPBooking_Input::post('service_type');
					if (is_array($service_type) and !empty($service_type)) {
						foreach ($service_type as $k => $v) {
							if ($v['name']) {
								update_user_meta($user_id, 'wpbooking_service_type_access_' . $k, 1);
								if ($v['certificate']) update_user_meta($user_id, 'wpbooking_service_type_certificate_' . $k, $v['certificate']);
							} else {
								update_user_meta($user_id, 'wpbooking_service_type_access_' . $k, 0);
							}

						}
					}

					wpbooking_set_message(esc_html__('Your account is registered successfully. You can login now', 'wpbooking'), 'success');

					// Hook after Register Success, maybe sending some email...etc
					do_action('wpbooking_partner_register_success', $user_id);
				}
			}
		}

		/**
		 * Hook Callback for Send Email after Registration, using template in admin
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $user_id
		 */
		function _send_registration_email($user_id)
		{
			$user_data = get_userdata($user_id);
			$title = $user_data->user_nicename . " - " . $user_data->user_email . " - " . $user_data->user_registered;
			$subject = sprintf(esc_html__('New Register Partner: %s', 'wpbooking'), $title);

			// Send To Admin
			if (wpbooking_get_option('on_registration_email_admin') and wpbooking_get_option('registration_email_admin')) {
				$to = wpbooking_get_option('email_from');
				$content = do_shortcode(wpbooking_get_option('registration_email_admin'));
				$content = $this->replace_email_shortcode($content, $user_id);
				WPBooking_Email::inst()->send($to, $subject, $content);
			}

			// Send To Customer
			if (wpbooking_get_option('on_registration_email_customer') and wpbooking_get_option('registration_email_customer')) {
				$to = $user_data->user_email;
				$content = do_shortcode(wpbooking_get_option('registration_email_customer'));
				$content = $this->replace_email_shortcode($content, $user_id);

				WPBooking_Email::inst()->send($to, $subject, $content);
			}
		}

		/**
		 * Hook Callback for Send Email For PARTNER after Registration, using template in admin
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $user_id
		 */
		function _send_partner_registration_email($user_id)
		{
			$user_data = get_userdata($user_id);
			$title = $user_data->user_nicename . " - " . $user_data->user_email . " - " . $user_data->user_registered;
			$subject = sprintf(esc_html__('New Register Partner: %s', 'wpbooking'), $title);

			// Send To Admin
			if (wpbooking_get_option('on_registration_partner_email_admin') and wpbooking_get_option('registration_partner_email_to_admin')) {
				$to = wpbooking_get_option('email_from');
				$content = do_shortcode(wpbooking_get_option('registration_partner_email_to_admin'));
				$content = $this->replace_email_shortcode($content, $user_id);

				WPBooking_Email::inst()->send($to, $subject, $content);
			}

			// Send To Partner
			if (wpbooking_get_option('on_registration_partner_email_partner') and wpbooking_get_option('registration_partner_email_to_partner')) {
				$to = $user_data->user_email;
				$content = do_shortcode(wpbooking_get_option('registration_partner_email_to_partner'));
				$content = $this->replace_email_shortcode($content, $user_id);

				WPBooking_Email::inst()->send($to, $subject, $content);
			}


		}

		/**
		 * Replace Content with Shortcode
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $content
		 * @param $user_id
		 * @return mixed
		 */
		function replace_email_shortcode($content, $user_id)
		{
			$all_shortcodes = $this->get_email_shortcodes();

			if (!empty($all_shortcodes)) {
				foreach ($all_shortcodes as $k => $v) {
					$v = apply_filters('wpbooking_registration_email_shortcode', $v, $k, $user_id);
					$v = apply_filters('wpbooking_registration_email_shortcode_' . $k, $v, $user_id);
					$content = str_replace($k, $v, $content);
				}
			}

			return $content;
		}

		/**
		 * Get All Available Email Shortcodes
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @return array|mixed|void
		 */
		function get_email_shortcodes()
		{
			$all_shortcodes = array(
				'user_name'      => '',
				'user_email'     => '',
				'profile_button' => '',
				'profile_url'    => FALSE

			);

			$all_shortcodes = apply_filters('wpbooking_registration_email_shortcodes', $all_shortcodes);

			return $all_shortcodes;
		}

		/**
		 * Hook Callback for get Email Shortcode Content
		 *
		 * @since 1.0
		 * @author dungdt
		 *
		 * @param $content
		 * @param $shortcode
		 * @param $user_id
		 * @return bool|string
		 */
		function _get_shortcode_content($content, $shortcode, $user_id)
		{
			if (!$user = get_userdata($user_id)) return FALSE;

			switch ($shortcode) {
				case "user_name":
					return $user->user_login;
					break;

				case "user_email":
					return $user->user_email;
					break;

				case "profile_button":
					return wpbooking_admin_load_view('user/email-shortcodes/profile_url', array('user_id' => $user_id));
					break;

				case "profile_url":
					return get_edit_profile_url($user_id);
					break;
			}

			return $content;
		}

		function _myaccount_shortcode($attr = array(), $content = FALSE)
		{
			return wpbooking_load_view('account/index');
		}

		function _partner_register_shortcode()
		{
			return wpbooking_load_view('account/partner-register');
		}

		function _add_shortcode()
		{
			add_shortcode('wpbooking-myaccount', array($this, '_myaccount_shortcode'));
			add_shortcode('wpbooking-partner-register', array($this, '_partner_register_shortcode'));
		}

		static function inst()
		{
			if (!self::$_inst) self::$_inst = new self();

			return self::$_inst;
		}
	}

	WPBooking_User::inst();
}