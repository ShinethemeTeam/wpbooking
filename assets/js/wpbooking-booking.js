/**
 * Created by Dungdt on 3/30/2016.
 */
jQuery(document).ready(function($){


    $('.wpbooking-rating-review a').hover(function(){
        var index=$(this).index();
        index=parseInt(index);

        $(this).addClass('active');
        $(this).prevAll().addClass('active');
        $(this).nextAll().removeClass('active');

        $(this).closest('.wpbooking-rating-review').find('.wpbooking_review_detail_rate').val(index+1);

        var totalRate=0;
        var rateStats=$('.wpbooking_review_detail_rate');
        if(rateStats.length){
            rateStats.each(function(){
                totalRate+=parseInt($(this).val());
            });
            $('[name=wpbooking_review]').val(parseFloat(totalRate/rateStats.length));
        }else{
            $('[name=wpbooking_review]').val(index+1);
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
    $('.wpbooking_order_form .submit-button').click(function(){
        var form=$(this).closest('.wpbooking_order_form');
        form.find('[name]').removeClass('input-error');
        var me=$(this);
        me.addClass('loading').removeClass('error');
        form.find('.wpbooking-message').remove();

        data=form.serialize();

        $.ajax({
            url:wpbooking_params.ajax_url,
            data:data,
            dataType:'json',
            type:'post',
            success:function(res){
                if(typeof grecaptcha !='undefined')
                grecaptcha.reset();
                if(res.status){
                    me.addClass('success');
                }else{
                    me.addClass('error');
                }
                if(res.message){
                    var message=$('<div/>');
                    message.addClass('wpbooking-message');
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
                        $(window).trigger('wpbooking_event_cart_update_content',[k,res.updated_content[k]]);
                    }
                }

                me.removeClass('loading');
            },
            error:function(e){
                if(typeof grecaptcha !='undefined')
                    grecaptcha.reset();
                var message=$('<div/>');
                message.addClass('wpbooking-message');
                message.html(e.responseText);
                me.after(message);
                me.removeClass('loading').addClass('error');

            }
        })
    });

    // Checkout Form
    $('.wpbooking_checkout_form .submit-button').click(function(){
        var form=$(this).closest('.wpbooking_checkout_form');
        form.find('[name]').removeClass('input-error');
        var me=$(this);
        me.addClass('loading').removeClass('error');
        form.find('.wpbooking-message').remove();

        data=form.serialize();

        $.ajax({
            url:wpbooking_params.ajax_url,
            data:data,
            dataType:'json',
            type:'post',
            success:function(res){
                if(typeof grecaptcha !='undefined')
                    grecaptcha.reset();
                if(res.status){
                    me.addClass('success');
                }else{
                    me.addClass('error');
                }

                if(res.message){
                    var message=$('<div/>');
                    message.addClass('wpbooking-message');
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
                if(typeof grecaptcha !='undefined')
                    grecaptcha.reset();
                me.removeClass('loading').addClass('error');
                var message=$('<div/>');
                message.addClass('wpbooking-message');
                message.html(e.responseText);
                me.after(message);
            }
        })
    });



    //////////////////////////////////
    /////////// Google Gmap //////////
    //////////////////////////////////

    $('.wpbooking_google_map').each(function(){
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
    $('.wpbooking-gateway-item [name=payment_gateway]').change(function(){
       var parent=$(this).closest('.wpbooking-gateway-item');
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
    has_date_picker.datepicker()
        .datepicker('widget')
        .wrap('<div class="ll-skin-melon"/>');

    $('.wpbooking-date-start').datepicker(
        {
            minDate:0,
            onSelect:function(selected) {
                var form=$(this).closest('form');
                var date_end=$('.wpbooking-date-end',form);
                date_end.datepicker("option","minDate", selected)

            }
        })
        .datepicker('widget').wrap('<div class="ll-skin-melon"/>');

    $('.wpbooking-date-end').datepicker( {
        minDate:0,
        onSelect:function(selected) {
            var form=$(this).closest('form');
            var date_end=$('.wpbooking-date-start',form);
            date_end.datepicker("option","maxDate", selected)

        }
    })
    .datepicker('widget')
    .wrap('<div class="ll-skin-melon"/>');

    $('.bravo-select2').select2();

    /**
     * Show More Search Fields
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-show-more-fields').click(function(){
        var parent=$(this).parent();
        $('.wpbooking-search-form-wrap').addClass('show-more-active');

        parent.find('.wpbooking-search-form-more').slideDown('fast');
        $(this).hide();

    });

    /**
     * Hide More Search Fields
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-hide-more-fields').click(function(){
        var parent=$(this).closest('.wpbooking-search-form-more-wrap');

        parent.find('.wpbooking-search-form-more').slideUp('fast',function(){

            parent.find('.wpbooking-show-more-fields').show();
        });

        $('.wpbooking-search-form-wrap').removeClass('show-more-active');
    });

    /**
     * Ion-RangeSlider for Price Search Field
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-ionrangeslider').each(function(){
        if(typeof $.fn.ionRangeSlider=='undefined') return false;
        var min=$(this).data('min');
        var max=$(this).data('max');
        var type=$(this).data('type');
        $(this).ionRangeSlider({
            min: min,
            max: max,
            type:type
        });
    });


    /**
     * Calendar Handler for Single Place Order Form
     *
     * @since 1.0
     * @author dungdt
     */
    var wpbooking_calendar_months=[];
    var wpbooking_enable_dates=[];
    var order_start_date=$('.wpbooking_order_form .wpbooking-field-date-start');
    var order_end_date=$('.wpbooking_order_form .wpbooking-field-date-end');

    // Init Datepicker
    order_start_date .datepicker({
        minDate:0,
        onSelect:function(selected) {
            order_end_date.datepicker("option","minDate", selected);
        },
        beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            for(i=0;i<wpbooking_enable_dates.length;i++){
                if(string==wpbooking_enable_dates[i]['date']) return [1,'wpbooking-enable-date',wpbooking_enable_dates[i]['tooltip_content']];
            }

            return [0,'wpbooking-disable-date'];
        },
        onChangeMonthYear:function(year,month,obj){
            loadCalendarMonth(year,month);
        },
        onClose:function(){
            $('.tooltip ').hide();
        }
    });
    order_end_date.datepicker({minDate:0,
        onSelect:function(selected) {
            if(selected){
                order_start_date.datepicker("option","maxDate", selected);
            }
        },
        beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            for(i=0;i<wpbooking_enable_dates.length;i++){
                if(string==wpbooking_enable_dates[i]['date']) return [1,'wpbooking-enable-date',wpbooking_enable_dates[i]['tooltip_content']];
            }

            return [0,'wpbooking-disable-date'];
        },
        onChangeMonthYear:function(year,month){
            loadCalendarMonth(year,month);
        },
        onClose:function(){
            $('.tooltip ').hide();
        }
    });

    $(document).on('hover','.wpbooking-enable-date',function(){
        $( this).tooltip({
            container:'body',
            trigger:'hover'
        }).tooltip('show');
    });

    function loadCalendarMonth(year,month)
    {
        var currentMonth=false;
        var currentYear=false;
        var key=false;

        if(typeof  year=='undefined' && typeof month=='undefined'){
            var date=new Date();
            currentMonth=date.getMonth();
            currentYear=date.getFullYear();
        }else{
            currentMonth=month;
            currentYear=year;
        }

        currentMonth=(parseInt(currentMonth)<10)?'0'+currentMonth:currentMonth;

        key=currentMonth+'_'+currentYear;

        // check in exists calendar month
        if($.inArray(key,wpbooking_calendar_months)==-1){
            $.ajax({
                url:wpbooking_params.ajax_url,
                type:'post',
                dataType:'json',
                data:{
                    post_id:$('[name=post_id]').val(),
                    currentMonth:currentMonth,
                    currentYear:currentYear,
                    action:'wpbooking_calendar_months'
                },
                success:function(res){
                    if(typeof res.months!='undefined'){

                        for(var k in res.months){
                            wpbooking_calendar_months.push(k);
                            wpbooking_enable_dates= $.merge(wpbooking_enable_dates,res.months[k]);
                        }
                       // order_start_date.datepicker('refresh');
                       // order_end_date.datepicker('refresh');
                    }
                }
            });
        }
    }

    loadCalendarMonth();
    //==========================================================================================
    // End Calendar Handler for Single Place Order Form
    //==========================================================================================


    /**
     * Price Chart
     */


    $(window).load(function(){
        $('.wpbooking-price-chart').each(function(){
            var me=$(this);
            console.log(me.data('chart'));
            try{
                var data=me.data('chart');
                console.log(data);
                var max=data[0];

                for(i=0;i<data.length;i++){
                    if(data[i]>max) max=data[i];
                }

                for(i=0;i<data.length;i++){
                    var bar=$('<div/>');
                    var height=parseInt(data[i]*25/max);
                    bar.addClass('bar-item');
                    bar.css({
                        height:height
                    });
                    me.append(bar);
                }
            }catch(e){

            }

        });


    });

    /**
     * Active Next Check In Field
     */
    $('[name=location_id]').change(function(){
        var parent=$(this).closest('.item-search');
        parent.next().find('[name=check_in]').focus();
    });

    // Ajax Search in Archive page
    var form_filter=$('.wpbooking-search-form');
    if($('.wpbooking-archive-page').length)
    {
        var me=$(this)
        form_filter.submit(function(){
            var loop_wrap=$('.wpbooking-loop-wrap');
            loop_wrap.addClass('loading');
            // Ajax Search
            $.ajax({
                url:form_filter.attr('action'),
                type:form_filter.attr('method'),
                data:form_filter.serialize(),
                dataType:'json',
                success:function(res){
                    loop_wrap.removeClass('loading');
                    if(typeof res.html!='undefined'){
                        loop_wrap.html(res.html);
                    }
                },
                error:function(){
                    loop_wrap.removeClass('loading');

                }
            });
            return false;
        });

        // Trigger Search
        $(this).find('input,select').change(function(){
            form_filter.submit();
        });
    }

    /**
     * Button Show More Terms in search fields
     */
    $('.show-more-terms').click(function(){
        $(this).closest('.row').find('.term-item').removeClass('hidden_term');
        $(this).parent().remove();
    });
});

