/**
 * Created by Dungdt on 3/30/2016.
 */
;(function ($) {
    $.fn.wpbookingSendAjax = function () {
        this.each(function () {
            var form = $(this);
            form.find('[name]').removeClass('input-error');
            var me = $('.submit-button', this);
            me.addClass('loading').removeClass('error');
            form.find('.wpbooking-message').remove();
            data = form.serialize();

            $.ajax({
                url     : wpbooking_params.ajax_url,
                data    : data,
                dataType: 'json',
                type    : 'post',
                success : function (res) {
                    if (typeof grecaptcha != 'undefined')
                        grecaptcha.reset();
                    if (res.status) {
                        me.addClass('success');
                    } else {
                        me.addClass('error');
                    }

                    if (res.message) {
                        form.find('.wpbooking-message').remove();
                        var message = $('<div/>');
                        message.addClass('wpbooking-message');
                        message.html(res.message);
                        me.after(message);
                    }
                    if (typeof res.data != 'undefined' && typeof res.data.redirect != 'undefined' && res.data.redirect) {
                        window.location.href = res.data.redirect;
                    }
                    if (res.redirect) {
                        window.location.href = res.redirect;
                    }

                    if (typeof res.error_fields != 'undefined') {
                        for (var k in res.error_fields) {
                            form.find("[name='" + k + "']").addClass('input-error');
                        }
                    }
                    if (typeof res.data != 'undefined' && typeof res.data.error_fields != 'undefined') {
                        for (var k in res.data.error_fields) {
                            form.find("[name='" + k + "']").addClass('input-error');
                        }
                    }
                    me.removeClass('loading');
                },
                error   : function (e) {
                    if (typeof grecaptcha != 'undefined')
                        grecaptcha.reset();
                    me.removeClass('loading').addClass('error');
                    var message = $('<div/>');
                    message.addClass('wpbooking-message');
                    message.html(e.responseText);
                    me.after(message);
                }
            })
        });
    };
})(jQuery);

jQuery(document).ready(function ($) {

    // Single Services
    // Helper functions
    function getFormData(form) {
        var data  = [];
        var data1 = form.serializeArray();
        for (var i = 0; i < data1.length; i++) {
            data.push({
                name : data1[i].name,
                value: data1[i].value
            });
        }
        var dataobj = {};
        for (var i = 0; i < data.length; ++i) {
            dataobj[data[i].name] = data[i].value;
        }

        return dataobj;
    };

    setTimeout(function () {
        $('.form-search-room input').trigger('change');
    }, 500);
    $(document).on('change', '.form-search-room input,.form-search-room select,.form-search-room textarea', function () {
        var searchbox      = $(this).closest('.form-search-room');
        var data_form_book = searchbox.find('input,select,textarea').serializeArray();
        for (var i = 0; i < data_form_book.length; i++) {
            $('.search-room-availablity').find('.form_book_' + data_form_book[i].name).val(data_form_book[i].value);
        }
    });
    // Order Form
    $('.wpbooking_order_form .submit-button').click(function () {
        var container = $(this).closest('.search-room-availablity');
        var form      = $(this).closest('.wpbooking_order_form');
        var me        = $(this);
        me.addClass('loading').removeClass('error');
        container.find('.search_room_alert').html('');
        me.addClass('loading');
        var data = form.serialize();
        $.ajax({
            url     : wpbooking_params.ajax_url,
            data    : data,
            dataType: 'json',
            type    : 'post',
            success : function (res) {
                if (typeof grecaptcha != 'undefined')
                    grecaptcha.reset();
                if (res.status) {
                    me.addClass('success');
                    me.hide();
                    me.parent().find('.wb-btn-to-checkout').show();
                    if (res.redirect) {
                        window.location = res.redirect;
                    }

                } else {
                    me.addClass('error');
                }
                if (res.message) {
                    container.find('.search_room_alert').html('');
                    container.find('.search_room_alert').html(res.message);
                }
                if (typeof  res.data != 'undefined' && res.data.redirect) {
                    window.location = res.data.redirect;
                }

                if (typeof res.error_fields != 'undefined') {
                    for (var k in res.error_fields) {

                        form.find("[name='" + k + "']").addClass('input-error');
                    }
                }
                if (typeof  res.updated_content != 'undefined') {

                    for (var k in res.updated_content) {
                        var element = $(k);
                        element.replaceWith(res.updated_content[k]);
                        $(window).trigger('wpbooking_event_cart_update_content', [k, res.updated_content[k]]);
                    }
                }

                me.removeClass('loading');
            },
            error   : function (e) {
                if (typeof grecaptcha != 'undefined')
                    grecaptcha.reset();
                container.find('.search_room_alert').html('');
                container.find('.search_room_alert').html(e.responseText);
                me.removeClass('loading').addClass('error');
            }
        })
    });

    $('.wpbooking_checkout_form input[name=term_condition]').change(function () {
        if ($(this).is(':checked')) {
            $('.wpbooking_checkout_form .submit-button').removeAttr('disabled');
        } else {
            $('.wpbooking_checkout_form .submit-button').attr('disabled', 'disabled');
        }
    });
    // Checkout Form
    $('.wpbooking_checkout_form .submit-button').click(function () {
        var form = $(this).closest('form');
        if ($('input[name^="passengers"]', form).length) {
            var validate = true;
            $('input[name^="passengers"].required', form).removeClass('input-error');
            $('input[name^="passengers"].required', form).each(function () {
                var val = $(this).val();
                if (val == '') {
                    $(this).addClass('input-error');
                    validate = false;
                }
            });
            if (!validate) {
                return false;
            }
        }
        form.trigger('wpbooking_before_checkout');

        var payment       = $('input[name="payment_gateway"]:checked', form).val();
        var wait_validate = $('input[name="wait_validate_' + payment + '"]', form).val();
        if (wait_validate === 'wait') {
            form.trigger('wpbooking_wait_checkout');
            return false;
        }

        form.wpbookingSendAjax();

    });

    // Coupon Apply
    function do_apply_coupon(me) {
        me.find('.wb-message').html('');
        if (me.hasClass('loading')) return false;
        if (!me.find('.form-control').val()) return false;

        me.addClass('loading');
        $.ajax({
            dataType: 'json',
            type    : 'post',
            url     : wpbooking_params.ajax_url,
            data    : {
                action: 'wpbooking_apply_coupon',
                coupon: me.find('.form-control').val()
            },
            success : function (res) {
                me.removeClass('loading');
                if (res.message != undefined) {
                    me.find('.wb-message').html(res.message);
                }
                if (typeof  res.updated_content != 'undefined') {

                    for (var k in res.updated_content) {
                        var element = $(k);
                        element.replaceWith(res.updated_content[k]);
                        $(window).trigger('wpbooking_event_cart_update_content', [k, res.updated_content[k]]);
                    }
                }
            },
            error   : function (e) {
                me.removeClass('loading');
                console.log(e.responseText);
                me.find('.wb-message').html(e.responseText);
            }
        });
    }

    $('.wpbooking-coupon-form .wb-coupon-code').keyup(function (event) {
        if (event.keyCode == '13') {
            var me = $(this).parent();
            do_apply_coupon(me);
        }
        return false;
    });

    $('.wpbooking-coupon-form .wb-coupon-apply').click(function () {
        var me = $(this).parent();
        do_apply_coupon(me);
    });

    // Remove the Coupon
    $(document).on('click', '.wpbooking-remove-coupon', function () {
        var me = $(this).parent();
        me.find('.wb-message').html('');
        if (me.hasClass('loading')) return false;

        me.addClass('loading');
        $.ajax({
            dataType: 'json',
            type    : 'post',
            url     : wpbooking_params.ajax_url,
            data    : {
                action: 'wpbooking_remove_coupon',
            },
            success : function (res) {
                me.removeClass('loading');
                if (res.message != undefined) {
                    me.find('.wb-message').html(res.message);
                }
                if (typeof  res.updated_content != 'undefined') {

                    for (var k in res.updated_content) {
                        var element = $(k);
                        element.replaceWith(res.updated_content[k]);
                        $(window).trigger('wpbooking_event_cart_update_content', [k, res.updated_content[k]]);
                    }
                }
            },
            error   : function (e) {
                me.removeClass('loading');
                console.log(e.responseText);
                me.find('.wb-message').html(e.responseText);
            }
        });
    });


    //////////////////////////////////
    /////////// Google Gmap //////////
    //////////////////////////////////

    function single_map() {
        $('.service-map-element').each(function () {
            var map_lat  = $(this).data('lat');
            var map_lng  = $(this).data('lng');
            var map_zoom = $(this).data('zoom');
            $(this).gmap3({
                map   : {
                    options: {
                        center: [map_lat, map_lng],
                        zoom  : map_zoom
                    }
                },
                marker: {
                    values : [
                        {latLng: [map_lat, map_lng]},
                    ],
                    options: {
                        draggable: false
                    }
                }
            });
        });
    }

    // Gateway Items
    setTimeout(function () {
        $('.wpbooking-gateway-item [name=payment_gateway]:checked').trigger('change');
    }, 500);
    $('.wpbooking-gateway-item [name=payment_gateway]').change(function () {
        var parent = $(this).closest('.wpbooking-gateway-item');
        if (!parent.hasClass('active')) {
            parent.siblings().removeClass('active');
            parent.addClass('active');
        }

        var name = $(this).val();
        if (!$('.wpbooking-gateway-item .gateway-desc.gateway-id-' + name).hasClass('active')) {
            $('.wpbooking-gateway-item .gateway-desc').removeClass('active');
            $('.wpbooking-gateway-item .gateway-desc.gateway-id-' + name).addClass('active');
        }
    });


    $('.item-search .wb-checkbox-search').on('change', function () {

        var list      = "";
        var container = $(this).closest('.list-checkbox');
        container.find(".wb-checkbox-search").each(function () {
            if ($(this).attr('checked')) {
                list += $(this).val() + ',';
            }
        });
        container.find('.data_taxonomy').val(list.substring(0, list.length - 1));

    });

    var has_date_picker = $('.has-date-picker');
    has_date_picker.datepicker()
        .datepicker('widget');
    if ($('.wpbooking-date-start', 'body').length) {
        $('.wpbooking-date-start', 'body').blur();
    }
    if ($('.wpbooking-date-end', 'body').length) {
        $('.wpbooking-date-end', 'body').blur();
    }
    $('.wpbooking-search-form-wrap').each(function () {
        var check_in     = $(this).find('.wpbooking-date-start');
        var check_out    = $(this).find('.wpbooking-date-end');
        var check_in_out = $(this).find('.wpbooking-check-in-out');
        var date_group   = $(this).find('.date-group');
        var customClass  = check_in_out.data('custom-class');
        if (check_in_out.length) {
            check_in_out.daterangepicker({
                    singleDatePicker: false,
                    autoApply       : true,
                    disabledPast    : true,
                    dateFormat      : wpbooking_params.dateformat,
                    customClass     : customClass
                },
                function (start, end, label) {
                    $('.checkin_d', date_group).val(start.format('DD'));
                    $('.checkin_m', date_group).val(start.format('MM'));
                    $('.checkin_y', date_group).val(start.format('YYYY'));
                    check_in.val(start.format(wpbooking_params.dateformat)).trigger('change');

                    $('.checkout_d', date_group).val(end.format('DD'));
                    $('.checkout_m', date_group).val(end.format('MM'));
                    $('.checkout_y', date_group).val(end.format('YYYY'));
                    check_out.val(end.format(wpbooking_params.dateformat)).trigger('change');
                    check_in_out.trigger('daterangepicker_change', [start, end]);
                });
            check_in.focus(function (e) {
                check_in_out.trigger('click');
                this.blur();
            });

            check_out.focus(function (e) {
                check_in_out.trigger('click');
                this.blur();
            });
        }
    });
    $(window).on('resize', function () {
        var single_calendar = false;
        if (window.matchMedia("(max-width: 767px)").matches) {
            single_calendar = true;
        }
        if ($('.departure-date-group', 'body').length) {
            $('.departure-date-group', 'body').each(function () {
                var t            = $(this);
                var check_in_out = $('.wpbooking-check-in-out', t);
                var check_in     = $('.wpbooking-date-start', t);
                var check_out    = $('.wpbooking-date-end', t);
                var customClass  = check_in_out.data('custom-class');
                var start_date   = $(this).data('start-month');
                check_in_out.daterangepicker({
                        singleDatePicker : single_calendar,
                        autoApply        : true,
                        disabledPast     : true,
                        dateFormat       : wpbooking_params.dateformat,
                        customClass      : customClass,
                        singleDay        : true,
                        sameDate         : true,
                        hideOldMonth     : true,
                        classNotAvailable: ['disabled'],
                        enableLoading    : true,
                        minDate          : start_date,
                        fetchEvents      : function (start, end, el, callback) {
                            var events = [];
                            if (el.flag_get_events) {
                                return false;
                            }
                            el.flag_get_events = true;
                            el.container.find('.overlay-load').removeClass('hidden');
                            var data = {
                                action  : 'wpbooking_get_availability_tour',
                                start   : start.format('YYYY-MM-DD'),
                                end     : end.format('YYYY-MM-DD'),
                                post_id : t.data('post_id'),
                                security: wpbooking_params.wpbooking_security
                            };
                            el.container.find('.overlay-load').removeClass('hidden');
                            $.post(wpbooking_params.ajax_url, data, function (respon) {
                                if (typeof respon === 'object') {
                                    if (typeof respon.events === 'object') {
                                        events = respon.events;
                                    }
                                } else {
                                    console.log('Can not get data');
                                }
                                callback(events, el);
                                el.flag_get_events = false;
                                el.container.find('.overlay-load').addClass('hidden');
                            }, 'json');
                        }
                    },
                    function (start, end, label) {
                        $('.checkin_d', t).val(start.format('DD'));
                        $('.checkin_m', t).val(start.format('MM'));
                        $('.checkin_y', t).val(start.format('YYYY'));
                        check_in.val(start.format(wpbooking_params.dateformat)).trigger('change');

                        $('.checkout_d', t).val(end.format('DD'));
                        $('.checkout_m', t).val(end.format('MM'));
                        $('.checkout_y', t).val(end.format('YYYY'));
                        check_out.val(end.format(wpbooking_params.dateformat)).trigger('change');
                        check_in_out.trigger('daterangepicker_change', [start, end]);
                    });
                check_in.focus(function () {
                    check_in_out.trigger('click');
                    this.blur();
                });

                check_out.focus(function () {
                    check_in_out.trigger('click');
                    this.blur();
                });
            });
        }
    }).resize();

    $('.wpbooking-select2').select2();

    $('.search-room-availablity').each(function () {
        var t            = $(this);
        var check_in     = $('.wpbooking-search-start', t),
            check_out    = $('.wpbooking-search-end', t),
            check_in_out = $('.wpbooking-check-in-out', t);

        check_in_out.daterangepicker({
                singleDatePicker: false,
                autoApply       : true,
                disabledPast    : true,
                dateFormat      : wpbooking_params.dateformat
            },
            function (start, end, label) {
                $('.checkin_d', t).val(start.format('DD'));
                $('.checkin_m', t).val(start.format('MM'));
                $('.checkin_y', t).val(start.format('YYYY'));
                $('.wpbooking-search-start', t).val(start.format(wpbooking_params.dateformat));

                $('.checkout_d', t).val(end.format('DD'));
                $('.checkout_m', t).val(end.format('MM'));
                $('.checkout_y', t).val(end.format('YYYY'));
                $('.wpbooking-search-end', t).val(end.format(wpbooking_params.dateformat));

            });
        check_in.focus(function (e) {
            check_in_out.trigger('click');
            this.blur();
        });

        check_out.focus(function (e) {
            check_in_out.trigger('click');
            this.blur();
        });
    });


    setTimeout(function () {
        if ($('.search-room-availablity .wpbooking-search-start').val() != '' && $('.search-room-availablity .wpbooking-search-end').val() != '') {
            $('.btn-do-search-room').click();
        }
    }, 500);
    $(document).on('click', '.button_show_price.is_single_search_result', function () {
        $('.btn-do-search-room').click();
    });
    $('.form-search-room .btn-do-search-room').click(function () {
        var searchbox = $(this).closest('.form-search-room');
        searchbox.find('.wpbooking_paged').val('1');
        do_search_room(searchbox, true);
    });
    $(document).on('click', '.content-search-room .btn_extra', function () {
        var parent = $(this).closest('.loop-room');
        var $extra = parent.find('.more-extra');
        if ($extra.hasClass('active')) {
            $extra.removeClass('active');
            $extra.slideUp();
        } else {
            $extra.addClass('active');
            $extra.slideDown();
        }
        return false;
    });
    $(document).on('click', '.wb-tour-form-wrap .btn_extra', function () {
        var parent = $(this).closest('.wb-tour-form-wrap');
        var $extra = parent.find('.more-extra');
        if ($extra.hasClass('active')) {
            $extra.removeClass('active');
            $extra.slideUp();
        } else {
            $extra.addClass('active');
            $extra.slideDown();
        }
        return false;
    });
    $(document).on('click', '.search-room-availablity .pagination-room a', function () {
        var parent = $(this).closest('.search-room-availablity');
        var paged  = $(this).data('page');
        parent.find('.form-search-room .wpbooking_paged').val(paged);

        var searchbox = parent.find('.form-search-room');
        do_search_room(searchbox, false);
    });

    /**
     * Do Search Room
     * @param searchbox
     * @param is_validate
     * @returns {boolean}
     */
    function do_search_room(searchbox, is_validate) {
        var parent = searchbox.closest('.search-room-availablity');
        var data   = {
            'nonce': searchbox.find('input[name=room_search]').val()
        };
        if (typeof searchbox != "undefined") {
            data               = searchbox.find('input,select,textarea').serializeArray();
            var data_form_book = searchbox.find('input[type=text],select,textarea').serializeArray();
            for (var i = 0; i < data_form_book.length; i++) {
                parent.find('.form_book_' + data_form_book[i].name).val(data_form_book[i].value);
            }
        }
        var dataobj = {};
        for (var i = 0; i < data.length; i++) {
            dataobj[data[i].name] = data[i].value;
        }
        var holder = $('.search_room_alert');
        holder.html('');
        searchbox.find('.form-control').removeClass('error');
        if (dataobj.check_in == "" && dataobj.check_out == "" && is_validate) {
            if (dataobj.check_in == "") {
                searchbox.find('[name=check_in]').addClass('error');
            }
            if (dataobj.check_out == "") {
                searchbox.find('[name=check_out]').addClass('error');
            }
            if ($('.search-room-availablity .wpbooking-search-start').length) {
                window.setTimeout(function () {
                    $('.search-room-availablity .wpbooking-search-start').datepicker('show');
                }, 100);
            }
            setMessage(holder, wpbooking_hotel_localize.is_not_select_date, 'danger');
            return false;
        }
        if (dataobj.check_in == "" && is_validate) {
            if (dataobj.check_in == "") {
                searchbox.find('[name=check_in]').addClass('error');
            }
            if ($('.search-room-availablity .wpbooking-search-start').length) {
                window.setTimeout(function () {
                    $('.search-room-availablity .wpbooking-search-start').datepicker('show');
                }, 100);
            }
            setMessage(holder, wpbooking_hotel_localize.is_not_select_check_in_date, 'danger');
            return false;
        }
        if (dataobj.check_out == '' && is_validate) {
            if (dataobj.check_out == "") {
                searchbox.find('[name=check_out]').addClass('error');
            }
            if ($('.search-room-availablity .wpbooking-search-end').length) {
                window.setTimeout(function () {
                    $('.search-room-availablity .wpbooking-search-end').datepicker('show');
                }, 100);
            }
            setMessage(holder, wpbooking_hotel_localize.is_not_select_check_out_date, 'danger');
            return false;
        }
        if (searchbox.hasClass('loading')) {
            alert('Still loading');
            return;
        }
        searchbox.addClass('loading');
        searchbox.find('.btn-do-search-room').addClass('loading');
        var content_list_room       = $('body').find('.content-loop-room');
        var content_search_room     = $('body').find('.content-search-room');
        var content_pagination_room = $('body').find('.pagination-room');
        $.ajax({
            'type'    : 'post',
            'dataType': 'json',
            'data'    : data,
            'url'     : wpbooking_params.ajax_url,
            'success' : function (data) {
                searchbox.removeClass('loading');
                searchbox.find('.btn-do-search-room').removeClass('loading');
                content_pagination_room.html('');
                if (data.status) {
                    if (typeof data.data != "undefined" && data.data) {
                        content_list_room.html(data.data);
                        content_search_room.show();
                        content_search_room.find('.wpbooking_order_form').removeClass('no_date');
                        content_pagination_room.html(data.pagination);
                    } else {
                        content_list_room.html('');
                        content_search_room.hide();
                    }
                }
                if (data.message) {
                    var status = 'danger';
                    if (typeof data.status_message != "undefined" && data.status_message) {
                        status = data.status_message;
                    }
                    setMessage(holder, data.message, status);
                    content_list_room.html('');
                    content_search_room.hide();
                }
                if (data.status && data.status == 2) {
                    var status = 'danger';
                    if (typeof data.status_message != "undefined" && data.status_message) {
                        status = data.status_message;
                    }
                    setMessage(holder, data.message, status);
                    if (typeof data.data != "undefined" && data.data) {
                        content_list_room.html(data.data);
                        content_search_room.show();
                        content_search_room.find('.wpbooking_order_form').addClass('no_date');
                    } else {
                        content_list_room.html('');
                        content_search_room.hide();
                    }
                }
                searchbox.trigger('wpbooking_do_search_room');
                $('.content-search-room .content-loop-room .loop-room .option_number_room').trigger('change');
            },
            error     : function (data) {
                searchbox.removeClass('loading');
                searchbox.find('.btn-do-search-room').removeClass('loading');
            }
        })
    }

    var my_modal;
    $(document).on('click', '.content-search-room .content-loop-room .loop-room .room-title,.content-search-room .content-loop-room .loop-room .room-image', function () {
        var container = $(this).closest('.loop-room');
        my_modal      = container.find('.modal');
        container.find('.modal').fadeIn(500);
        container.find('.fotorama_room').fotorama();
    });
    $(document).on('click', '.content-search-room .content-loop-room .loop-room .close', function () {
        var container = $(this).closest('.loop-room');
        container.find('.modal').fadeOut();
    });
    window.onclick = function (event) {
        if (my_modal) {
            if ($(event.target).attr('class') == my_modal.attr('class')) {
                $('.modal').fadeOut();
            }
        }
    };
    $(document).on('change', '.content-search-room .content-loop-room .loop-room .option_number_room', function () {
        var container         = $(this).closest('.content-search-room');
        var total_number_room = 0;
        var total_price       = 0;
        // Room Price
        container.find('.loop-room').each(function () {
            var number = $(this).find('.option_number_room').val();
            var price  = $(this).find('.option_number_room').data('price-base');
            var diff   = parseFloat($('.more-extra', this).attr('data-diff'));
            var person = parseFloat($('.more-extra', this).attr('data-person'));
            if (diff <= 0) {
                diff = 1;
            }
            if (person <= 0) {
                person = 1;
            }
            number = parseFloat(number);
            if (number < 0) {
                number = 0;
            }
            total_number_room += parseFloat(number);
            if (!price) {
                price = 0;
            }
            total_price += parseFloat(price) * number;
            if (number > 0) {
                // Extra Price
                $(this).find('.option_is_extra').each(function () {
                    if ($(this).is(':checked')) {
                        var parent_extra = $(this).closest('tr');
                        var number_extra = parent_extra.find('.option_extra_quantity').val();
                        var price_extra  = parent_extra.find('.option_extra_quantity').data('price-extra');
                        var price_type   = parent_extra.find('.option_extra_quantity').data('type-extra');
                        if (!price_extra) {
                            price_extra = 0;
                        }
                        if (price_type == 'per_night') {
                            price_extra = parseFloat(price_extra) * number_extra * diff;
                        } else if (price_type == 'per_night_people') {
                            price_extra = parseFloat(price_extra) * number_extra * diff * person
                        } else if (price_type == 'fixed_people') {
                            price_extra = parseFloat(price_extra) * number_extra * person;
                        } else {
                            price_extra = parseFloat(price_extra) * number_extra;
                        }
                        if (price_extra) {
                            total_price += price_extra * number;
                        }
                    }
                });
            }
        });
        container.find('.info_number').html(total_number_room).trigger('change');
        container.find('.info_price').html(format_money(total_price)).trigger('change');
    });
    $(document).on('change', '.content-search-room .content-loop-room .loop-room .option_extra_quantity', function () {
        $('.content-search-room .content-loop-room .loop-room .option_number_room').trigger('change');
    });
    $(document).on('change', '.content-search-room .content-loop-room .loop-room .option_is_extra', function () {
        $('.content-search-room .content-loop-room .loop-room .option_number_room').trigger('change');
    });
    setTimeout(function () {
        $('.content-search-room .content-loop-room .loop-room .option_number_room').trigger('change');
    }, 500);

    /**
     * setMessage
     * @author quadq
     * @param holder
     * @param message
     * @param type
     */
    function setMessage(holder, message, type) {
        if (typeof type == 'undefined') {
            type = 'infomation';
        }
        var html = '<div class="alert alert-' + type + '">' + message + '</div>';
        if (!holder.length) return;
        holder.html('');
        holder.html(html);
        do_scrollTo(holder);
    }

    function do_scrollTo(el) {
        if (el.length) {
            var top = el.offset().top;
            if ($('#wpadminbar').length && $('#wpadminbar').css('position') == 'fixed') {
                top -= 32;
            }
            top -= 450;
            $('html,body').animate({
                'scrollTop': top
            }, 500);
        }
    }

    /**
     * Format money
     * @author quadq
     * @since 1.0
     *
     * @param $money
     * @returns {*}
     */
    function format_money($money) {
        $money            = wpboooking_number_format($money, wpbooking_params.currency_precision, wpbooking_params.decimal_separator, wpbooking_params.thousand_separator);
        var $symbol       = wpbooking_params.currency_symbol;
        var $money_string = '';

        switch (wpbooking_params.currency_position) {
            case "right":
                $money_string = $money + $symbol;
                break;
            case "left_with_space":
                $money_string = $symbol + " " + $money;
                break;

            case "right_with_space":
                $money_string = $money + " " + $symbol;
                break;
            case "left":
            default:
                $money_string = $symbol + $money;
                break;
        }
        return $money_string;
    }

    /**
     * convert number format
     * @author quadq
     * @since 1.0
     * @param number
     * @param decimals
     * @param dec_point
     * @param thousands_sep
     * @returns {string|*}
     */
    function wpboooking_number_format(number, decimals, dec_point, thousands_sep) {
        number         = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
        var n          = !isFinite(+number) ? 0 : +number,
            prec       = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep        = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec        = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s          = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s              = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
            .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }

    /**
     * Show More Search Fields
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-show-more-fields').click(function () {
        var parent = $(this).parent();
        if (!$(this).hasClass('active')) {
            $('.wpbooking-search-form-wrap').addClass('show-more-active');
            parent.find('.wpbooking-search-form-more').slideDown('fast');
            $(this).addClass('active');
            $(this).find('.fa').removeClass('fa-caret-down');
            $(this).find('.fa').addClass('fa-caret-up');
        } else {
            var gr_parent = $(this).closest('.wpbooking-search-form-more-wrap');
            gr_parent.find('.wpbooking-search-form-more').slideUp('fast', function () {
                gr_parent.find('.wpbooking-show-more-fields').show();
            });
            $('.wpbooking-search-form-wrap').removeClass('show-more-active');
            $(this).removeClass('active');
            $(this).find('.fa').removeClass('fa-caret-up');
            $(this).find('.fa').addClass('fa-caret-down');
        }


    });

    /**
     * Hide More Search Fields
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-hide-more-fields').click(function () {
        var parent = $(this).closest('.wpbooking-search-form-more-wrap');

        parent.find('.wpbooking-search-form-more').slideUp('fast', function () {

            parent.find('.wpbooking-show-more-fields').show();
        });

        $('.wpbooking-search-form-wrap').removeClass('show-more-active');
    });

    /**
     * Ion-RangeSlider for Price Search Field
     * @author dungdt
     * @since 1.0
     */
    $('.wpbooking-ionrangeslider').each(function () {
        if (typeof $.fn.ionRangeSlider == 'undefined') return false;
        var min     = $(this).data('min');
        var max     = $(this).data('max');
        var type    = $(this).data('type');
        var prefix  = $(this).data('prefix');
        var postfix = $(this).data('postfix');
        $(this).ionRangeSlider({
            min    : min,
            max    : max,
            type   : type,
            prefix : prefix,
            postfix: postfix
        });
    });


    /**
     * Calendar Handler for Single Place Order Form
     *
     * @since 1.0
     * @author dungdt
     */
    var wpbooking_calendar_months       = [];
    var wpbooking_enable_dates          = [];
    var wpbooking_checkin_enable_dates  = [];// Enable for Checkin
    var wpbooking_checkout_enable_dates = [];// Enable for Checkout
    var order_start_date                = $('.wpbooking_order_form .wpbooking-field-date-start');
    var order_end_date                  = $('.wpbooking_order_form .wpbooking-field-date-end');
    var minimum_stay                    = $('.minimum_stay').val();
    var last_calendar_open              = false;

    // Init Datepicker
    order_start_date.datepicker({
        minDate : 0,
        onSelect: function (selected) {
            var m = new moment(selected, 'MM/DD/YYYY');
            m.add(parseInt(minimum_stay), 'days');
            selected = m.format('MM/DD/YYYY');
            order_end_date.datepicker("option", "minDate", selected);
            //order_end_date.focus();
            window.setTimeout(function () {
                order_end_date.datepicker("show");
            }, 100);

        },

        beforeShowDay    : function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
            for (i = 0; i < wpbooking_checkin_enable_dates.length; i++) {
                if (string == wpbooking_checkin_enable_dates[i]['date'] && wpbooking_checkin_enable_dates[i]['can_check_in']) return [1, 'wpbooking-enable-date', wpbooking_checkin_enable_dates[i]['tooltip_content']];
            }

            return [0, 'wpbooking-disable-date'];
        },
        onChangeMonthYear: function (year, month, obj) {
            last_calendar_open = 1;
            loadCalendarMonth(year, month);
        },
        onClose          : function () {
            $('.tooltip ').hide();
        }
    });
    order_end_date.datepicker({
        minDate          : 0,
        onClose          : function () {
        },
        onSelect         : function (selected) {
            if (selected) {
                var m = new moment(selected, 'MM/DD/YYYY');
                m.subtract(parseInt(minimum_stay), 'days');
                selected = m.format('MM/DD/YYYY');
                order_start_date.datepicker("option", "maxDate", selected);
            }
        },
        beforeShowDay    : function (date) {
            var string = jQuery.datepicker.formatDate('yy-mm-dd', date);

            for (i = 0; i < wpbooking_checkout_enable_dates.length; i++) {
                if (string == wpbooking_checkout_enable_dates[i]['date'] && wpbooking_checkout_enable_dates[i]['can_check_out']) return [1, 'wpbooking-enable-date', wpbooking_checkout_enable_dates[i]['tooltip_content']];
            }

            return [0, 'wpbooking-disable-date'];
        },
        onChangeMonthYear: function (year, month) {
            last_calendar_open = 2;
            loadCalendarMonth(year, month);
        },
        onClose          : function () {
            $('.tooltip ').hide();
        }
    });

    $(document).on('hover', '.wpbooking-enable-date', function () {
        $(this).tooltip({
            container: 'body',
            trigger  : 'hover'
        }).tooltip('show');
    });

    function loadCalendarMonth(year, month) {
        var currentMonth                       = false;
        var currentYear                        = false;
        var key                                = false;
        wpbooking_checkin_enable_dates.length  = 0;
        wpbooking_checkout_enable_dates.length = 0;

        if (typeof  year == 'undefined' && typeof month == 'undefined') {
            var date     = new Date();
            currentMonth = date.getMonth();
            currentYear  = date.getFullYear();
        } else {
            currentMonth = month;
            currentYear  = year;
        }

        currentMonth = (parseInt(currentMonth) < 10) ? '0' + currentMonth : currentMonth;

        key = currentMonth + '_' + currentYear;
        // check in exists calendar month
        //if($.inArray(key,wpbooking_calendar_months)==-1){
        $.ajax({
            url     : wpbooking_params.ajax_url,
            type    : 'post',
            dataType: 'json',
            data    : {
                post_id     : $('[name=post_id]').val(),
                currentMonth: currentMonth,
                currentYear : currentYear,
                action      : 'wpbooking_calendar_months'
            },
            success : function (res) {
                if (typeof res.months != 'undefined') {

                    for (var k in res.months) {
                        wpbooking_calendar_months.push(k);
                        wpbooking_enable_dates = $.merge(wpbooking_enable_dates, res.months[k]);
                    }
                    // order_start_date.datepicker('refresh');
                    // order_end_date.datepicker('refresh');
                }
                if (typeof res.dates != 'undefined') {
                    for (var k in res.dates) {
                        if (res.dates[k].can_check_in == 1)
                            wpbooking_checkin_enable_dates.push(res.dates[k]);
                        if (res.dates[k].can_check_out == 1)
                            wpbooking_checkout_enable_dates.push(res.dates[k]);
                    }
                }
                if (last_calendar_open == 1) {
                    order_start_date.datepicker('refresh');
                }
                if (last_calendar_open == 2) {
                    order_end_date.datepicker('refresh');
                }
            }
        });
        // }
    }

    if (order_start_date.length || order_end_date.length) {

        loadCalendarMonth();
    }
    //==========================================================================================
    // End Calendar Handler for Single Place Order Form
    //==========================================================================================


    /**
     * Price Chart
     */


    $(window).load(function () {
        var ctx = $('#wpbooking-price-chart2');
        if (ctx.length) {
            var myLineChart = new Chart(ctx, {
                type   : 'line',
                data   : {
                    labels  : ["1", "2", "3", "4", "5", "6", "7", 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28],
                    datasets: [{
                        label                    : "My First dataset",
                        fill                     : false,
                        lineTension              : 0.1,
                        borderWidth              : 1,
                        //backgroundColor: "red",
                        borderColor              : "#c1c1c1",
                        borderCapStyle           : 'butt',
                        borderDash               : [],
                        borderDashOffset         : 0.0,
                        borderJoinStyle          : 'miter',
                        pointBorderColor         : "transparent",
                        pointBackgroundColor     : "#fff",
                        pointBorderWidth         : 0,
                        pointHoverRadius         : 5,
                        pointHoverBackgroundColor: "transparent",
                        pointHoverBorderColor    : "transparent",
                        pointHoverBorderWidth    : 2,
                        pointRadius              : 0,
                        pointHitRadius           : 10,
                        data                     : ctx.data('chart'),
                        spanGaps                 : false,
                    }]
                },
                options: {
                    scaleShowLabels: false,
                    tooltips       : {
                        enabled: false
                    },
                    legend         : {
                        display: false
                    },
                    scales         : {
                        xAxes: [{
                            display  : false,
                            gridLines: {
                                color: "white"
                            }
                        }],
                        yAxes: [{
                            display  : false,
                            gridLines: {
                                color: "white"
                            }
                        }]
                    }

                }
            });
        }
    });

    /**
     * Active Next Check In Field
     */
    $('[name=location_id]').change(function () {
        var parent = $(this).closest('.item-search');
        parent.next().find('[name=check_in]').focus();
    });

    // $('.button_show_price.is_page_search_result').click(function(){
    //     form_filter.submit();
    // });
    // Ajax Search in Archive page

    $('.wpbooking-search-form.is_search_form').submit(function () {
        var form_filter = $(this);

        // Validate Required Field
        var is_validated = true;
        var scrollTo     = false;
        form_filter.find('.wb-required').removeClass('wb-error');
        form_filter.find('.item-search').removeClass('wb-error');

        form_filter.find('.wb-required').each(function () {
            if ($(this).val() == false) {
                is_validated = false;
                $(this).addClass('wb-error');
                $(this).closest('.item-search').addClass('wb-error');

                if ($(this).closest('.wpbooking-search-form-more').length) {
                    $(this).closest('.wpbooking-search-form-wrap').find('.wpbooking-show-more-fields').trigger('click');
                }

                // Scroll to first error input
                if (!scrollTo) {
                    scrollTo = $(this).offset().top - 200;
                }
            }
        });

        if (!is_validated) {
            if (scrollTo) {
                $('html,body').animate({
                    scrollTop: scrollTo
                }, 'fast');
            }
            return false;
        }

    });

    // Remove Class Wb-error on input
    $(this).find('input,select').change(function () {
        if ($(this).hasClass('wb-error') && $(this).val()) {
            $(this).removeClass('wb-error');
            $(this).closest('.item-search').removeClass('wb-error');
        }
    });

    /**
     * Button Show More Terms in search fields
     */
    $('.show-more-terms').click(function () {
        $(this).closest('.list-checkbox').find('.term-item').removeClass('hidden_term');
        $(this).parent().remove();
    });

    // Partner Register

    $('.upload-certificate').each(function () {
        var me = $(this);
        $('.service_type_checkbox').change(function () {
            if ($(this).attr('checked')) {
                me.addClass('active');
            } else {
                me.removeClass('active');
            }
        })

        me.find('.upload_input').change(function () {
            var formData = new FormData();
            formData.append('action', 'wpbooking_upload_certificate');
            formData.append('image', $(this)[0].files[0]);

            me.find('.upload-message').html('');

            $.ajax({
                type       : "POST",
                url        : wpbooking_params.ajax_url,
                enctype    : 'multipart/form-data',
                data       : formData,
                processData: false,
                contentType: false,
                dataType   : "json",
                success    : function (data) {
                    me.find('.uploaded_image_preview').remove();
                    if (data.message) {
                        me.find('.upload-message').html(data.message);
                    }
                    if (data.status && data.image) {
                        me.find('.image_url').val(data.image.url);
                        me.append('<img class="uploaded_image_preview" alt="" src="' + data.image.url + '">');
                    }
                },
                error      : function (e) {
                    if (e.responseText) {
                        me.find('.upload-message').html(e.responseText);
                    }
                }
            });
        });

    });

    // MY Account
    // Table Check All
    $('.select-all [type=checkbox]').change(function () {
        if ($(this).attr('checked')) {
            $(this).closest('table').find('tbody .select-all [type=checkbox]').prop('checked', true);
        } else {
            $(this).closest('table').find('tbody .select-all [type=checkbox]').prop('checked', false);
        }
    });

    // Accordion
    $('.wpbooking-accordion-title').click(function () {
        if ($(this).parent().hasClass('active')) {
            $(this).parent().find('.wpbooking-metabox-accordion-content').slideUp('fast');
            $(this).parent().removeClass('active');
        } else {
            var s = $(this).parent().siblings('.wpbooking-metabox-accordion');
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
    var condition_object = 'select, input[type="radio"]:checked, input[type="text"], input[type="hidden"], input.ot-numeric-slider-hidden-input,input[type="checkbox"]';
    // condition function to show and hide sections
    $('.wpbooking-form-group').on('change.conditionals', condition_object, function (e) {
        run_condition_engine();
    });
    run_condition_engine();

    function run_condition_engine() {
        $('.wpbooking-condition[data-condition]').each(function () {

            var passed;
            var conditions = get_match_condition($(this).data('condition'));
            var operator   = ($(this).data('operator') || 'and').toLowerCase();
            $.each(conditions, function (index, condition) {

                var target = $('[name=' + condition.check + ']');

                var targetEl = !!target.length && target.first();

                if (!target.length || (!targetEl.length && condition.value.toString() != '')) {
                    return;
                }


                var v1 = targetEl.length ? targetEl.val().toString() : '';
                var v2 = condition.value.toString();
                var result;

                if (targetEl.length && targetEl.attr('type') == 'radio') {
                    v1 = $('[name=' + condition.check + ']:checked').val();
                }
                if (targetEl.length && targetEl.attr('type') == 'checkbox') {
                    v1 = targetEl.is(':checked') ? v1 : '';
                }


                switch (condition.rule) {
                    case 'less_than':
                        result = (parseInt(v1) < parseInt(v2));
                        break;
                    case 'less_than_or_equal_to':
                        result = (parseInt(v1) <= parseInt(v2));
                        break;
                    case 'greater_than':
                        result = (parseInt(v1) > parseInt(v2));
                        break;
                    case 'greater_than_or_equal_to':
                        result = (parseInt(v1) >= parseInt(v2));
                        break;
                    case 'contains':
                        result = (v1.indexOf(v2) !== -1 ? true : false);
                        break;
                    case 'is':
                        result = (v1 == v2);
                        break;
                    case 'not':
                        result = (v1 != v2);
                        break;
                }

                if ('undefined' == typeof passed) {
                    passed = result;
                }

                switch (operator) {
                    case 'or':
                        passed = (passed || result);
                        break;
                    case 'and':
                    default:
                        passed = (passed && result);
                        break;
                }

            });

            if (passed) {
                $(this).show();
            } else {
                $(this).hide();
            }

            delete passed;

        });
    }

    function get_match_condition(condition) {
        var match;
        var regex      = /(.+?):(is|not|contains|less_than|less_than_or_equal_to|greater_than|greater_than_or_equal_to)\((.*?)\),?/g;
        var conditions = [];

        while (match = regex.exec(condition)) {
            conditions.push({
                'check': match[1],
                'rule' : match[2],
                'value': match[3] || ''
            });
        }

        return conditions;
    }

    // Please do not edit condition section if you don't understand what it is

    jQuery(window).load(function () {

        ///////////////////////////
        //////  Gmap    //////////
        ///////////////////////////

        function load_gmap() {
            var last_center = false;
            if ($('.wpbooking-gmap-wrapper').length) {
                $('.wpbooking-gmap-wrapper').each(function (index, el) {
                    var t               = $(this);
                    var gmap            = $('.gmap-content', t);
                    var map_lat         = parseFloat($('input[name="map_lat"]', t).val());
                    var map_long        = parseFloat($('input[name="map_long"]', t).val());
                    var map_zoom        = parseInt($('input[name="map_zoom"]', t).val());
                    var bt_ot_searchbox = $('input.gmap-search', t);
                    var current_marker;
                    var map_options     = {
                        map   : {
                            options: {
                                mapTypeId            : google.maps.MapTypeId.ROADMAP,
                                mapTypeControl       : true,
                                mapTypeControlOptions: {
                                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                                },
                                navigationControl    : true,
                                scrollwheel          : true,
                            },
                            events : {
                                click: function (marker, event, context) {
                                    last_center = event.latLng;
                                    $('input[name="map_lat"]', t).val(event.latLng.lat());
                                    $('input[name="map_long"]', t).val(event.latLng.lng());
                                    $('input[name="map_zoom"]', t).val(marker.zoom);

                                    $(this).gmap3({
                                        clear: {
                                            name: ["marker"],
                                            last: true
                                        }
                                    });
                                    $(this).gmap3({
                                        marker: {
                                            values : [
                                                {latLng: event.latLng},
                                            ],
                                            options: {
                                                draggable: false
                                            },
                                        }
                                    });
                                }
                            }
                        },
                        marker: {
                            values: [
                                [map_lat, map_long],
                            ],
                        }
                    };
                    if (map_lat && map_long) {
                        map_options.map.options.center = [map_lat, map_long];
                        map_options.map.options.zoom   = map_zoom;
                    }
                    gmap.gmap3(map_options);
                    var gmap_obj = gmap.gmap3('get');
                    if (!map_lat || !map_long) {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (showPosition) {
                                var gmap_obj = gmap.gmap3('get');
                                map_lat      = showPosition.coords.latitude;
                                map_long     = showPosition.coords.longitude;
                                last_center  = new google.maps.LatLng(map_lat, map_long);
                                gmap_obj.setCenter(last_center);
                                gmap_obj.setZoom(13);
                                $('input[name="map_lat"]', t).val(map_lat);
                                $('input[name="map_long"]', t).val(map_long);
                                $('input[name="map_zoom"]', t).val(13);
                                gmap.gmap3({
                                    clear: {
                                        name: ["marker"],
                                        last: true
                                    }
                                });
                                gmap.gmap3({
                                    marker: {
                                        values : [
                                            {latLng: last_center},
                                        ],
                                        options: {
                                            draggable: false
                                        },
                                    }
                                });
                            });
                        }
                    }
                    var geocoder = new google.maps.Geocoder;
                    var map_type = "roadmap";
                    if (bt_ot_searchbox.length) {
                        var searchBox = new google.maps.places.SearchBox(bt_ot_searchbox[0]);
                        google.maps.event.addListener(searchBox, 'places_changed', function () {
                            var places = searchBox.getPlaces();
                            if (places.length == 0) {
                                return;
                            }
                            // For each place, get the icon, place name, and location.
                            var bounds = new google.maps.LatLngBounds();
                            for (var i = 0, place; place = places[i]; i++) {
                                bounds.extend(place.geometry.location);
                                if (i == 0) {

                                    gmap.gmap3({
                                        clear: {
                                            name: ["marker"],
                                            last: true
                                        }
                                    });
                                    gmap.gmap3({
                                        marker: {
                                            values : [
                                                {latLng: place.geometry.location},
                                            ],
                                            options: {
                                                draggable: false
                                            },
                                        }
                                    });

                                    $('input[name="map_lat"]', t).val(place.geometry.location.lat());
                                    $('input[name="map_long"]', t).val(place.geometry.location.lng());
                                    $('input[name="map_zoom"]', t).val(gmap_obj.getZoom());

                                }
                            }
                            //gmap_obj.fitBounds(bounds);
                        });
                    }
                    google.maps.event.addListener(gmap_obj, "zoom_changed", function (event) {
                        $('input[name="map_zoom"]', t).val(gmap_obj.getZoom());
                    });
                    $(window).resize(function () {
                        google.maps.event.trigger(gmap_obj, 'resize');
                        if (last_center) {
                            gmap_obj.setCenter(last_center);
                        }
                    });
                });
            }
        }

        load_gmap();
    });

    // ALl Booking Calendar
    if (typeof $.fn.fullCalendar == 'function') {

        $('#wpbooking_order_calendar .calendar-wrap').fullCalendar({
            header        : {
                left  : 'prev,next today',
                center: 'title',
                right : 'month,agendaWeek,agendaDay'
            },
            //eventLimit: true, // allow "more" link when too many events,
            height        : 500,
            numberOfMonths: 2,
            events        : function (start, end, timezone, callback) {
                var filter = $('.tablenav');
                $.ajax({
                    url     : wpbooking_params.ajax_url,
                    dataType: 'json',
                    type    : 'post',
                    data    : {
                        action: 'wpbooking_order_calendar',
                        start : start.unix(),
                        end   : end.unix(),
                        filter: filter.find('input,select').serialize()
                    },
                    success : function (doc) {
                        if (typeof doc == 'object') {
                            callback(doc);
                        }
                    },
                    error   : function (e) {
                        alert('Can not get the order data. Lost connect with your sever');
                        console.log(e);
                    }
                });
            },
            eventMouseover: function (event, element, view) {
                var html = event.tooltipContent;
                $(this).popover({
                    content  : html,
                    placement: 'bottom',
                    container: 'body',
                    html     : true
                });
                $(this).popover('show');

            },
            eventMouseout : function () {
                //$(this).popover('hide');
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
    function reloadOldMessages() {
        var me = $('old-messages');

        if (!me.length) return;

        me.addClass('loading');
        $.ajax({
            url     : wpbooking_params.ajax_url,
            data    : {
                action : 'wpbooking_reload_old_message',
                user_id: me.data('user-id')
            },
            dataType: 'json',
            type    : 'post',
            success : function (res) {
                me.removeClass('loading');
                if (res.html) {
                    me.html(res.html);
                }
            },
            error   : function (e) {
                me.html(e.responseText);
            }
        })
    }

    function appendNewMessage(messageHtml) {
        $('.old-messages').append(messageHtml);
        window.setTimeout(function () {
            $('.old-messages').animate({scrollTop: $('.old-messages')[0].scrollHeight}, 'fast');

        }, 100);
    }

    $('.wb-send-message-form').submit(function () {
        var me = $(this);
        $(this).addClass('loading');
        $(this).find('.message-box').html('');

        $.ajax({
            type    : 'post',
            dataType: 'json',
            url     : $(this).attr('action'),
            data    : $(this).serialize(),
            success : function (res) {
                me.removeClass('loading');
                if (res.message) {
                    me.find('.message-box').html(res.message);
                }

                if (res.status) {
                    // For User Dashboard Page
                    if (me.data('reload') && typeof res.messageHTML != 'undefined') {
                        //reloadOldMessages();
                        appendNewMessage(res.messageHTML);
                    }

                    // Clear the Form
                    me.find('textarea').val('');

                    var scroll_item = me.closest('ul');
                }

            },
            error   : function (e) {
                me.removeClass('loading');
                me.find('.message-box').html(e.responseText);
            }
        })
    });

    $('.upload-avatar').each(function () {
        var me = $(this);
        me.find('.upload_input').change(function () {
            var container = $(this).closest('.item_avatar');
            var formData  = new FormData();
            formData.append('action', 'wpbooking_upload_avatar');
            formData.append('image', $(this)[0].files[0]);

            me.find('.upload-message').html('');

            $.ajax({
                type       : "POST",
                url        : wpbooking_params.ajax_url,
                enctype    : 'multipart/form-data',
                data       : formData,
                processData: false,
                contentType: false,
                dataType   : "json",
                success    : function (data) {
                    me.find('.uploaded_image_preview').remove();
                    if (data.message) {
                        me.find('.upload-message').html(data.message);
                    }
                    if (data.status && data.image) {
                        container.find(".image_url").val(data.image.url);
                        container.find(".avatar img").attr("src", data.image.url);
                        //me.find('.image_url').val(data.image.url);
                        //me.append('<img class="uploaded_image_preview" alt="" src="'+data.image.url+'">');
                    }
                },
                error      : function (e) {
                    if (e.responseText) {
                        me.find('.upload-message').html(e.responseText);
                    }
                }
            });
        });

    });


    // Loop Grid Gallery
    if (typeof $.fn.owlCarousel == 'function')
        $('.service-gallery-slideshow').owlCarousel(
            {
                items  : 1,
                nav    : true,
                navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>']
            }
        );

    // Form Order By Submit
    $('.wpbooking-loop-sort-by').change(function () {
        $(this).closest('form').submit();
    });

    // wpbooking-view-switch
    $('.wpbooking-view-switch a').click(function () {
        var cname  = 'wpbooking_view_type';
        var cvalue = $(this).data('view');
        var d      = new Date();
        d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
        var expires     = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;

        $('.wpbooking-loop-items').removeClass('list').addClass(cvalue);
        $(this).addClass('active').siblings().removeClass('active');

        if (typeof $.fn.owlCarousel == 'function')
            $('.service-gallery-slideshow').owlCarousel(
                {
                    items  : 1,
                    nav    : true,
                    navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>']
                }
            );

        return false;
    });

    // Add Favorite
    $('.service-fav').click(function () {
        var me = $(this);
        $.ajax({
            url     : wpbooking_params.ajax_url,
            dataType: 'json',
            type    : 'post',
            data    : {
                action : 'wpbooking_add_favorite',
                post_id: $(this).data('post')
            },
            success : function (res) {
                if (res.status) {
                    if (res.fav_status) me.addClass('active');
                    else me.removeClass('active');
                }

                if (res.message) alert(res.message);
            },
            error   : function (e) {
                console.log(e.responseText);
            }
        });

        return false;
    });

    // Lighbox gallery
    $('.service-gallery-single').each(function () {
        $(this).magnificPopup({
            delegate: 'a.hover-tag',
            type    : 'image',
            gallery : {
                enabled: true
            }
        });
    });

    //$('.wpbooking-vote-for-review .review-do-vote').click(function(){
    //    var me=$(this);
    //    $.ajax({
    //        data:{
    //            action:'wpbooking_vote_review',
    //            review_id:$(this).data('review-id'),
    //        },
    //        type:'post',
    //        url:wpbooking_params.ajax_url,
    //        dataType:'json',
    //        success:function(res){
    //            if(res.status){
    //                me.closest('.wpbooking-vote-for-review').find('.review-vote-count').html(res.vote_count);
    //            }
    //            if(res.voted){
    //                me.addClass('active');
    //                me.closest('.wpbooking-vote-for-review').find('.wb-like-text').addClass('wb-none');
    //            }else{
    //                me.removeClass('active');
    //                me.closest('.wpbooking-vote-for-review').find('.wb-like-text').removeClass('wb-none');
    //            }
    //        }
    //    })
    //});
    $('.wpbooking-account-tab .user-reviews .review-do-vote').click(function () {
        var me = $(this);

        $.ajax({
            data    : {
                action   : 'wpbooking_vote_review',
                review_id: $(this).data('review-id'),
            },
            type    : 'post',
            url     : wpbooking_params.ajax_url,
            dataType: 'json',
            success : function (res) {
                if (res.status) {
                    if (res.count > 0) {
                        me.closest('.comment_container').find('.count_like').html(res.vote_count_2);
                        me.closest('.comment_container').find('.item_count_like').removeClass('hide');
                    } else {
                        me.closest('.comment_container').find('.count_like').html('');
                        me.closest('.comment_container').find('.item_count_like').addClass('hide');
                    }
                }
                if (res.voted) {
                    me.addClass('active');
                } else {
                    me.removeClass('active');
                }
            }
        })
    });

    //$('.wb-btn-reply-comment').click(function(){
    //   var parent=$(this).closest('li');
    //   parent.find('ul .reply-comment-form').toggleClass('active');
    //});
    //
    //// Reply
    //$('.reply-submit a').click(function(){
    //    var me=$(this);
    //    var parent=me.closest('.wpbooking-add-reply');
    //    me.addClass('loading');
    //    var message=parent.find('.reply_content').val();
    //    if(!message) return false;
    //
    //    $.ajax({
    //        data:{
    //            action:'wpbooking_write_reply',
    //            review_id:me.data('review-id'),
    //            message:message
    //        },
    //        url:wpbooking_params.ajax_url,
    //        dataType:'json',
    //        type:'post',
    //        success:function(res){
    //            me.removeClass('loading');
    //            if(res.status){
    //                me.closest('.comment').find('.wb-btn-reply-comment').hide();
    //                me.closest('.content_comment_profile').find('.wb-btn-reply-comment').hide();
    //                me.closest('ul').html(res.html);
    //            }
    //        },
    //        error:function(e){
    //            console.log(e.responseText);
    //            me.removeClass('loading');
    //        }
    //    })
    //
    //});

    // Cart Item
    $('.cart-item-order-form-fields-wrap .show-more-less').click(function () {
        $(this).closest('.cart-item-order-form-fields-wrap').toggleClass('active');
    });

    // Order Item
    $('.order-item-form-fields-wrap .show-more-less').click(function () {
        $(this).closest('.order-item-form-fields-wrap').toggleClass('active');
    });

    // show_more_review_order
    $('.show_more_review_order').click(function () {
        $(this).parent().toggleClass('active');
    });

    // On-off
    $(document).on('click', '.wpbooking-switch', function (event) {
        $(this).toggleClass("switchOn", function () {

        });
        var checkbox = $(this).closest('.wpbooking-switch-wrap').find('.checkbox');

        if ($(this).hasClass('switchOn')) {
            checkbox.val('on');
            checkbox.trigger('change');
        } else {
            checkbox.val('off');
            checkbox.trigger('change');
        }
    });

    // Ajax Enable/Disable Property in Your Listing Page
    $('.wpbooking_service_change_status').change(function () {
        var me = $(this);
        me.closest('.service-item').addClass('loading');

        $.ajax({
            type    : 'post',
            url     : wpbooking_params.ajax_url,
            dataType: 'json',
            data    : {
                action : 'wpbooking_enable_property',
                status : me.val(),
                post_id: me.data('id')
            },
            success : function (res) {
                me.closest('.service-item').removeClass('loading');
                if (res.message) {
                    console.log(res.message);
                }
            },
            error   : function (e) {
                console.log(e.responseText);
                me.closest('.service-item').removeClass('loading');
            }
        })
    });

    // AJax loax more inbox list
    $('.wb-load-more-message').click(function () {
        var me = $(this);
        me.addClass('loading');
        $.ajax({
            type    : 'post',
            url     : wpbooking_params.ajax_url,
            dataType: 'json',
            data    : {
                action: 'wpbooking_load_message',
                offset: me.data('offset')
            },
            success : function (res) {
                me.removeClass('loading');
                if (res.message) {
                    alert.log(res.message);
                }
                if (res.html) {
                    me.parent().before(res.html);
                }
                if (res.offset) {
                    me.data('offset', res.offset);
                } else {
                    me.parent().remove();
                }

            },
            error   : function (e) {
                console.log(e.responseText);
                me.removeClass('loading');
            }
        });
    });

    // AJax loax more reply
    $('.wb-load-more-reply').click(function () {
        var me = $(this);
        me.addClass('loading');
        $.ajax({
            type    : 'post',
            url     : wpbooking_params.ajax_url,
            dataType: 'json',
            data    : {
                action : 'wpbooking_load_reply',
                user_id: me.data('user-id'),
                offset : me.data('offset')
            },
            success : function (res) {
                me.removeClass('loading');
                if (res.message) {
                    alert.log(res.message);
                }
                if (res.html) {
                    me.parent().after(res.html);
                }
                if (res.offset) {
                    me.data('offset', res.offset);
                } else {
                    me.parent().remove();
                }

            },
            error   : function (e) {
                console.log(e.responseText);
                me.removeClass('loading');
            }
        });
    });

    // Scroll Bottom Div
    $('.wb-scroll-bottom').each(function () {
        var me = $(this);
        window.setTimeout(function () {
            me.animate({scrollTop: me[0].scrollHeight}, 'fast');

        }, 100);
    })
    //$('#comments input[type=submit]').click(function(){
    //    var check = true;
    //    $(".wpbooking_review_detail_rate").each(function(){
    //        if($(this).val() < 1){
    //            check = false
    //        }
    //    });
    //    $("input[name=wpbooking_review]").each(function(){
    //        if($(this).val() < 1){
    //            check = false
    //        }
    //    });
    //    if(check == false){
    //        var msg = '<div class="alert alert-danger"><p>'+wpbooking_params.select_comment_review+'</p></div>';
    //        $(this).closest('.form-submit').find('.alert').remove();
    //        $(this).closest('.form-submit').append(msg);
    //        return false;
    //    }
    //});

    $.fn.wb_tabs = function (params) {
        var setting     = params;
        var t           = $(this);
        var tab_content = $(this).parent().find('.wp-tabs-content');
        tab_content.find('.wp-tab-item').hide();
        tab_content.find('.wp-tab-item:first').show();
        $(this).find('li a').on('click', function (event) {
            event.preventDefault();
            single_map();
            var id = $(this).attr('href');
            t.find('li').each(function () {
                $(this).removeClass('active');
            });
            $(this).parent().addClass('active');
            tab_content.find('.wp-tab-item').each(function () {
                $(this).hide();
                id = id.replace('#', '');
                if ($(this).attr('id') == id) {
                    $(this).fadeIn('slow');
                }
            });
        });
    };

    $('.wb-tabs').wb_tabs();

    $(document).on('change', 'input[type=number]', function () {
        $(this).each(function () {
            var number = $(this).val();
            number     = parseFloat(number);
            if (isNaN(number)) {
                number = 0;
            }
            $(this).val(number);
        });
    });

    if ($('.wpbooking_check_empty_cart').val() == 'true') {
        $.ajax({
            url     : wpbooking_params.ajax_url,
            data    : {
                'action': 'wpbooking_check_empty_cart'
            },
            dataType: 'json',
            type    : 'post',
            success : function (res) {
                if (res.status == 'false') {
                    location.reload();
                }
            }
        })
    }
    $('#wpbooking-register-form .accept-term input').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).closest('.form-group-wrap').find('button[type=submit]').removeClass('wb-disabled');
        } else {
            $(this).closest('.form-group-wrap').find('button[type=submit]').addClass('wb-disabled');
        }
    });
    if ($('#wpbooking-register-form .accept-term input').is(':checked')) {
        $('#wpbooking-register-form .form-group-wrap').find('button[type=submit]').removeClass('wb-disabled');
    }

    $(document).on('click', '.btn_detail_checkout', function () {
        var $content = $(this).parent();
        var info     = $content.find('.content_details');
        if (info.css('display') == 'none') {
            info.slideDown();
        } else {
            info.slideUp();
        }
    });

    // Tour Single
    $('.wb-departure-month').change(function () {
        var v = $(this).val();
        if (!v) return;
        $('.wb-departure-date option').hide();
        $('.wb-departure-date option.' + v).show();
        $('.wb-departure-date').trigger('change');
    });

    $('.wb-departure-month').trigger('change');

    $('.wb-departure-date').change(function () {
        var v = $(this).val();
        if (!v) return;
        var price = $(this).find('option[value=' + v + ']').data('price');
        $('.wb-price-html .price').html(price);
    });

    $('.wb-departure-date').trigger('change');

    // Tour Booking
    $('.wb-tour-booking-form').submit(function () {
        var self = $(this);
        self.addClass('loading');
        self.find('.booking-message').html('');
        var data = self.serialize();
        data += '&action=wpbooking_add_to_cart';

        $.ajax({
            dataType: 'json',
            type    : 'post',
            data    : data,
            url     : wpbooking_params.ajax_url,
            success : function (res) {
                if (res) {

                }
                if (res.message) {
                    self.find('.booking-message').html(res.message);
                }

                if (typeof res.redirect != 'undefined') {
                    window.location = res.redirect;
                }

                if (typeof res.error_fields != 'undefined') {
                    for (var k in res.error_fields) {

                        self.find("[name='" + k + "']").addClass('input-error');
                    }
                }
                if (typeof  res.updated_content != 'undefined') {

                    for (var k in res.updated_content) {
                        var element = $(k);
                        element.replaceWith(res.updated_content[k]);
                        $(window).trigger('wpbooking_event_cart_update_content', [k, res.updated_content[k]]);
                    }
                }

                self.removeClass('loading');
            },
            error   : function (e) {
                console.log(e.reponseText);
            }
        })

        return false;
    });

    $('.wpbooking-location-item').each(function () {
        var t        = $(this);
        var location = t.data().address;
        var unit     = t.data().unit;
        $.simpleWeather({
            location: location.trim(),
            woeid   : '',
            unit    : unit.trim(),
            success : function (weather) {
                /*
                html = '<h2><i class="icon-location icon-' + weather.code + '"></i> ' + weather.temp + '&deg;' + weather.units.temp + '</h2>';
                html += '<ul><li>' + weather.city + ', ' + weather.region + '</li>';
                html += '<li class="currently">' + weather.currently + '</li>';
                html += '<li>' + weather.wind.direction + ' ' + weather.wind.speed + ' ' + weather.units.speed + '</li></ul>';*/
                $('.wpbooking-location-temp', t).html('<i class="icon-location icon-' + weather.code + '"></i> ' + weather.temp + '&deg;' + weather.units.temp);
            },
            error   : function (error) {
                $("#weather").html('<p>' + error + '</p>');
            }
        });
    });

    if ($('ul.wpbooking-all-gateways').length) {
        $('ul.wpbooking-all-gateways li:first-child input').prop('checked', true);
    }

    $(".wpbooking-loop-items.slide").owlCarousel({
        nav       : true,
        items     : 2,
        navText   : ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
        dot       : true,
        loop      : true,
        autoplay  : true,
        margin    : 15,
        responsive: {
            0  : {
                items: 1
            },
            480: {
                items: 2
            },
            769: {
                items: 2
            }
        }
    });

    /* search tab lncj */

    $('.wpbooking_form_search_tab').each(function () {
        var thisE = $(this);
        thisE.find('ul li a').click(function (e) {
            e.preventDefault();
            $(this).parent().addClass('active');
            var attr = $(this).attr('href');
            if ($(this).parent().siblings().hasClass('active')) {
                $(this).parent().siblings().removeClass('active');
            }
            thisE.find('.tab-content .tab-pane').each(function () {
                var tab_content = "#" + $(this).attr('id');
                if (attr == tab_content) {
                    $(this).siblings().removeClass('in active');
                    $(this).addClass('in active');
                }
            })
        })
    });

    $('.wpbooking-check-all-container').each(function (e) {
        var t        = $(this);
        var checkall = $('.wpbooking-check-all', t);
        var check    = t.find(checkall.data().target);
        checkall.click(function () {
            if ($(this).is(':checked')) {
                check.each(function () {
                    check.prop('checked', true);
                });
            } else {
                check.prop('checked', false);
            }
        });
    });

    if ($('.wpbooking-check-all-container').length) {
        $('.wpbooking-check-all-container').each(function () {
            var parent = $(this);
            var value = '';
            var fetchTo = parent.data('fetch');
            $('.wpbooking-check-all', parent).change(function (e) {
                var t = $(this);
                value = '';
                $('.wpbooking-check', parent).each(function () {
                    $(this).prop('checked', t.prop('checked'));
                    if ($(this).is(':checked')) {
                        value += $(this).val() + ',';
                    }
                });
                if (value.length > 0) {
                    value = value.substr(0, value.length - 1);
                }
                $(fetchTo).attr('value',value);
            });
            $('.wpbooking-check', parent).change(function () {
                value = '';
                $('.wpbooking-check', parent).each(function () {
                    if ($(this).is(':checked')) {
                        value += $(this).val() + ',';
                    }
                });
                if (value.length > 0) {
                    value = value.substr(0, value.length - 1);
                }
                $(fetchTo).attr('value',value);
            });
            $('.wpbooking-check', parent).each(function () {
                if ($(this).is(':checked')) {
                    value += $(this).val() + ',';
                }
            });
            if (value.length > 0) {
                value = value.substr(0, value.length - 1);
            }
            $(fetchTo).attr('value',value);
        });

    }
});


