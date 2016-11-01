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
            add_action('wp_enqueue_scripts', array($this, '_add_scripts'));
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
             * Ajax Handler Upload Certificate before Register
             * @author dungdt
             * @since 1.0
             */
            add_action('wp_ajax_wpbooking_upload_avatar', array($this, '_ajax_upload_avatar'));


            /**
             * Send Email to User after Registration
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_register_success', array($this, '_send_registration_email'),10,1);
            add_action('wpbooking_partner_register_success', array($this, '_send_partner_registration_email'));

            /**
             * Get Email Shortcode Content
             *
             * @since 1.0
             * @author dungdt
             */
            add_filter('wpbooking_registration_email_shortcode', array($this, '_get_shortcode_content'), 10, 3);

            /**
             * Preview Email
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_register_email_preview', array($this, '_preview_email'));

            /**
             * Handle Action in My Account Page eg Insert/Update Service
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('init', array($this, '_myaccount_page_handler'), 20);

            /**
             * Add Endpoints to My Account Page
             *
             * @since 1.0
             * @author dungdt
             *
             */
            add_action('init', array($this, 'add_endpoints'));

            /**
             * Ajax Handler for Enable Property in Your Listing Page
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wp_ajax_wpbooking_enable_property', array($this, '_ajax_enable_property'));

            /**
             * Check If Current User Can Access My Account page
             *
             * @since 1.0
             * @author dungdt
             *
             */
            add_action('template_redirect', array($this, '_check_myaccount_page_permisison'));
            /**
             * Change avatar
             *
             * @since 1.0
             * @author quandq
             *
             */
            add_filter("pre_get_avatar",array($this, '_change_profile_avatar'),10,3);

            /**
             * redirect lost password url and do send email to retrieve password
             *
             * @since 1.0
             * @author tienhd
             */
            add_action('login_form_lostpassword',array($this,'_retrieve_password'));


            /**
             * redirect logout url
             *
             * @since 1.0
             * @author tienhd
             */
            add_filter('logout_url',array($this,'redirect_logout_url'),10,2);

            /**
             * Redirect login url
             *
             * @since 1.0
             * @author tienhd
             */
            add_filter('login_url',array($this,'redirect_login_url'));

            /**
             * Redirect reset password url
             *
             * @since 1.0
             * @author tienhd
             */
            add_action( 'login_form_rp', array( $this, 'redirect_reset_password_url' ) );
            add_action( 'login_form_resetpass', array( $this, 'redirect_reset_password_url' ) );

            /**
             * Do reset password
             *
             * @since 1.0
             * @author tienhd
             */
            add_action( 'login_form_resetpass', array( $this, '_reset_password' ) );
        }

        /**
         * Check If Current User Can Access My Account page
         *
         * @since 1.0
         * @author dungdt
         *
         */
        function _check_myaccount_page_permisison()
        {
            // Check Profile Tabs, check is not author, can't view profile
            if($user_id = get_query_var('profile'))
            {
                $current_user = get_userdata( $user_id );
                $allowed_roles = array('editor', 'administrator', 'author');
                if( ! array_intersect($allowed_roles, $current_user->roles ) ) {
                    wp_safe_redirect(home_url("/"));
                    die;
                }
            } else {
                if (is_user_logged_in() and get_query_var('tab') == 'profile' and !current_user_can('publish_posts')) {
                    wp_safe_redirect(get_permalink(wpbooking_get_option('myaccount-page')));
                    die;
                }
            }

        }

        /**
         * Ajax Handler for Enable Property
         *
         * @since 1.0
         * @author dungdt
         *
         */
        function _ajax_enable_property()
        {
            $res = array(
                'status' => 0
            );
            $validator = new WPBooking_Form_Validator();
            $validator->set_rules('post_id', esc_html__('Post ID', 'wpbooking'), 'required');
            $validator->set_rules('status', esc_html__('Status', 'wpbooking'), 'required');

            $service = new WB_Service(WPBooking_Input::post('post_id'));

            if ($validator->run()) {
                if (current_user_can('manage_options') or get_current_user_id() == $service->get_author('ID')) {
                    $service->update_meta('enable_property', WPBooking_Input::post('status'));
                    $res['status'] = 1;
                } else {
                    $res['message'] = esc_html__('You don not have permission to do that', 'wpbooking');
                }
            } else {
                $res['message'] = $validator->error_string();
            }

            echo json_encode($res);
            die;
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
         * Upload Avatar Ajax Handler
         *
         * @since 1.0
         * @author dungdt
         */
        function _ajax_upload_avatar()
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
                    if(!empty($user->get_error_code())){
                        wpbooking_set_message($this->get_error_message($user->get_error_code()), 'danger');
                    }else{
                        wpbooking_set_message(esc_html__('You need to enter a username and a password to login', 'wpbooking'), 'danger');
                    }
                } else {
                    // Login Success
                    // Redirect if url is exists
                    if ($redirect = WPBooking_Input::post('url')) {
                        wp_redirect($redirect);
                        die;
                    } else {
                        // redirect to account page
                        wp_redirect(get_permalink(wpbooking_get_option('myaccount-page')));
                        die;

                    }
                }

            }


            // Register
            if (WPBooking_Input::post('action') == 'wpbooking_do_register') {
                $this->_do_register();
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
            $validate->set_rules('rg-login', esc_html__('Username', 'wpbooking'), 'required|min_length[4]|max_length[50]|alpha_dash|is_unique[users.user_login]');
            $validate->set_rules('rg-email', esc_html__('Email', 'wpbooking'), 'required|max_length[100]|valid_email|is_unique[users.user_email]');
            $validate->set_rules('rg-password', esc_html__('Password', 'wpbooking'), 'required|min_length[8]|max_length[50]');
            $validate->set_rules('rg-repassword', esc_html__('Re-Type Password', 'wpbooking'), 'required|min_length[8]|max_length[50]|matches[rg-password]');
            $validate->set_rules('term_condition', esc_html__('Term & Condition', 'wpbooking'), 'required');

            $is_validated = TRUE;

            if (!$validate->run()) {
                wpbooking_set_message($validate->error_string(), 'danger');
                $is_validated = FALSE;
            }

            // Allow to add filter before register
            $is_validated = apply_filters('wpbooking_register_validate', $is_validated);

            if ($is_validated) {
                // Start Create User
                $user_email = WPBooking_Input::post('rg-email');
                $user_name = WPBooking_Input::post('rg-login');
                $password = WPBooking_Input::post('rg-password');
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

                    WPBooking_Session::set('wpbooking_user_pass', $password);
                    // Hook after Register Success, maybe sending some email...etc
                    /**
                     * @see WPBooking_User::_send_registration_email()
                     */
                    do_action('wpbooking_register_success', $user_id);

                    WPBooking_Session::destroy('wpbooking_user_pass');
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
            $validate->set_rules('login', esc_html__('Username', 'wpbooking'), 'required|max_length[100]|is_unique[users.user_login]');
            $validate->set_rules('email', esc_html__('Email', 'wpbooking'), 'required|max_length[100]|valid_email|is_unique[users.user_email]');
            $validate->set_rules('password', esc_html__('Password', 'wpbooking'), 'required|min_length[6]|max_length[100]');
            $validate->set_rules('repassword', esc_html__('Re-Type Password', 'wpbooking'), 'required|min_length[6]|max_length[100]|matches[password]');
            $validate->set_rules('service_type[]', esc_html__('Certificate', 'wpbooking'), 'required');
            $validate->set_rules('term_condition', esc_html__('Term & Condition', 'wpbooking'), 'required');

            $is_validated = TRUE;

            if (!$validate->run()) {

                $is_validated = FALSE;

                // Validate Certificate Upload
                $is_select_service = FALSE;
                $service_type = WPBooking_Input::post('service_type');
                if (is_array($service_type) and !empty($service_type)) {
                    foreach ($service_type as $k => $v) {
                        if (!empty($v[$k]['name'])) $is_select_service = TRUE;
                    }
                }

                if (!$is_select_service) {
                    $is_validated = FALSE;
                    $validate->set_error_message('service_type', esc_html__('Please select at lease one Service Type!', 'wpbooking'));
                }


                wpbooking_set_message($validate->error_string(), 'danger');
            }


            // Allow to add filter before register
            $is_validated = apply_filters('wpbooking_partner_register_validate', $is_validated);


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
                    /**
                     * @see WPBooking_User::_send_partner_registration_email()
                     */
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
            $subject = sprintf(esc_html__('New Registration: %s', 'wpbooking'), $title);

            // Send To Admin
            if (wpbooking_get_option('on_registration_email_admin') and wpbooking_get_option('registration_email_admin')) {
                $to = wpbooking_get_option('system_email');
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
                $to = wpbooking_get_option('system_email');
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
                    $v = apply_filters('wpbooking_registration_email_shortcode', FALSE, $k, $user_id);
                    $v = apply_filters('wpbooking_registration_email_shortcode_' . $k, $v, $user_id);
                    $content = str_replace('[' . $k . ']', $v, $content);
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
                'user_login'     => esc_html__('Your Username', 'wpbooking'),// Default Value for Preview
                'user_email'     => esc_html__('email@domain.com', 'wpbooking'),
                'profile_url'    => esc_html__('http://domain.com/profile.php', 'wpbooking'),
                'edit_user_url'  => esc_html__('http://domain.com/wp-admin/user-edit.php', 'wpbooking'),
                'user_pass'      => esc_html__('Default Password', 'wpbooking')
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
                case "user_login":
                    return $user->user_login;
                    break;

                case "user_email":
                    return $user->user_email;
                    break;

                case "profile_url":
                    return WPBooking_User::inst()->account_page_url();
                    break;

                case "edit_user_url":
                    return add_query_arg( 'user_id', $user_id, self_admin_url( 'user-edit.php' ) );
                    break;

                case "user_pass":
                    $user_pass = WPBooking_Session::get('wpbooking_user_pass');

                    return $user_pass;
                    break;
            }

            return $content;
        }

        /**
         * Preview Registration Email
         *
         * @since 1.0
         * @author dungdt
         */
        function _preview_email()
        {
            $allowed = array(
                'registration_email_customer',
                'registration_email_admin',
                'registration_partner_email_to_partner',
                'registration_partner_email_to_admin',
            );
            if (in_array(WPBooking_Input::get('email'), $allowed)) {

                $content = wpbooking_get_option(WPBooking_Input::get('email'));
                $content = do_shortcode($content);

                // Apply Default Shortcode Content
                $all_shortcodes = $this->get_email_shortcodes();

                if (!empty($all_shortcodes)) {
                    foreach ($all_shortcodes as $k => $v) {
                        $content = str_replace('[' . $k . ']', $v, $content);
                    }
                }

                $content = WPBooking_Email::inst()->apply_css($content);
                echo($content);
                die;
            }
        }

        /**
         * Add Js, CSS To Account Page
         *
         * @since 1.0
         * @author dungdt
         */
        function _add_scripts()
        {
            if (get_query_var('service')) {
                wp_enqueue_style('full-calendar', wpbooking_admin_assets_url('/css/fullcalendar.min.css'), FALSE, '1.1.6');

                wp_enqueue_script('moment-js', wpbooking_admin_assets_url('js/moment.min.js'), array('jquery'), null, TRUE);

                wp_enqueue_script('full-calendar', wpbooking_admin_assets_url('js/fullcalendar.min.js'), array('jquery', 'moment-js'), null, TRUE);

                wp_enqueue_script('fullcalendar-lang', wpbooking_admin_assets_url('/js/lang-all.js'), array('jquery'), null, TRUE);

                wp_enqueue_script('wpbooking-calendar-room', wpbooking_admin_assets_url('js/wpbooking-calendar-room.js'), array('jquery', 'jquery-ui-datepicker'), null, TRUE);
            }

            if (in_array(get_query_var('tab'), array('orders', 'booking_history')) and WPBooking_Input::get('subtab') == 'calendar') {

                wp_enqueue_style('full-calendar', wpbooking_admin_assets_url('/css/fullcalendar.min.css'), FALSE, '1.1.6');

                wp_enqueue_script('moment-js', wpbooking_admin_assets_url('js/moment.min.js'), array('jquery'), null, TRUE);

                wp_enqueue_script('full-calendar', wpbooking_admin_assets_url('js/fullcalendar.min.js'), array('jquery', 'moment-js'), null, TRUE);

                wp_enqueue_script('fullcalendar-lang', wpbooking_admin_assets_url('/js/lang-all.js'), array('jquery'), null, TRUE);

            }
        }

        /**
         * Hook callback for Handle My Account Page Actions
         *
         * @since 1.0
         * @author dungdt
         *
         */
        function _myaccount_page_handler()
        {
            $action = WPBooking_Input::post('action');
            switch ($action) {
                case "wpbooking_save_service":
                    if (is_user_logged_in()) {
                        $validate = $this->validate_service();
                        if ($validate) {
                            if ($service_id = get_query_var('service')) {
                                $service = get_post($service_id);
                                // Update
                                wp_update_post(array(
                                    'ID'           => $service_id,
                                    'post_title'   => WPBooking_Input::post('service_title'),
                                    'post_content' => WPBooking_Input::post('service_content'),
                                    'post_author'  => $service->post_author
                                ));

                                wpbooking_set_message(esc_html__('Update Successful', 'wpbooking'), 'success');

                                // Save Metabox
                                //WPBooking_Metabox::inst()->do_save_metabox($service_id);

                                do_action('wpbooking_after_user_update_service', $service_id);

                            } else {
                                // Insert
                                $service_id = wp_insert_post(array(
                                    'post_title'   => WPBooking_Input::post('service_title'),
                                    'post_content' => WPBooking_Input::post('service_content'),
                                ));

                                if (!is_wp_error($service_id)) {
                                    // Success
                                    wpbooking_set_message(esc_html__('Create Successful', 'wpbooking'), 'success');

                                    // Save Metabox
                                    //WPBooking_Metabox::inst()->do_save_metabox($service_id);

                                    do_action('wpbooking_after_user_insert_service_success', $service_id);

                                    // Redirect To Edit Page
                                    $myaccount_page = get_permalink(wpbooking_get_option('myaccount-page'));
                                    $edit_url = $myaccount_page . 'service/' . $service_id;
                                    wp_redirect(esc_url_raw($edit_url));
                                    die;

                                } else {
                                    // Create Error
                                    wpbooking_set_message($service_id->get_error_message(), 'danger');

                                    do_action('wpbooking_after_user_insert_service_error', $service_id);
                                }


                            }

                        }
                    }

                    break;

                // Update Profile
                case "wpbooking_update_profile":
                    if (is_user_logged_in()) {

                        do_action('wpbooking_before_update_profile');

                        $validate = new WPBooking_Form_Validator();
                        $validate->set_rules('u_fist_name', esc_html__('Fist name', 'wpbooking'), 'required|max_length[500]');
                        $validate->set_rules('u_last_name', esc_html__('Fist name', 'wpbooking'), 'required|max_length[500]');
                        $validate->set_rules('u_email', esc_html__('Email', 'wpbooking'), 'required|max_length[255]|valid_email');
                        $validate->set_rules('u_phone', esc_html__('Phone Number', 'wpbooking'), 'required|max_length[255]');
                        $validate->set_rules('u_address', esc_html__('Address', 'wpbooking'), 'required');

                        $is_validate = TRUE;
                        $is_updated = FALSE;

                        if (!$validate->run()) {
                            $is_validate = FALSE;
                            wpbooking_set_message($validate->error_string(), 'danger');
                        }

                        $is_validate = apply_filters('wpbooking_update_profile_validate', $is_validate);

                        if ($is_validate) {
                            // Start Update
                            $is_updated = wp_update_user(array(
                                'ID'           => get_current_user_id(),
                                'first_name' => WPBooking_Input::post('u_fist_name'),
                                'last_name' => WPBooking_Input::post('u_last_name'),
                                'user_email'   => WPBooking_Input::post('u_email'),
                            ));

                            if (is_wp_error($is_updated)) {
                                wpbooking_set_message($is_updated->get_error_message(), 'danger');
                            } else {
                                wpbooking_set_message(esc_html__('Updated Successfully', 'wpbooking'), 'success');
                                // Update meta user
                                update_user_meta(get_current_user_id(), 'avatar', WPBooking_Input::post('u_avatar'));
                                update_user_meta(get_current_user_id(), 'phone', WPBooking_Input::post('u_phone'));
                                update_user_meta(get_current_user_id(), 'address', WPBooking_Input::post('u_address'));
                                update_user_meta(get_current_user_id(), 'postcode', WPBooking_Input::post('u_postcode'));
                                update_user_meta(get_current_user_id(), 'apt_unit', WPBooking_Input::post('u_apt_unit'));
                            }
                        }

                        do_action('wpbooking_after_update_profile', $is_validate, $is_updated);
                    }
                    break;

                // Change Password
                case "wpbooking_change_password":
                    if (is_user_logged_in()) {

                        do_action('wpbooking_before_change_password');

                        $validate = new WPBooking_Form_Validator();
                        $validate->set_rules('u_password', esc_html__('Password', 'wpbooking'), 'required|max_length[255]');
                        $validate->set_rules('u_new_password', esc_html__('New Password', 'wpbooking'), 'required|max_length[255]');
                        $validate->set_rules('u_re_new_password', esc_html__('New Password Again', 'wpbooking'), 'required|max_length[255]|matches[u_new_password]');

                        $is_validate = TRUE;
                        $is_updated = FALSE;

                        if (!$validate->run()) {
                            $is_validate = FALSE;
                            wpbooking_set_message($validate->error_string(), 'danger');
                        }

                        global $current_user;

                        if (!wp_check_password(WPBooking_Input::post('u_password'), $current_user->user_pass)) {
                            $is_validate = FALSE;
                            wpbooking_set_message(esc_html__('Your Current Password is not correct', 'wpbooking'), 'danger');
                        }

                        $is_validate = apply_filters('wpbooking_change_password_validate', $is_validate);

                        if ($is_validate) {
                            // Start Update
                            $is_updated = wp_update_user(array(
                                'ID'        => get_current_user_id(),
                                'user_pass' => WPBooking_Input::post('u_new_password'),
                            ));

                            if (is_wp_error($is_updated)) {
                                wpbooking_set_message($is_updated->get_error_message(), 'danger');
                            } else {

                                wpbooking_set_message(esc_html__('Password Changed Successfully', 'wpbooking'), 'success');
                            }
                        }


                        do_action('wpbooking_after_change_password', $is_validate, $is_updated);
                    }
                    break;

            }
        }

        /**
         * Validate Post Data and Permission before Saving the Service
         *
         * @since 1.0
         * @author dungdt
         * @return bool
         */
        function validate_service()
        {
            // Is Logged In?
            if (!is_user_logged_in()) return FALSE;

            $service = get_post(get_query_var('service'));

            $myaccount_page = get_permalink(wpbooking_get_option('myaccount-page'));

            // Service Exists
            if (!$service or $service->post_type != 'wpbooking_service') {
                wpbooking_set_message(esc_html__('Service does not exists', 'wpbooking'), 'danger');
                wp_redirect(add_query_arg(array('tab' => 'services'), $myaccount_page));
                die;
            }

            // Permission
            if ($service->post_author != get_current_user_id() or !current_user_can('manage_options')) {
                wpbooking_set_message(esc_html__('You do not have permission to access this page', 'wpbooking'), 'danger');
                wp_redirect(add_query_arg(array('tab' => 'services'), $myaccount_page));
                die;
            }

            $validate = apply_filters('wpbooking_user_validate_service', TRUE, $service);

            return $validate;

        }

        /**
         * Hook Callback to create Endpoints in Account Page
         *
         * @since 1.0
         * @author dungdt
         */
        function add_endpoints()
        {
            // Tab
            add_rewrite_endpoint('tab', EP_PAGES);

            // Edit, Create Service
            add_rewrite_endpoint('service', EP_PAGES);

            // Detail Order
            add_rewrite_endpoint('order-detail', EP_PAGES);

            // update-profile
            add_rewrite_endpoint('update-profile', EP_PAGES);

            // View Profile
            add_rewrite_endpoint('profile', EP_PAGES);

            // Lost Password
            add_rewrite_endpoint('lost-password', EP_PAGES);

            // reset password
            add_rewrite_endpoint('reset-password', EP_PAGES);


            flush_rewrite_rules();

        }


        /**
         * Get All Tabs in My Account Pages.
         *
         * @since 1.0
         * @author dungdt
         */
        function get_tabs()
        {
            $tabs = array(
                'dashboard' => array(
                    'label' => esc_html__('Dashboard', 'wpbooking'),
                ),
            );
            $tabs['booking_history'] = array(
                'label'    => esc_html__('Your Booking', 'wpbooking'),
            );
            $tabs['edit_profile'] = array('label' => esc_html__('Edit Profile', 'wpbooking'));

            $tabs['logout'] = array('label' => esc_html__('Logout', 'wpbooking'));

            return apply_filters('wpbooking_myaccount_tabs', $tabs);
        }

        function _myaccount_shortcode($attr = array(), $content = FALSE)
        {

            // Set Page Tabs
            if (get_query_var('order-detail')) {
                set_query_var('tab', 'booking_history');
            }
            if (get_query_var('service')) {
                set_query_var('tab', 'services');
            }
            if (get_query_var('update-profile')) {
                set_query_var('tab', 'profile');
            }
            if (get_query_var('profile')) {
                set_query_var('tab', 'profile');
            }

            return wpbooking_load_view('account/index');
        }

        function _partner_register_shortcode()
        {

            return wpbooking_load_view('account/partner-register');
        }

        function _add_shortcode()
        {
            add_shortcode('wpbooking-myaccount', array($this, '_myaccount_shortcode'));
            //add_shortcode('wpbooking-partner-register', array($this, '_partner_register_shortcode'));
        }

        /**
         * Create an User in Checkout Step
         *
         * @since 1.0
         * @author dungdt
         *
         * @param array $data
         * @return bool|int|WP_Error
         */
        function order_create_user($data = array())
        {
            $data = wp_parse_args($data, array(
                'user_email' => '',
                'first_name' => '',
                'last_name'  => '',
                'user_pass'  => ''
            ));
            if (!$data['user_email']) return FALSE;

            if (!$data['user_pass']) $data['user_pass'] = $this->generate_password();

            $user_name = $this->generate_username();
            if ($user_name) {

                $create_user = wp_insert_user(array(
                    'user_login' => $user_name,
                    'user_email' => $data['user_email'],
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'user_pass'  => $data['user_pass']
                ));

                if (!is_wp_error($create_user)) {

                    // Set Global for Email Shortcode Access
                    WPBooking_Session::set('wpbooking_user_pass', $data['user_pass']);
                    WPBooking_Session::set('wpbooking_user_id', $create_user);

                    do_action('wpbooking_register_success', $create_user);

                    WPBooking_Session::destroy('wpbooking_user_pass');

                    return $create_user;
                }
            }

            return FALSE;

        }

        /**
         * Get Permalink of Account Page
         *
         * @since 1.0
         * @author dungdt
         *
         * @return false|string
         */
        function account_page_url()
        {
            $myaccount_page = get_permalink(wpbooking_get_option('myaccount-page'));

            return $myaccount_page;
        }

        /**
         * Count Number of Comment inside Comment Loop or use specific author email
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $author_email bool|string
         * @return null|string
         */
        function count_reviews($author_email = FALSE)
        {
            if (!$author_email) $author_email = get_comment_author_email();
            global $wpdb;

            return $count = $wpdb->get_var('SELECT COUNT(comment_ID) FROM ' . $wpdb->comments . ' WHERE comment_approved=1 and comment_parent=0 and  comment_author_email = "' . $author_email . '"');
        }

        /**
         * Get Term & Condition Page Permalink
         *
         * @since 1.0
         * @author dungdt
         *
         * @return false|string
         */
        function get_term_condition_link()
        {
            if ($page = wpbooking_get_option('term-page') and get_post($page)) {
                return get_permalink($page);
            }
        }

        /**
         * Generate Username
         *
         * @since 1.0
         * @author dungdt
         *
         * @return string
         */
        function generate_username()
        {
            $prefix = apply_filters('wpbooking_generated_username_prefix', esc_html__('wpbooking_', 'wpbooking'));
            $user_name = $prefix . time() . rand(0, 999);
            if (username_exists($user_name)) return $this->generate_username();

            return $user_name;
        }

        /**
         * Generate Random Password
         *
         * @since 1.0
         * @author dungdt
         *
         * @param int $length
         * @return string
         */
        function generate_password($length = 10)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            return strtolower($randomString);
        }
        /**
         * Generate Change Avatar
         *
         * @since 1.0
         * @author quandq
         *
         * @return string
         */
        function _change_profile_avatar($avatar, $id_or_email, $args ){
            if ( ! is_numeric( $id_or_email ) ) {
                if(!empty($id_or_email->comment_author_email)){
                    $data = get_user_by('email',$id_or_email->comment_author_email);
                }else if(!is_object($id_or_email)){
                    $data = get_user_by('email',$id_or_email);
                }
                if(!empty($data->ID)){
                    $id_or_email = $data->ID;
                }
            }
            $gravatar_pic_url = get_user_meta($id_or_email, 'avatar', true);
            if(!empty($gravatar_pic_url)){
                return '<img alt="avatar"  style="height: '.$args['height'].'px; width: '.$args['width'].'px;"  width='.$args['width'].' height='.$args['height'].' src="'.$gravatar_pic_url.'" class="avatar" >';
            }
            return $avatar;
        }

        /**
         * Retrieve password
         *
         * @since 1.0
         * @author tienhd
         */
        function _retrieve_password(){

            if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
                $account_page = wpbooking_get_option('myaccount-page');
                if ($account_page) {
                    $url = get_permalink($account_page) . 'lost-password';
                    wp_redirect($url);
                    exit();
                }
            }

            if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
                $errors = retrieve_password();
                if (is_wp_error($errors)) {
                    // Errors found
                    $account_page = wpbooking_get_option('myaccount-page');
                    if(!empty($account_page))
                        $redirect_url = get_permalink($account_page).'lost-password';

                    wpbooking_set_message(self::get_error_message($errors->get_error_code()),'danger');
                    $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
                } else {
                    // Email sent
                    $account_page = wpbooking_get_option('myaccount-page');
                    if(!empty($account_page))
                        $redirect_url = get_permalink($account_page);
                    else
                        $redirect_url = wp_login_url();

                    wpbooking_set_message(esc_html__('Check your email for a link to reset your password.','wpbooking'),'info');
                    $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
                }
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        public function get_error_message( $error_code ) {
            switch ( $error_code ) {
                // Login errors

                case 'empty_username':
                    return esc_html__( 'You need to enter a username to login.', 'wpbooking' );
                case 'empty_password':
                    return esc_html__( 'You need to enter a password to login.', 'wpbooking' );
                case 'invalid_username':
                    return esc_html__(
                        "We don't have any users with that email address. Maybe you used a different one when signing up?",
                        'wpbooking'
                    );
                case 'incorrect_password':
                    return esc_html__('The password you entered wasn\'t quite right.','wpbooking');

                case 'email':
                    return esc_html__( 'The email address you entered is not valid.', 'wpbooking' );

                case 'email_exists':
                    return esc_html__( 'An account exists with this email address.', 'wpbooking' );

                case 'empty_username':
                    return esc_html__( 'You need to enter your email address to continue.', 'wpbooking' );
                case 'invalid_email':
                case 'invalidcombo':
                    return esc_html__( 'There are no users registered with this email address.', 'wpbooking' );

                case 'expiredkey':
                case 'invalidkey':
                    return esc_html__( 'The password reset link you used is not valid anymore.', 'wpbooking' );

                case 'password_reset_mismatch':
                    return esc_html__( "The two passwords you entered don't match.", 'wpbooking' );

                case 'password_reset_empty':
                    return esc_html__( "Sorry, we don't accept empty passwords.", 'wpbooking' );

                default:
                    break;
            }

            return esc_html__( 'An unknown error occurred. Please try again later.', 'personalize-login' );
        }


        /**
         * redirect reset pass url
         * @return string
         */
        function redirect_reset_password_url(){
            if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {

                $account_page = wpbooking_get_option('myaccount-page');
                $user = check_password_reset_key( WPBooking_Input::request('key'), WPBooking_Input::request('login') );
                if ( ! $user || is_wp_error( $user ) ) {

                    if(!empty($account_page))
                        $redirect_url = get_permalink($account_page);
                    else
                        $redirect_url = wp_login_url();

                    if ( $user && $user->get_error_code() === 'expired_key' ) {
                        wpbooking_set_message(self::get_error_message('expiredkey'),'danger');
                    } else {
                        wpbooking_set_message(self::get_error_message('invalidkey'),'danger');
                    }
                    $redirect_url = add_query_arg( 'reset', 'error', $redirect_url );
                    wp_redirect($redirect_url);

                    exit;
                }

                if(!empty($account_page)){
                    $redirect_url = get_permalink($account_page).'reset-password';
                }else{
                    $redirect_url = home_url('/');
                }

                $redirect_url = add_query_arg( 'login', esc_attr( WPBooking_Input::request('login') ), $redirect_url );
                $redirect_url = add_query_arg( 'key', esc_attr( WPBooking_Input::request('key') ), $redirect_url );

                wp_redirect( $redirect_url );
                exit;
            }
        }

        function _reset_password(){
            if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
                $account_page = wpbooking_get_option('myaccount-page');

                $rp_key = WPBooking_Input::request('rp_key');
                $rp_login = WPBooking_Input::request('rp_login');


                $user = check_password_reset_key($rp_key, $rp_login);

                if (!$user || is_wp_error($user)) {
                    if (!empty($account_page))
                        $redirect_url = get_permalink($account_page);
                    else
                        $redirect_url = wp_login_url();

                    if ($user && $user->get_error_code() === 'expired_key') {
                        wpbooking_set_message(self::get_error_message('expiredkey'), 'danger');
                    } else {
                        wpbooking_set_message(self::get_error_message('invalidkey'), 'danger');
                    }
                    $redirect_url = add_query_arg('reset', 'error', $redirect_url);
                    wp_redirect($redirect_url);
                    exit;
                }

                $validator = new WPBooking_Form_Validator();

                $validator->set_rules('new_password',esc_html__('New password','wpbooking'),'required|min_length[8]|max_length[100]');
                $validator->set_rules('confirm_password',esc_html__('Confirm password','wpbooking'),'required|min_length[8]|max_length[100]|matches[new_password]');

                if(!$validator->run()){
                    wpbooking_set_message($validator->error_string(),'danger');
                    $redirect_url = get_permalink($account_page) . 'reset-password';
                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('reset', 'error', $redirect_url);
                    wp_redirect($redirect_url);
                    exit;
                }

                // Parameter checks OK, reset password
                reset_password($user, $_POST['new_password']);
                wpbooking_set_message(esc_html__('Your password has been changed. You can sign in now.', 'wpbooking'), 'success');
                $rd_after_reset = get_permalink($account_page);
                $rd_after_reset = add_query_arg('password', 'changed', $rd_after_reset);
                wp_redirect($rd_after_reset);

                exit;

            }
        }
        /**
         * redirect logout url
         * @return string
         */
        function redirect_logout_url($logouturl,$redir){
            $account_page = wpbooking_get_option('myaccount-page');
            if($account_page) {
                $redir = get_permalink($account_page);
                return $logouturl . '&amp;redirect_to=' . urlencode($redir);
            }else {
                return $logouturl;
            }
        }
        /**
         * redirect login url
         * @return string
         */
        function redirect_login_url($login_url){
            $account_page = wpbooking_get_option('myaccount-page');
            if($account_page) {
                $redir = get_permalink($account_page);
                return $redir;
            }else {
                return $login_url;
            }
        }


        public function load_lost_password_form(){
            if(  WPBooking_Input::get('wb-reset-pass') == 'true' ){

                return wpbooking_load_view('account/reset-password');
            }
            return wpbooking_load_view('account/lost-password');

        }



        /**
         * List Preferred Language
         *
         * @since 1.0
         * @author quandq
         *
         * @return array
         */
        static function _get_list_preferred_language(){
            $language_codes = array(
                'en' => 'English' ,
                'aa' => 'Afar' ,
                'ab' => 'Abkhazian' ,
                'af' => 'Afrikaans' ,
                'am' => 'Amharic' ,
                'ar' => 'Arabic' ,
                'as' => 'Assamese' ,
                'ay' => 'Aymara' ,
                'az' => 'Azerbaijani' ,
                'ba' => 'Bashkir' ,
                'be' => 'Byelorussian' ,
                'bg' => 'Bulgarian' ,
                'bh' => 'Bihari' ,
                'bi' => 'Bislama' ,
                'bn' => 'Bengali/Bangla' ,
                'bo' => 'Tibetan' ,
                'br' => 'Breton' ,
                'ca' => 'Catalan' ,
                'co' => 'Corsican' ,
                'cs' => 'Czech' ,
                'cy' => 'Welsh' ,
                'da' => 'Danish' ,
                'de' => 'German' ,
                'dz' => 'Bhutani' ,
                'el' => 'Greek' ,
                'eo' => 'Esperanto' ,
                'es' => 'Spanish' ,
                'et' => 'Estonian' ,
                'eu' => 'Basque' ,
                'fa' => 'Persian' ,
                'fi' => 'Finnish' ,
                'fj' => 'Fiji' ,
                'fo' => 'Faeroese' ,
                'fr' => 'French' ,
                'fy' => 'Frisian' ,
                'ga' => 'Irish' ,
                'gd' => 'Scots/Gaelic' ,
                'gl' => 'Galician' ,
                'gn' => 'Guarani' ,
                'gu' => 'Gujarati' ,
                'ha' => 'Hausa' ,
                'hi' => 'Hindi' ,
                'hr' => 'Croatian' ,
                'hu' => 'Hungarian' ,
                'hy' => 'Armenian' ,
                'ia' => 'Interlingua' ,
                'ie' => 'Interlingue' ,
                'ik' => 'Inupiak' ,
                'in' => 'Indonesian' ,
                'is' => 'Icelandic' ,
                'it' => 'Italian' ,
                'iw' => 'Hebrew' ,
                'ja' => 'Japanese' ,
                'ji' => 'Yiddish' ,
                'jw' => 'Javanese' ,
                'ka' => 'Georgian' ,
                'kk' => 'Kazakh' ,
                'kl' => 'Greenlandic' ,
                'km' => 'Cambodian' ,
                'kn' => 'Kannada' ,
                'ko' => 'Korean' ,
                'ks' => 'Kashmiri' ,
                'ku' => 'Kurdish' ,
                'ky' => 'Kirghiz' ,
                'la' => 'Latin' ,
                'ln' => 'Lingala' ,
                'lo' => 'Laothian' ,
                'lt' => 'Lithuanian' ,
                'lv' => 'Latvian/Lettish' ,
                'mg' => 'Malagasy' ,
                'mi' => 'Maori' ,
                'mk' => 'Macedonian' ,
                'ml' => 'Malayalam' ,
                'mn' => 'Mongolian' ,
                'mo' => 'Moldavian' ,
                'mr' => 'Marathi' ,
                'ms' => 'Malay' ,
                'mt' => 'Maltese' ,
                'my' => 'Burmese' ,
                'na' => 'Nauru' ,
                'ne' => 'Nepali' ,
                'nl' => 'Dutch' ,
                'no' => 'Norwegian' ,
                'oc' => 'Occitan' ,
                'om' => '(Afan)/Oromoor/Oriya' ,
                'pa' => 'Punjabi' ,
                'pl' => 'Polish' ,
                'ps' => 'Pashto/Pushto' ,
                'pt' => 'Portuguese' ,
                'qu' => 'Quechua' ,
                'rm' => 'Rhaeto-Romance' ,
                'rn' => 'Kirundi' ,
                'ro' => 'Romanian' ,
                'ru' => 'Russian' ,
                'rw' => 'Kinyarwanda' ,
                'sa' => 'Sanskrit' ,
                'sd' => 'Sindhi' ,
                'sg' => 'Sangro' ,
                'sh' => 'Serbo-Croatian' ,
                'si' => 'Singhalese' ,
                'sk' => 'Slovak' ,
                'sl' => 'Slovenian' ,
                'sm' => 'Samoan' ,
                'sn' => 'Shona' ,
                'so' => 'Somali' ,
                'sq' => 'Albanian' ,
                'sr' => 'Serbian' ,
                'ss' => 'Siswati' ,
                'st' => 'Sesotho' ,
                'su' => 'Sundanese' ,
                'sv' => 'Swedish' ,
                'sw' => 'Swahili' ,
                'ta' => 'Tamil' ,
                'te' => 'Tegulu' ,
                'tg' => 'Tajik' ,
                'th' => 'Thai' ,
                'ti' => 'Tigrinya' ,
                'tk' => 'Turkmen' ,
                'tl' => 'Tagalog' ,
                'tn' => 'Setswana' ,
                'to' => 'Tonga' ,
                'tr' => 'Turkish' ,
                'ts' => 'Tsonga' ,
                'tt' => 'Tatar' ,
                'tw' => 'Twi' ,
                'uk' => 'Ukrainian' ,
                'ur' => 'Urdu' ,
                'uz' => 'Uzbek' ,
                'vi' => 'Vietnamese' ,
                'vo' => 'Volapuk' ,
                'wo' => 'Wolof' ,
                'xh' => 'Xhosa' ,
                'yo' => 'Yoruba' ,
                'zh' => 'Chinese' ,
                'zu' => 'Zulu' ,
            );
            return $language_codes;
        }
        /**
         * Get Detail Rate
         *
         * @since 1.0
         * @author quandq
         *
         * @return number
         */
        static function get_detail_rate($user_id){
            $data = array("rate"=>0,'total'=>0);
            global $wpdb;
            $sql = "SELECT SQL_CALC_FOUND_ROWS
                        SUM({$wpdb->prefix}commentmeta.meta_value) as rate,
                        count({$wpdb->prefix}comments.comment_ID) as total
                    FROM
                        {$wpdb->prefix}comments
                    INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}comments.comment_post_ID
                    INNER JOIN {$wpdb->prefix}commentmeta ON {$wpdb->prefix}comments.comment_ID = {$wpdb->prefix}commentmeta.comment_id
                    WHERE
                        1 = 1
                    AND {$wpdb->prefix}posts.post_author = '{$user_id}'
                    AND {$wpdb->prefix}posts.post_type = 'wpbooking_service'
                    AND {$wpdb->prefix}comments.comment_parent = '0'
                    AND {$wpdb->prefix}commentmeta.meta_key = 'wpbooking_review'
                    AND {$wpdb->prefix}commentmeta.meta_value > 0
                    AND {$wpdb->prefix}comments.comment_approved = 1
                    ORDER BY
                        {$wpdb->prefix}comments.comment_ID DESC";
            $rs=$wpdb->get_row($sql);
            if(!empty($rs)){
                $data['rate'] = number_format(($rs->rate/$rs->total),1);
                $data['total'] = $rs->total;
            }
           return $data;
        }
        /**
         * Count Review By Rate
         *
         * @since 1.0
         * @author quandq
         *
         * @return number
         */
        static function count_review_by_rate($user_id,$rate){

            $number = 0;
            global $wpdb;
            $next_rate = ($rate+1);
            $sql = "SELECT SQL_CALC_FOUND_ROWS *
                    FROM
                        {$wpdb->prefix}comments
                    INNER JOIN {$wpdb->prefix}posts ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}comments.comment_post_ID
                    INNER JOIN {$wpdb->prefix}commentmeta ON {$wpdb->prefix}comments.comment_ID = {$wpdb->prefix}commentmeta.comment_id
                    WHERE
                        1 = 1
                    AND {$wpdb->prefix}posts.post_author = '{$user_id}'
                    AND {$wpdb->prefix}posts.post_type = 'wpbooking_service'
                    AND {$wpdb->prefix}comments.comment_parent = '0'
                    AND {$wpdb->prefix}commentmeta.meta_key = 'wpbooking_review'
                    AND
                    (
                        {$wpdb->prefix}commentmeta.meta_value >= {$rate} and {$wpdb->prefix}commentmeta.meta_value < {$next_rate}
                    )
                    AND {$wpdb->prefix}comments.comment_approved = 1
                    ORDER BY
                        {$wpdb->prefix}comments.comment_ID DESC
                    LIMIT 1";
            $wpdb->get_row($sql);
            $total_item=$wpdb->get_var('SELECT FOUND_ROWS()');
            if(!empty($total_item)){
                $number = $total_item;
            }
            return $number;
        }
        static function inst()
        {
            if (!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_User::inst();
}