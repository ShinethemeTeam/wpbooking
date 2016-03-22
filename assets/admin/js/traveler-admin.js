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
            console.log(conditions);
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
        $(".btn_remove_demo_gallery").click(function(){
            var container = $(this).parent();
            container.find('.fg_metadata').val('');
            container.find('.demo-image-gallery').hide();
        });
        $('.btn_upload_gallery').each(function(event) {
            var file_frame;
            $(this).click(function (event) {
                var container = $(this).parent();
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
                    var ids = container.find('#fg_metadata').val();
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
                        imageHTML += '<img id="'+attachment.attributes.id+'" class="demo-image-gallery settings-demo-gallery" src="'+attachment.attributes.url+'">';
                    });
                    metadataString = imageIDArray.join(",");
                    if (metadataString) {
                        container.find('#fg_metadata').val(metadataString);
                        container.find('.featuredgallerydiv').html(imageHTML);
                    }
                });
                file_frame.open();
            })
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
        container.find('.traveler-setting-setting-body').slideUp(500);
        container.find('.number_list_' + number_list + ' .traveler-setting-setting-body').slideDown(500);
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
        container_full.find('.traveler-setting-setting-body').slideUp(500);
        $check = container.find('.traveler-setting-setting-body').css('display');
        if ($check == "none") {
            container.find('.traveler-setting-setting-body').slideDown(500);
        } else {
            container.find('.traveler-setting-setting-body').slideUp(500);
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

});