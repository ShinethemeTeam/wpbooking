/**
 * Created by Administrator on 11/8/2017.
 */
;(function ($, window, document) {
    if (typeof Object.create !== "function") {
        Object.create = function (obj) {
            function F() {
            }

            F.prototype = obj;
            return new F();
        };
    }

    var Inventory = {
        init    : function (options, el) {
            var base          = this;
            base.$elem        = $(el);
            base.curr_options = $.extend({}, $.fn.wpInventory.optionss, options);
            base.gantt        = null;
            base.rooms        = null;
            base.render();
        },
        render  : function (start, end, ajax_url, ajax) {
            var base = this;
            base.setStart(start);
            base.setEnd(end);
            base._fetch(false);
            if (typeof ajax !== 'undefined') {

                $.post(ajax_url, ajax, function (respon) {
                    if (typeof respon == 'object') {
                        base.setRoom(respon.rooms);
                        base._fetch();
                    }
                }, 'json');
            }
        },
        _fetch  : function (loader) {
            if (typeof loader == 'undefined') {
                loader = true;
            }
            var base   = this;
            base.gantt = base.$elem.gantt({
                source      : base.rooms,
                navigate    : "scroll",
                maxScale    : "days",
                itemsPerPage: 20,
                dateStart   : base.startDate,
                dateEnd     : base.endDate,
                loader      : loader,
                onAddClick  : function (dt, row, col) {
                }
            });

            base.gantt = base.gantt.data('Gantt');
        },
        setStart: function (start) {
            var base       = this;
            base.startDate = moment().format();
            if (typeof start != 'undefined') {
                base.startDate = start;
            }
        },
        setEnd  : function (end) {
            var base     = this;
            base.endDate = moment().add(30, 'days').format();
            if (typeof end != 'undefined') {
                base.endDate = end;
            }
        },
        setRoom : function (rooms) {
            var base   = this;
            base.rooms = [];
            base.rooms = rooms;
        }
    };

    $.fn.wpInventory = function (options) {
        return this.each(function () {
            if ($(this).data("inventory-init") === true) {
                return false;
            }
            $(this).data("inventory-init", true);
            var inventory = Object.create(Inventory);
            inventory.init(options, this);
            $.data(this, "Inventory", inventory);
        });
    };

    $.fn.wpInventory.options = {};

    jQuery(document).ready(function ($) {
        'use strict';

        var body           = $('.st-metabox-wrapper');
        var inventory      = $('.wpbooking-inventory', body).wpInventory();
        var inventory_data = inventory.data('Inventory');
        $('a[href="#st-metabox-tab-item-inventory_tab"]').click(function () {
            var start = moment().format();
            var end   = moment().add(30, 'days').format();
            var data  = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment(start).format("YYYY-MM-DD"),
                'end'    : moment(end).format("YYYY-MM-DD"),
                'id_post': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(start, end, wpbooking_params.ajax_url, data);
        });
        $('.wpbooking-inventory', body).on('wpbooking_update_price_inventory', function (ev, start, end) {
            var data = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment(start).format("YYYY-MM-DD"),
                'end'    : moment(end).format("YYYY-MM-DD"),
                'id_post': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(start, end, wpbooking_params.ajax_url, data);
        });
        $('.wpbooking-inventory', body).on('wpbooking_next_month_inventory', function (ev, start, end) {
            var data = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment(end).format("YYYY-MM-DD"),
                'end'    : moment(end).add(30, 'days').format("YYYY-MM-DD"),
                'id_post': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(moment(end).format(), moment(end).add(30, 'days').format(), wpbooking_params.ajax_url, data);
        });
        $('.wpbooking-inventory', body).on('wpbooking_prev_month_inventory', function (ev, start, end) {
            var data = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment(start).subtract(30, 'days').format("YYYY-MM-DD"),
                'end'    : moment(start).format("YYYY-MM-DD"),
                'id_post': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(moment(start).subtract(30, 'days').format(), moment(start).format(), wpbooking_params.ajax_url, data);
        });
        $('.wpbooking-inventory', body).on('wpbooking_now_inventory', function (ev, start, end) {
            var data = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment().format("YYYY-MM-DD"),
                'end'    : moment().add(30, 'days').format("YYYY-MM-DD"),
                'id_post': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(moment().format(), moment().add(30, 'days').format(), wpbooking_params.ajax_url, data);
        });

        var form = $('.wpbooking-inventory-form', body);

        var check_out = $('.wpbooking-inventory-end', form).datepicker({
            dateFormat: 'yy-mm-dd'
        });
        console.log(check_out);

        var check_in = $('.wpbooking-inventory-start', form).datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect  : function (selected) {
                console.log(selected);
                var m    = new moment(selected, 'YYYY-MM-DD');
                selected = m.format('YYYY-MM-DD');
                check_out.datepicker("option", "minDate", selected);
                window.setTimeout(function () {
                    check_out.datepicker("show");
                }, 100);

            }
        });


        var goto = $('.wpbooking-inventory-goto', form).click(function (ev) {
            ev.preventDefault();
            var start = check_in.val();
            var end   = check_out.val();
            if (start != '' && end != '') {
                var data = {
                    'action' : 'fetch_inventory_accommodation',
                    'start'  : moment(start).format("YYYY-MM-DD"),
                    'end'    : moment(end).format("YYYY-MM-DD"),
                    'id_post': $('.wpbooking-inventory', body).data('id')
                };
                inventory_data.render(moment(start).format(), moment(end).format(), wpbooking_params.ajax_url, data);
            }
        });

        $('.calendar-bulk-close').click(function (ev) {
            ev.preventDefault();
            $(this).closest('.form-bulk-edit').fadeOut();
            var data = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment().format("YYYY-MM-DD"),
                'end'    : moment().add(30, 'days').format("YYYY-MM-DD"),
                'id_post': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(moment().format(), moment().add(30, 'days').format(), wpbooking_params.ajax_url, data);
        });
    });
})(jQuery, window, document);