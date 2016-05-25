/**
 * Created by Dungdt on 3/30/2016.
 */
jQuery(document).ready(function($){
    $('.traveler-rating-review a').hover(function(){
        var index=$(this).index();
        index=parseInt(index);

        $(this).addClass('active');
        $(this).prevAll().addClass('active');
        $(this).nextAll().removeClass('active');

        $(this).closest('.traveler-rating-review').find('.traveler_review_detail_rate').val(index+1);

        var totalRate=0;
        var rateStats=$('.traveler_review_detail_rate');
        if(rateStats.length){
            rateStats.each(function(){
                totalRate+=parseInt($(this).val());
            });
            $('[name=traveler_review]').val(parseFloat(totalRate/rateStats.length));
        }else{
            $('[name=traveler_review]').val(index+1);
        }


    });

    // Single Services
    // Helper functions
    function getFormData(form){
        var data=[];
        var data1 = form.serializeArray();
        for(var i = 0; i < data1.length; i++){
            data.push({
                name : data1[i].name,
                value : data1[i].value
            });
        }
        var dataobj = {};
        for (var i = 0; i < data.length; ++i){
            dataobj[data[i].name] = data[i].value;
        }

        return dataobj;
    };


    // Order Form
    $('.traveler_order_form .submit-button').click(function(){
        var form=$(this).closest('.traveler_order_form');
        form.find('[name]').removeClass('input-error');
        var me=$(this);
        me.addClass('loading').removeClass('error');
        form.find('.traveler-message').remove();

        data=form.serialize();

        $.ajax({
            url:traveler_params.ajax_url,
            data:data,
            dataType:'json',
            type:'post',
            success:function(res){
                if(res.status){
                    me.addClass('success');
                }else{
                    me.addClass('error');
                }
                if(res.message){
                    var message=$('<div/>');
                    message.addClass('traveler-message');
                    message.html(res.message);
                    me.after(message);
                }
                if(typeof  res.data!='undefined' && res.data.redirect){
                    window.location=res.data.redirect;
                }

                if(typeof res.error_fields!='undefined')
                {
                    for(var k in res.error_fields){

                        form.find("[name='"+k+"']").addClass('input-error');
                    }
                }
                if(typeof  res.updated_content!='undefined'){

                    for (var k in res.updated_content){
                        var element=$(k);
                        element.replaceWith(res.updated_content[k]);
                        $(window).trigger('traveler_event_cart_update_content',[k,res.updated_content[k]]);
                    }
                }

                me.removeClass('loading');
            },
            error:function(e){
                var message=$('<div/>');
                message.addClass('traveler-message');
                message.html(e.responseText);
                me.after(message);
                me.removeClass('loading').addClass('error');
            }
        })
    });

    // Checkout Form
    $('.traveler_checkout_form .submit-button').click(function(){
        var form=$(this).closest('.traveler_checkout_form');
        form.find('[name]').removeClass('input-error');
        var me=$(this);
        me.addClass('loading').removeClass('error');
        form.find('.traveler-message').remove();

        data=form.serialize();

        $.ajax({
            url:traveler_params.ajax_url,
            data:data,
            dataType:'json',
            type:'post',
            success:function(res){
                if(res.status){
                    me.addClass('success');
                }else{
                    me.addClass('error');
                }

                if(res.message){
                    var message=$('<div/>');
                    message.addClass('traveler-message');
                    message.html(res.message);
                    me.after(message);
                }
                if(typeof res.data !='undefined'&& typeof res.data.redirect !='undefined' && res.data.redirect){
                    window.location.href=res.data.redirect;
                }
                if(res.redirect){
                    window.location.href=res.redirect;
                }

                if(typeof res.error_fields!='undefined')
                {
                    console.log(res.error_fields);
                    for(var k in res.error_fields){
                        form.find("[name='"+k+"']").addClass('input-error');
                    }
                }
                me.removeClass('loading');
            },
            error:function(e){
                console.log(e);
                me.removeClass('loading').addClass('error');
                var message=$('<div/>');
                message.addClass('traveler-message');
                message.html(e.responseText);
                me.after(message);
            }
        })
    });



    //////////////////////////////////
    /////////// Google Gmap //////////
    //////////////////////////////////

    $('.traveler_google_map').each(function(){
        var map_lat = $(this).data('lat');
        var map_lng = $(this).data('lng');
        var map_zoom = $(this).data('zoom');
        console.log(map_zoom);
        $(this).gmap3({
            map:{
                options:{
                    center:[map_lat,map_lng],
                    zoom: map_zoom
                }
            },
            marker:{
                values:[
                    {latLng:[map_lat, map_lng]},
                ],
                options:{
                    draggable: false
                }
            }
        });
    });

    // Gateway Items
    $('.traveler-gateway-item [name=payment_gateway]').change(function(){
       var parent=$(this).closest('.traveler-gateway-item');
        if(!parent.hasClass('active'))
        {
            parent.siblings().removeClass('active');
            parent.addClass('active');
        }
    });


    $(document).on('click','.item-search .item_taxonomy',function(){
        var container  = $(this).parent().parent();
        var list = "";
        container.find(".item_taxonomy").each(function(){
            if($(this).attr('checked')) {
                list +=  $(this).val()+',';
            }
        });
        container.find('.data_taxonomy').val(list.substring(0,list.length - 1));
    });

    var has_date_picker=$('.has-date-picker');
    has_date_picker.datepicker();
    var datepicker=has_date_picker.datepicker('widget');
    datepicker.wrap('<div class="ll-skin-melon"/>');

    $('.traveler-date-start').datepicker(
        {
            minDate:0,
            onSelect:function(selected) {
                var form=$(this).closest('form');
                var date_end=$('.traveler-date-end',form);
                date_end.datepicker("option","minDate", selected)

            }
        });
    datepicker=$('.traveler-date-start').datepicker('widget');
    datepicker.wrap('<div class="ll-skin-melon"/>');

    $('.traveler-date-end').datepicker( {
        minDate:0,
        onSelect:function(selected) {
            var form=$(this).closest('form');
            var date_end=$('.traveler-date-start',form);
            date_end.datepicker("option","maxDate", selected)

        }
    });
    datepicker=$('.traveler-date-end').datepicker('widget');
    datepicker.wrap('<div class="ll-skin-melon"/>');

});

