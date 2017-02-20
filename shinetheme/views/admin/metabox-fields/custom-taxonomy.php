<?php
$taxs = WPBooking_Admin_Taxonomy_Controller::inst()->get_tax_service_type($data['service_type']);
if (!empty($taxs)) {
    foreach ($taxs as $tax) {
        $tax_object = get_taxonomy($tax['name']);
        if (!is_wp_error($tax_object)) {
            $extra_tax= array(
                'label' => $tax_object->label,
                'id'    => $tax['name'],
                'type'  => 'taxonomy_fee_select',
                'taxonomy'=>$tax['name'],
                'std'=>'',
                'width'=>'',
                'desc'=>false
            );
            echo wpbooking_admin_load_view('metabox-fields/taxonomy_fee_select',array('data'=>$extra_tax,'post_id'=>$post_id));
        }
    }
}
