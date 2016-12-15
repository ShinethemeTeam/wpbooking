<div id="<?php echo esc_html($data['id']) ?>" class="open_section_metabox">
    <?php if(!empty($data['control']) and $data['control'] == true){ ?>
        <div class="content-accodition <?php  if(!empty($data['open_section']) and $data['open_section'] == true){echo 'active';} ?>">
            <?php
            if(!empty($data['open_section']) and $data['open_section'] == true){
                echo balanceTags('<i class="fa fa-chevron-up"></i>');
            } else {
                echo balanceTags('<i class="fa fa-chevron-down"></i>');
            }
            ?>
        </div>
    <?php }

    if(!empty($data['conner_button'])){
        echo do_shortcode($data['conner_button']);
    }
    ?>
    <div class="content-metabox <?php if(isset($data['open_section']) and $data['open_section'] == false){ echo 'no-active'; }?>">
