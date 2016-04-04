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
					
					
				},
				events:function(start, end, timezone, callback) {
                    
                },
				eventClick: function(event, element, view){
                    
				},
				eventRender: function(event, element, view){
					
				},
                loading: function(isLoading, view){
                    
                },

			});
		}
    }

    if( $('.traveler-calendar-wrapper').length ){
    	$('.traveler-calendar-wrapper').each(function(index, el) {
    		var t = $(this);
    		var calendar = new RoomCalendar( t );
    		calendar.init();
    	});
    	
    }
});