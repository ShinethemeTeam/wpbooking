<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 10/18/2016
 * Time: 9:33 AM
 */
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

            add_action('wp_ajax_wpbooking_add_to_cart', array($this, '_add_to_cart'));
            add_action('wp_ajax_nopriv_wpbooking_add_to_cart', array($this, '_add_to_cart'));

            add_action('init', array($this, '_register_shortcode'));

            add_action('template_redirect', array($this, '_delete_cart_item'));

            parent::__construct();
        }

        /**
         * Ajax Add To Cart Handler
         * @since 1.0
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
            if ($check_in = WPBooking_Input::post('wpbooking_check_in')) {
                $cart_params['check_in_timestamp'] = strtotime($check_in);

                if ($check_out = WPBooking_Input::post('wpbooking_check_out')) {
                    $cart_params['check_out_timestamp'] = strtotime($check_out);
                } else {
                    $cart_params['check_out_timestamp'] = $cart_params['check_in_timestamp'];
                }
            }

            $cart_params = apply_filters('wpbooking_cart_item_params', $cart_params, $post_id, $service_type);
            $cart_params = apply_filters('wpbooking_cart_item_params_' . $service_type, $cart_params, $post_id);


            $is_validate = apply_filters('wpbooking_add_to_cart_validate', $is_validate, $service_type, $post_id,$cart_params);
            $is_validate = apply_filters('wpbooking_add_to_cart_validate_' . $service_type, $is_validate, $service_type, $post_id,$cart_params);


            //var_dump($cart_params);
            if (!$is_validate) {
                $res['status'] = FALSE;
                $res['message'] = wpbooking_get_message(TRUE);

            } else {


                WPBooking_Session::set('wpbooking_cart', $cart_params);

               // wpbooking_set_message(sprintf(__('Add to %s success', 'wpbooking'), sprintf('<a href="%s">%s</a>', $this->get_checkout_url(), __('cart', 'wpbooking'))), 'success');
                $res = array(
                    'status'  => 1,
                    'message' => '',
                    'redirect' => $this->get_checkout_url(),
                );

            }
            $res['updated_content'] = apply_filters('wpbooking_cart_updated_content', array(),$is_validate);

            $res = apply_filters('wpbooking_ajax_add_to_cart', $res, $post_id,$is_validate);
            $res = apply_filters('wpbooking_ajax_add_to_cart_' . $service_type, $res, $post_id,$is_validate);

            echo json_encode($res);

            die;
        }
        /**
         * Return permalink of the Cart Page
         * @return false|string
         */
        function get_checkout_url()
        {
            return get_permalink(wpbooking_get_option('checkout_page'));
        }

        /**
         * register shortcode
         */
        function _register_shortcode()
        {
            add_shortcode('wpbooking_checkout_page', array($this, '_render_checkout_shortcode'));
        }
        function _render_checkout_shortcode($attr = array(), $content = FALSE)
        {
            return wpbooking_load_view('checkout/index');
        }

        /**
         * Get Total Cart
         * @author dungdt
         * @since 1.0
         *
         * @return mixed|void
         */
        function get_cart_total()
        {
            $cart = $this->get_cart();
            $price = $cart['price'];
            $service_type = $cart['service_type'];
            $price = apply_filters('wpbooking_get_cart_total', $price, $cart);
            $price = apply_filters('wpbooking_get_cart_total_'.$service_type, $price, $cart);
            return $price;
        }

        /**
         * Get Total Cart With Tax
         * @author dungdt
         * @since 1.0
         *
         * @return mixed|void
         */
        function get_cart_total_with_tax($args)
        {
            $cart = $this->get_cart();
            $args = wp_parse_args($args, array(
                'without_deposit'        => true
            ));
            $price_cart = $this->get_cart_total();
            $tax = $this->get_cart_tax_price();

            $total_price = $price_cart + $tax['total_price'];



            if($args['without_deposit']){
                var_dump($cart['deposit']);
                if(!empty($cart['deposit']['status'])){
                    switch ($cart['deposit']['status']) {
                        case "percent":
                            if ($cart['deposit']['amount'] > 100) $cart['deposit']['amount'] = 100;
                            $price = $total_price * $cart['deposit']['amount'] / 100;
                            break;
                        case "amount":
                        default:
                            if ($cart['deposit']['amount'] < $total_price)
                                $price = $cart['deposit']['amount'];
                            break;

                    }
                    var_dump($price);
                }



            }
            return $total_price;
        }


        /**
         * Get all cart items
         *
         * @author dungdt
         * @since 1.0
         *
         * @return array
         */
        function get_cart()
        {
            return WPBooking_Session::get('wpbooking_cart');
        }




        function get_field_form_billing(){
            $field_form = array(
                array(
                    'title'=>esc_html__("First name","wpbooking"),
                    'placeholder'=>esc_html__("First name","wpbooking"),
                    'type'=>'text',
                    'name'=>'fist_name',
                    'size'=>'6',
                    'required'=>true,
                ),
                array(
                    'title'=>esc_html__("Last name","wpbooking"),
                    'placeholder'=>esc_html__("Last name","wpbooking"),
                    'type'=>'text',
                    'name'=>'last_name',
                    'size'=>'6',
                    'required'=>true,
                ),
                array(
                    'title'=>esc_html__("Email","wpbooking"),
                    'placeholder'=>esc_html__("Email","wpbooking"),
                    'desc'=>esc_html__("Email to confirmation","wpbooking"),
                    'type'=>'text',
                    'name'=>'email',
                    'size'=>'12',
                    'required'=>true,
                ),
                array(
                    'title'=>esc_html__("Telephone","wpbooking"),
                    'placeholder'=>esc_html__("Telephone","wpbooking"),
                    'type'=>'text',
                    'name'=>'phone',
                    'size'=>'12',
                    'required'=>true,
                ),
                array(
                    'title'=>esc_html__("Address","wpbooking"),
                    'placeholder'=>esc_html__("Address","wpbooking"),
                    'type'=>'text',
                    'name'=>'address',
                    'size'=>'12',
                    'required'=>true,
                ),
                array(
                    'title'=>esc_html__("Postcode / ZIP","wpbooking"),
                    'placeholder'=>esc_html__("Postcode / ZIP","wpbooking"),
                    'type'=>'text',
                    'name'=>'postcode_zip',
                    'size'=>'6',
                    'required'=>false,
                ),
                array(
                    'title'=>esc_html__("Apt/ Unit","wpbooking"),
                    'placeholder'=>esc_html__("Apt/ Unit","wpbooking"),
                    'type'=>'text',
                    'name'=>'apt_unit',
                    'size'=>'6',
                    'required'=>false,
                ),
                array(
                    'title'=>esc_html__("Special request","wpbooking"),
                    'placeholder'=>esc_html__("Notes about your order, e.g. special notes for  delivery.","wpbooking"),
                    'type'=>'textarea',
                    'name'=>'special_request',
                    'size'=>'12',
                    'required'=>false,
                ),
            );

            $field_form = apply_filters('wpbooking_get_field_form_billing', $field_form);

            return $field_form;
        }
        /**
         * Handler Action Delete Cart Item
         *
         * @since 1.0
         * @author quandq
         */
        function _delete_cart_item()
        {
            if (isset($_GET['delete_cart_item'])) {
                $index = WPBooking_Input::get('delete_cart_item');
                $all = WPBooking_Session::get('wpbooking_cart');
                if(!empty($all['service_type'])){
                    $service_type=$all['service_type'];
                    $redirect_to_home = false;
                    switch($service_type){
                        case 'accommodation':
                            unset($all['rooms'][$index]);
                            if(empty($all['rooms'])){
                                WPBooking_Session::set('wpbooking_cart', array());
                                $redirect_to_home = true;
                            }
                            break;
                    }
                    if($redirect_to_home){
                        wp_redirect(home_url());
                    }else{
                        WPBooking_Session::set('wpbooking_cart', $all);
                        wpbooking_set_message(__("Delete item successfully", 'wpbooking'), 'success');
                    }
                }
            }
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

            $diff=$cart['check_out_timestamp'] - $cart['check_in_timestamp'];
            $date_diff = $diff / (60 * 60 * 24);

            $total_price = $this->get_cart_total();
            $total_tax = 0;
            if(!empty($cart['tax'])){
                foreach($cart['tax'] as $key => $value){
                    if($value['excluded'] != 'no'){
                        $unit = $value['unit'];
                        $tax[$key] = $value;
                        $price = 0;
                        switch($unit){
                            case "percent":
                            case "stay":
                                $price = $value['amount'];
                                break;
                            case "fixed":
                                $price = $total_price * ($value['amount'] / 100);
                                break;
                            case "night":
                                $price = $value['amount'] * $date_diff;
                                break;
                            case "person_per_stay":
                                if(!empty($cart['person'] )){
                                    $person = $cart['person'];
                                    $price = $person *  $value['amount'];
                                }
                                break;
                            case "person_per_night":
                                if(!empty($cart['person'] )){
                                    $person = $cart['person'];
                                    $price =  ( $value['amount'] * $person ) * $date_diff;
                                }
                                break;
                            default:
                        }
                        $total_tax += $price;
                        $tax[$key]['price'] = floatval($price);
                    }
                }
            }
            $tax['total_price'] = $total_tax;
            $tax = apply_filters('wpbooking_get_cart_tax_price', $tax, $cart);
            return $tax;
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