<?php
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
            add_action('init', array($this, '_login_register_handler'),11);


            /**
             * Send Email to User after Registration
             *
             * @since 1.0
             * @author dungdt
             */
            add_action('wpbooking_register_success', array($this, '_send_registration_email'),10,1);

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
            add_filter('logout_url',array($this,'_redirect_logout_url'),10,2);

            /**
             * Redirect login url
             *
             * @since 1.0
             * @author tienhd
             *
             * update 1.2
             * disable redirect login admin
             * @author quandq
             */
//            add_filter('login_url',array($this,'_redirect_login_url'),10,3);

            /**
             * Redirect reset password url
             *
             * @since 1.0
             * @author tienhd
             */
            add_action( 'login_form_rp', array( $this, '_redirect_reset_password_url' ) );
            add_action( 'login_form_resetpass', array( $this, '_redirect_reset_password_url' ) );

            /**
             * Do reset password
             *
             * @since 1.0
             * @author tienhd
             */
            add_action( 'login_form_resetpass', array( $this, '_reset_password' ) );

            /**
             * Email reset password
             *
             * @since 1.0
             * @author tienhd
             */
            add_filter( 'retrieve_password_message', array( $this, '_email_retrieve_password_template' ), 10, 4 );

            /**
             * Send email message when change password successful
             *
             * @since 1.0
             * @author tienhd
             */
            add_action('send_password_change_email',array($this, '_send_email_changed_password'),10, 3);

            add_action('template_redirect',array($this,'_redirect_register_url'));

        }

        function _redirect_register_url(){
            global $wp_query;

            if(!wpbooking_is_any_register() and isset($wp_query->query_vars['register'])){
                wp_redirect($this->get_login_url());
            }
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
                        WPBooking()->set('error_code',$user->get_error_code());
                        wpbooking_set_message($this->get_error_message($user->get_error_code()), 'danger');
                    }else{
                        wpbooking_set_message(esc_html__('You need to enter an username and a password to login', 'wp-booking-management-system'), 'danger');
                    }
                } else {
                    // Login Success
                    // Redirect if url is exists
                    if ($redirect = WPBooking_Input::request('redirect_to')) {
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
            if (WPBooking_Input::post('action') == 'wpbooking_do_register' && wpbooking_is_any_register()) {
                $this->_do_register();
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
            $validate->set_rules('rg-login', esc_html__('username', 'wp-booking-management-system'), 'required|min_length[4]|max_length[50]|is_unique[users.user_login]');
            $validate->set_rules('rg-email', esc_html__('email', 'wp-booking-management-system'), 'required|max_length[100]|valid_emails|is_unique[users.user_email]');
            $validate->set_rules('rg-password', esc_html__('password', 'wp-booking-management-system'), 'required|min_length[8]|max_length[50]');
            $validate->set_rules('rg-repassword', esc_html__('re-type password', 'wp-booking-management-system'), 'required|min_length[8]|max_length[50]|matches[rg-password]');
            $validate->set_rules('privacy', esc_html__('Privacy policy', 'wp-booking-management-system'), 'required');
            $is_validated = TRUE;
            //Validate
            if (!$validate->run()) {
                wpbooking_set_message($validate->error_string(), 'danger');
                $error_field = $validate->get_error_fields();

                if(WPBooking_Input::post('rg-email') && email_exists(WPBooking_Input::post('rg-email'))){
                    wpbooking_set_message(esc_html__('This email has been registered. You can login','wp-booking-management-system').'<a href="'.esc_url($this->get_login_url()).'">'.esc_html__('here','wp-booking-management-system').'</a>', 'danger');
                }
                if(WPBooking_Input::post('rg-login') && !validate_username(WPBooking_Input::post('rg-login'))){
                    wpbooking_set_message(esc_html__('The username can only contain underscores and alphanumeric characters and dashes. It must be unique, and must not include spaces.','wp-booking-management-system'), 'danger');
                    if(!isset($error_field['rg-login'])){
                        $error_field['rg-login'] = esc_html__('Username is invalid','wp-booking-management-system');
                    }
                }
                if(WPBooking_Input::post('rg-login') && username_exists(WPBooking_Input::post('rg-login'))){
                    wpbooking_set_message(esc_html__('This username has been registered. You can login','wp-booking-management-system').'<a href="'.esc_url($this->get_login_url()).'">'.esc_html__('here','wp-booking-management-system').'</a>', 'danger');
                    $error_field['rg-login'] = esc_html__('Username exist','wp-booking-management-system');
                }
                WPBooking()->set('error_r_field', $error_field);
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
                    wpbooking_set_message(esc_html__('User cannot be created. Please try it again later', 'wp-booking-management-system'), 'danger');
                    do_action('wpbooking_register_failed', $user_id);

                } else {
                    wpbooking_set_message(esc_html__('Your account is registered successfully. You can login now:', 'wp-booking-management-system').'<a href="'.esc_url($this->get_login_url()).'">'.esc_html__('Login', 'wp-booking-management-system').'</a>', 'success');
                    WPBooking_Session::set('wpbooking_user_pass', $password);
                    WPBooking()->set('register','successful');
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
         * Hook Callback for Send Email after Registration, using templates in admin
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $user_id
         */
        function _send_registration_email($user_id)
        {
            if ( is_multisite() )
                $blog_name = $GLOBALS['current_site']->site_name;
            else
                $blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
            $user_data = get_userdata($user_id);
            $title = $user_data->user_nicename . " - " . $user_data->user_email . " - " . $user_data->user_registered;
            $subject = sprintf('['.$blog_name.']'.esc_html__('New Registration: %s', 'wp-booking-management-system'), $title);
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
                'user_login'     => esc_html__('Your Username', 'wp-booking-management-system'),// Default Value for Preview
                'user_email'     => esc_html__('email@domain.com', 'wp-booking-management-system'),
                'profile_url'    => esc_html__('http://domain.com/profile.php', 'wp-booking-management-system'),
                'edit_user_url'  => esc_html__('http://domain.com/wp-admin/user-edit.php', 'wp-booking-management-system'),
                'user_pass'      => esc_html__('Default Password', 'wp-booking-management-system')
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
                    return '<a href="'.WPBooking_User::inst()->account_page_url().'">'.WPBooking_User::inst()->account_page_url().'</a>';
                    break;

                case "edit_user_url":
                    return '<a href="'.add_query_arg( 'user_id', $user_id, self_admin_url( 'user-edit.php' ) ).'">'.add_query_arg( 'user_id', $user_id, self_admin_url( 'user-edit.php' ) ).'</a>';
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

                $head =  wpbooking_get_option("email_header");

                $footer = wpbooking_get_option("email_footer");

                $content = $head.$content.$footer;

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

                                wpbooking_set_message(esc_html__('Update Successful', 'wp-booking-management-system'), 'success');

                                do_action('wpbooking_after_user_update_service', $service_id);

                            } else {
                                // Insert
                                $service_id = wp_insert_post(array(
                                    'post_title'   => WPBooking_Input::post('service_title'),
                                    'post_content' => WPBooking_Input::post('service_content'),
                                ));

                                if (!is_wp_error($service_id)) {
                                    // Success
                                    wpbooking_set_message(esc_html__('Create Successful', 'wp-booking-management-system'), 'success');

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
                    $user = new WP_User(get_current_user_id());
                    if (is_user_logged_in()) {

                        do_action('wpbooking_before_update_profile');

                        $validate = new WPBooking_Form_Validator();
                        $validate->set_rules('u_fist_name', esc_html__('first name', 'wp-booking-management-system'), 'required|max_length[500]');
                        $validate->set_rules('u_last_name', esc_html__('last name', 'wp-booking-management-system'), 'required|max_length[500]');
                        $validate->set_rules('u_email', esc_html__('email', 'wp-booking-management-system'), 'required|max_length[255]|valid_emails');
                        $validate->set_rules('u_phone', esc_html__('phone number', 'wp-booking-management-system'), 'required|max_length[255]');
                        $validate->set_rules('u_address', esc_html__('address', 'wp-booking-management-system'), 'required');

                        $is_validate = TRUE;
                        $is_updated = FALSE;

                        if (!$validate->run()) {
                            $is_validate = FALSE;
                            WPBooking()->set('error_ed_fields',$validate->get_error_fields());
                            wpbooking_set_message($validate->error_string(), 'danger');
                        }

                        $is_validate = apply_filters('wpbooking_update_profile_validate', $is_validate);

                        if ($is_validate) {
                            $full_name  = WPBooking_Input::post( 'u_fist_name' ) . " " . WPBooking_Input::post( 'u_last_name' );
                            $is_updated = wp_update_user( array(
                                'ID'           => get_current_user_id() ,
                                'first_name'   => WPBooking_Input::post( 'u_fist_name' ) ,
                                'last_name'    => WPBooking_Input::post( 'u_last_name' ) ,
                                'user_email'   => WPBooking_Input::post( 'u_email' ) ,
                                'display_name' => $full_name ,
                            ) );

                            if (is_wp_error($is_updated)) {
                                wpbooking_set_message($is_updated->get_error_message(), 'danger');
                            } else {
                                wpbooking_set_message(esc_html__('Updated Successfully', 'wp-booking-management-system'), 'success');
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
                        $validate->set_rules('u_password', esc_html__('Password', 'wp-booking-management-system'), 'required|max_length[255]');
                        $validate->set_rules('u_new_password', esc_html__('New Password', 'wp-booking-management-system'), 'required|min_length[8]|max_length[255]');
                        $validate->set_rules('u_re_new_password', esc_html__('Confirm New Password', 'wp-booking-management-system'), 'required|min_length[8]|max_length[255]|matches[u_new_password]');
                        $is_validate = TRUE;
                        $is_updated = FALSE;
                        if (!$validate->run()) {
                            $is_validate = FALSE;
                            WPBooking_Session::set('error_c_fields',$validate->get_error_fields());
                            wpbooking_set_message($validate->error_string(), 'danger');
                        }
                        global $current_user;
                        if (!wp_check_password(WPBooking_Input::post('u_password'), $current_user->user_pass)) {
                            $is_validate = FALSE;
                            WPBooking_Session::set('old_pass','wb-error');
                            wpbooking_set_message(esc_html__('The old password is incorrect.', 'wp-booking-management-system'), 'danger');
                        }
                        $is_validate = apply_filters('wpbooking_change_password_validate', $is_validate);
                        if ($is_validate) {
                            WPBooking_Session::set('new_changed_pass',WPBooking_Input::post('u_new_password'));
                            // Start Update
                            $is_updated = wp_update_user(array(
                                'ID'        => get_current_user_id(),
                                'user_pass' => WPBooking_Input::post('u_new_password'),
                            ));
                            WPBooking_Session::destroy('new_changed_pass');
                            if (is_wp_error($is_updated)) {
                                wpbooking_set_message($is_updated->get_error_message(), 'danger');
                            } else {
                                wpbooking_set_message(esc_html__('Change password successfully! The new password is changed successfully.', 'wp-booking-management-system'), 'success');
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
                wpbooking_set_message(esc_html__('Service does not exist', 'wp-booking-management-system'), 'danger');
                wp_redirect(add_query_arg(array('tab' => 'services'), $myaccount_page));
                die;
            }

            // Permission
            if ($service->post_author != get_current_user_id() or !current_user_can('manage_options')) {
                wpbooking_set_message(esc_html__('You do not have permission to access this page', 'wp-booking-management-system'), 'danger');
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

            //register
            add_rewrite_endpoint('register', EP_PAGES);


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
                    'label' => esc_html__('Dashboard', 'wp-booking-management-system'),
                ),
            );
            $tabs['booking_history'] = array(
                'label'    => esc_html__('Your Booking', 'wp-booking-management-system'),
            );
            $tabs['edit_profile'] = array('label' => esc_html__('Edit Profile', 'wp-booking-management-system'));

            $tabs['logout'] = array('label' => esc_html__('Logout', 'wp-booking-management-system'));

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
        function order_create_user($data = array(),$meta_fields = array())
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

                    if(!empty($meta_fields) and is_array($meta_fields)){
                        $f = array('user_email', 'user_first_name', 'user_last_name', 'user_phone' , 'user_address','user_postcode','user_apt_unit');
                        foreach($meta_fields as $key=>$value){
                            if (array_key_exists($key, $f))
                            update_user_meta($create_user, str_replace('user_','',$key) , $value['value']);
                        }
                    }

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
         * Update User Billing Info if empty
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $customer_id
         * @param $meta_fields
         */
        public function order_update_user($customer_id,$meta_fields){
            if(!empty($meta_fields) and is_array($meta_fields)){
                $f = array('user_email', 'user_first_name', 'user_last_name', 'user_phone' , 'user_address','user_postcode','user_apt_unit', 'passengers');
                foreach($meta_fields as $key=>$value){
                    if (array_key_exists($key, $f))
                        update_user_meta($customer_id, str_replace('user_','',$key) , $value['value']);
                }
            }
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
            $prefix = apply_filters('wpbooking_generated_username_prefix', esc_html__('wpbooking_', 'wp-booking-management-system'));
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
                $css = WPBooking_Assets::build_css_class('height: '.$args['height'].'px; width: '.$args['width'].'px;');
                return '<img alt="avatar"  width='.$args['width'].' height='.$args['height'].' src="'.$gravatar_pic_url.'" class="avatar '.$css.'" >';

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
                add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
                $errors = retrieve_password();
                if (is_wp_error($errors)) {
                    // Errors found
                    $account_page = wpbooking_get_option('myaccount-page');
                    if(!empty($account_page))
                        $redirect_url = get_permalink($account_page).'lost-password';

                    wpbooking_set_message($this->get_error_message($errors->get_error_code()),'danger');
                    $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
                } else {
                    // Email sent
                    $account_page = wpbooking_get_option('myaccount-page');
                    if(!empty($account_page))
                        $redirect_url = get_permalink($account_page).'lost-password';
                    else
                        $redirect_url = wp_login_url();

                    wpbooking_set_message(esc_html__('Your request for resetting password has been sent. Continuously, please check your email','wp-booking-management-system'),'info');
                    $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
                }
                remove_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
                wp_safe_redirect($redirect_url);
                exit;
            }
        }

        /**
         * @return string
         */
        function set_html_content_type()
        {
            return 'text/html';
        }

        /**
         * get error message
         *
         * @since 1.0
         *
         * @param $error_code
         * @return string
         */
        public function get_error_message( $error_code ) {
            switch ( $error_code ) {
                // Login errors

                case 'empty_username':
                    return esc_html__( 'The username field is not empty.', 'wp-booking-management-system' );
                case 'empty_password':
                    return esc_html__( 'You need to enter a password to login.', 'wp-booking-management-system' );
                case 'invalid_username':
                    return esc_html__("Username is incorrect.",'wp-booking-management-system');
                case 'incorrect_password':
                    return esc_html__('Your password is incorrect.','wp-booking-management-system');
                case 'email':
                    return esc_html__( 'Your email is invalid.', 'wp-booking-management-system' );
                case 'email_exists':
                    return esc_html__( 'An account exists with this email address.', 'wp-booking-management-system' );
                case 'invalid_email':
                    return esc_html__( 'Email is incorrect.', 'wp-booking-management-system' );
                case 'invalidcombo':
                    return esc_html__( 'Username or email is incorrect.', 'wp-booking-management-system' );
                case 'expiredkey':
                case 'invalidkey':
                    return esc_html__( 'The password for resetting link you used is invalid anymore.', 'wp-booking-management-system' );

                default:
                    break;
            }

            return esc_html__( 'An unknown error occurs. Please try again later.', 'wp-booking-management-system' );
        }


        /**
         * redirect reset pass url
         * @return string
         */
        function _redirect_reset_password_url(){
            if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {

                $account_page = wpbooking_get_option('myaccount-page');
                $user = check_password_reset_key( WPBooking_Input::request('key'), WPBooking_Input::request('login') );
                if ( ! $user || is_wp_error( $user ) ) {

                    if(!empty($account_page))
                        $redirect_url = get_permalink($account_page);
                    else
                        $redirect_url = wp_login_url();

                    if ( $user && $user->get_error_code() === 'expired_key' ) {
                        wpbooking_set_message($this->get_error_message('expiredkey'),'danger');
                    } else {
                        wpbooking_set_message($this->get_error_message('invalidkey'),'danger');
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
                        $redirect_url = get_permalink($account_page).'reset-password';
                    else
                        $redirect_url = wp_login_url();

                    if ($user && $user->get_error_code() === 'expired_key') {
                        wpbooking_set_message($this->get_error_message('expiredkey'), 'danger');
                    } else {
                        wpbooking_set_message($this->get_error_message('invalidkey'), 'danger');
                    }
                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('reset', 'error', $redirect_url);
                    wp_redirect($redirect_url);
                    exit;
                }

                $validator = new WPBooking_Form_Validator();

                $validator->set_rules('new_password',esc_html__('new password','wp-booking-management-system'),'required|min_length[8]|max_length[100]');
                $validator->set_rules('confirm_password',esc_html__('confirm password','wp-booking-management-system'),'required|min_length[8]|max_length[100]|matches[new_password]');

                if(!$validator->run()){
                    wpbooking_set_message($validator->error_string(),'danger');
                    WPBooking_Session::set('error_rs_field',$validator->get_error_fields());
                    $redirect_url = get_permalink($account_page) . 'reset-password';
                    $redirect_url = add_query_arg('key', $rp_key, $redirect_url);
                    $redirect_url = add_query_arg('login', $rp_login, $redirect_url);
                    $redirect_url = add_query_arg('reset', 'error', $redirect_url);
                    wp_redirect($redirect_url);
                    exit;
                }

                // Parameter checks OK, reset password
                reset_password($user, $_POST['new_password']);
                wpbooking_set_message(esc_html__('Your password has been changed. You can sign in now.', 'wp-booking-management-system'), 'success');
                $rd_after_reset = get_permalink($account_page);
                $rd_after_reset = add_query_arg('password', 'changed', $rd_after_reset);
                wp_redirect($rd_after_reset);

                exit;

            }
        }

        /**
         * Email templates for retrieve password
         *
         * @param $message
         * @param $key
         * @param $user_login
         * @param $user_data
         * @return mixed|string|void
         */
        public function _email_retrieve_password_template( $message, $key, $user_login, $user_data ) {
            // add header and footer email templates

            $message = wpbooking_load_view('emails/templates/lost_password',array('key' => $key,'user_login' => $user_login));
            $message = WPBooking_Email::inst()->insert_header_footer_email_template($message);

            // Apply CSS to Inline CSS
            if (class_exists('Emogrifier') and $email_css = wpbooking_get_option('email_stylesheet')) {
                try {
                    $Emogrifier = new Emogrifier();
                    $Emogrifier->setHtml($message);
                    $Emogrifier->setCss($email_css);
                    $message = $Emogrifier->emogrify();
                } catch (Exception $e) {

                }

            }

            return $message;
        }

        /**
         * send email when changed password successful
         *
         * @since 1.0
         * @author tienhd
         *
         * @param $is_success
         * @param $user
         */
        public function _send_email_changed_password($is_success, $user, $userdata){
            if($is_success){
                if(!empty($user)){
                    $to = $user['user_email'];
                    if(is_email($to)){

                        if (is_multisite())
                            $blog_name = $GLOBALS['current_site']->site_name;
                        else
                            $blog_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                        $subject = '['.$blog_name.'] '.esc_html__('Change password successfully','wp-booking-management-system');
                        $subject = apply_filters('wpbooking_title_changed_password_email',$subject);

                        $message = wpbooking_load_view('emails/templates/changed_password',array('user' => $user));

                        WPBooking_Email::inst()->send($to, $subject, $message);

                    }
                }
            }
        }

        /**
         * redirect logout url
         *
         * @since 1.0
         * @author tienhd
         *
         * @return string
         */
        function _redirect_logout_url($logouturl,$redir){
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
        function _redirect_login_url($login_url,$redirect){
            $account_page = wpbooking_get_option('myaccount-page');
            $account_page = apply_filters('wpbooking_set_page_login', $account_page);
            if(!empty($account_page)) {
                $redir = add_query_arg('redirect_to',$redirect,get_permalink($account_page));
                return esc_url($redir);
            }else {
                return $login_url;
            }
        }

        /**
         * Get register url
         *
         * @since 1.0
         * @author tienhd
         *
         * @return string|void
         */
        function get_register_url(){
            $account_page = wpbooking_get_option('myaccount-page');
            if($account_page) {
                $url = get_permalink($account_page).'register';
                return $url;
            }
            return home_url('/');
        }

        /**
         * Get login url
         *
         * @since 1.0
         * @author tienhd
         *
         * @return string|void
         */
        function get_login_url(){
            $account_page = wpbooking_get_option('myaccount-page');
            if($account_page) {
                $url = get_permalink($account_page);
                return $url;
            }
            return admin_url('/');
        }

        /**
         * Get Detail Rate
         *
         * @since 1.0
         * @author quandq
         *
         * @return array
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

        /**
         * Get HTML Status Booking History
         * @param $status
         * @return string
         */
        function get_status_booking_history_html($status)
        {
            if($status){
                $all_status=WPBooking_Config::inst()->item('order_status');
                if(array_key_exists($status,$all_status)){
                    switch($status){
                        case "on_hold":
                            return sprintf('<label class="label pay-on-hold">%s</label>',$all_status[$status]['label']);
                            break;
                        case "payment_failed":
                            return sprintf('<label class="label pay-failed">%s</label>',$all_status[$status]['label']);
                            break;
                        case "completed":
                            return sprintf('<label class="label pay-completed">%s</label>',$all_status[$status]['label']);
                            break;
                        case "cancelled":
                        case "refunded":
                            return sprintf('<label class="label pay-danger">%s</label>',$all_status[$status]['label']);
                            break;

                        default:
                            return sprintf('<label class="label pay-on-hold">%s</label>',$all_status[$status]['label']);
                            break;
                    }
                }else{
                    return sprintf('<label class="bold text_up">%s</label>',esc_html__('Unknown','wp-booking-management-system'));
                }
            }
        }
        /**
         * Gate Gateway Info or Gateway Object
         *
         * @since 1.0
         * @author quandq
         *
         * @param string $gateway
         * @return bool|mixed|object|string
         */
        function get_payment_gateway($gateway){
            if($gateway){
                $gateway_object=WPBooking_Payment_Gateways::inst()->get_gateway($gateway);
                if($gateway_object){
                    return $gateway_object->get_info('label');
                }else{
                    return $gateway;
                }
            }else{
                return esc_html__('Unknow','wp-booking-management-system');
            }
        }
        static function inst()
        {
            if (!self::$_inst) self::$_inst = new self();

            return self::$_inst;
        }
    }

    WPBooking_User::inst();
}