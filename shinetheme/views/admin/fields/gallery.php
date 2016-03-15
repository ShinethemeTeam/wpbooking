<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="text" id="fg_metadata" class="fg_metadata none" value="<?php echo esc_html($value) ?>" name="traveler_booking_<?php echo esc_html($data['id']) ?>">
        <br>
        <div class="featuredgallerydiv max-width-500">

                <?php
                if(count($tmp = explode(',',$value) ) > 0){ ?>
                    <?php foreach($tmp as $k=>$v){ ?>
                        <?php
                        $url = wp_get_attachment_image_url($v);
                        if(!empty($url)){?>
                        <img src="<?php echo esc_url($url) ?>" class="demo-image-gallery settings-demo-gallery" >
                    <?php } } ?>
                <?php } ?>
        </div>
        <button id="btn_upload_gallery" class="btn button button-primary" type="button" name=""><?php _e("Add Gallery","traveler-booking") ?></button>
        <?php   if(count($tmp = explode(',',$value) ) > 0){ ?>
            <button class="btn button btn_remove_demo_gallery button-secondary" type="button" name=""><?php _e("Remove Gallery","traveler-booking") ?></button>
        <?php }?>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>
<script>
    jQuery(document).ready(function($){
        $(".btn_remove_demo_gallery").click(function(){
            var container = $(this).parent();
            container.find('.fg_metadata').val('');
            container.find('.demo-image-gallery').hide();
        });
        var file_frame;
        jQuery('#btn_upload_gallery').on('click', function(event){
            var container = $(this).parent();
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }
            // Create the media frame.
            file_frame = wp.media.frame = wp.media({
                frame: "post",
                state: "gallery",
                library : { type : 'image'},
               // button: {text: "Edit Image Order"},
                multiple: true
            });
            file_frame.on('open', function() {
                var selection = file_frame.state().get('selection');
                var ids = jQuery('#fg_metadata').val();
                if (ids) {
                    idsArray = ids.split(',');
                    idsArray.forEach(function(id) {
                        attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add( attachment ? [ attachment ] : [] );
                    });
                }
            });
            // When an image is selected, run a callback.
            file_frame.on('update', function() {
                var imageIDArray = [];
                var imageHTML = '';
                var metadataString = '';
                images = file_frame.state().get('library');
                images.each(function(attachment) {
                    imageIDArray.push(attachment.attributes.id);
                    imageHTML += '<img id="'+attachment.attributes.id+'" class="demo-image-gallery settings-demo-gallery" src="'+attachment.attributes.url+'">';
                });
                metadataString = imageIDArray.join(",");
                if (metadataString) {
                    container.find('#fg_metadata').val(metadataString);
                    container.find('.featuredgallerydiv').html(imageHTML);
                }
            });
            file_frame.open();
        });
    });
</script>