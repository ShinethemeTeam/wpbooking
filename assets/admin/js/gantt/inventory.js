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
            console.log('f');
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
            base.endDate = moment().add(1, 'month').format();
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

        var body           = $('body');
        var inventory      = $('.wpbooking-inventory', body).wpInventory();
        var inventory_data = inventory.data('Inventory');
        $('a[href="#st-metabox-tab-item-inventory_tab"]').click(function () {
            var start = moment().format();
            var end   = moment().add(1, 'month').format();
            var data  = {
                'action' : 'fetch_inventory_accommodation',
                'start'  : moment(start).format("YYYY-MM-DD"),
                'end'    : moment(end).format("YYYY-MM-DD"),
                'post_id': $('.wpbooking-inventory', body).data('id')
            };
            inventory_data.render(start, end, wpbooking_params.ajax_url, data);
        });
        $('.wpbooking-inventory', body).on('wpbooking_update_price_inventory', function (ev, start, end) {
            inventory_data.render(start, end);
        });
    });
})(jQuery, window, document);