<?php
if(function_exists( 'traveler_add_field_form_builder' )) {
    traveler_add_field_form_builder( array(
            "title"    => __( "Submit Button" , 'wpbooking' ) ,
            "name"     => 'traveler_booking_submit_buttom' ,
            "category" => 'Standard Fields' ,
            "options"  => array(
                array(
                    "type"             => "text" ,
                    "title"            => __( "Label" , 'wpbooking' ) ,
                    "name"             => "label" ,
                    "desc"             => __( "Label" , 'wpbooking' ) ,
                    'edit_field_class' => 'traveler-col-md-12' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Name" , 'wpbooking' ) ,
                    "name"             => "name" ,
                    "desc"             => __( "Name" , 'wpbooking' ) ,
                    'edit_field_class' => 'traveler-col-md-4' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "ID" , 'wpbooking' ) ,
                    "name"             => "id" ,
                    "desc"             => __( "ID" , 'wpbooking' ) ,
                    'edit_field_class' => 'traveler-col-md-4' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'wpbooking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'wpbooking' ) ,
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
                'class' => ' ' ,
            ) , $attr , 'traveler_booking_submit_buttom' );
        extract( $data );
		$class.=' submit-button';
        return '<button type="submit" name="' . $name . '" id="' . $id . '" class="' . $class . '" >' . $label . '</button>';
    }
}
add_shortcode( 'traveler_booking_submit_buttom' , 'traveler_sc_booking_submit_buttom' );