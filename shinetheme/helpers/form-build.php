<?php
if(!function_exists('wpbooking_add_field_form_builder'))
{
    function wpbooking_add_field_form_builder($option=array()){
        WPBooking_Admin_Form_Build::inst()->add_field_form_builder($option);
    }
}


if(!function_exists('wpbooking_get_form_fields'))
{
    function wpbooking_get_form_fields($form_id){
        return WPBooking_Admin_Form_Build::inst()->get_form_fields($form_id);
    }
}

