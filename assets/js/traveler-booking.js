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
        data.push({
            name : 'action',
            value : 'st_add_to_cart'
        });

        var dataobj = {};
        for (var i = 0; i < data.length; ++i){
            dataobj[data[i].name] = data[i].value;
        }

        return dataobj;
    };


    // Order Form
    $('.traveler_order_form .submit-button').click(function(){
        var form=$(this).closest('.traveler_order_form');
        var me=$(this);
        var data=getFormData(form);
        me.addClass('loading').removeClass('error');
        form.find('.traveler-message').remove();

        data.action='traveler_add_to_cart';

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
                console.log(res.message);
                if(res.message){
                    var message=$('<div/>');
                    message.addClass('traveler-message');
                    message.html(res.message);
                    me.after(message);
                }
                if(me.data.redirect){
                    window.location=me.data.redirect;
                }

                me.removeClass('loading');
            },
            error:function(e){
                me.removeClass('loading').addClass('error');
            }
        })
    });

    // Checkout Form
    $('.traveler_checkout_form .submit-button').click(function(){
        var form=$(this).closest('.traveler_order_form');
        var me=$(this);
        var data=getFormData(form);
        me.addClass('loading').removeClass('error');
        form.find('.traveler-message').remove();

        data.action='traveler_do_checkout';

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
                if(me.data.redirect){
                    window.location=me.data.redirect;
                }
                if(me.redirect){
                    window.location=me.redirect;
                }

                me.removeClass('loading');
            },
            error:function(e){
                me.removeClass('loading').addClass('error');
            }
        })
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

});