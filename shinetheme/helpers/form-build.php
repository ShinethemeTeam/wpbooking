<?php
if(!function_exists('traveler_add_field_form_builder'))
{
    function traveler_add_field_form_builder($option=array()){
        Traveler_Admin_Form_Build::inst()->traveler_add_field_form_builder($option);
    }
}


if(!function_exists('traveler_get_form_fields'))
{
    function traveler_get_form_fields($form_id){
        return Traveler_Admin_Form_Build::inst()->get_form_fields($form_id);
    }
}

