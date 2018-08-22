<?php
/**
 * Created by PhpStorm.
 * User: lncj
 * Date: 07/08/2018
 * Time: 02:03 CH
 */

extract($atts);
if(!empty($id)){
    $id = explode(',',$id);
}
$form_fields = get_option('widget_wpbooking_widget_form_search');
$id_form = [];
$data_form = '';
foreach($form_fields as $id_forms=> $v ){
    $id_form[] = $id_forms;
    $data_form[$id_forms] = $v['service_type'];
}
if(!empty($id)){ ?>
    <div class="wpbooking_form_search_tab">
        <ul class="nav nav-tabs">
            <?php
            $i = 0;
            $i2 = 0;
            $class = '';
            $class_content = '';
            foreach($id as $index => $v){
                if($i==0){
                    $class = 'active';
                }else{
                    $class='';
                }

               if(in_array($v,$id_form)){
                    ?>
                       <li class="<?php echo esc_attr($class); ?>">
                           <a data-toggle="tab" href="#tab<?php echo esc_attr($v); ?>">
                               <?php if(!empty($data_form[$v])) {
                                   echo esc_html($data_form[$v]); }else{
                                   echo esc_html__('Service','wp-booking-management-system'); } ?>
                           </a>
                       </li>
               <?php }  ?>
            <?php  $i++; }  ?>
        </ul>
        <div class="tab-content">
            <?php foreach($id as $v){
                if($i2==0){
                    $class_content = 'in active';
                }else{
                    $class_content = '';
                }
                ?>
                <div id="tab<?php echo esc_attr($v); ?>" class="tab-pane fade <?php echo esc_attr($class_content); ?>">
                    <?php echo do_shortcode('[wpbooking_search_form id="'.$v.'"]'); ?>
                </div>
            <?php
                $i2++; } ?>
        </div>
    </div>
<?php } ?>