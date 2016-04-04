<?php
if(function_exists( 'traveler_add_field_form_builder' )) {
    traveler_add_field_form_builder( array(
            "title"    => __( "Submit Button" , 'traveler-booking' ) ,
            "name"     => 'traveler_booking_submit_buttom' ,
            "category" => 'Standard Fields' ,
            "options"  => array(
                array(
                    "type"             => "text" ,
                    "title"            => __( "Label" , 'traveler-booking' ) ,
                    "name"             => "label" ,
                    "desc"             => __( "Label" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-12' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Name" , 'traveler-booking' ) ,
                    "name"             => "name" ,
                    "desc"             => __( "Name" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-4' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "ID" , 'traveler-booking' ) ,
                    "name"             => "id" ,
                    "desc"             => __( "ID" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-4' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'traveler-booking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'traveler-booking' ) ,
                    'edit_field_class' => 'traveler-col-md-4' ,
                    'value'            => ""
                ) ,
            )
        )
    );
}
if(!function_exists( 'traveler_sc_booking_submit_buttom' )) {
    function traveler_sc_booking_submit_buttom( $attr , $content = false )
    {
        $data = shortcode_atts(
            array(
                'label' => '' ,
                'name'  => '' ,
                'id'    => '' ,
                'class' => '' ,
            ) , $attr , 'traveler_booking_submit_buttom' );
        extract( $data );
        return '<button type="submit" name="' . $name . '" id="' . $id . '" class="' . $class . '" >' . $label . '</button>';
    }
}
add_shortcode( 'traveler_booking_submit_buttom' , 'traveler_sc_booking_submit_buttom' );