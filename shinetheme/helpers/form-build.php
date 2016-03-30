<?php
if(!function_exists('traveler_add_field'))
{
    function traveler_add_field($option=array()){
        return Traveler_Admin_Form_Build::inst()->traveler_add_field($option);
    }
}