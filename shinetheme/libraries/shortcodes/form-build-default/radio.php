<?php
if(function_exists( 'wpbooking_add_field_form_builder' )) {
    wpbooking_add_field_form_builder( array(
            "title"   => __( "Radio" , 'wpbooking' ) ,
            "name"    => 'wpbooking_form_field_radio' ,
            "category" => 'Standard Fields',
            "options" => array(
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
                    'value' => ""
                ) ,
                array(
                    "type"             => "text" ,
                    "title"            => __( "Class" , 'wpbooking' ) ,
                    "name"             => "class" ,
                    "desc"             => __( "Class" , 'wpbooking' ) ,
                    'edit_field_class' => 'wpbooking-col-md-6' ,
                    'value' => ""
                ) ,
                array(
                    "type"             => "textarea" ,
                    "title"            => __( "Options" , 'wpbooking' ) ,
                    "name"             => "options" ,
                    "desc"             => __("Ex: Title 1:value_1|Title 2:value_2",'wpbooking'),
                    'edit_field_class' => 'wpbooking-col-md-12' ,
                    'value' => ""
                ) ,
            )
        )
    );
}
if(!function_exists( 'wpbooking_sc_booking_radio' )) {
    function wpbooking_sc_booking_radio( $attr , $content = false )
    {
        $data = wp_parse_args(
			$attr,
            array(
                'title'          => '' ,
                'name'          => '' ,
                'id'          => '' ,
                'class'       => '' ,
                'options'       => '' ,
            ));
        extract( $data );
        $list_item = "";
        if(!empty($options)){
            $tmp_list_item = explode('|',$options);
            if(!empty($tmp_list_item)){
                foreach($tmp_list_item as $k=>$v){
                    $tmp = explode(':',$v);
                    if(!empty($tmp[0]) and !empty($tmp[1])){
                        $list_item .= '
                                        <label>
                                            <input type="radio" name="'.$name.'" id="'.$id.'" class="'.$class.'" value="'.$tmp[1].'"> '.$tmp[0].'
                                        </label>';
                    }
                }
            }
        }
        WPBooking_Admin_Form_Build::inst()->add_form_field($name,array('data'=>$data,'rule'=>''));
        return $list_item;
    }
}
add_shortcode( 'wpbooking_form_field_radio' , 'wpbooking_sc_booking_radio' );
