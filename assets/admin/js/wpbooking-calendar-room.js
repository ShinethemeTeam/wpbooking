jQuery(document).ready(function($) {
	$('.date-picker').datepicker({
        dateFormat: "mm/dd/yy",
        beforeShowDay: function(date){
            var d = new Date();
            if( date.getTime() < d.getTime()){
                return [false];
            }else{
                return [true];
            }
        }
    });

    var RoomCalendar = function( container ){
    	var self = this;
		this.container = container;
		this.calendar = null;
		this.form_container = null;
        this.last_start_date=false;
        this.last_end_date=false;
        this.onMouseDown=false;
        this.selectedQueue=[];

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
                //defaultView: 'year',
                //yearColumns: 3,
				selectable: true,
				select : function(start, end, jsEvent, view){
                    self.last_start_date=moment(start._d);
                    self.last_end_date=moment(end._d);
                    var today_object=moment();
                    // Check Past Date
                    if(self.last_end_date.diff(today_object)<0){
                        return false;
                    }
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

                    //Highlight
                    // Check not allow past date
                    if(self.last_start_date.diff(today_object)<0){
                        self.last_start_date=today_object;
                    }
                    self.showDateRange();

				},
                events:function(start, end, timezone, callback) {
                    $.ajax({
                        url: wpbooking_params.ajax_url,
                        dataType: 'json',
                        type:'post',
                        data: {
                            action: 'wpbooking_load_availability',
                            post_id: self.container.data('post-id'),
                            post_encrypt: self.container.data('post-encrypt'),
                            start: start.unix(),
                            end: end.unix(),
                            security: wpbooking_params.wpbooking_security
                        },
                        success: function(doc){
                        	if(typeof doc == 'object'){
                            	callback(doc);

                        	}
                            self.clearDateRange();
                        },
                        error:function(e){
                            alert('Can not get the availability slot. Lost connect with your sever');
                            self.clearDateRange();

                        }
                    });
                },
				eventClick: function(event, element, view){
                    return false;
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

            // Event Click and Select Date Range
            //$(document).on('mousedown','.fc-day',function(){
            //    self.clearDateRange();
            //    var date=$(this).data('date');
            //    if(!date) return;
            //    var moment_object=moment(date);
            //    var today_object=moment();
            //    // Check Past Date
            //    if(moment_object.diff(today_object)<0){
            //        return false;
            //    }
            //
            //    self.calendar.addClass('on-selected');
            //    self.last_start_date=moment_object;
            //    self.onMouseDown=true;
            //
            //});
            //$(document).on('mouseleave','.fc-day',function(){
            //    if(!self.onMouseDown) return false;
            //    $(this).removeClass('wb-highlight');
            //});
            //$(document).on('mouseup',function(){
            //    self.last_start_date=false;
            //    self.onMouseDown=false;
            //});
            //
            //// Event Mouse Move
            //$(document).on('mousemove','.fc-day',function(){
            //    if(!self.last_start_date) return false;
            //    if(!self.onMouseDown) return false;
            //
            //    var date=$(this).data('date');
            //    if(!date) return;
            //    var moment_object=moment(date);
            //    var today_object=moment();
            //    // Check Past Date
            //    if(moment_object.diff(today_object)<0){
            //        return false;
            //    }
            //
            //    if(moment_object.diff(self.last_start_date)){
            //        self.last_end_date=self.last_start_date;
            //        self.last_start_date=moment_object;
            //    }
            //
            //    $(this).addClass('wb-highlight');
            //    self.calendar.find('.fc-content-skeleton [data-date='+ $(this).data('date')+']').addClass('wb-highlight');
            //});
		}

        this.showDateRange=function(){
            if(self.last_end_date && self.last_start_date){
               self.calendar.find('.fc-bg .fc-day').removeClass('wb-highlight');
               self.calendar.find('.fc-content-skeleton .fc-day-number').removeClass('wb-highlight');
               var diff= self.last_end_date.diff(self.last_start_date,'days');
                var temp=self.last_start_date;
               for(i=1;i<=diff; i++){
                   self.calendar.find('.fc-bg [data-date='+ temp.format('YYYY-MM-DD')+']').addClass('wb-highlight');
                   self.calendar.find('.fc-content-skeleton [data-date='+ temp.format('YYYY-MM-DD')+']').addClass('wb-highlight');
                   temp.add(1,'day');
               }

               self.calendar.addClass('on-selected');
            }
        }
        this.clearDateRange=function(){

            self.calendar.find('.fc-bg .fc-day').removeClass('wb-highlight');
            self.calendar.find('.fc-content-skeleton .fc-day-number').removeClass('wb-highlight');
            self.calendar.removeClass('on-selected');

        }
    }

    function setCheckInOut(check_in, check_out, form_container){
		$('#calendar-checkin', form_container).val(check_in);
		$('#calendar-checkout', form_container).val(check_out);
	}

    var room_calendar;
    if( $('.wpbooking-calendar-sidebar .calendar-room-form').length ){
        $('.wpbooking-calendar-sidebar .calendar-room-form').each(function(index, el) {
            var t = $(this).parents('.wpbooking-calendar-wrapper');
            room_calendar = new RoomCalendar( t );
            room_calendar.init();
        });
    }
    if( $( ".st-metabox-tabs" ).length ){
        $( ".st-metabox-tabs" ).tabs({
            activate: function( event, ui ) {
                if( room_calendar ){
                    $('.wpbooking-calendar-wrapper .calendar-room').fullCalendar( 'today' );
                }
            }
        });
    }
    

    var flag_add = false;
    if( $('.wpbooking-calendar-sidebar .calendar-room-form').length ){
    	$('.wpbooking-calendar-sidebar .calendar-room-form #calendar-save').click(function(event) {
    		var container = $(this).parents('.wpbooking-calendar-wrapper');

    		var parent = $(this).parents('.calendar-room-form');

    		var data = {
    			'check_in' : $('#calendar-checkin', parent).val(),
    			'check_out' : $('#calendar-checkout', parent).val(),
    			'price' : $('#calendar-price', parent).val(),
    			'status' : $('#calendar-status', parent).val(),
    			'post-id' : $('#calendar-post-id', parent).val(),
    			'post-encrypt' : $('#calendar-post-encrypt', parent).val(),
    			'action' : 'wpbooking_add_availability',
    			'security': wpbooking_params.wpbooking_security,
                'weekly':$('#calendar-price-week').val(),
                'monthly':$('#calendar-price-month').val(),
                'can_check_in':$('#calendar-can-check-in').val(),
                'can_check_out':$('#calendar-can-check-out').val()
    		}
    		if( flag_add ) return false; flag_add = true;

    		$('.form-message', parent).html('').removeClass('error success');
    		$('.overlay', container).addClass('open');

    		$.ajax({
    			url: wpbooking_params.ajax_url,
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
                room_calendar.clearDateRange();

    		})
    		.fail(function() {
    			alert('Can not save data.');
                room_calendar.clearDateRange();

                })
    		.always(function() {
    			flag_add = false;

    			$('.calendar-room', container).fullCalendar('refetchEvents');

    			$('.overlay', container).removeClass('open');
                room_calendar.clearDateRange();

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
            $(this).closest('.wpbooking-calendar-wrapper').find('.calendar-room').fullCalendar('refetchEvents');
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
            var container = $(this).closest('.wpbooking-calendar-wrapper');

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
                'action' : 'wpbooking_calendar_bulk_edit',
                'security': wpbooking_params.wpbooking_security
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
                'action' : 'wpbooking_calendar_bulk_edit',
                'security': wpbooking_params.wpbooking_security
            }
        }
        
        $.ajax({
            url: wpbooking_params.ajax_url,
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