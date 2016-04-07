jQuery(document).ready(function($) {
	$('.date-picker').datepicker({
        dateFormat: "mm/dd/yy"
    });

    var RoomCalendar = function( container ){
    	var self = this;
		this.container = container;
		this.calendar = null;
		this.form_container = null;

		this.init = function(){
			self.container = container;
			self.calendar = $('.calendar-room', self.container);
			self.form_container = $('.form-container', self.container);
			self.initCalendar();
		}

		this.initCalendar = function(){
			self.calendar.fullCalendar({
				firstDay: 1,
				customButtons: {
			        reloadButton: {
                        text: 'Refresh',
			            click: function() {
			                self.calendar.fullCalendar( 'refetchEvents' );
			            }
			        }
			    },
				header : {
				    left:   'today,reloadButton',
                    center: 'title',
                    right:  'prev, next'
				},
				selectable: true,
				select : function(start, end, jsEvent, view){
					var zone = moment(start._d).format('Z');
							zone = zone.split(':');
							zone = "" + parseInt(zone[0]) + ":00";

					var start_date = moment(start._d).utcOffset(zone).format("MM");
					var end_date = moment(end._d).utcOffset(zone).format("MM");

					var start_year = moment(start._d).utcOffset(zone).format("YYYY");
					var end_year = moment(end._d).utcOffset(zone).format("YYYY");

					var today = moment().format("MM");

					var today_year = moment().format("YYYY");

					if((start_date < today && start_year <= today_year) || (end_date < today && end_year <= today_year)){
						self.calendar.fullCalendar('unselect');
						setCheckInOut('', '', self.form_container);
					}else{
						var check_in = moment(start._d).utcOffset(zone).format("MM/DD/YYYY");
						var	check_out = moment(end._d).utcOffset(zone).subtract(1, 'day').format("MM/DD/YYYY");
						setCheckInOut(check_in, check_out, self.form_container);
					}
				},
				events:function(start, end, timezone, callback) {
                    $.ajax({
                        url: traveler_params.ajax_url,
                        dataType: 'json',
                        type:'post',
                        data: {
                            action: 'traveler_load_availability',
                            post_id: self.container.data('post-id'),
                            post_encrypt: self.container.data('post-encrypt'),
                            start: start.unix(),
                            end: end.unix(),
                            security: traveler_params.traveler_security
                        },
                        success: function(doc){
                        	if(typeof doc == 'object'){
                            	callback(doc);
                        	}
                        },
                        error:function(e){
                            alert('Can not get the availability slot. Lost connect with your sever');
                        }
                    });
                },
				eventClick: function(event, element, view){
                    
				},
				eventRender: function(event, element, view){
					var html = '';
					if(event.status == 'available'){
						html += '<div class="price">Price: '+event.price+'</div>';
						
					}
					if(typeof event.status == 'undefined' || event.status != 'available'){
						html += '<div class="not_available">Not Available</div>';
					}
					$('.fc-content', element).html(html);
				},
                loading: function(isLoading, view){
                    if(isLoading){
                    	$('.overlay', self.container).addClass('open');
                    }else{
                    	$('.overlay', self.container).removeClass('open');
                    }
                },

			});
		}
    }

    function setCheckInOut(check_in, check_out, form_container){
		$('#calendar-checkin', form_container).val(check_in);
		$('#calendar-checkout', form_container).val(check_out);
	}

    if( $('.traveler-calendar-sidebar .calendar-room-form').length ){
    	$('.traveler-calendar-sidebar .calendar-room-form').each(function(index, el) {
    		var t = $(this).parents('.traveler-calendar-wrapper');
    		var calendar = new RoomCalendar( t );
    		calendar.init();
    	});
    	
    }

    var flag_add = false;
    if( $('.traveler-calendar-sidebar .calendar-room-form').length ){
    	$('.traveler-calendar-sidebar .calendar-room-form #calendar-save').click(function(event) {
    		var container = $(this).parents('.traveler-calendar-wrapper');

    		var parent = $(this).parents('.calendar-room-form');

    		var data = {
    			'check_in' : $('#calendar-checkin', parent).val(),
    			'check_out' : $('#calendar-checkout', parent).val(),
    			'price' : $('#calendar-price', parent).val(),
    			'status' : $('#calendar-status', parent).val(),
    			'post-id' : $('#calendar-post-id', parent).val(),
    			'post-encrypt' : $('#calendar-post-encrypt', parent).val(),
    			'action' : 'traveler_add_availability',
    			'security': traveler_params.traveler_security
    		}
    		if( flag_add ) return false; flag_add = true;

    		$('.form-message', parent).html('').removeClass('error success');
    		$('.overlay', container).addClass('open');

    		$.ajax({
    			url: traveler_params.ajax_url,
    			type: 'POST',
    			dataType: 'json',
    			data: data,
    		})
    		.done(function( respon ) {
    			if( typeof( respon ) == 'object' ){
    				if( respon.status == 0 ){
    					$('.form-message', parent).html( respon.message ).addClass( 'error' );
    				}
    				if( respon.status == 1 ){
    					$('.form-message', parent).html( respon.message ).addClass( 'success' );
    				}
    			}
    		})
    		.fail(function() {
    			alert('Can not save data.');
    		})
    		.always(function() {
    			flag_add = false;

    			$('.calendar-room', container).fullCalendar('refetchEvents');

    			$('.overlay', container).removeClass('open');
    		});
    		
    		return false;
    	});
    }



    /////////////////////////////////
    /////// Select all checkbox /////
    /////////////////////////////////
    $('.check-all').change(function(event) {
        var name = $(this).data('name');
        $("input[name='"+ name +"[]']").prop('checked', $(this).prop("checked"));
    });

    if( $('#form-bulk-edit').length ){
        $('#calendar-bulk-close').click(function(event) {
            $(this).closest('#form-bulk-edit').fadeOut();
            $(this).closest('.traveler-calendar-wrapper').find('.calendar-room').fullCalendar('refetchEvents');
        });
    }

    $('#calendar-bulk-edit').click(function(event) {
        if( $('#form-bulk-edit').length ){
            $('#form-bulk-edit').fadeIn();
        }
    });

    var flag_save_bulk = false;
    if( $('#form-bulk-edit').length ){
        $('#calendar-bulk-save').click(function(event) {
            var parent = $(this).closest('#form-bulk-edit');
            var container = $(this).closest('.traveler-calendar-wrapper');

            if( flag_save_bulk ) return false; flag_save_bulk = true;

            /*  Get values */
            var day_of_week = [];
            $('input[name="day-of-week[]"]:checked', parent).each(function(i){
                day_of_week[i] = $(this).val();
            });

            var day_of_month = [];
            $('input[name="day-of-month[]"]:checked', parent).each(function(i){
                day_of_month[i] = $(this).val();
            });

            var months = [];
            $('input[name="months[]"]:checked', parent).each(function(i){
                months[i] = $(this).val();
            });

            var years = [];
            $('input[name="years[]"]:checked', parent).each(function(i){
                years[i] = $(this).val();
            });

            var data = {
                'day-of-week' : day_of_week,
                'day-of-month' : day_of_month,
                'months' : months,
                'years' : years,
                'price_bulk' : $('input[name="price-bulk"]').val(),
                'post_id' : $('input[name="post-id"]', parent).val(),
                'post_encrypt' : $('input[name="post-encrypt"]', parent).val(),
                'action' : 'traveler_calendar_bulk_edit',
                'security': traveler_params.traveler_security
            };

            $('.form-message', parent).html('').removeClass('error success');
            $('.overlay', parent).addClass('open');

            step_add_bulk( '', '', '', '', '', '', '', container, data);
            
            return false;
        });
    }

    function step_add_bulk( data1, posts_per_page, total, current_page, all_days, post_id, post_encrypt, container, data_first ){
        var data;
        if( typeof( data_first) == 'object' ){
            data = data_first;
        }else{
            data = {
                'data' : data1,
                'posts_per_page' : posts_per_page,
                'total' : total,
                'current_page' : current_page,
                'all_days' : all_days,
                'post_id' : post_id,
                'post_encrypt' : post_encrypt,
                'action' : 'traveler_calendar_bulk_edit',
                'security': traveler_params.traveler_security
            }
        }
        
        $.ajax({
            url: traveler_params.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
        })
        .done(function( respon ) {
            if( typeof( respon ) == 'object' ){
                if( respon.status == 2 ){
                    step_add_bulk( respon.data, respon.posts_per_page, respon.total, respon.current_page, respon.all_days, respon.post_id, respon.post_encrypt, container, '');
                }else{
                    $('#form-bulk-edit .form-message', container).html( respon.message ).addClass( 'success' );
                    $('#form-bulk-edit .overlay', container).removeClass('open');
                }
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            flag_save_bulk = false;
        });
        
    }
});