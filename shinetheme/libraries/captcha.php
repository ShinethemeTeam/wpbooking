<?php
/**
 * Created by PhpStorm.
 * User: Dungdt
 * Date: 10/18/2016
 * Time: 9:33 AM
 */
if(!class_exists('WPBooking_Captcha'))
{
    class WPBooking_Captcha
    {
        static $_inst;
        protected $_api_key=array(
            'recaptcha'=>array(
                'key'=>'6LfmuAoUAAAAAOE-Ter8doCk8wHswR9709PCiMbH',
                'secret_key'=>'6LfmuAoUAAAAAAh4prggz2KM-3jL-SSfo_CwgO60'
            )
        );
        function __construct()
        {
            add_action('wp_enqueue_scripts',array($this,'_add_scripts'));

            // Validate Captcha on Do Check Out
            //add_filter('wpbooking_do_checkout_validate',array($this,'_validate_do_checkout_captcha'),10,2);
            //parent::__construct();
        }


        /**
         * Add Some Scripts for captcha libraries
         *
         * @since 1.0
         * @author dungdt
         */
        function _add_scripts()
        {
            wp_register_script('recaptcha','https://www.google.com/recaptcha/api.js',array(),null,true);
			wp_localize_script('recaptcha','wpbooking_recaptcha',array(
				'sitekey'=>$this->_api_key['recaptcha']['key']
			));
        }



        /**
         * Get Google reCAPTCHA HTML output
         *
         * @since 1.0
         * @author dungdt
         *
         * @return string
         */
        function get_recaptcha()
        {
            wp_enqueue_script('recaptcha');
            return '<div id="wpbooking_recaptcha_field" class="g-recaptcha" data-sitekey="'.$this->_api_key['recaptcha']['key'].'"></div>';
        }

        /**
         * Hook callback for validate captcha on checkout step
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $is_validate
         * @param array $cart
         * @return bool
         */
        function _validate_do_checkout_captcha($is_validate,$cart=array())
        {
            /*$form_id = WPBooking_Order::inst()->get_checkout_form_id();
            $fields = wpbooking_get_form_fields($form_id);

            if(!empty($fields))
            {
                // check if this form is contain a captcha field and captcha type is not empty
                if(in_array('captcha',array_keys($fields)) and !empty($fields['captcha']['data']['type'])){
                    $is_validate=$this->validate($fields['captcha']['data']['type']);
                }
            }*/

            return $is_validate;
        }

        /**
         * Validate Captcha by Captcha Type
         *
         * @since 1.0
         * @autor dungdt
         *
         * @param bool|FALSE $captcha_type
         * @return bool
         */
        function validate($captcha_type=FALSE){
            switch($captcha_type){
                case "recaptcha":
                default:
                    return $this->validate_recaptcha();
                    break;
            }
        }


        /**
         * Validate Google reCAPTCHA
         *
         * @since 1.0
         * @author dungdt
         *
         * @return bool
         */
        function validate_recaptcha()
        {
            $url='https://www.google.com/recaptcha/api/siteverify';

            $url=add_query_arg(array(
                'secret'=>$this->_api_key['recaptcha']['secret_key'],
                'response'=>WPBooking_Input::post('g-recaptcha-response'),
                'remoteip'=>WPBooking_Input::ip_address()
            ),$url);

            $data=wp_remote_get($url);

            if(!is_wp_error($data)){
                $body=wp_remote_retrieve_body($data);

                $body_obj=json_decode($body);

                if(isset($body_obj->success) and $body_obj->success){
                    return true;
                }else{
                    wpbooking_set_message(esc_html__('Your captcha is not correct','wpbooking'),'danger');
                    return FALSE;
                }
            }else{
                wpbooking_set_message(esc_html__('Can not verify captcha','wpbooking'),'danger');
                return FALSE;
            }
        }
        static function inst()
        {
            if(!self::$_inst){
                self::$_inst=new self();
            }
            return self::$_inst;
        }

    }
    WPBooking_Captcha::inst();
}