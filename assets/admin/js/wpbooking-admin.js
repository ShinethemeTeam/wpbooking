/**
 * Created by Dungdt on 3/15/2016.
 */
jQuery(document).ready(function( $ ){
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

                var target   = $( '[name='+ condition.check+']' );

                var targetEl = !! target.length && target.first();

                if ( ! target.length || ( ! targetEl.length && condition.value.toString() != '' ) ) {
                    return;
                }



                var v1 = targetEl.length ? targetEl.val().toString() : '';
                var v2 = condition.value.toString();
                var result;

                if(targetEl.length && targetEl.attr('type')=='radio'){
                    v1 = $( '[name='+ condition.check+']:checked').val();
                }
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



    ///////////////////////////////////
    /////// MEDIA GALLERY /////////////
    ///////////////////////////////////
    jQuery(document).ready(function($){
        $("body").on('click','.btn_remove_demo_gallery',function(){
            var container = $(this).parent();
            container.find('.fg_metadata').val('');
            container.find('.demo-image-gallery').hide();
        });

        var file_frame;
        $('body').on('click','.btn_upload_gallery',function (event) {
            var container = $(this).parent();
            console.log(container);
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
                var ids = container.find('.fg_metadata').val();
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
                console.log(images);
                images.each(function(attachment) {
                    imageIDArray.push(attachment.attributes.id);
                    imageHTML += '<img class="demo-image-gallery settings-demo-gallery" src="'+attachment.attributes.url+'">';
                });

                metadataString = imageIDArray.join(",");
                if (metadataString) {
                    $('.fg_metadata',container).val(metadataString);
                    $('.featuredgallerydiv',container).html(imageHTML).show();
                    console.log($('.featuredgallerydiv',container).html());
                }
            });
            file_frame.open();
        });
    });
    ///////////////////////////////////
    /////// MEDIA IMAGE ///////////////
    ///////////////////////////////////
    jQuery(document).ready(function($){
        //$(".btn_remove_demo_image").click(function(){
        $(document).on('click','.btn_remove_demo_image',function(){
            var container = $(this).parent();
            container.find('.demo-url-image').val('');
            container.find('.demo-image').hide();
        });
        $('.btn_upload_media').each(function(){
            $(this).click(function(e){
                var container = $(this).parent();
                var insertImage = wp.media.controller.Library.extend({
                    defaults :  _.defaults({
                        id:        'insert-image',
                        title:      'Insert Image Url',
                        allowLocalEdits: true,
                        displaySettings: true,
                        displayUserSettings: true,
                        type : 'image'
                    }, wp.media.controller.Library.prototype.defaults )
                });
                var frame = wp.media({
                    button : { text : 'Select' },
                    state : 'insert-image',
                    states : [
                        new insertImage()
                    ]
                });
                frame.on( 'select',function() {
                    var state = frame.state('insert-image');
                    var selection = state.get('selection');
                    if ( ! selection ) return;
                    selection.each(function(attachment) {
                        console.log(attachment);
                        container.find('#st_url_media').val(attachment.attributes.url);
                        container.find('#demo_img').attr("src",attachment.attributes.url).show();

                    });
                });
                frame.on('open',function() {
                    var selection = frame.state('insert-image').get('selection');
                    selection.each(function(image) {
                        var attachment = wp.media.attachment( image.attributes.id );
                        attachment.fetch();
                        selection.remove( attachment ? [ attachment ] : [] );
                    });
                });
                frame.open();
            });
        })
    });
    ///////////////////////////////////
    /////// IMAGE THUMB ///////////////
    ///////////////////////////////////
    $(document).on('keyup', '.wpbooking_image_thumb_width', function(event) {
        var container = $(this).parent();
        _save_data_image_thumb(container);
    });
    $(document).on('keyup', '.wpbooking_image_thumb_height', function(event) {
        var container = $(this).parent();
        _save_data_image_thumb(container);
    });
    $(document).on('change', '.wpbooking_image_thumb_crop', function(event) {
        var container = $(this).closest('td');
        _save_data_image_thumb(container);
    });
    function _save_data_image_thumb(container){
        var height = container.find('.wpbooking_image_thumb_height').val();
        var width = container.find('.wpbooking_image_thumb_width').val();
        if ( container.find('.wpbooking_image_thumb_crop').is(":checked"))
        {
            var crop = 'on';
        }else{
            var crop = 'off';
        }
        console.log(crop);
        var value = width+","+height+","+crop;
        console.log(value);
        container.find('.data_value').val(value);
    }

    ///////////////////////////////////
    /////// Meta Box ///////////////
    ///////////////////////////////////
    var resize;
    $(window).resize(function(event) {
        clearTimeout( resize );

        resize = setTimeout(function(){
            //if( $(window).width() < 1024 ){
            //    if( $( ".st-metabox-tabs" ).length ){
            //        $( ".st-metabox-tabs" ).tabs().removeClass( "ui-tabs-vertical ui-helper-clearfix" );
            //        $( ".st-metabox-tabs li" ).addClass( "ui-corner-top" ).removeClass( "ui-corner-left" );
            //    }
            //}else{
            //    if( $( ".st-metabox-tabs" ).length ){
            //        $( ".st-metabox-tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
            //        $( ".st-metabox-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
            //    }
            //}
        }, 500);
    }).resize();

    // move field to hndle tags
    $( '.wpbooking-hndle-tag-input').each(function(){
        var me=$(this);
        var hndle=me.closest('.postbox').find('.hndle');
        hndle.find('span').append(me.html());
        hndle.unbind( 'click.postboxes' );
        hndle.click( function( event ) {
            if ( $( event.target ).filter( 'input, option, label, select' ).length ) {
                return;
            }
            me.closest('.postbox').toggleClass( 'closed' );
        });
        me.detach();
    });


    ///////////////////////////////////
    /////// LIST ITEM /////////////////
    ///////////////////////////////////
    $( ".data_content_list_item" ).each(function(){
        $(this).sortable({
            handle: ".dashicons"
        });
        //dashicons
    });
    //$(".btn_add_new_list_item").click(function () {
    $(document).on('click','.btn_add_new_list_item',function(){
        var container = $(this).parent();
        var content_html = container.find(".content_list_item_hide").html();
        var number_list = container.find('.wpbooking_number_last_list_item').val();
        content_html = content_html.replace(/__number_list__/g, number_list);
        container.find('.data_content_list_item').append(content_html);
        container.find('.wpbooking-setting-setting-body').slideUp('fast');
        container.find('.number_list_' + number_list + ' .wpbooking-setting-setting-body').slideDown('fast');
        number_list = Number(number_list) + 1;
        container.find('.wpbooking_number_last_list_item').val(number_list);
    });
    $(document).on('click', '.btn_list_item_del', function (event) {
        var confirm_delete=confirm('Are You Want To Delete It?');
        if(!confirm_delete) return false;
        var container = $(this).parent().parent().parent();
        container.remove();
    });
    $(document).on('click', '.btn_list_item_edit', function (event) {
        var container_full = $(this).parent().parent().parent().parent();
        var container = $(this).parent().parent().parent();
        container_full.find('.wpbooking-setting-setting-body').slideUp('fast');
        $check = container.find('.wpbooking-setting-setting-body').css('display');
        if ($check == "none") {
            container.find('.wpbooking-setting-setting-body').slideDown('fast');
        } else {
            container.find('.wpbooking-setting-setting-body').slideUp('fast');
        }
    });
    $(document).on('click', '.list_item_title', function (event) {
        $(this).keyup(function () {
            var $value = $(this).val();
            var container = $(this).parent().parent().parent().parent().parent().parent().parent();
            console.log(container);
            container.find('.list-title').html($value);
        });
    });

    ///////////////////////////
    //////  Gmap    //////////
    ///////////////////////////
    
    function load_gmap(){
        if( $('.wpbooking-gmap-wrapper').length ){
            $('.wpbooking-gmap-wrapper').each(function(index, el) {
                console.log('map load ...');
                var t = $(this);
                var gmap = $('.gmap-content', t);
                var map_lat = parseFloat( $('input[name="map_lat"]', t).val() );
                var map_long = parseFloat( $('input[name="map_long"]', t).val() );

                var map_zoom = parseInt( $('input[name="map_zoom"]', t).val() );

                var bt_ot_searchbox = $('input.gmap-search', t);

                var current_marker;
                var map_options={
                    options:{
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                        mapTypeControl: true,
                        mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                        },
                        navigationControl: true,
                        scrollwheel: true,
                    },
                    //events:{
                    //    click: function(marker, event, context){
                    //        $('input[name="map_lat"]', t).val( marker.latLng.lat() );
                    //        $('input[name="map_long"]', t).val( marker.latLng.lng() );
                    //        $('input[name="map_zoom"]', t).val( marker.zoom );
                    //
                    //        $(this).gmap3({
                    //            clear: {
                    //                name:["marker"],
                    //                last: true
                    //            }
                    //        });
                    //        $(this).gmap3({
                    //            marker:{
                    //                values:[
                    //                    {latLng:marker.latLng },
                    //                ],
                    //                options:{
                    //                    draggable: false
                    //                },
                    //            }
                    //        });
                    //    }
                    //}
                };
                if(map_lat && map_long){
                    map_options.options.center=[map_lat, map_long];
                    map_options.marker={
                                        values:[
                                            {latLng:new google.maps.LatLng({lat: map_lat, lng: map_long}) },
                                        ],
                                        options:{
                                            draggable: false
                                        },
                                    };
                }
                if(map_zoom){
                    map_options.options.zoom=map_zoom;
                }

                console.log(map_options);
                gmap.gmap3({
                    map:map_options
                });

                var gmap_obj = gmap.gmap3('get');

                // Map Click
                gmap_obj.addListener('click', function(e) {

                    $('input[name="map_lat"]', t).val( e.latLng.lat() );
                    $('input[name="map_long"]', t).val( e.latLng.lng() );

                    gmap.gmap3({
                        clear: {
                            name:["marker"],
                            last: true
                        }
                    });
                    gmap.gmap3({
                        marker:{
                            values:[
                                {latLng:e.latLng },
                            ],
                            options:{
                                draggable: false
                            },
                        }
                    });
                });

                if(!map_lat || !map_long){
                     //Try to get current location
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(showPosition){
                            var gmap_obj = gmap.gmap3('get');
                            map_lat=showPosition.coords.latitude;
                            map_long=showPosition.coords.longitude;
                            gmap_obj.setCenter(new google.maps.LatLng(map_lat,map_long));
                            gmap_obj.setZoom(11);
                            $('input[name="map_lat"]', t).val( map_lat );
                            $('input[name="map_long"]', t).val( map_long );
                            $('input[name="map_zoom"]', t).val( 11);
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

                        gmap_obj.fitBounds(bounds);

                    });

                }

                google.maps.event.addListener(gmap_obj, "zoom_changed", function(event) {
                    $('input[name="map_zoom"]', t).val( gmap_obj.getZoom() );
                });

                $(window).resize(function(){
                    google.maps.event.trigger(gmap_obj, 'resize');
                });
            });
        }
    }


    $('.wpbooking_extra_service-checklist,.wpbooking_location-checklist').prev().prev().hide();



    if( $( ".st-metabox-tabs" ).length ){
        $( ".st-metabox-tabs" ).tabs({
            activate: function( event, ui ) {
                if( room_calendar ){
                    $('.wpbooking-calendar-wrapper .calendar-room').fullCalendar( 'today' );
                }
            }
        });
    }

    /////////////////////////////////
    /////// List item //////////////
    ///////////////////////////////
    $( ".wpbooking-list-item-wrapper .wpbooking-list" ).sortable({
        cursor: "move"
    });

    //$('.wpbooking-add-item').click(function(event) {
    $(document).on('click','.wpbooking-add-item',function(){
        /* Act on the event */
        if( $('#wpbooking-list-item-draft').length ){
            var content = $('#wpbooking-list-item-draft').html();

            var parent = $(this).closest('.wpbooking-list-item-wrapper');

            $('.wpbooking-list', parent).append( content );
            $( ".wpbooking-list-item-wrapper .wpbooking-list" ).sortable({
                cursor: "move"
            });
            $('.icp-auto').iconpicker();
        }
        return false;
    });

    $('.wpbooking-list-item-wrapper').on('click', '.btn_list_item_edit', function(event) {
        var parent = $(this).closest('.list-item-head');
        parent.next().stop(true, true).toggleClass('hidden');

        var list_item=$(this).closest('.wpbooking-list-item');
        if(parent.next().hasClass('hidden')){
            list_item.removeClass('active');
        }else{
            list_item.addClass('active');
        }

        //if(list_item.hasClass('active')){
        //    list_item.removeClass('active');
        //}else{
        //    wrap.find('.wpbooking-list-item').removeClass('active');
        //    list_item.addClass('active');
        //}

        event.preventDefault();
        /* Act on the event */
    });

    $('.wpbooking-list-item-wrapper').on('click', '.btn_list_item_del', function(event) {
        var parent = $(this).closest('.wpbooking-list-item');
        parent.remove();

        event.preventDefault();
        /* Act on the event */
    });

    

    $('.wpbooking-list-item-wrapper').on('keyup', '.input-title', function(event) {
        var parent = $(this).closest('.wpbooking-list-item');
        var val = $(this).val();
        $('.item-title', parent).text( val );

        event.preventDefault();
        /* Act on the event */
    });

    /////////////////////////////
    ////////// Location ////////
    ////////////////////////////

    if( $('.wpbooking-select-loction').length ){
        $('.wpbooking-select-loction').each(function(index, el) {
            var parent = $(this);
            var input = $('input[name="search"]', parent);
            var list = $('.list-location-wrapper', parent);
            var timeout;
            input.keyup(function(event) {
                clearTimeout( timeout );
                var t = $(this);
                timeout = setTimeout(function(){
                    var text = t.val();
                    if( text == ''){
                        $('.item', list).show();
                    }else{
                        $('.item', list).hide();
                        $(".item[data-name*='"+text.toLowerCase()+"']", list).show();
                    }
                    
                }, 500);
            });
        });
    }

    $(window).load(function(){
        $('.ace-editor').each(function(){
            var type=$(this).data('type');
            var input=$(this).next('textarea');
            var editor = ace.edit($(this).attr('id'));
            editor.setTheme("ace/theme/monokai");
            editor.getSession().setMode("ace/mode/css");
            editor.getSession().setValue( input.val() );

            $('#form-settings-admin').submit(function(){
                input.val(editor.getSession().getValue());
            });
        });
    });


    // ALl Booking Calendar
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
            $('.popover').remove();
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
            //$(this).popover('hide');
        }

    });

    // Accordion
    //$('.wpbooking-metabox-accordion').accordion();
    //$('.wpbooking-accordion-title').click(function(){
    $(document).on('click','.wpbooking-accordion-title',function(){
       if($(this).parent().hasClass('active')){
           $(this).parent().find('.wpbooking-metabox-accordion-content').slideUp('fast');
           $(this).parent().removeClass('active');
       }else{
           var s=$(this).parent().siblings('.wpbooking-metabox-accordion');
           s.find('.wpbooking-metabox-accordion-content').slideUp('fast');
           s.removeClass('active');
           $(this).parent().find('.wpbooking-metabox-accordion-content').slideDown('fast');
           $(this).parent().addClass('active');
            console.log(1);
           $(window).trigger('resize');
       }
    });

    $(document).on('keyup', '.wpbooking_image_thumb_height', function(event) {
        var container = $(this).parent();
        _save_data_image_thumb(container);
    });
    // On-off
    $(document).on('click', '.wpbooking-switch', function(event) {
        $(this).toggleClass("switchOn",function(){

        });
        var checkbox=$(this).closest('.wpbooking-switch-wrap').find('.checkbox');

        if($(this).hasClass('switchOn')){
            checkbox.val('on');
        }else{
            checkbox.val('off');
        }
    });

    //Popover
    $('.wb-help-popover').popover({
        //container:'body',
        template:'<div class="popover wb-help-popover-el" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    });

    // Next, Prev Button
    //$('.wb-prev-section').click(function(){
    $(document).on('click','.wb-prev-section',function(){
        var h=$('#st_post_metabox').offset().top;
        $('.st-metabox-nav li.ui-state-active').prev().find('a').trigger('click');
        $('html,body').animate({'scrollTop':parseInt(h)-200});
        return false;
    });
    //$('.wb-next-section').click(function(){
    $(document).on('click','.wb-next-section',function(){
        var next_a,section;
        next_a=$('.st-metabox-nav li.ui-state-active').next().find('a');
        section=$(this).closest('.st-metabox-tabs-content');
        saveMetaboxSection(section,$(this),function(){
            next_a.trigger('click');
            var h=$('#st_post_metabox').offset().top;
            $('html,body').animate({'scrollTop':parseInt(h)-200});
        });

        return false;
    });

    function saveMetaboxSection(section,button,success_callback){

        if(section.hasClass('active')) return;
        section.addClass('loading');

        var data=section.find('input,select,textarea').serialize();

        data+='&action=wpbooking_save_metabox_section';

        $.ajax({
            url:wpbooking_params.ajax_url,
            data:data,
            dataType:'json',
            type:'post',
            success:function(res){
                if(res.status){
                    success_callback(res);
                }

                if(res.message){
                    alert(message);
                }

                section.removeClass('loading');
            },
            error:function(e){
                alert('Can not save section data, please reload page and try again');
                console.log(e.responseText);
                section.removeClass('loading');
            }
        })
    }

    // Ajax Create Term
   // $('.wb-btn-add-term').click(function(){
    $(document).on('click','.wb-btn-add-term',function(){

        var parent=$(this).parent();
        var me=$(this);
        var list_terms=$(this).closest('.st-metabox-right').find('.list-terms-checkbox');
        var term_name=parent.find('.term-name').val();
        var tax_name=me.data('tax');
        console.log(term_name);
        if(!term_name) return false;

        parent.addClass('loading');
        $.ajax({
            url:wpbooking_params.ajax_url,
            type:'post',
            dataType:'json',
            data:{
                action:'wpbooking_add_term',
                term_name:term_name,
                taxonomy:tax_name,
                other_data:parent.find('input').serialize()
            },
            success:function(res){
                parent.removeClass('loading');
                if(res.status){
                    if(res.data.term_id && res.data.name){
                        var input_name=me.data('name');

                        // Icon
                        var extra_html='';
                        if(typeof res.extra_fields!='undefined' && typeof res.extra_fields.icon !='undefined'){
                            extra_html+='<span class="icon"><i class="'+res.extra_fields.icon+'"></i></span>';
                        }

                        list_terms.append('<div class="term-checkbox"><label><input type="checkbox" name="'+input_name+'['+tax_name+'][]" value="'+res.data.term_id+'">'+extra_html+'<span>'+res.data.name+'</span></label></div>')
                    }
                    parent.find('.term-name').val('');
                }
                if(res.message){
                    alert(res.message);
                }
            },
            error:function(e){
                parent.removeClass('loading');
                list_terms.append(e.responseText);
            }
        })
    });


    // Add Extra Services
    //$('.wb-btn-add-extra-service').click(function(){
    $(document).on('click','.wb-btn-add-extra-service',function(){
        var parent=$(this).parent();
        var wrap=$(this).closest('.add-new-extra-service');
        var me=$(this);
        var list_terms=$(this).closest('.st-metabox-right').find('.list-extra-services');
        var term_name=parent.find('.service-name ').val();
        var service_type=me.data('type');
        var id=me.data('id');
        if(!term_name) return false;

        parent.addClass('loading');
        $.ajax({
            url:wpbooking_params.ajax_url,
            type:'post',
            dataType:'json',
            data:{
                action:'wpbooking_add_extra_service',
                service_name:term_name,
                service_type:service_type
            },
            success:function(res){
                parent.removeClass('loading');
                if(res.status){

                    var input_name=me.data('name');
                    var html=wrap.find('.extra-item-default .extra-item').clone();
                    var count=list_terms.find('.extra-item').length;
                    html.find('.title input').attr('name',id+'['+service_type+']'+'['+(count)+'][is_selected]');
                    html.find('.title input').val(term_name);
                    html.find('.extra-item-name').html(term_name);
                    html.find('.money-number input').attr('name',id+'['+service_type+']'+'['+(count)+'][money]');
                    html.find('.require-options select').attr('name',id+'['+service_type+']'+'['+(count)+'][require]');

                    list_terms.append(html);

                    parent.find('.service-name').val('');
                }
                if(res.message){
                    alert(res.message);
                }
            },
            error:function(e){
                parent.removeClass('loading');
                list_terms.append(e.responseText);
            }
        })
    });

    // Icon picker
    $('.icp-auto').iconpicker();

    /**
     * I-Check
     */
    $('.wb-icheck').each(function(){
        $(this).iCheck({
            checkboxClass:$(this).data('style'),
            radioClass:$(this).data('style'),
        });
    });

    // Show More Less
    //$('.cart-item-order-form-fields-wrap .show-more-less').click(function(){
    $(document).on('click','.cart-item-order-form-fields-wrap .show-more-less',function(){
        $(this).parent().toggleClass('active');
    })


    // Datepicker Field
    $('.wb-date').datepicker();

    $(window).load(function(){
        // Auto Complete Post Type
        $('.wb-autocomplete').each(function() {
                var me = $(this);
                me.select2({
                    ajax: {
                        url: wpbooking_params.ajax_url,
                        dataType: 'json',
                        type:'post',
                        data: function (params) {
                            return {
                                q: params.term, // search term
                                action: 'wpbooking_autocomplete_post',
                                page: params.page,
                                type:me.data('type')
                            };
                        },
                        processResults: function (data, params) {
                            // parse the results into the format expected by Select2
                            // since we are using custom formatting functions we do not need to
                            // alter the remote JSON data, except to indicate that infinite
                            // scrolling can be used
                            params.page = params.page || 1;

                            return {
                                results: data
                            };
                        },
                        cache: true
                    },
                    minimumInputLength:3,
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    templateResult:  function (post) {
                        if(post.loading) return post.text;
                        var markup = "<div class='select2-result-repository clearfix'>" +
                            "<div class='select2-result-repository__avatar'>"+post.thumb+"</div>" +
                            "<div class='select2-result-repository__meta'>" +
                            "<div class='select2-result-repository__title'>" + post.text + "</div>";

                        if (post.address) {
                            markup += "<div class='select2-result-repository__description'>" + post.address + "</div>";
                        }
                        markup+='</div>';

                        return markup;
                    },
                    templateSelection:function(post){
                        return post.text;
                    }
                });

            });

    });

    $('.wpbooking-metabox-template').on('wpbooking_change_service_type_metabox',function(){
        var me=$(this);
        me.find('.wpbooking-tabs').tabs();

        //Popover
        $('.wb-help-popover').popover({
            //container:'body',
            template:'<div class="popover wb-help-popover-el" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
        });

        load_gmap();

        $('.icp-auto').iconpicker();

        // Load Condition
        $('.wpbooking-form-group').on( 'change.conditionals', condition_object, function(e) {
            run_condition_engine();
        });


    });

    $('[name=service_type]').change(function(){
        var v=$(this).val();
        var metabox_template=$('.wpbooking-metabox-template');
        var t=$('#tmpl-wpbooking-metabox-'+v).html();
        metabox_template.html(t);
        if(t===undefined) $('.wpbooking-metabox-template').html('');

        metabox_template.trigger('wpbooking_change_service_type_metabox');


        var container = $(this).closest('.list-radio');
        container.find('[name=service_type]').closest('.wb-radio-button').removeClass('active');
        $(this).closest('.wb-radio-button').addClass('active');

    });
    $('[name=service_type][checked=checked]').trigger('change');


    $(document).on('click','.open_section_metabox',function(){
        $('.open_section_metabox').removeClass('active');
        $(this).addClass("active");
    });

    $(document).on('mouseenter', '.phone_country_number .input-group-addon', function() {
        $(this).find('.list_phone_country_number').show();
    });
    $(document).on('mouseleave', '.phone_country_number .input-group-addon', function() {
        var $this = $(this);
        $this.find('.list_phone_country_number').hide();
    });
    $(document).on('click','.phone_country_number .list_phone_country_number li',function(){
        var $value = $(this).data('code');
        var $country = $(this).data('country');
        var $container = $(this).closest('.phone_country_number');
        $container.find('.phone_code').val($value);
        $container.find('.demo-flag').attr("class","demo-flag flag-icon flag-icon-"+$country);
        $container.find('.list_phone_country_number').hide();

    });
    $(document).on('click','.radio_pro input',function(){
        var $container = $(this).closest('.st-metabox-content-wrapper');
        $container.find('.radio_pro').removeClass('checked');
        $(this).closest('.radio_pro').addClass('checked');
    // Report dropdown
    });
    $(document).on('click','.wb-repeat-dropdown-add',function(){
        var parent=$(this).closest('.form-group');
        var item=parent.find('.default-item').html();
        parent.find('.add-more-box').append('<div class="more-item">'+item+'<span class="wb-repeat-dropdown-remove"><i class="fa fa-trash"></i> '+wpbooking_params.delete_string+'</span></div>');

    });
    $(document).on('click','.wb-repeat-dropdown-remove',function(){
        $(this).closest('.more-item').remove();

    });
    $(document).on('change','.taxonomy_room_select .item_all',function(){
        var container = $(this).closest('.wpbooking-row');
        if ($(this).is(":checked"))
        {
            container.find('.item_base').prop('checked', true);
            container.find('.item_custom').prop('checked', false);
        }else{
            container.find('.item_base').prop('checked', false);
            container.find('.item_custom').prop('checked', false);
        }
        container.find('.item_post').prop('checked', false);
        container.find('.list_post').hide();
    });
    $(document).on('change','.taxonomy_room_select .item_custom',function(){
        var container = $(this).closest('.wpbooking-row');
        if ($(this).is(":checked"))
        {
            container.find('.list_post').show();
            container.find('.item_base').prop('checked', false);
            container.find('.item_all').prop('checked', false);
        }else{
            container.find('.list_post').hide();
            container.find('.item_base').prop('checked', false);
        }
    });
    $(document).on('change','.taxonomy_room_select .item_post',function(){
        var container = $(this).closest('.wpbooking-row');
        var check = false;
        $(this).each(function(){
            if ($(this).is(":checked"))
            {
                check = true;
            }
        });
        if (check == true)
        {
            container.find('.item_base').prop('checked', true);
        }else{
            container.find('.item_base').prop('checked', false);
        }
    });

    $(document).on('change','.taxonomy_fee_select .term-checkbox',function(){
        if($(this).attr('checked')){
            $(this).closest('.term-item').addClass('active');
        }else{
            $(this).closest('.term-item').removeClass('active');
        }
    });

});