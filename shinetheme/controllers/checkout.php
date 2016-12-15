<?php
if(!class_exists('WPBooking_Checkout_Controller'))
{
    class WPBooking_Checkout_Controller extends WPBooking_Controller
    {
        static $_inst;

        function __construct()
        {
            if(!session_id())
            {
                session_start();
            }
            /**
             * Ajax Do Check Out
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wp_ajax_wpbooking_do_checkout', array($this, 'do_checkout'));
            add_action('wp_ajax_nopriv_wpbooking_do_checkout', array($this, 'do_checkout'));

            /**
             * Ajax Add To Cart
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wp_ajax_wpbooking_add_to_cart', array($this, '_add_to_cart'));
            add_action('wp_ajax_nopriv_wpbooking_add_to_cart', array($this, '_add_to_cart'));

            /**
             * Ajax Check Empty Cart
             *
             * @since 1.0
             * @author quandq
             */
            add_action('wp_ajax_wpbooking_check_empty_cart', array($this, '_check_empty_cart'));
            add_action('wp_ajax_nopriv_wpbooking_check_empty_cart', array($this, '_check_empty_cart'));

            /**
             * Register Page CheckOut
             *
             * @since 1.0
             * @author quandq
             */
            add_action('init', array($this, '_register_shortcode'));


            /**
             * Register Order Status
             *
             * @since 1.0
             * @author quandq
             */
            add_action( 'init',  array($this, '_register_order_status') );

            parent::__construct();
        }

        /**
         * Register Order Status
         *
         * @since 1.0
         * @author quandq
         */
        function _register_order_status(){
            $order_status = WPBookingConfig()->item('order_status');
            if(!empty($order_status)){
                foreach($order_status as $k=>$v){
                    register_post_status( $k, array(
                        'label'                     => $v['label'],
                        'public'                    => true,
                        'exclude_from_search'       => false,
                        'show_in_admin_all_list'    => true,
                        'show_in_admin_status_list' => true,
                        'publicly_queryable' => false,
                        'label_count'               => _n_noop( $v['label'].' <span class="count">(%s)</span>', $v['label'].' <span class="count">(%s)</span>' ),
                    ) );
                }
            }

        }

        /**
         * Ajax Checkout Handler
         *
         * @since 1.0
         * @author quandq
         */
        function do_checkout()
        {

            $cart = WPBooking_Session::get('wpbooking_cart');
            $service_type = $cart['service_type'];
            $res = array();
            $is_validate = TRUE;

            if(empty(WPBooking_Input::request('term_condition'))){
                $is_validate = FALSE;
                wpbooking_set_message(__("You do not accept our terms!", 'wpbooking'), 'error');
            }

            if (empty($cart)) {
                $is_validate = FALSE;
                wpbooking_set_message(__("Sorry! Your cart is currently empty", 'wpbooking'), 'error');
            }

            $fields = $this->get_billing_form_fields();
            // Validate Form Billing
            $validator = new WPBooking_Form_Validator();
            if (!empty($fields) and $is_validate) {
                foreach ($fields as $key => $value) {
                    $validator->set_rules($value['name'], $value['title'], $value['rule']);
                }
                if ($is_validate and !$validator->run()) {
                    $is_validate = FALSE;
                    wpbooking_set_message($validator->error_string(), 'error');
                    $res['error_type'] = 'form_validate';
                    $res['error_fields'] = $validator->get_error_fields();
                }
            }

            $pay_amount = $this->get_cart_total();
            if ($is_validate and empty($pay_amount)) {
                $is_validate = FALSE;
                wpbooking_set_message(__("Price basket of 0. You can not make this payment!", 'wpbooking'), 'error');
            }

            // Require Payment Gateways
            $gateway_manage = WPBooking_Payment_Gateways::inst();
            $selected_gateway = WPBooking_Input::post('payment_gateway');
            $gateway=$gateway_manage->get_gateway($selected_gateway);

            if ($is_validate and $pay_amount) {
                if (empty($selected_gateway)) {
                    $is_validate = FALSE;
                    wpbooking_set_message(__("Please select a payment method.", 'wpbooking'), 'error');
                } elseif (!$gateway and !$gateway->is_available()) {
                    $is_validate = FALSE;
                    wpbooking_set_message(sprintf(__("Gateway: %s is not ready to use, please choose other gateway", 'wpbooking'), $selected_gateway), 'error');
                }
            }



            $is_validate = apply_filters('wpbooking_do_checkout_validate', $is_validate, $cart);
            $is_validate = apply_filters('wpbooking_do_checkout_validate_'.$service_type, $is_validate , $cart);


            if (!$is_validate) {
                $res ['status'] = 0;
                $res['message'] = wpbooking_get_message(TRUE);
            } else {
                // Checkout form data
                if (!empty($fields)) {
                    foreach ($fields as $k => $v) {
                        $fields[$k]['value'] = WPBooking_Input::post($k);
                    }
                }
                // Register User
                $customer_id = FALSE;
                if(is_user_logged_in()){
                    $customer_id=get_current_user_id();
                }else{
                    // Default Fields
                    $post_data = wp_parse_args(WPBooking_Input::post(), array(
                        'user_first_name'          => FALSE,
                        'user_last_name'           => FALSE,
                        'user_email'               => FALSE,
                    ));
                    if ($email = $post_data['user_email']) {
                        // Check User Exists
                        if ($user_id = email_exists($email)) $customer_id = $user_id;
                        if(empty($customer_id)){
                            $customer_id = WPBooking_User::inst()->order_create_user(array(
                                'user_email' => $email,
                                'first_name' => $post_data['user_first_name'],
                                'last_name'  => $post_data['user_last_name'],
                            ),$fields);
                        }
                    }
                }

                /**
                 * Update User Billing Info if empty
                 *
                 * @since 1.0
                 * @author dungdt
                 */
                if($customer_id) WPBooking_User::inst()->order_update_user($customer_id,$fields);


                $order_id = WPBooking_Session::get('wpbooking_order_id');
                if(!empty($order_id)){
                    $order=new WB_Order($order_id);
                }else{
                    $order=new WB_Order(FALSE);
                    $order_id = $order->create($cart, $fields, $selected_gateway, $customer_id);
                }

                if ($order_id) {
                    WPBooking_Session::set('wpbooking_order_id',$order_id);
                    $data = array(
                        'status' => 1
                    );
                    $res['status'] = 1;
                    // Only work with Order Table bellow
                    try {
                        if ($selected_gateway) {
                            $data = WPBooking_Payment_Gateways::inst()->do_checkout($selected_gateway, $order_id);
                            if (!$data['status']) {
                                $res = array(
                                    'status'  => 0,
                                    'message' => wpbooking_get_message(TRUE),
                                    'data'    => $data
                                );
                                // If Payment Fail update the status
                                $order->payment_failed();
                            }
                            if($data['status'] and isset($data['complete_purchase']) and $data['complete_purchase']){
                                $order->complete_purchase();
                            }
                        }
                        if ($res['status']) {
                            //Clear the Order Id after create new order,
                            WPBooking_Session::set('wpbooking_order_id','');
                            //Clear the Cart after create new order,
                            WPBooking_Session::set('wpbooking_cart', array());

                            wpbooking_set_message(__('Booking Success', 'wpbooking'));
                            //do checkout
                            $res['data'] = $data;
                            $res['message'] = wpbooking_get_message(TRUE);
                        }
                    } catch (Exception $e) {
                        wpbooking_set_message($e->getMessage(), 'error');
                        //do checkout
                        $res = array(
                            'status'  => 0,
                            'message' => wpbooking_get_message(TRUE),
                        );
                    }
                    if (empty($data['redirect'])) {
                        $res['redirect'] = get_permalink($order_id);
                    }

                    if (!empty($data['redirect'])) {
                        $res['redirect'] = $data['redirect'];
                        WPBooking_Session::set('wpbooking_order_id','');
                    }
                    if(isset($data['complete_purchase']) and !$data['complete_purchase']){
                        $res['redirect'] = "";
                    }

                    do_action('wpbooking_after_checkout_success', $order_id);

                } else {
                    $res = array(
                        'status'  => 0,
                        'message' => __('Can not create the order. Please contact the Admin', 'wpbooking')
                    );
                }

            }


            $res = apply_filters('wpbooking_ajax_do_checkout', $res, $cart);

            echo json_encode($res);
            die;
        }

        /**
         * Ajax Add To Cart Handler
         *
         * @since 1.0
         * @author quandq
         * @return string
         */
        function _add_to_cart(){

            $res = array();

            $post_id = WPBooking_Input::post('post_id');

            $service_type = get_post_meta($post_id, 'service_type', TRUE);


            // Validate Order Form
            $is_validate = TRUE;

            // Validate Post and Post Type
            if (!$post_id or get_post_type($post_id) != 'wpbooking_service') {
                $is_validate = FALSE;
                wpbooking_set_message(__("You do not select any service", 'wpbooking'), 'error');
            }

            $service = new WB_Service($post_id);

            $cart_params = array(
                'post_id'                => $post_id ,
                'service_type'           => $service_type ,
                'currency'               => WPBooking_Currency::get_current_currency( 'currency' ) ,
                'price'                  => 0 ,
                'discount'               => array() ,
            );

            $cart_params['tax']['vat']['excluded'] = $service->get_meta('vat_excluded');
            if($service->get_meta('vat_excluded') != 'no'){
                $cart_params['tax']['vat']['amount'] = $service->get_meta('vat_amount');
                $cart_params['tax']['vat']['unit'] = $service->get_meta('vat_unit');
            }
            $cart_params['tax']['citytax']['excluded'] = $service->get_meta('citytax_excluded');
            if($service->get_meta('citytax_excluded') != 'no'){
                $cart_params['tax']['citytax']['amount'] = $service->get_meta('citytax_amount');
                $cart_params['tax']['citytax']['unit'] = $service->get_meta('citytax_unit');
            }
            $cart_params['deposit']['status'] = $service->get_meta('deposit_payment_status');
            if($service->get_meta('deposit_payment_status') != ''){
                $cart_params['deposit']['amount'] = $service->get_meta('deposit_payment_amount');
            }

            // Convert Check In and Check Out to Timestamp if available

            $check_in = WPBooking_Input::request('wpbooking_checkin_y')."-".WPBooking_Input::request('wpbooking_checkin_m')."-".WPBooking_Input::request('wpbooking_checkin_d');
            $check_out = WPBooking_Input::request('wpbooking_checkout_y')."-".WPBooking_Input::request('wpbooking_checkout_m')."-".WPBooking_Input::request('wpbooking_checkout_d');
            if($check_in == '--')$check_in='';
            if($check_out == '--')$check_out='';
            if ($check_in) {
                $cart_params['check_in_timestamp'] = strtotime($check_in);
                if ($check_out) {
                    $cart_params['check_out_timestamp'] = strtotime($check_out);
                } else {
                    $cart_params['check_out_timestamp'] = $cart_params['check_in_timestamp'];
                }
            }

            $cart_params = apply_filters('wpbooking_cart_item_params', $cart_params, $post_id, $service_type);
            $cart_params = apply_filters('wpbooking_cart_item_params_' . $service_type, $cart_params, $post_id);

            $is_validate = apply_filters('wpbooking_add_to_cart_validate', $is_validate, $service_type, $post_id,$cart_params);
            $is_validate = apply_filters('wpbooking_add_to_cart_validate_' . $service_type, $is_validate, $service_type, $post_id,$cart_params);

            if (!$is_validate) {
                $res['status'] = FALSE;
                $res['message'] = wpbooking_get_message(TRUE);
            } else {
                WPBooking_Session::set('wpbooking_cart', $cart_params);
                $res = array(
                    'status'  => 1,
                    'message' => '',
                    'redirect' => $this->get_checkout_url(),
                );
                WPBooking_Session::set('wpbooking_order_id',false);
            }
            $res['updated_content'] = apply_filters('wpbooking_cart_updated_content', array(),$is_validate);

            $res = apply_filters('wpbooking_ajax_add_to_cart', $res, $post_id,$is_validate);
            $res = apply_filters('wpbooking_ajax_add_to_cart_' . $service_type, $res, $post_id,$is_validate);

            echo json_encode($res);

            die;
        }

        /**
         * Return permalink of the Cart Page
         *
         * @since 1.0
         * @author quandq
         * @return false|string
         */
        function get_checkout_url()
        {
            return get_permalink(wpbooking_get_option('checkout_page'));
        }

        /**
         * Register shortcode
         *
         * @since 1.0
         * @author quandq
         */
        function _register_shortcode()
        {
            add_shortcode('wpbooking_checkout_page', array($this, '_render_checkout_shortcode'));
        }

        /**
         * CheckOut shortcode
         *
         * @since 1.0
         * @author quandq
         */
        function _render_checkout_shortcode($attr = array(), $content = FALSE)
        {
            return wpbooking_load_view('checkout/index');
        }

        /**
         * Get Total Cart
         * @author quandq
         * @since 1.0
         *
         * @return mixed|void
         */
        function get_cart_total($args=array(),$cart=false)
        {
            $args = wp_parse_args($args, array(
                'without_deposit'        => false,
                'without_tax'            => false
            ));

            if(empty($cart)){
                $cart = $this->get_cart();
            }
            $total_price = $cart['price'];
            $service_type = $cart['service_type'];
            $total_price = apply_filters('wpbooking_get_cart_total', $total_price, $cart);
            $total_price = apply_filters('wpbooking_get_cart_total_'.$service_type, $total_price, $cart);

            if($args['without_tax']){
                $tax = $this->get_cart_tax_price();
                $total_price = $total_price + $tax['total_price'];
            }
            if($args['without_deposit']){
                if(!empty($cart['deposit']['status'])){
                    $price_deposit = 0;
                    switch ($cart['deposit']['status']) {
                        case "percent":
                            if ($cart['deposit']['amount'] > 100) $cart['deposit']['amount'] = 100;
                            $price_deposit = round($total_price * $cart['deposit']['amount'] / 100,2);
                            break;
                        case "amount":
                        default:
                            if ($cart['deposit']['amount'] < $total_price)
                                $price_deposit = $cart['deposit']['amount'];
                            break;

                    }
                    $total_price = $price_deposit;
                }
            }
            return $total_price;
        }

        /**
         * Get cart
         *
         * @author quandq
         * @since 1.0
         *
         * @return array
         */
        function get_cart()
        {
            return WPBooking_Session::get('wpbooking_cart');
        }

        /**
         * Set cart
         *
         * @author quandq
         * @since 1.0
         * @param $cart
         */
        function set_cart($cart)
        {
            return WPBooking_Session::set('wpbooking_cart',$cart);
        }

        /**
         * Billing Form Fields
         *
         * @author quandq
         * @since 1.0
         *
         * @return array|mixed|void
         */
        function get_billing_form_fields(){
            $field_form = array(
                'user_first_name'       => array(
                    'title'       => esc_html__( "First Name" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "First name" , "wpbooking" ) ,
                    'type'        => 'text' ,
                    'name'        => 'user_first_name' ,
                    'size'        => '6' ,
                    'required'    => true ,
                    'rule'        => 'required|max_length[100]' ,
                ) ,
                'user_last_name'       => array(
                    'title'       => esc_html__( "Last Name" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Last name" , "wpbooking" ) ,
                    'type'        => 'text' ,
                    'name'        => 'user_last_name' ,
                    'size'        => '6' ,
                    'required'    => true ,
                    'rule'        => 'required|max_length[100]' ,
                ) ,
                'user_email'           => array(
                    'title'       => esc_html__( "Email" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Email" , "wpbooking" ) ,
                    'desc'        => esc_html__( "Email to confirmation" , "wpbooking" ) ,
                    'type'        => 'text' ,
                    'name'        => 'user_email' ,
                    'size'        => '12' ,
                    'required'    => true ,
                    'rule'        => 'required|max_length[100]|valid_email' ,
                ) ,
                'user_phone'           => array(
                    'title'       => esc_html__( "Telephone" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Telephone" , "wpbooking" ) ,
                    'type'        => 'number' ,
                    'name'        => 'user_phone' ,
                    'size'        => '12' ,
                    'required'    => true ,
                    'rule'        => 'required|max_length[100]' ,
                ) ,
                'user_address'         => array(
                    'title'       => esc_html__( "Address" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Address" , "wpbooking" ) ,
                    'type'        => 'text' ,
                    'name'        => 'user_address' ,
                    'size'        => '12' ,
                    'required'    => true ,
                    'rule'        => 'required|max_length[100]' ,
                ) ,
                'user_postcode'    => array(
                    'title'       => esc_html__( "Postcode / ZIP" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Postcode / ZIP" , "wpbooking" ) ,
                    'type'        => 'text' ,
                    'name'        => 'user_postcode' ,
                    'size'        => '6' ,
                    'required'    => false ,
                    'rule'        => '' ,
                ) ,
                'user_apt_unit'        => array(
                    'title'       => esc_html__( "Apt/ Unit" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Apt/ Unit" , "wpbooking" ) ,
                    'type'        => 'text' ,
                    'name'        => 'user_apt_unit' ,
                    'size'        => '6' ,
                    'required'    => false ,
                    'rule'        => '' ,
                ) ,
                'user_special_request' => array(
                    'title'       => esc_html__( "Special Request" , "wpbooking" ) ,
                    'placeholder' => esc_html__( "Notes about your order, e.g. special notes for  delivery." , "wpbooking" ) ,
                    'type'        => 'textarea' ,
                    'name'        => 'user_special_request' ,
                    'size'        => '12' ,
                    'required'    => false ,
                    'rule'        => '' ,
                ) ,
            );

            $field_form = apply_filters('wpbooking_get_billing_form_fields', $field_form);

            return $field_form;
        }

        /**
         * Get Cart Tax Total
         *
         * @since 1.0
         * @author dungdt
         *
         * @return int|mixed|void
         */
        public function get_cart_tax_price(){
            $tax = array();
            $cart = $this->get_cart();
            $total_price = $this->get_cart_total(array('without_tax'=>false));
            $total_tax = 0;
            $tax_total = 0;
            $service_type = $cart['service_type'];
            if(!empty($cart['tax'])){
                foreach($cart['tax'] as $key => $value){
                    if($value['excluded'] != 'no'){
                        $unit = $value['unit'];
                        $tax[$key] = $value;
                        switch($unit){
                            case "percent":
                                $price = round($total_price * ($value['amount'] / 100),2);
                                break;
                            case "fixed":
                            default:
                                $price = $value['amount'];
                                break;
                        }
                        if($value['excluded'] == 'yes_not_included'){
                            $total_tax += $price;
                        }

                        $tax_total += (float)$price;

                        $tax[$key]['price'] = floatval($price);
                    }
                }
            }
            $tax['total_price'] = $total_tax;
            $tax['tax_total'] = $tax_total;
            $tax = apply_filters('wpbooking_get_cart_tax_price', $tax, $cart);
            $tax = apply_filters('wpbooking_get_cart_tax_price_'.$service_type, $tax, $cart);
            return $tax;
        }

        /**
         * Ajax Check Empty Cart
         *
         * @since 1.0
         * @author quandq
         *
         * @return mixed
         */
        function _check_empty_cart(){
            $cart = $this->get_cart();
            if(!empty($cart)){
                echo json_encode(array('status'=>'true'));
            }else{
                echo json_encode(array('status'=>'false'));
            }
           wp_die();
        }

        static function inst()
        {
            if(!self::$_inst){
                self::$_inst=new self();
            }
            return self::$_inst;
        }

    }
    WPBooking_Checkout_Controller::inst();
}