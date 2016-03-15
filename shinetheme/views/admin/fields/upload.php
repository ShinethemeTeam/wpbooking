<?php $value = traveler_get_option($data['id'],$data['std']); ?>
<tr class="<?php echo esc_html($data['id']) ?>">
    <th scope="row">
        <label for="<?php echo esc_html($data['id']) ?>"><?php echo esc_html($data['label']) ?>:</label>
    </th>
    <td>
        <input type="text" id="st_url_media" class="form-control form-control-admin" value="<?php echo esc_html($value) ?>" name="st_traveler_booking_settings[<?php echo esc_html($data['id']) ?>]" placeholder="<?php echo esc_html($data['label']) ?>">
        <br>
        <img src="<?php echo esc_url($value) ?>" id="demo_img" class="form-control form-control-admin none" >
        <br>
        <button id="btn_upload_media" class="btn button" type="button" name=""><?php _e("Upload","traveler-booking") ?></button>
        <i class="traveler-desc"><?php echo balanceTags($data['desc']) ?></i>
    </td>
</tr>

<script>
    jQuery(document).ready(function($){
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
                    container.find('#demo_img').attr("src",attachment.url);
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