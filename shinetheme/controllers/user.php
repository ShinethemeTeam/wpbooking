<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 6/22/2016
 * Time: 3:28 PM
 */
if(!class_exists('WPBooking_User'))
{
	class WPBooking_User
	{
		static $_inst;

		function __construct()
		{
			add_action('init',array($this,'_add_shortcode'));
			/**
			 * Login & Register handler
			 *
			 * @author dungdt
			 * @since 1.0
			 */
			add_action('init',array($this,'_login_register_handler'));
		}


		/**
		 * Login & Register handler
		 *
		 * @since 1.0
		 * @author dungdt
		 */
		function _login_register_handler(){

			if(is_user_logged_in()) return false;
			// Login
			if(WPBooking_Input::post('action')=='wpbooking_do_login'){

				$creds['user_login'] = WPBooking_Input::post('login');
				$creds['user_password'] = WPBooking_Input::post('password');
				$creds['remember'] = WPBooking_Input::post('remember');

				$user = wp_signon( $creds ,FALSE);

				if ( is_wp_error($user) ){
					wpbooking_set_message(esc_html__('Your Username or Password is not correct! Please try again','wpbooking'),'danger');

				}else{
					// Login Success
					// Redirect if url is exists
					if($redirect=WPBooking_Input::post('url')){
						wp_redirect($redirect);die;
					}
				}

			}


			// Register
			if(WPBooking_Input::post('action')=='wpbooking_do_register'){


			}
		}

		function _myaccount_shortcode($attr=array(),$content=FALSE)
		{
			return wpbooking_load_view('account/index');
		}
		function _partner_register_shortcode()
		{
			return wpbooking_load_view('account/partner-register');
		}
		function _add_shortcode()
		{
			add_shortcode('wpbooking-myaccount',array($this,'_myaccount_shortcode'));
			add_shortcode('wpbooking-partner-register',array($this,'_partner_register_shortcode'));
		}

		static function inst()
		{
			if(!self::$_inst) self::$_inst=new self();
			return self::$_inst;
		}
	}

	WPBooking_User::inst();
}