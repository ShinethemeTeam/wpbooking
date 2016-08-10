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

    $('.service-map-element').each(function(){
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

        var name=$(this).val();
        if(!$('.wpbooking-gateways-desc .gateway-desc.gateway-id-'+name).hasClass('active')){
            $('.wpbooking-gateways-desc .gateway-desc').removeClass('active');
            $('.wpbooking-gateways-desc .gateway-desc.gateway-id-'+name).addClass('active');
        }
    });


    $('.item-search .wb-icheck').on('ifChanged',function(){

        var list = "";
        var container=$(this).closest('.list-checkbox');
        container.find(".wb-icheck").each(function(){
            if($(this).attr('checked')) {
                list +=  $(this).val()+',';
            }
        });
        container.find('.data_taxonomy').val(list.substring(0,list.length - 1));

    });

    var has_date_picker=$('.has-date-picker');
    has_date_picker.datepicker()
        .datepicker('widget');
        //.wrap('<div class="ll-skin-melon"/>');

    $('.wpbooking-date-start').datepicker(
        {
            minDate:0,
            onSelect:function(selected) {
                var form=$(this).closest('form');
                var date_end=$('.wpbooking-date-end',form);
                date_end.datepicker("option","minDate", selected)
                if($('.wpbooking-date-end').length){
                    window.setTimeout(function(){
                        $('.wpbooking-date-end').datepicker('show');
                    },100);
                }
                $(this).trigger('change');
            }
        })
        .datepicker('widget');//.wrap('<div class="ll-skin-melon"/>');

    $('.wpbooking-date-end').datepicker( {
        minDate:0,
        onSelect:function(selected) {
            var form=$(this).closest('form');
            var date_end=$('.wpbooking-date-start',form);
            date_end.datepicker("option","maxDate", selected)

        }
    })
    .datepicker('widget');
    //.wrap('<div class="ll-skin-melon"/>');

    $('.wpbooking-select2').select2();

    /**
     * Show More Search Fields
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-show-more-fields').click(function(){
        var parent=$(this).parent();
        if(!$(this).hasClass('active')){
            $('.wpbooking-search-form-wrap').addClass('show-more-active');
            parent.find('.wpbooking-search-form-more').slideDown('fast');
            $(this).addClass('active');
        }else{
            var gr_parent=$(this).closest('.wpbooking-search-form-more-wrap');
            gr_parent.find('.wpbooking-search-form-more').slideUp('fast',function(){
                gr_parent.find('.wpbooking-show-more-fields').show();
            });
            $('.wpbooking-search-form-wrap').removeClass('show-more-active');
            $(this).removeClass('active');
        }


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
    var wpbooking_checkin_enable_dates=[];// Enable for Checkin
    var wpbooking_checkout_enable_dates=[];// Enable for Checkout
    var order_start_date=$('.wpbooking_order_form .wpbooking-field-date-start');
    var order_end_date=$('.wpbooking_order_form .wpbooking-field-date-end');

    // Init Datepicker
    order_start_date.datepicker({
        minDate:0,
        onSelect:function(selected) {
            order_end_date.datepicker("option","minDate", selected);
            //order_end_date.focus();
            window.setTimeout(function(){
                order_end_date.datepicker( "show" );
            },100);

        },

        beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
            for(i=0;i<wpbooking_checkin_enable_dates.length;i++){
                if(string==wpbooking_checkin_enable_dates[i]['date'] && wpbooking_checkin_enable_dates[i]['can_check_in']) return [1,'wpbooking-enable-date',wpbooking_checkin_enable_dates[i]['tooltip_content']];
            }

            return [0,'wpbooking-disable-date'];
        },
        onChangeMonthYear:function(year,month,obj){
            console.log('xx');
            loadCalendarMonth(year,month);
        },
        onClose:function(){
            $('.tooltip ').hide();
        }
    });
    order_end_date.datepicker({minDate:0,
        onClose:function(){
            console.log(2);
        },
        onSelect:function(selected) {
            if(selected){
                order_start_date.datepicker("option","maxDate", selected);
            }
        },
        beforeShowDay: function(date){
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            for(i=0;i<wpbooking_checkout_enable_dates.length;i++){
                if(string==wpbooking_checkout_enable_dates[i]['date'] && wpbooking_checkout_enable_dates[i]['can_check_out']) return [1,'wpbooking-enable-date',wpbooking_checkout_enable_dates[i]['tooltip_content']];
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
        wpbooking_checkin_enable_dates.length=0;
        wpbooking_checkout_enable_dates.length=0;

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
        //if($.inArray(key,wpbooking_calendar_months)==-1){
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
                if(typeof res.dates!='undefined'){
                    for(var k in res.dates){
                        if(res.dates[k].can_check_in==1)
                        wpbooking_checkin_enable_dates.push(res.dates[k]);
                        if(res.dates[k].can_check_out==1)
                        wpbooking_checkout_enable_dates.push(res.dates[k]);
                    }
                }
                order_start_date.datepicker('refresh');
                //order_end_date.datepicker('refresh');
            }
        });
       // }
    }
    if(order_start_date.length || order_end_date.length){

        loadCalendarMonth();
    }
    //==========================================================================================
    // End Calendar Handler for Single Place Order Form
    //==========================================================================================


    /**
     * Price Chart
     */


    $(window).load(function(){
        var ctx=$('#wpbooking-price-chart2');
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["1", "2", "3", "4", "5", "6", "7",8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28],
                datasets: [{
                    label: "My First dataset",
                    fill: false,
                    lineTension: 0.1,
                    borderWidth:1,
                    //backgroundColor: "red",
                    borderColor: "#c1c1c1",
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "transparent",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 0,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "transparent",
                    pointHoverBorderColor: "transparent",
                    pointHoverBorderWidth: 2,
                    pointRadius: 0,
                    pointHitRadius: 10,
                    data: ctx.data('chart'),
                    spanGaps: false,
                }]
            },
            options: {
                scaleShowLabels:false,
                tooltips:{
                        enabled:false
                    },
                legend:{
                    display:false
                },
                scales: {
                    xAxes: [{
                        display: false,
                        gridLines: {
                            color: "white"
                        }
                    }],
                    yAxes: [{
                        display: false,
                        gridLines: {
                            color: "white"
                        }
                    }]
                }

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
    form_filter.submit(function(){

        // Validate Required Field
        var is_validated=true;
        var scrollTo=false;
        form_filter.find('.wb-required').removeClass('wb-error');
        form_filter.find('.item-search').removeClass('wb-error');

        form_filter.find('.wb-required').each(function(){
            if($(this).val()==false){
                console.log($(this));
                is_validated=false;
                $(this).addClass('wb-error');
                $(this).closest('.item-search').addClass('wb-error');

                if($(this).closest('.wpbooking-search-form-more').length){
                    $(this).closest('.wpbooking-search-form-wrap').find('.wpbooking-show-more-fields').trigger('click');
                }

                // Scroll to first error input
                if(!scrollTo){
                    scrollTo=$(this).offset().top-200;
                }
            }
        });

        if(!is_validated){
            if(scrollTo){
                $('html,body').animate({
                    scrollTop:scrollTo
                },'fast');
            }
            return false;
        }
    });
    // Remove Class Wb-error on input
    $(this).find('input,select').change(function(){
        if($(this).hasClass('wb-error') && $(this).val()){
            $(this).removeClass('wb-error');
            $(this).closest('.item-search').removeClass('wb-error');
        }
    });

    if($('.wpbooking-archive-page').length)
    {

        var me=$(this);
        //form_filter.submit(function(){
        //    var loop_wrap=$('.wpbooking-loop-wrap');
        //    loop_wrap.addClass('loading');
        //    // Ajax Search
        //    $.ajax({
        //        url:form_filter.attr('action'),
        //        type:form_filter.attr('method'),
        //        data:form_filter.serialize(),
        //        dataType:'json',
        //        beforeSend:function(){
        //
        //            $('body,html').animate({
        //                scrollTop:($('.wpbooking-loop-header').offset().top-100)
        //            },'fast');
        //        },
        //        success:function(res){
        //            loop_wrap.removeClass('loading');
        //            if(typeof res.html!='undefined'){
        //                loop_wrap.html(res.html);
        //            }
        //            if(typeof res.updated_element!='undefined'){
        //                for(var key in res.updated_element){
        //                    $(''+key+'').html(res.updated_element[key]);
        //                }
        //
        //            }
        //            // Loop Grid Gallery
        //            if(typeof $.fn.owlCarousel=='function')
        //                $('.service-gallery-slideshow').owlCarousel(
        //                    {
        //                        items:1,
        //                        nav:true,
        //                        navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
        //                    }
        //                );
        //        },
        //        error:function(){
        //            loop_wrap.removeClass('loading');
        //
        //        }
        //    });
        //    return false;
        //});

        // Trigger Search
        //$(this).find('input,select').change(function(){
        //    form_filter.submit();
        //});
    }

    /**
     * Button Show More Terms in search fields
     */
    $('.show-more-terms').click(function(){
        $(this).closest('.list-checkbox').find('.term-item').removeClass('hidden_term');
        $(this).parent().remove();
    });

    // Partner Register

    $('.upload-certificate').each(function(){
        var me=$(this);
        $('.service_type_checkbox').change(function(){
            if($(this).attr('checked')){
                me.addClass('active');
            }else{
                me.removeClass('active');
            }
        })

        me.find('.upload_input').change(function(){
            var formData = new FormData();
            formData.append('action','wpbooking_upload_certificate');
            formData.append('image',$(this)[0].files[0]);

            me.find('.upload-message').html('');

            $.ajax({
                type: "POST",
                url: wpbooking_params.ajax_url,
                enctype: 'multipart/form-data',
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(data) {
                    me.find('.uploaded_image_preview').remove();
                    if(data.message){
                        me.find('.upload-message').html(data.message);
                    }
                    if(data.status && data.image){
                        me.find('.image_url').val(data.image.url);
                        me.append('<img class="uploaded_image_preview" alt="" src="'+data.image.url+'">');
                    }
                },
                error:function(e){
                    if(e.responseText){
                        me.find('.upload-message').html(e.responseText);
                    }
                }
            });
        });

    });

    // MY Account
    // Table Check All
    $('.select-all [type=checkbox]').change(function(){
       if($(this).attr('checked')){
           $(this).closest('table').find('tbody .select-all [type=checkbox]').prop('checked',true);
       }else{
           $(this).closest('table').find('tbody .select-all [type=checkbox]').prop('checked',false);
       }
    });

    // Accordion
    $('.wpbooking-accordion-title').click(function(){
        if($(this).parent().hasClass('active')){
            $(this).parent().find('.wpbooking-metabox-accordion-content').slideUp('fast');
            $(this).parent().removeClass('active');
        }else{
            var s=$(this).parent().siblings('.wpbooking-metabox-accordion');
            s.find('.wpbooking-metabox-accordion-content').slideUp('fast');
            s.removeClass('active');
            $(this).parent().find('.wpbooking-metabox-accordion-content').slideDown('fast');
            $(this).parent().addClass('active');
            $(window).trigger('resize');
        }
    });
    /**
     * Condition tags
     * @type {string}
     */
    var condition_object='select, input[type="radio"]:checked, input[type="text"], input[type="hidden"], input.ot-numeric-slider-hidden-input,input[type="checkbox"]';
    // condition function to show and hide sections
    $('.wpbooking-form-group').on( 'change.conditionals', condition_object, function(e) {
        run_condition_engine();
    });
    run_condition_engine();
    function run_condition_engine(){
        $('.wpbooking-condition[data-condition]').each(function() {

            var passed;
            var conditions = get_match_condition( $( this ).data( 'condition' ) );
            var operator = ( $( this ).data( 'operator' ) || 'and' ).toLowerCase();
            $.each( conditions, function( index, condition ) {

                var target   = $(  '#'+ condition.check );

                var targetEl = !! target.length && target.first();

                if ( ! target.length || ( ! targetEl.length && condition.value.toString() != '' ) ) {
                    return;
                }

                var v1 = targetEl.length ? targetEl.val().toString() : '';
                var v2 = condition.value.toString();
                var result;

                if(targetEl.length && targetEl.attr('type')=='checkbox'){
                    v1=targetEl.is(':checked')?v1:'';
                }

                switch ( condition.rule ) {
                    case 'less_than':
                        result = ( parseInt( v1 ) < parseInt( v2 ) );
                        break;
                    case 'less_than_or_equal_to':
                        result = ( parseInt( v1 ) <= parseInt( v2 ) );
                        break;
                    case 'greater_than':
                        result = ( parseInt( v1 ) > parseInt( v2 ) );
                        break;
                    case 'greater_than_or_equal_to':
                        result = ( parseInt( v1 ) >= parseInt( v2 ) );
                        break;
                    case 'contains':
                        result = ( v1.indexOf(v2) !== -1 ? true : false );
                        break;
                    case 'is':
                        result = ( v1 == v2 );
                        break;
                    case 'not':
                        result = ( v1 != v2 );
                        break;
                }

                if ( 'undefined' == typeof passed ) {
                    passed = result;
                }

                switch ( operator ) {
                    case 'or':
                        passed = ( passed || result );
                        break;
                    case 'and':
                    default:
                        passed = ( passed && result );
                        break;
                }

            });

            if ( passed ) {
                $(this).show();
            } else {
                $(this).hide();
            }

            delete passed;

        });
    }

    function get_match_condition(condition){
        var match;
        var regex = /(.+?):(is|not|contains|less_than|less_than_or_equal_to|greater_than|greater_than_or_equal_to)\((.*?)\),?/g;
        var conditions = [];

        while( match = regex.exec( condition ) ) {
            conditions.push({
                'check': match[1],
                'rule':  match[2],
                'value': match[3] || ''
            });
        }

        return conditions;
    }
    // Please do not edit condition section if you don't understand what it is

    jQuery(window).load(function(){

        ///////////////////////////
        //////  Gmap    //////////
        ///////////////////////////

        function load_gmap(){
            var last_center=false;
            if( $('.wpbooking-gmap-wrapper').length ){
                $('.wpbooking-gmap-wrapper').each(function(index, el) {

                    var t = $(this);
                    var gmap = $('.gmap-content', t);
                    var map_lat = parseFloat( $('input[name="map_lat"]', t).val() );
                    var map_long = parseFloat( $('input[name="map_long"]', t).val() );

                    var map_zoom = parseInt( $('input[name="map_zoom"]', t).val() );

                    var bt_ot_searchbox = $('input.gmap-search', t);

                    var current_marker;

                    var map_options={
                        map:{
                            options:{
                                mapTypeId: google.maps.MapTypeId.ROADMAP,
                                mapTypeControl: true,
                                mapTypeControlOptions: {
                                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                                },
                                navigationControl: true,
                                scrollwheel: true,
                            },
                            events:{
                                click: function(marker, event, context){
                                    last_center=event.latLng;
                                    $('input[name="map_lat"]', t).val( event.latLng.lat() );
                                    $('input[name="map_long"]', t).val( event.latLng.lng() );
                                    $('input[name="map_zoom"]', t).val( marker.zoom );

                                    $(this).gmap3({
                                        clear: {
                                            name:["marker"],
                                            last: true
                                        }
                                    });
                                    $(this).gmap3({
                                        marker:{
                                            values:[
                                                {latLng:event.latLng },
                                            ],
                                            options:{
                                                draggable: false
                                            },
                                        }
                                    });
                                }
                            }
                        }

                    };
                    if(map_lat && map_long){
                        map_options.map.options.center=[map_lat, map_long];
                        map_options.map.options.zoom=map_zoom;
                    }
                    gmap.gmap3(map_options);

                    var gmap_obj = gmap.gmap3('get');

                    if(!map_lat || !map_long){
                        // Try to get current location
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(showPosition){

                                var gmap_obj = gmap.gmap3('get');
                                map_lat=showPosition.coords.latitude;
                                map_long=showPosition.coords.longitude;

                                last_center=new google.maps.LatLng(map_lat,map_long);

                                gmap_obj.setCenter(last_center);
                                gmap_obj.setZoom(13);
                                $('input[name="map_lat"]', t).val( map_lat );
                                $('input[name="map_long"]', t).val( map_long );
                                $('input[name="map_zoom"]', t).val( 13);

                                gmap.gmap3({
                                    clear: {
                                        name:["marker"],
                                        last: true
                                    }
                                });
                                gmap.gmap3({
                                    marker:{
                                        values:[
                                            {latLng:last_center },
                                        ],
                                        options:{
                                            draggable: false
                                        },
                                    }
                                });
                            });

                        }
                    }


                    var geocoder = new google.maps.Geocoder;

                    var map_type = "roadmap";

                    if( bt_ot_searchbox.length ){
                        var searchBox = new google.maps.places.SearchBox( bt_ot_searchbox[0] );

                        google.maps.event.addListener(searchBox, 'places_changed', function() {
                            var places = searchBox.getPlaces();
                            if (places.length == 0) {
                                return;
                            }

                            // For each place, get the icon, place name, and location.
                            var bounds = new google.maps.LatLngBounds();
                            for (var i = 0, place; place = places[i]; i++) {

                                bounds.extend(place.geometry.location);

                                if(i == 0){

                                    gmap.gmap3({
                                        clear: {
                                            name:["marker"],
                                            last: true
                                        }
                                    });
                                    gmap.gmap3({
                                        marker:{
                                            values:[
                                                {latLng: place.geometry.location },
                                            ],
                                            options:{
                                                draggable: false
                                            },
                                        }
                                    });

                                    $('input[name="map_lat"]', t).val( place.geometry.location.lat() );
                                    $('input[name="map_long"]', t).val( place.geometry.location.lng() );
                                    $('input[name="map_zoom"]', t).val( gmap_obj.getZoom() );

                                }
                            }

                            //gmap_obj.fitBounds(bounds);

                        });

                    }

                    google.maps.event.addListener(gmap_obj, "zoom_changed", function(event) {
                        $('input[name="map_zoom"]', t).val( gmap_obj.getZoom() );
                    });

                    $(window).resize(function(){
                        google.maps.event.trigger(gmap_obj, 'resize');
                        if(last_center){
                            gmap_obj.setCenter(last_center);
                        }
                    });
                });
            }
        }

        load_gmap();
    });

    // ALl Booking Calendar
    if(typeof $.fn.fullCalendar=='function'){

        $('#wpbooking_order_calendar .calendar-wrap').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            //eventLimit: true, // allow "more" link when too many events,
            height:500,
            numberOfMonths: 2,
            events:function(start, end, timezone, callback) {
                var filter=$('.tablenav');
                $.ajax({
                    url: wpbooking_params.ajax_url,
                    dataType: 'json',
                    type:'post',
                    data: {
                        action: 'wpbooking_order_calendar',
                        start: start.unix(),
                        end: end.unix(),
                        filter:filter.find('input,select').serialize()
                    },
                    success: function(doc){
                        if(typeof doc == 'object'){
                            callback(doc);
                        }
                    },
                    error:function(e){
                        alert('Can not get the order data. Lost connect with your sever');
                        console.log(e);
                    }
                });
            },
            eventMouseover: function(event, element, view){
                var html = event.tooltipContent;
                $(this).popover({
                    content:html,
                    placement:'bottom',
                    container:'body',
                    html:true
                });
                $(this).popover('show');

            },
            eventMouseout:function(){
                $(this).popover('hide');
            }

        });
    }

    /**
     * Ajax Reload Old Messages
     *
     * @since 1.0
     * @author dungdt
     *
     */
    function reloadOldMessages()
    {
        var me=$('old-messages');

        if(!me.length) return;

        me.addClass('loading');
        $.ajax({
            url:wpbooking_params.ajax_url,
            data:{
                action:'wpbooking_reload_old_message',
                user_id:me.data('user-id')
            },
            dataType:'json',
            type:'post',
            success:function(res){
                me.removeClass('loading');
                if(res.html){
                    me.html(res.html);
                }
            },
            error:function(e){
                me.html(e.responseText);
            }
        })
    }
    function appendNewMessage(messageHtml)
    {
        $('.old-messages').prepend(messageHtml);
    }

    $('.wb-send-message-form').submit(function(){
        console.log(1);
        var me=$(this);
        $(this).addClass('loading');
        $(this).find('.message-box').html('');

        $.ajax({
            type:'post',
            dataType:'json',
            url:$(this).attr('action'),
            data:$(this).serialize(),
            success:function(res){
                me.removeClass('loading');
                if(res.message){
                    me.find('.message-box').html(res.message);
                }

                if(res.status){
                    // For User Dashboard Page
                    if(me.data('reload') && typeof res.messageHTML!='undefined'){
                        //reloadOldMessages();
                        appendNewMessage(res.messageHTML);
                    }

                    // Clear the Form
                    me.find('textarea').val('');
                }

            },
            error:function(e){
                me.removeClass('loading');
                me.find('.message-box').html(e.responseText);
            }
        })
    });

    $('.upload-avatar').each(function(){
        var me=$(this);
        me.find('.upload_input').change(function(){
            var formData = new FormData();
            formData.append('action','wpbooking_upload_avatar');
            formData.append('image',$(this)[0].files[0]);

            me.find('.upload-message').html('');

            $.ajax({
                type: "POST",
                url: wpbooking_params.ajax_url,
                enctype: 'multipart/form-data',
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(data) {
                    me.find('.uploaded_image_preview').remove();
                    if(data.message){
                        me.find('.upload-message').html(data.message);
                    }
                    if(data.status && data.image){
                        me.find('.image_url').val(data.image.url);
                        me.append('<img class="uploaded_image_preview" alt="" src="'+data.image.url+'">');
                    }
                },
                error:function(e){
                    if(e.responseText){
                        me.find('.upload-message').html(e.responseText);
                    }
                }
            });
        });

    });

    /**
     * I-Check
     */
    $('.wb-icheck').each(function(){
        $(this).iCheck({
            checkboxClass:$(this).data('style'),
            radioClass:$(this).data('style'),
        });

        if($(this).hasClass('disable')){
            $(this).iCheck('disable');
        }
    });

    // Loop Grid Gallery
    if(typeof $.fn.owlCarousel=='function')
    $('.service-gallery-slideshow').owlCarousel(
        {
            items:1,
            nav:true,
            navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
        }
    );

    // Form Order By Submit
    $('.wpbooking-loop-sort-by').change(function(){
       $(this).closest('form').submit();
    });

    // wpbooking-view-switch
    $('.wpbooking-view-switch a').click(function(){
        var cname='wpbooking_view_type';
        var cvalue=$(this).data('view');
        var d = new Date();
        d.setTime(d.getTime() + (365*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;

        $('.wpbooking-loop-items').removeClass('list').addClass(cvalue);
        $(this).addClass('active').siblings().removeClass('active');

        if(typeof $.fn.owlCarousel=='function')
            $('.service-gallery-slideshow').owlCarousel(
                {
                    items:1,
                    nav:true,
                    navText:['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
                }
            );

        return false;
    });

    // Add Favorite
    $('.service-fav').click(function(){
        var me=$(this);
        $.ajax({
            url:wpbooking_params.ajax_url,
            dataType:'json',
            type:'post',
            data:{
                action:'wpbooking_add_favorite',
                post_id:$(this).data('post')
            },
            success:function(res){
                if(res.status){
                    if(res.fav_status) me.addClass('active');
                    else me.removeClass('active');
                }

                if(res.message) alert(res.message);
            },
            error:function(e){
                console.log(e.responseText);
            }
        });

        return false;
    });

    //
    $('.wpbooking-search-form.is_filter_form .item-search>label').click(function(){
       $(this).closest('.item-search').toggleClass('closed');
    });
    $('.wpbooking-search-form.is_filter_form .item-search .wb-collapse').click(function(){
       $(this).closest('.item-search').toggleClass('closed');
    });


    // Lighbox gallery
    $('.service-gallery-single').each(function() {
        $(this).magnificPopup({
            delegate: 'a.hover-tag',
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });

    $('.wp-show-detail-review span').click(function(){
       $(this).closest('.wpbooking-more-review-detail').toggleClass('active');
    });

    $('.wpbooking-vote-for-review .review-do-vote').click(function(){
        var me=$(this);
        $.ajax({
            data:{
                action:'wpbooking_vote_review',
                review_id:$(this).data('review-id'),
            },
            type:'post',
            url:wpbooking_params.ajax_url,
            dataType:'json',
            success:function(res){
                if(res.status){
                    $('.review-vote-count').html(res.vote_count);
                }
                if(res.voted){
                    me.addClass('active');
                }else{
                    me.removeClass('active');
                }
            }
        })
    });

    $('.wb-btn-reply-comment').click(function(){
       var parent=$(this).closest('li');
       parent.find('ul .reply-comment-form').toggleClass('active');
    });

    // Reply
    $('.reply-submit a').click(function(){
        var me=$(this);
        var parent=me.closest('.wpbooking-add-reply');
        me.addClass('loading');
        var message=parent.find('.reply_content').val();
        if(!message) return false;

        $.ajax({
            data:{
                action:'wpbooking_write_reply',
                review_id:me.data('review-id'),
                message:message
            },
            url:wpbooking_params.ajax_url,
            dataType:'json',
            type:'post',
            success:function(res){
                me.removeClass('loading');
                if(res.status){
                    me.closest('.comment').find('.wb-btn-reply-comment').hide();
                    me.closest('ul').html(res.html);
                }
            },
            error:function(e){
                console.log(e.responseText);
                me.removeClass('loading');
            }
        })

    });

    // Cart Item
    $('.cart-item-order-form-fields-wrap .show-more-less').click(function(){
       $(this).closest('.cart-item-order-form-fields-wrap').toggleClass('active');
    });

    // Order Item
    $('.order-item-form-fields-wrap .show-more-less').click(function(){
       $(this).closest('.order-item-form-fields-wrap').toggleClass('active');
    });
});


