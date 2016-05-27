<?php
if(function_exists( 'wpbooking_add_field_form_builder' )) {
    wpbooking_add_field_form_builder( array(
            "title"    => __( "TextArea" , 'wpbooking' ) ,
            "name"     => 'wpbooking_booking_textarea' ,
            "category" => 'Standard Fields' ,
            "options"  => array(
                array(
                    "type"             => "required" ,
                    "title"            => __( "Set as <strong>required</strong>" , 'wpbooking' ) ,
                    "desc"             => "" ,
                    'edit_field_class' => 'wpbooking-col-md-12' ,
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Title" , 'wpbooking' ) ,
                    "name"             => "title" ,
                    "desc"             => __( "Title" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Name" , 'wpbooking' ) ,
                    "name"             => "name" ,
                    "desc"             => __( "Name" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "ID" , 'wpbooking' ) ,
                    "name"             => "id" ,
                    "desc"             => __( "ID" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'wpbooking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "textarea" ,
                    "title"            => __( "Value" , 'wpbooking' ) ,
                    "name"             => "value" ,
                    "desc"             => __( "Value" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-12' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Rows" , 'wpbooking' ) ,
                    "name"             => "rows" ,
                    "desc"             => __( "Rows" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Columns" , 'wpbooking' ) ,
                    "name"             => "columns" ,
                    "desc"             => __( "Columns" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value'            => ""
                ) ,

            )
        )
    );
}
if(!function_exists( 'wpbooking_sc_booking_textarea' )) {
    function wpbooking_sc_booking_textarea( $attr , $content = false )
    {
        $data = shortcode_atts(
            array(
                'is_required' => 'off' ,
                'title'        => '' ,
                'name'        => '' ,
                'id'          => '' ,
                'class'       => '' ,
                'value'       => '' ,
                'rows'        => '' ,
                'columns'     => '' ,
            ) , $attr , 'wpbooking_booking_textarea' );
        extract( $data );
        $required = "";
        $rule = "";
        if($is_required == "on") {
            $required = "required";
            $rule .= "required";
        }
        WPBooking_Admin_Form_Build::inst()->add_form_field($name,array('data'=>$data,'rule'=>$rule));
        return '<textarea name="' . $name . '" id="' . $id . '" class="' . $class . '" rows="' . $rows . '" cols="' . $columns . '" ' . $required . ' >' . $value . '</textarea>';
    }
}
add_shortcode( 'wpbooking_booking_textarea' , 'wpbooking_sc_booking_textarea' );
