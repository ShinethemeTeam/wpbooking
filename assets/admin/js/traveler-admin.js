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
    $('.traveler-form-group').on( 'change.conditionals', condition_object, function(e) {
        run_condition_engine();
    });
    run_condition_engine();
    function run_condition_engine(){
        $('.traveler-condition[data-condition]').each(function() {
            
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
        $(".btn_remove_demo_image").click(function(){
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
    $(document).on('keyup', '.traveler_booking_image_thumb_width', function(event) {
        var container = $(this).parent();
        _save_data_image_thumb(container);
    });
    $(document).on('keyup', '.traveler_booking_image_thumb_height', function(event) {
        var container = $(this).parent();
        _save_data_image_thumb(container);
    });
    $(document).on('change', '.traveler_booking_image_thumb_crop', function(event) {
        var container = $(this).parent();
        _save_data_image_thumb(container);
    });
    function _save_data_image_thumb(container){
        var height = container.find('.traveler_booking_image_thumb_height').val();
        var width = container.find('.traveler_booking_image_thumb_width').val();
        if ( container.find('.traveler_booking_image_thumb_crop').is(":checked"))
        {
            var crop = 'on';
        }else{
            var crop = 'off';
        }
        var value = width+","+height+","+crop
        container.find('.data_value').val(value);
    }

    ///////////////////////////////////
    /////// Meta Box ///////////////
    ///////////////////////////////////
    var resize;
    $(window).resize(function(event) {
        clearTimeout( resize );

        resize = setTimeout(function(){
            if( $(window).width() < 1024 ){
                if( $( ".st-metabox-tabs" ).length ){
                    $( ".st-metabox-tabs" ).tabs({active: 0}).removeClass( "ui-tabs-vertical ui-helper-clearfix" );
                    $( ".st-metabox-tabs li" ).addClass( "ui-corner-top" ).removeClass( "ui-corner-left" );
                }
            }else{
                if( $( ".st-metabox-tabs" ).length ){
                    $( ".st-metabox-tabs" ).tabs({active: 0}).addClass( "ui-tabs-vertical ui-helper-clearfix" );
                    $( ".st-metabox-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
                }
            }
        }, 500);
    }).resize();

    // move field to hndle tags
    $( '.traveler-hndle-tag-input').each(function(){
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
    $(".btn_add_new_list_item").click(function () {
        var container = $(this).parent();
        var content_html = container.find(".content_list_item_hide").html();
        var number_list = container.find('.traveler_booking_number_last_list_item').val();
        content_html = content_html.replace(/__number_list__/g, number_list);
        container.find('.data_content_list_item').append(content_html);
        container.find('.traveler-setting-setting-body').slideUp('fast');
        container.find('.number_list_' + number_list + ' .traveler-setting-setting-body').slideDown('fast');
        number_list = Number(number_list) + 1;
        container.find('.traveler_booking_number_last_list_item').val(number_list);
    });
    $(document).on('click', '.btn_list_item_del', function (event) {
        var container = $(this).parent().parent().parent();
        container.remove();
    });
    $(document).on('click', '.btn_list_item_edit', function (event) {
        var container_full = $(this).parent().parent().parent().parent();
        var container = $(this).parent().parent().parent();
        container_full.find('.traveler-setting-setting-body').slideUp('fast');
        $check = container.find('.traveler-setting-setting-body').css('display');
        if ($check == "none") {
            container.find('.traveler-setting-setting-body').slideDown('fast');
        } else {
            container.find('.traveler-setting-setting-body').slideUp('fast');
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
        if( $('.st-metabox-content-wrapper').length ){
            $('.st-metabox-content-wrapper').each(function(index, el) {
                var t = $(this);
                var gmap = $('.gmap-content', t);
                var map_lat = parseFloat( $('input[name="map_lat"]', t).val() );
                var map_long = parseFloat( $('input[name="map_long"]', t).val() );

                var map_zoom = parseInt( $('input[name="map_zoom"]', t).val() );

                var bt_ot_searchbox = $('input.gmap-search', t);

                var current_marker;

                gmap.gmap3({
                    map:{
                        options:{
                            center:[map_lat, map_long],
                            zoom:map_zoom,
                            mapTypeId: google.maps.MapTypeId.ROADMAP,
                            mapTypeControl: true,
                            mapTypeControlOptions: {
                                style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                            },
                            navigationControl: true,
                            scrollwheel: true,
                            streetViewControl: true
                        },
                        events:{
                            click: function(map){
                            }
                        }
                    }

                });



                var gmap_obj = gmap.gmap3('get');
                var geocoder = new google.maps.Geocoder;
                var map_type = "roadmap";

                current_marker = new google.maps.Marker({
                    position : new google.maps.LatLng( map_lat, map_long ),
                    map : gmap_obj
                });

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

                            if(i==0){
                                current_marker.setPosition(place.geometry.location);

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
            });
        }
    }

    load_gmap();

     $( ".st-metabox-tabs" ).tabs({
        activate: function( event, ui ) {
            setTimeout(function(){
                load_gmap();
            }, 500);
        }
     });

    /////////////////////////////////
    /////// List item //////////////
    ///////////////////////////////
    $( ".traveler-list-item-wrapper .traveler-list" ).sortable({
        cursor: "move"
    });

    $('.traveler-add-item').click(function(event) {
        /* Act on the event */
        if( $('#traveler-list-item-draft').length ){
            var content = $('#traveler-list-item-draft').html();

            var parent = $(this).closest('.traveler-list-item-wrapper');

            $('.traveler-list', parent).append( content );
            $( ".traveler-list-item-wrapper .traveler-list" ).sortable({
                cursor: "move"
            });
        }
        return false;
    });

    $('.traveler-list-item-wrapper').on('click', '.btn_list_item_edit', function(event) {
        var parent = $(this).closest('.list-item-head');
        parent.next().stop(true, true).toggleClass('hidden');

        event.preventDefault();
        /* Act on the event */
    });

    $('.traveler-list-item-wrapper').on('click', '.btn_list_item_del', function(event) {
        var parent = $(this).closest('.traveler-list-item');
        parent.remove();

        event.preventDefault();
        /* Act on the event */
    });

    

    $('.traveler-list-item-wrapper').on('keyup', '.input-title', function(event) {
        var parent = $(this).closest('.traveler-list-item');
        var val = $(this).val();
        $('.item-title', parent).text( val );

        event.preventDefault();
        /* Act on the event */
    });

    /////////////////////////////
    ////////// Location ////////
    ////////////////////////////

    if( $('.traveler-select-loction').length ){
        $('.traveler-select-loction').each(function(index, el) {
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
                        $(".item[data-name*='"+text+"']", list).show();
                    }
                    
                }, 500);
            });
        });
    }

});