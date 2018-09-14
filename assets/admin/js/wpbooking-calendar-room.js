jQuery(document).ready(function ($) {

    var RoomCalendar = function (container) {
        var self             = this;
        this.container       = container;
        this.calendar        = null;
        this.form_container  = null;
        this.last_start_date = false;
        this.last_end_date   = false;
        this.onMouseDown     = false;
        this.selectedQueue   = [];
        this.calendarObject  = false;

        this.init = function () {
            self.container      = container;
            self.calendar       = $('.calendar-room', self.container);
            self.form_container = $('.form-container', self.container);
            self.initCalendar();
            self.bindEvent();
            //self.calendar.fullCalendar( 'refetchEvents' );
        }

        this.initCalendar = function () {
            self.calendarObject = $('.calendar-room2', self.container).wbCalendar({
                    sourceBinding   : function (start, end, callback) {
                        $.ajax({
                            url     : wpbooking_params.ajax_url,
                            dataType: 'json',
                            type    : 'post',
                            data    : {
                                action      : 'wpbooking_load_availability',
                                post_id     : self.container.data('post-id'),
                                post_encrypt: self.container.data('post-encrypt'),
                                table       : self.container.data('table'),
                                start       : start,
                                end         : end,
                                security    : wpbooking_params.wpbooking_security
                            },
                            success : function (doc) {
                                if (typeof doc == 'object') {
                                    callback(doc.data);
                                }
                                //self.clearDateRange();
                            },
                            error   : function (e) {
                                alert('Can not get the availability slot. Lost connect with your sever');
                                //self.clearDateRange();
                            }
                        });
                    },
                    sourceRender    : function (source, element) {

                        if ($('.calendar-room2').hasClass('tour')) {
                            if ($('.calendar-room2').hasClass('per_person')) {
                                if (source.status == 'available' && (source.adult_price != 0 || source.infant_price != 0 || source.child_price != 0)) {
                                    var html_info = '<span class="wb-person">';
                                    if (source.adult_price != undefined && source.adult_price != 0) {
                                        html_info += 'Adult: ' + source.adult_price;
                                    }
                                    if (source.child_price != undefined && source.child_price != 0) {
                                        html_info += '<br>Child: ' + source.child_price;
                                    }
                                    if (source.infant_price != undefined && source.infant_price != 0) {
                                        html_info += '<br>Infant: ' + source.infant_price;
                                    }
                                    html_info += '</span>';
                                    element.append(html_info);
                                }
                            } else {
                                if (source.status == 'available' && source.calendar_price != undefined && source.calendar_price != 0) {
                                    element.append('<span class="wb-price">' + source.price_text + '</span>');
                                }
                            }
                        } else {
                            if (source.status == 'available' && typeof source.price_text != 'undefined') {
                                element.append('<span class="wb-price">' + source.price_text + '</span>');
                            }
                        }
                    },
                    dayClick        : function (element, source) {
                        if (source) {
                            console.log(source);
                            var start = moment(source.start);
                            var end   = moment(source.start);
                            setCheckInOut(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'), self.form_container);

                            $('#calendar-price').val(source.price);

                            $('#calendar-status option[value=' + source.status + ']').prop('selected', true);

                            $('#calendar-price-week').val(source.weekly);
                            $('#calendar-price-month').val(source.monthly);

                            if (source.calendar_price && source.calendar_price != 0) {
                                $('input[name=calendar_minimum]').val(source.calendar_minimum);
                                $('input[name=calendar_maximum]').val(source.calendar_maximum);
                                $('input[name=calendar_price]').val(source.calendar_price);
                            } else {
                                $('input[name=calendar_minimum]').val('');
                                $('input[name=calendar_maximum]').val('');
                                $('input[name=calendar_price]').val('');
                            }

                            if (source.adult_price != undefined && source.adult_price != 0) {
                                $('input[name=calendar_adult_minimum]').val(source.adult_minimum);
                                $('input[name=calendar_adult_price]').val(source.adult_price);
                            } else {
                                $('input[name=calendar_adult_minimum]').val('');
                                $('input[name=calendar_adult_price]').val('');
                            }
                            if (source.child_price != undefined && source.child_price != 0) {
                                $('input[name=calendar_child_minimum]').val(source.child_minimum);
                                $('input[name=calendar_child_price]').val(source.child_price);
                            } else {
                                $('input[name=calendar_child_minimum]').val('');
                                $('input[name=calendar_child_price]').val('');
                            }
                            if (source.child_price != undefined && source.child_price != 0) {
                                $('input[name=calendar_infant_minimum]').val(source.infant_minimum);
                                $('input[name=calendar_infant_price]').val(source.infant_price);
                            } else {
                                $('input[name=calendar_infant_minimum]').val('');
                                $('input[name=calendar_infant_price]').val('');
                            }
                            if (source.max_people != undefined) {
                                $('input[name=calendar_max_people]').val(source.max_people);
                            }
                        }
                    },
                    onSelectionRange: function (start, end) {
                        var start_moment = moment(start.data('date'));
                        var end_moment   = moment(end.data('date'));
                        setCheckInOut(start_moment.format('MM/DD/YYYY'), end_moment.format('MM/DD/YYYY'), self.form_container);
                    }
                }
            );

        }

        this.bindEvent = function () {

            // Reload Calendar
            $('.wpbooking-metabox-template').on('wpbooking_change_service_type_metabox', function () {
                self.initCalendar();
            });

            if ($('.form-bulk-edit').length) {
                $('.calendar-bulk-close').click(function (event) {
                    $(this).closest('.form-bulk-edit').fadeOut();
                    self.calendarObject.refreshCalendar();
                });
            }
            $('#calendar-checkin').datepicker({
                dateFormat   : "mm/dd/yy",
                beforeShowDay: function (date) {
                    var d = new Date();
                    if (date.getTime() < d.getTime()) {
                        return [false];
                    } else {
                        return [true];
                    }
                },
                onSelect     : function (date_string) {
                    var dt     = new Date(date_string);
                    var end_dt = new Date($('#calendar-checkout').val());

                    if (dt <= end_dt) {
                        self.last_start_date = moment(date_string);
                        self.showDateRange();
                    } else {
                        $('#calendar-checkout').val('');
                        self.last_end_date = false;
                        window.setTimeout(function () {
                            $('#calendar-checkout').datepicker('show');
                        }, 100)

                    }

                }
            });
            $('#calendar-checkout').datepicker({
                dateFormat   : "mm/dd/yy",
                beforeShowDay: function (date) {
                    var d = new Date();
                    if (date.getTime() < d.getTime()) {
                        return [false];
                    } else {
                        return [true];
                    }
                },
                onSelect     : function (date_string) {
                    var dt     = new Date($('#calendar-checkin').val());
                    var end_dt = new Date(date_string);
                    if (dt <= end_dt) {
                        self.last_start_date = moment(date_string);
                        self.showDateRange();
                    } else {
                        $('#calendar-in').val('');
                        self.last_start_date = false;
                        window.setTimeout(function () {
                            $('#calendar-checkin').datepicker('show');
                            ;
                        }, 100)
                    }
                }
            });

            $('.property_available_forever').on('ifChecked', function () {
                var val = $(this).val();
                // Show Loading
                self.ajaxSavePropertyAvailableFor(val, $(this).data('post-id'));
            });
            $('.property_available_specific').on('ifChecked', function () {
                var val = $(this).val();
                // Show Loading
                self.ajaxSavePropertyAvailableFor(val, $(this).data('post-id'));
            });

            // Check In

            var flag_add = false;
            if ($('.wpbooking-calendar-sidebar .calendar-room-form').length) {
                $('.wpbooking-calendar-sidebar .calendar-room-form #calendar-save').click(function (event) {
                    var container = $(this).parents('.wpbooking-calendar-wrapper');

                    var parent        = $(this).parents('.calendar-room-form');
                    var can_check_in  = $('#calendar-can-check-in').attr('checked') == 'checked' ? 1 : 0;
                    var can_check_out = $('#calendar-can-check-out').attr('checked') == 'checked' ? 1 : 0;

                    var max_guest = parseInt($('#max_guests').val());
                    if ($('input[name="calendar_max_people"]').length > 0 && $('input[name="calendar_max_people"]').val() != '') {
                        max_guest = parseInt($('input[name="calendar_max_people"]').val());
                    }
                    var data = {
                        'check_in'               : $('#calendar-checkin', parent).val(),
                        'check_out'              : $('#calendar-checkout', parent).val(),
                        'price'                  : $('#calendar-price', parent).val(),
                        'status'                 : $('#calendar-status', parent).val(),
                        'post-id'                : $('#calendar-post-id', parent).val(),
                        'post-encrypt'           : $('#calendar-post-encrypt', parent).val(),
                        'table'                  : $('#table_name', parent).val(),
                        'action'                 : 'wpbooking_add_availability',
                        'security'               : wpbooking_params.wpbooking_security,
                        'weekly'                 : $('#calendar-price-week').val(),
                        'monthly'                : $('#calendar-price-month').val(),
                        'can_check_in'           : can_check_in,
                        'can_check_out'          : can_check_out,
                        'calendar_minimum'       : $('input[name=calendar_minimum]', parent).val(),
                        'calendar_maximum'       : $('input[name=calendar_maximum]', parent).val(),
                        'calendar_price'         : $('input[name=calendar_price]', parent).val(),
                        'calendar_adult_minimum' : $('input[name=calendar_adult_minimum]', parent).val(),
                        'calendar_adult_price'   : $('input[name=calendar_adult_price]', parent).val(),
                        'calendar_child_minimum' : $('input[name=calendar_child_minimum]', parent).val(),
                        'calendar_child_price'   : $('input[name=calendar_child_price]', parent).val(),
                        'calendar_infant_minimum': $('input[name=calendar_infant_minimum]', parent).val(),
                        'calendar_infant_price'  : $('input[name=calendar_infant_price]', parent).val(),
                        'calendar_max_people'    : max_guest
                    };

                    if (flag_add) return false;
                    flag_add = true;

                    $('.form-message', parent).html('').removeClass('error success');
                    $('.overlay', container).addClass('open');

                    $.ajax({
                        url     : wpbooking_params.ajax_url,
                        type    : 'POST',
                        dataType: 'json',
                        data    : data,
                    })
                        .done(function (respon) {
                            if (typeof(respon) == 'object') {
                                if (respon.status == 0) {
                                    $('.form-message', parent).html(respon.message).addClass('error');
                                }
                                if (respon.status == 1) {
                                    $('.form-message', parent).html(respon.message).addClass('success');
                                }
                            }

                        })
                        .fail(function () {
                            alert('Can not save data.');

                        })
                        .always(function () {
                            flag_add = false;

                            //$('.calendar-room', container).fullCalendar('refetchEvents');
                            self.calendarObject.refreshCalendar();

                            $('.overlay', container).removeClass('open');

                        });

                    return false;
                });

                $(document).on('click', '.wb-save-now-section.reload_calender', function () {
                    self.calendarObject.refreshCalendar();
                });
            }
        };


        this.ajaxSavePropertyAvailableFor = function (val, post_id) {
            $('.overlay', self.container).addClass('open');
            if (val == 'specific_periods') {
                self.calendar.addClass('specific_periods');
            } else {
                self.calendar.removeClass('specific_periods');
            }
            // do ajax save the reload the calendar
            $.ajax({
                url     : wpbooking_params.ajax_url,
                data    : {
                    action                : 'wpbooking_save_property_available_for',
                    property_available_for: val,
                    post_id               : post_id
                },
                dataType: 'json',
                type    : 'post',
                success : function () {
                    $('.overlay', self.container).removeClass('open');

                    //self.calendar.fullCalendar( 'refetchEvents' );
                    self.calendarObject.refreshCalendar();
                },
                error   : function (e) {
                    console.log(e.responseText);
                    alert('Can you save the value');
                    $('.overlay', self.container).removeClass('open');
                }
            });

        }

        this.showDateRange  = function () {
            if (self.last_end_date && self.last_start_date) {
                self.calendar.find('.fc-bg .fc-day').removeClass('wb-highlight');
                self.calendar.find('.fc-content-skeleton .fc-day-number').removeClass('wb-highlight');
                var diff = self.last_end_date.diff(self.last_start_date, 'days');
                var temp = self.last_start_date;
                for (i = 1; i <= diff; i++) {
                    self.calendar.find('.fc-bg [data-date=' + temp.format('YYYY-MM-DD') + ']').addClass('wb-highlight');
                    self.calendar.find('.fc-content-skeleton [data-date=' + temp.format('YYYY-MM-DD') + ']').addClass('wb-highlight');
                    temp.add(1, 'day');
                }

                self.calendar.addClass('on-selected');
            }
        }
        this.clearDateRange = function () {

            self.calendar.find('.fc-bg .fc-day').removeClass('wb-highlight');
            self.calendar.find('.fc-content-skeleton .fc-day-number').removeClass('wb-highlight');
            self.calendar.removeClass('on-selected');

        }
    }

    function setCheckInOut(check_in, check_out, form_container) {
        $('#calendar-checkin', form_container).val(check_in);
        $('#calendar-checkout', form_container).val(check_out);
    }

    var room_calendar;
    if ($('.wpbooking-calendar-sidebar .calendar-room-form').length) {
        $('.wpbooking-calendar-sidebar .calendar-room-form').each(function (index, el) {
            var t         = $(this).parents('.wpbooking-calendar-wrapper');
            room_calendar = new RoomCalendar(t);
            room_calendar.init();
        });
    }

    $(document).on('change', '[name=service_type]', function () {
        if ($('.wpbooking-calendar-sidebar .calendar-room-form').length) {
            $('.wpbooking-calendar-sidebar .calendar-room-form').each(function (index, el) {
                var t         = $(this).parents('.wpbooking-calendar-wrapper');
                room_calendar = new RoomCalendar(t);
                room_calendar.init();
            });
        }
    });

    $(document).on('click', '.hotel_room_list .room-edit', function () {
        window.setTimeout(function () {
            if ($('.wpbooking-calendar-sidebar .calendar-room-form').length) {
                $('.wpbooking-calendar-sidebar .calendar-room-form').each(function (index, el) {
                    var t         = $(this).parents('.wpbooking-calendar-wrapper');
                    room_calendar = new RoomCalendar(t);
                    room_calendar.init();
                });
            }
        }, 3000);
    });

    $(document).on('click', '.hotel_room_list .create-room', function () {
        window.setTimeout(function () {
            if ($('.wpbooking-calendar-sidebar .calendar-room-form').length) {
                $('.wpbooking-calendar-sidebar .calendar-room-form').each(function (index, el) {
                    var t         = $(this).parents('.wpbooking-calendar-wrapper');
                    room_calendar = new RoomCalendar(t);
                    room_calendar.init();
                });
            }
        }, 2000);
    });


    /////////////////////////////////
    /////// Select all checkbox /////
    /////////////////////////////////
    $('body').on('change', '.check-all', function (event) {
        var name = $(this).data('name');
        $("input[name='" + name + "[]']").prop('checked', $(this).prop("checked"));
    });


    $('body').on('click', '.wpbooking-calendar-wrapper .calendar-bulk-edit', function (event) {
        var t         = $(this);
        var container = t.closest('.wpbooking-calendar-wrapper');

        if ($('.form-bulk-edit', container).length) {
            $('.form-bulk-edit', container).fadeIn();
        }
        event.preventDefault();
    });

    var flag_save_bulk = false;
    $('body').on('click', '.wpbooking-calendar-wrapper .calendar-bulk-save', function (event) {
        var parent    = $(this).closest('.form-bulk-edit');
        var container = $(this).closest('.wpbooking-calendar-wrapper');

        if (flag_save_bulk) return false;
        flag_save_bulk = true;

        /*  Get values */
        var day_of_week = [];
        $('input[name="day-of-week[]"]:checked', parent).each(function (i) {
            day_of_week[i] = $(this).val();
        });

        var day_of_month = [];
        $('input[name="day-of-month[]"]:checked', parent).each(function (i) {
            day_of_month[i] = $(this).val();
        });

        var months = [];
        $('input[name="months[]"]:checked', parent).each(function (i) {
            months[i] = $(this).val();
        });

        var years = [];
        $('input[name="years[]"]:checked', parent).each(function (i) {
            years[i] = $(this).val();
        });

        var data = {
            'day-of-week'    : day_of_week,
            'day-of-month'   : day_of_month,
            'months'         : months,
            'years'          : years,
            'price_bulk'     : $('input[name="price-bulk"]', parent).val(),
            'adult_bulk'     : $('input[name="adult-bulk"]', parent).val(),
            'child_bulk'     : $('input[name="child-bulk"]', parent).val(),
            'infant_bulk'    : $('input[name="infant-bulk"]', parent).val(),
            'max_people_bulk': $('input[name="max_people_bulk"]', parent).val(),
            'status_bulk'    : $('select[name="status-bulk"]', parent).val(),
            'post_type'      : $('input[name="type-bulk"]', parent).val(),
            'price_type'     : $('input[name="price-type"]', parent).val(),
            'post_id'        : $('.post-bulk', parent).val(),
            'post_encrypt'   : $('input[name="post-encrypt"]', parent).val(),
            'table'          : $('input[name="table"]', parent).val(),
            'action'         : 'wpbooking_calendar_bulk_edit',
            'security'       : wpbooking_params.wpbooking_security
        };

        $('.form-message', parent).html('').removeClass('error updated');
        $('.overlay', parent).addClass('open');

        step_add_bulk('', '', '', '', '', '', '', container, data);

        return false;
    });

    function step_add_bulk(data1, posts_per_page, total, current_page, all_days, post_id, post_encrypt, container, data_first) {
        var data;
        if (typeof(data_first) == 'object') {
            data = data_first;
        } else {
            data = {
                'data'          : data1,
                'posts_per_page': posts_per_page,
                'total'         : total,
                'current_page'  : current_page,
                'all_days'      : all_days,
                'post_id'       : post_id,
                'post_encrypt'  : post_encrypt,
                'table'         : data1.table,
                'action'        : 'wpbooking_calendar_bulk_edit',
                'security'      : wpbooking_params.wpbooking_security
            }
        }

        $.ajax({
            url     : wpbooking_params.ajax_url,
            type    : 'POST',
            dataType: 'json',
            data    : data
        })
            .done(function (respon) {
                if (typeof(respon) == 'object') {
                    if (respon.status == 2) {
                        step_add_bulk(respon.data, respon.posts_per_page, respon.total, respon.current_page, respon.all_days, respon.post_id, respon.post_encrypt, container, '');
                    } else {
                        if (respon.status == 1) {
                            $('.form-bulk-edit .form-message', container).html(respon.message).addClass('updated');
                        } else {
                            $('.form-bulk-edit .form-message', container).html(respon.message).addClass('error');
                        }

                        $('.form-bulk-edit .overlay', container).removeClass('open');
                    }
                }
            })
            .fail(function () {
                console.log("error");
            })
            .always(function () {
                flag_save_bulk = false;
            });
    }
});
