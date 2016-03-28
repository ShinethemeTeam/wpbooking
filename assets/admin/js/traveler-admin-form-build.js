jQuery(document).ready(function( $ ){
/**
 * Condition tags
 * @type {string}
 */
    $('#traveler-layout-id').change(function(){
        var container = $(this).parent().parent();
        var value = $(this).val();
        var url = container.find('.url_layout').data('url');
        url = url.replace(/__layout__id/g, value);
        container.find('.url_layout').attr('href',url);
        console.log(url);
    });

    $(".btn_del_layout").click(function(){
        var $msg = $(this).data('msg');
        var r = confirm($msg);
        if (r == true) {

        } else {
            return false;
        }
    });

});