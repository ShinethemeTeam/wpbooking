<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!class_exists('WPBooking_Abstract_Payment_Gateway')) {
    class WPBooking_Abstract_Payment_Gateway
    {
        protected $gateway_id = FALSE;
        protected $gateway_info = array();
        protected $settings = array();

        function __construct()
        {
            if (!$this->gateway_id) return FALSE;
            $this->gateway_info = wp_parse_args($this->gateway_info, array(
                'label'       => '',
                'description' => '',
                'id'          => $this->gateway_id
            ));

            add_filter('wpbooking_payment_gateways', array($this, '_register_gateway'));
            add_filter('wpbooking_payment_settings_sections', array($this, '_add_setting_section'));
        }

        /**
         * Hook Callback for Create Setting Section
         *
         * @since 1.0
         * @author dungdt
         *
         * @param array $sections
         * @return array
         */
        function _add_setting_section($sections = array())
        {
            $settings = $this->get_settings_fields();
            if (!empty($settings)) {
                foreach ($settings as $key => $value) {
                    if (!empty($value['id']))
                        $settings[$key]['id'] = 'gateway_' . $this->gateway_id . '_' . $value['id'];
                }
            }
            $sections['payment_' . $this->gateway_id] = array(
                'id'     => 'payment_' . $this->gateway_id,
                'label'  => $this->get_info('label'),
                'fields' => $settings
            );

            return $sections;
        }

        /**
         * Get all settings fields
         *
         * @sicne 1.0
         * @author dungdt
         *
         * @return mixed|void
         */
        function get_settings_fields()
        {
            return apply_filters('wpbooking_payment_' . $this->gateway_id . '_settings_fields', $this->settings);
        }

        /**
         * Get Gateway Information by Key
         *
         * @since 1.0
         * @author dungdt
         *
         * @param bool $key
         * @return bool|mixed|void
         */
        function get_info($key = FALSE)
        {
            $info = apply_filters('wpbooking_gateway_info', $this->gateway_info);
            $info = apply_filters('wpbooking_gateway_' . $this->gateway_id . '_info', $info);

            if ($key) {

                $data = isset($info[$key]) ? $info[$key] : FALSE;

                $data = apply_filters('wpbooking_gateway_info_' . $key, $data);
                $data = apply_filters('wpbooking_gateway_' . $this->gateway_id . '_info_' . $key, $data);

                return $data;
            }

            return $info;
        }

        /**
         * Get Data from Gateway Settings
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $key
         * @param bool $default
         * @return bool|mixed|void
         */
        function get_option($key, $default = FALSE)
        {
            return wpbooking_get_option('gateway_' . $this->gateway_id . '_' . $key, $default);
        }

        /**
         * Return Status of current Gateway
         *
         * @since 1.0
         * @author dungdt
         *
         * @return bool
         */
        function is_available()
        {
            return ($this->get_option('enable')=='on' or $this->get_option('enable')==1) ? TRUE : FALSE;
        }

        /**
         * Get default cancel url
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $order_id
         * @return string
         */
        function get_cancel_url($order_id)
        {

            $array = array(
                //'action'  => 'cancel_purchase',
                'gateway' => $this->gateway_id
            );

            return add_query_arg($array, get_permalink($order_id));
        }

        /**
         * Get default return url
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $order_id
         * @return string
         */
        function get_return_url($order_id)
        {

            $array = array(
                'action'  => 'complete_purchase',
                'gateway' => $this->gateway_id
            );

            return add_query_arg($array, get_permalink($order_id));

        }

        /**
         * Redirect form for gateway using POST Method
         *
         * @since 1.0
         * @author dungdt
         *
         * @param $res
         * @return string
         */
        function getRedirectForm($res)
        {
            $hiddenFields = '';
            foreach ($res->getRedirectData() as $key => $value) {
                $hiddenFields .= sprintf(
                        '<input type="hidden" name="%1$s" value="%2$s" />',
                        htmlentities($key, ENT_QUOTES, 'UTF-8', FALSE),
                        htmlentities($value, ENT_QUOTES, 'UTF-8', FALSE)
                    ) . "\n";
            }

            $url = htmlentities($res->getRedirectUrl(), ENT_QUOTES, 'UTF-8', FALSE);

            return sprintf('<form action="%s" method="post" id="wpbooking_payment_redirect_form">

    						<script>document.getElementById(\'wpbooking_payment_redirect_form\').submit();</script>
							%s
						</form>', $url, $hiddenFields);
        }


        /**
         * Do Complete Purchase Action
         *
         * @param $order_id
         * @return bool
         * @since 1.0
         */
        function complete_purchase($order_id)
        {
            return TRUE;
        }

        /**
         * Check out functions
         * @param $order_id
         */
        function do_checkout($order_id)
        {

        }

        /**
         * Hook callback for register Gateway Sections
         *
         * @since 1.0
         * @author dungdt
         *
         * @param array $gateways
         * @return array
         */
        function _register_gateway($gateways = array())
        {
            $gateways[$this->gateway_id] = $this;

            return $gateways;
        }

        function is_test_mode(){
            if($this->get_option('test_mode') == 'on' or $this->get_option('test_mode') ==1){
                return true;
            }
            else return false;
        }

    }
}