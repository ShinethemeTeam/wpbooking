<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="traveler-setting-<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="text" id="st_url_media" class="demo-url-image form-control form-control-admin min-width-500" value="<?php echo esc_html($value) ?>" name="traveler_booking_<?php echo esc_html($data['id']) ?>" placeholder="<?php echo esc_html($data['label']) ?>">
        <button class="btn button btn_remove_demo_image button-secondary" type="button" name=""><?php _e("Remove","traveler-booking") ?></button>
        <br>
        <img src="<?php echo esc_url($value) ?>" id="demo_img" class="demo-image form-control settings-demo-image form-control-admin <?php if(empty($value)) echo "none"; ?>" >
        <br>
        <button id="btn_upload_media" class="btn button button-primary" type="button" name=""><?php _e("Upload","traveler-booking") ?></button>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>

<script>
    jQuery(document).ready(function($){
        $(".btn_remove_demo_image").click(function(){
            var container = $(this).parent();
            container.find('.demo-url-image').val('');
            container.find('.demo-image').hide();
        });

        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;
        $('#btn_upload_media').click(function(e) {
            var container = $(this).parent();
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(this);

            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){
                if ( _custom_media ) {
                    container.find('#st_url_media').val(attachment.url);
                    container.find('#demo_img').attr("src",attachment.url).show();
                } else {
                    return _orig_send_attachment.apply( this, [props, attachment] );
                };
            }
            wp.media.editor.open(button);
            return false;
        });
        $('.btn_upload_media').on('click', function(){
            _custom_media = false;
        });
    });
</script>