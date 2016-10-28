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
                'cart_key'               => md5( $post_id . time() . rand( 0 , 999 ) ) ,
                'service_type'           => $service_type ,
                'currency'               => WPBooking_Currency::get_current_currency( 'currency' ) ,
                'price_base'             => 0 ,
                'discount'               => array() ,
            );

            $cart_params['tax']['vat']['vat_excluded'] = $service->get_meta('vat_excluded');
            if($service->get_meta('vat_excluded') != 'no'){
                $cart_params['tax']['vat']['vat_amount'] = $service->get_meta('vat_amount');
                $cart_params['tax']['vat']['vat_unit'] = $service->get_meta('vat_unit');
            }
            $cart_params['tax']['citytax']['citytax_excluded'] = $service->get_meta('citytax_excluded');
            if($service->get_meta('citytax_excluded') != 'no'){
                $cart_params['tax']['citytax']['citytax_amount'] = $service->get_meta('citytax_amount');
                $cart_params['tax']['citytax']['citytax_unit'] = $service->get_meta('citytax_unit');
            }
            $cart_params['deposit']['deposit_payment_status'] = $service->get_meta('deposit_payment_status');
            if($service->get_meta('deposit_payment_status') != ''){
                $cart_params['deposit']['deposit_payment_amount'] = $service->get_meta('deposit_payment_amount');
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
            $res['updated_content'] = apply_filters('wpbooking_cart_updated_content', array());

            $res = apply_filters('wpbooking_ajax_add_to_cart', $res, $post_id);
            $res = apply_filters('wpbooking_ajax_add_to_cart_' . $service_type, $res, $post_id);

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