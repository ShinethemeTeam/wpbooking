jQuery(document).ready(function( $ ){
/**
 * Condition tags
 * @type {string}
 */
    $('#wpbooking-layout-id').change(function(){
        var container = $(this).parent().parent();
        var value = $(this).val();
        var url = container.find('.url_layout').data('url');
        url = url.replace(/__form__id/g, value);
        container.find('.url_layout').attr('href',url);

    });

    $(".btn_del_layout").click(function(){
        var $msg = $(this).data('msg');
        var r = confirm($msg);
        if (r == true) {

        } else {
            return false;
        }
    });

    $('#wpbooking_field_form_build').prop('selectedIndex',0);

    $("#wpbooking_field_form_build").change(function(){
        var container  = $(this).parent().parent();
        var value = $(this).val();
        var html = container.find('#item-'+value).html();
        var title = container.find('#item-'+value).data('title');
        var div_control = container.find('.select-control');
        div_control.find('.head').html(title);
        div_control.find('.content-flied-control').html(html);
        div_control.find('#wpbooking-shortcode-flied').val("");
        if(value != ""){
            container.find(".div-content-control").show(500);
        }else{
            container.find(".div-content-control").hide(500);
        }


        // add shortcode
        var name_shortcode = container.find('#item-'+value).data('name-shortcode');
        if(name_shortcode != undefined){
            var shortcode = "["+name_shortcode;
            container.find('.div-content-control .item').each(function(){
                var item_name =$(this).attr('name');
                if(typeof item_name!='undefined'){
                    var item_value = $(this).val();

                    if(item_value != ""){
                        shortcode += ' '+item_name+'="'+item_value+'"';
                    }
                }

            });
            shortcode += " ]";
            $("#wpbooking-shortcode-flied").val(shortcode);
        }
    });


    // add shortcode
    $(document).on('keyup change','.content-flied-control .item',function(){
        var container  = $(this).parent().parent().parent().parent();
        var name_shortcode = $(this).data('name-shortcode');
        var shortcode = "["+name_shortcode;
        var content = "";
        container.find('.item').each(function(){
            var item_name =$(this).attr('name');
            var item_value = $(this).val();
            var item_type = $(this).data('type');
            if(typeof item_name!='undefined'){
                if(item_type == "content"){
                    content = item_value;
                }else if(item_type == "is_required"){
                    if($(this).attr('checked')) {
                        shortcode += ' '+item_name+'="'+item_value+'"';
                    }
                }else{
                    if(item_value != ""){
                        shortcode += ' '+item_name+'="'+item_value+'"';
                    }
                }
            }

        });
        shortcode += " ]";
        if(content){
            shortcode += content+"[/"+name_shortcode+"]";
        }
        $("#wpbooking-shortcode-flied").val(shortcode);
    });
    $(document).on('change','.content-flied-control .group-checkbox .item_check_box',function(){
        var value = '';
        var container =  $(this).parent().parent().parent().parent();
        if($(this).hasClass('single_checkbox')){
            container.find('.item_check_box').each(function(){
                if($(this).attr('checked')) {
                    value +=  $(this).val()+',';
                }
            });
            container.find('.item').val(value.substring(0,value.length - 1));
        }else{
            container.find('.item').val($(this).val());
        }

        container.find('.item').change();
    });

    // add field search form
    $(document).on('click','.btn_add_field_search_form',function(){

        var container_full = $(this).parent().parent().parent().parent();
        var container = $(this).parent().parent().parent();
        var post_type = $(this).data('post-type');
        var number = $(this).attr("data-number");
        var name_field_search = $(this).attr("data-name-field-search");
        var item_html = container_full.find('.div_content_hide_'+post_type).html();
        item_html = item_html.replace(/__number__/g, number);
        item_html = item_html.replace(/__name_field_search__/g, name_field_search);
        container.find('.content_list_search_form_widget').append(item_html);

        number = Number(number) + 1;

        $(this).attr("data-number",number);
        $('.content_list_search_form_widget').sortable('refresh');
    })
    $(document).on('click','.btn_remove_field_search_form',function(){
        if(confirm(wpbooking_params.delete_confirm)){

            $(this).parent().parent().remove();
        }
    });
    jQuery(document).on('widget-updated', function(e, widget){
        $('.content_list_search_form_widget').sortable();
    });

    $('.content_list_search_form_widget').sortable();

    $(document).on('change','.option_service_search_form',function(){
        var container = $(this).parent().parent().parent();
        var post_type = $(this).val();
        container.find(".list_item_widget").hide();
        container.find(".div_content_"+post_type).show();
    });

    $(document).on('keyup change','.content_list_search_form_widget input.title',function(){
        var container  = $(this).parent().parent().parent().parent().parent().parent();
        var text = $(this).val();
        container.find(".head-title").html(text);
    });
    $('.content_list_search_form_widget input.title').each(function(){
        var container  = $(this).parent().parent().parent().parent().parent().parent();
        var text = $(this).val();
        container.find(".head-title").html(text);
    });

    $(document).on('click','.btn_edit_field_search_form',function(){
        var container  = $(this).parent().parent();
        var check = container.find('.control-hide').css('display');
        if(check == 'none'){
            container.find('.control-hide').show(500);
        }else{
            container.find('.control-hide').hide(500);
        }
    });

    /////////////////////////////////
    /////// Widget Cusotm ///////////
    /////////////////////////////////

    $(document).on('change click','.list_item_widget .field_type',function(){
        var container  = $(this).parent().parent().parent().parent().parent().parent();
        var value = $(this).val();

        if(value == "taxonomy"){
            container.find(".div_taxonomy").show(500);;
            container.find(".div_taxonomy_show").show(500);;
            container.find(".div_taxonomy_operator").show(500);;
        }else{
            container.find(".div_taxonomy").hide(500);;
            container.find(".div_taxonomy_show").hide(500);;
            container.find(".div_taxonomy_operator").hide(500);;
        }

        if(value == "review_rate"){
            container.find(".div_review_operator").show(500);;
        }else{
            container.find(".div_review_operator").hide(500);;
        }
    });
    $(document).on('click','.btn_edit_field_search_form',function(){
        $(this).parent().parent().find(".field_type").each(function(){
            var container  = $(this).parent().parent().parent().parent().parent().parent();
            var value = $(this).val();

            if(value == "taxonomy"){
                container.find(".div_taxonomy").show();
                container.find(".div_taxonomy_show").show();
                container.find(".div_taxonomy_operator").show();
            }else{
                container.find(".div_taxonomy").hide();;
                container.find(".div_taxonomy_show").hide();;
                container.find(".div_taxonomy_operator").hide();;
            }

            if(value == "review_rate"){
                container.find(".div_review_operator").show(500);;
            }else{
                container.find(".div_review_operator").hide(500);;
            }
        });
    })
    $(".list_item_widget .field_type").each(function(){
        var container  = $(this).parent().parent().parent().parent().parent().parent();
        var value = $(this).val();

        if(value == "taxonomy"){
            container.find(".div_taxonomy").show(500);;
            container.find(".div_taxonomy_show").show(500);;
            container.find(".div_taxonomy_operator").show(500);;
        }else{
            container.find(".div_taxonomy").hide(500);;
            container.find(".div_taxonomy_show").hide(500);;
            container.find(".div_taxonomy_operator").hide(500);;
        }

        if(value == "review_rate"){
            container.find(".div_review_operator").show(500);;
        }else{
            container.find(".div_review_operator").hide(500);;
        }
    });



    $(document).on('click','.button-copy-shortcode',function(){
        var container  = $(this).closest('tr');
        var input = container.find('#wpbooking-shortcode-flied');
        var value = input.val();
        setTimeout(function(){
            input.trigger('focus');
            input.trigger('select');
        },100)
        copyToClipboard(value);
    })

    function copyToClipboard(text) {
        if (window.clipboardData && window.clipboardData.setData) {
            // IE specific code path to prevent textarea being shown while dialog is visible.
            return clipboardData.setData("Text", text);

        } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
            var textarea = document.createElement("textarea");
            textarea.textContent = text;
            textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
            document.body.appendChild(textarea);
            textarea.select();
            try {
                return document.execCommand("copy");  // Security exception may be thrown by some browsers.
            } catch (ex) {
                console.warn("Copy to clipboard failed.", ex);
                return false;
            } finally {
                document.body.removeChild(textarea);
            }
        }
    }

});