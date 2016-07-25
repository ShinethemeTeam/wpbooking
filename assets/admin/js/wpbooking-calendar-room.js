jQuery(document).ready(function($) {


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
            self.bindEvent();
            //self.calendar.fullCalendar( 'refetchEvents' );
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
                    self.last_start_date=moment(start.format('YYYY-MM-DD'));
                    self.last_end_date=end;
                    var today_object=moment();
                    today_object=moment(today_object.format('YYYY-MM-DD'));
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

					//if((start_date < today && start_year <= today_year) || (end_date < today && end_year <= today_year)){
					//	self.calendar.fullCalendar('unselect');
					//	setCheckInOut('', '', self.form_container);
					//}else{
					//	var check_in = moment(start._d).format("MM/DD/YYYY");
					//	var	check_out = moment(end._d).subtract(1, 'day').format("MM/DD/YYYY");
					//	setCheckInOut(check_in, check_out, self.form_container);
					//}
                    setCheckInOut(self.last_start_date.format('MM/DD/YYYY'), self.last_end_date.format('MM/DD/YYYY'), self.form_container);
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
                            	callback(doc.data);
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

                    setCheckInOut(self.last_start_date.format('MM/DD/YYYY'), self.last_end_date.format('MM/DD/YYYY'), self.form_container);

                    $('#calendar-price').val(event.price);

                    $('#calendar-status option[value='+event.status+']').prop('selected',true);

                    $('#calendar-price-week').val(event.weekly);
                    $('#calendar-price-month').val(event.monthly);
                    if(event.can_check_in){
                        $('.calendar-can-check-in').iCheck('check');
                    }else{
                        $('.calendar-can-check-in').iCheck('uncheck');
                    }

                    if(event.can_check_out){
                        $('.calendar-can-check-out').iCheck('check');
                    }else{
                        $('.calendar-can-check-out').iCheck('uncheck');
                    }

                    // Show Date Range
                    self.last_start_date=moment(event.start);
                    self.last_end_date=moment(event.end);

                    self.showDateRange();

                    return false;
				},
				eventRender: function(event, element, view){
					var html = '';
					if(event.status == 'available'){
                        if(typeof event.price_text!='undefined'){
                            html += '<div class="price"><div class="price-title">'+event.price_text+'</div></div>';
                        }
                        self.calendar.find('.fc-bg [data-date='+ event.start.format('YYYY-MM-DD')+']').removeClass('bg-disable');

					}
					if(typeof event.status == 'undefined' || event.status != 'available'){
						html += '<div class="not_available"></div>';

                        self.calendar.find('.fc-bg [data-date='+ event.start.format('YYYY-MM-DD')+']').addClass('bg-disable');
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

        this.bindEvent=function(){
            $('.st-metabox-nav li>a[href=#st-metabox-tab-item-calendar_tab]').click(function(){
                self.calendar.fullCalendar( 'refetchEvents' );
            });
            $('#calendar-checkin').datepicker({
                dateFormat: "mm/dd/yy",
                beforeShowDay: function(date){
                    var d = new Date();
                    if( date.getTime() < d.getTime()){
                        return [false];
                    }else{
                        return [true];
                    }
                },
                onSelect:function(date_string){
                    var dt=new Date(date_string);
                    var end_dt=new Date($('#calendar-checkout').val());

                    if(dt<=end_dt){
                        self.last_start_date=moment(date_string);
                        self.showDateRange();
                    }else{
                        $('#calendar-checkout').val('');
                        self.last_end_date=false;
                        $('#calendar-checkout').datepicker('show');
                    }

                }
            });
            $('#calendar-checkout').datepicker({
                dateFormat: "mm/dd/yy",
                beforeShowDay: function(date){
                    var d = new Date();
                    if( date.getTime() < d.getTime()){
                        return [false];
                    }else{
                        return [true];
                    }
                },
                onSelect:function(date_string){
                    var dt=new Date($('#calendar-checkin').val());
                    var end_dt=new Date(date_string);
                    if(dt<=end_dt){
                        self.last_start_date=moment(date_string);
                        self.showDateRange();
                    }else{
                        $('#calendar-in').val('');
                        self.last_start_date=false;
                        $('#calendar-checkin').datepicker('show');
                    }
                }
            });

            $('.property_available_forever').on('ifChecked',function() {
                var val = $(this).val();
                // Show Loading
                self.ajaxSavePropertyAvailableFor(val,$(this).data('post-id'));
            });
            $('.property_available_specific').on('ifChecked',function() {
                var val = $(this).val();
                // Show Loading
                self.ajaxSavePropertyAvailableFor(val,$(this).data('post-id'));
            });

            // Check In

        };
        this.ajaxSavePropertyAvailableFor=function(val,post_id){
                $('.overlay', self.container).addClass('open');
                if(val=='specific_periods'){
                    self.calendar.addClass('specific_periods');
                }else{
                    self.calendar.removeClass('specific_periods');
                }
                // do ajax save the reload the calendar
                $.ajax({
                    url:wpbooking_params.ajax_url,
                    data:{
                        action:'wpbooking_save_property_available_for',
                        property_available_for:val,
                        post_id:post_id
                    },
                    dataType:'json',
                    type:'post',
                    success:function(){
                        $('.overlay', self.container).removeClass('open');

                        self.calendar.fullCalendar( 'refetchEvents' );
                    },
                    error:function(e){
                        console.log(e.responseText);
                        alert('Can you save the value');
                        $('.overlay', self.container).removeClass('open');
                    }
                });

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