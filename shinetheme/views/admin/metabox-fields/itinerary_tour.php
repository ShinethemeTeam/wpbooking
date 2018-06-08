<?php
/**
 * Created by WpBooking Team.
 * User: NAZUMI
 * Date: 12/7/2016
 * Version: 1.0
 */

$old_data = esc_html( $data['std'] );

if(!empty($data['custom_name'])){
    if(isset($data['custom_data'])) $old_data=$data['custom_data'];
}else{
    $old_data=get_post_meta( $post_id, esc_html( $data['id'] ), true);
}
if( !empty( $value ) ){
    $old_data = $value;
}

?>
<div class="wb-itinerary-wrap">
    <h3 class="title"><?php echo esc_attr($data['label']); ?></h3>

    <div class="iti-header">
        <div class="title">
            <strong><?php echo esc_html__('Title','wp-booking-management-system'); ?></strong>
        </div>
        <div class="desc">
            <strong><?php echo esc_html__('Desctiprion','wp-booking-management-system'); ?></strong>
        </div>
    </div>
    <div class="itinerary-content">
        <?php
        if(!empty($old_data['title']) && is_array($old_data['title'])){
            foreach($old_data['title'] as $key => $val) {
                ?>
                <div class="item-itinerary">
                    <div class="input-title">
                        <input type="text" name="<?php echo esc_attr($data['id']) ?>[title][]" value="<?php echo esc_attr($val); ?>">
                    </div>
                    <div class="input-desc">
                        <textarea rows="5" name="<?php echo esc_attr($data['id']) ?>[desc][]"><?php echo esc_attr($old_data['desc'][$key]); ?></textarea>
                    </div>
                    <div class="item-itinerary-del">
                        <a href="#" class="item-itinerary-del"><i class="fa fa-times"></i></a>
                    </div>
                </div>
                <?php
            }
        }else{
            ?>
            <div class="item-itinerary">
                <div class="input-title">
                    <input type="text" name="<?php echo esc_attr($data['id'])?>[title][]" value="" >
                </div>
                <div class="input-desc">
                    <textarea rows="5" name="<?php echo esc_attr($data['id'])?>[desc][]"></textarea>
                </div>
                <div class="item-itinerary-del">
                    <a href="#" class="item-itinerary-del"><i class="fa fa-times"></i></a>
                </div>
            </div>
        <?php
        } ?>
    </div>
    <p class="iti-description"><?php echo esc_attr($data['desc']); ?></p>
    <div class="iti-footer">
        <button data-id="<?php echo esc_attr($data['id'])?>" type="button" class="btn button button-primary wb-itinerary-add-new"><?php echo esc_html__('Add new','wp-booking-management-system'); ?></button>
    </div>
</div>
<div class="item-itinerary-draft hidden">
    <div class="item-itinerary">
        <div class="input-title">
            <input type="text" name="" value="" >
        </div>
        <div class="input-desc">
            <textarea rows="5" name=""></textarea>
        </div>
        <div class="item-itinerary-del">
            <a class="item-itinerary-del" href="#"><i class="fa fa-times"></i></a>
        </div>
    </div>
</div>


