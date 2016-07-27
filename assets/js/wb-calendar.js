/**
 * Created by Dungdt on 7/25/2016.
 */
(function($){
    $.fn.wbCalendar=function(options ){
        var self=this;
        var settings = $.extend({
            prevText:'<i class="fa fa-chevron-left"></i>',
            nextText:'<i class="fa fa-chevron-right"></i>',
            dayClick:function(){},
            sourceBinding:function(){},
            sourceRender:function(){},
            onSelectionRange:function(){},
            debug:false
        }, options );

        this.calendarDiv=false;

        this.currentYear=false;
        this.currentMonth=false;

        this.sourceData={};// Store Current Month Source


        // Selection Params
        this.startSelectionEl=false;
        this.endSelectionEl=false;
        this.onMouseDown=false;
        this.iconSelectionPrev=false;
        this.iconSelectionNext=false;

        this.iconSelectionDown=false;

        this.init=function(){
            var current=moment();
            self.currentMonth=current.format('M');
            self.currentYear=current.format('YYYY');

            self.drawCalendarWrap();

            // Bind Events
            self.bindEvent();
        };


        this.drawCalendarWrap=function(){
            var month=self.currentMonth;
            var year=self.currentYear;
            var dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thrusday", "Friday", "Saturday"];

            var wb_right='<div class="wb-head-right"><a href="#" class="wb-prev-month button" onclick="return false">'+settings.prevText+'</a><a href="#" class="wb-next-month button" onclick="return false">'+settings.nextText+'</a></div>';

            // Outputing the calendar onto the site.  Also, putting in the month name and days of the week.
            var calendarTable = "<div class='wb-calendar'> <div class='wb-heading'><div class='wb-center'></div>"+wb_right+"</div>";
            calendarTable += "<ul class='wb-weekdays'>  <li>Sun</li>  <li>Mon</li> <li>Tues</li> <li>Wed</li> <li>Thu</li> <li>Fri</li> <li>Sat</li> </ul>";
            calendarTable += "<ul class='wb-days'>";
            calendarTable += "</ul>" +
                "<span class='wb-icon-select wb-icon-prev'><i class='fa fa-angle-double-left'></i></span>" +
                "<span class='wb-icon-select wb-icon-next'><i class='fa fa-angle-double-right'></i></span>" +
                "</div>";
            self.html(calendarTable);

            self.drawCalendarDays();

            self.calendarDiv=self.find('.wb-calendar');
            self.iconSelectionPrev=self.find('.wb-icon-prev');
            self.iconSelectionNext=self.find('.wb-icon-next');
        };

        this.drawCalendarDays=function(){

            var monthNames = ["Jan", "Feb", "March", "April", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"];
            var month=parseInt(self.currentMonth);
            var year=parseInt(self.currentYear);
            var current = new Date();
            var day = current.getDate();
            var current_moment=moment();
            current_moment=moment(current_moment.format('YYYY-MM-DD'));
            var totalFeb = "";
            var padding='';
            var tempDay='';
            var tempMonth = parseInt(month) + 1; //+1; //Used to match up the current month with the correct start date.
            var prevMonth = parseInt(month) - 1;
            var tempYear=year;
            if(prevMonth==0){
                prevMonth=12;
                tempYear--;
            }
            var i = 1;
            // Temp values to get the number of days in current month, and previous month. Also getting the day of the week.
            var tempDate = new Date(month + ' 1 ,' + year);
            var tempweekday = tempDate.getDay();
            var tempweekday2 = tempweekday;
            var dayAmount =self.getDaysAmount(month,year);

            var allDays=[];
            // Prev Years
            var prevMonthDays=self.getDaysAmount(prevMonth,tempYear);
            if(prevMonth<10){
                prevMonth='0'+prevMonth;
            }
            while (tempweekday > 0) {
                tempDay=(prevMonthDays-tempweekday+1);
                if(tempDay<10){
                    tempDay='0'+tempDay;
                }
                allDays.push(moment(tempYear+'-'+prevMonth+'-'+tempDay));

                tempweekday--;
            }
             //Filling in the calendar with the current month days in the correct location along.
            // current Month
            var month_string=month;
            if(month_string<10){
                month_string='0'+month_string;
            }
            while (i <= dayAmount) {
                var i_string=i;
                if(i_string<10){
                    i_string='0'+i_string;
                }
                allDays.push(moment(year+'-'+month_string+'-'+i_string));
                i++;
            }
            // Next Month, reset before calculate
            tempYear=year;
            if(tempMonth==13){
                tempMonth=1;
                tempYear=parseInt(year)+1;
            }
            tempDate = new Date(month + ' '+dayAmount+' ,' + year);
            tempweekday = 6-tempDate.getDay();
            if(tempDate.getDay()<6){
                if(tempMonth<10){
                    tempMonth='0'+tempMonth;
                }
                for(var i=0;i<tempweekday;i++){

                    tempDay=i+1;
                    if(tempDay<10){
                        tempDay='0'+tempDay;
                    }
                    allDays.push(moment(tempYear+'-'+tempMonth+'-'+tempDay));
                }
            }


            if(allDays.length){
                for(var i=0;i<allDays.length;i++){
                    var moment_object=allDays[i];
                    var day_object=$('<li/>');
                    var diff_today=moment_object.diff(current_moment,'days');
                    day_object.append('<span class="day-title">'+moment_object.format('D')+'</span>');
                    day_object.addClass('wb-day');

                    day_object.attr('data-date',moment_object.format('YYYY-MM-DD'));

                    if (moment_object.format('') == day) {
                        day_object.addClass('wb-current-day');
                    }
                    if(diff_today<0){
                        day_object.addClass('wb-past');
                    }else{
                        if(diff_today==0){
                            day_object.addClass('wb-current-day');
                        }

                        day_object.addClass('wb-enable');
                    }

                    padding+=day_object[0].outerHTML;
                }
            }
            self.find('.wb-days').html(padding);
            self.find('.wb-heading .wb-center').html(monthNames[month-1] + " " + year);
            // Start Append Source
            settings.sourceBinding(allDays[0].format('YYYY-MM-DD'),allDays[allDays.length-1].format('YYYY-MM-DD'),self.sourceHandler);
        };

        this.drawSelection=function(){

            if(!self.startSelectionEl || !self.endSelectionEl) return false;
            if(self.startSelectionEl.index()==self.endSelectionEl.index()){
                self.startSelectionEl.addClass('wb-highlight');
                self.startSelectionEl.siblings().removeClass('wb-highlight');

            }else{
                // If We Select DESC
                if(self.startSelectionEl.index()>self.endSelectionEl.index()){
                    self.startSelectionEl.addClass('wb-highlight');
                    self.startSelectionEl.prevUntil(self.endSelectionEl).addClass('wb-highlight');
                    self.endSelectionEl.addClass('wb-highlight');

                    self.find('.wb-day:nth-child(n+'+(self.startSelectionEl.index()+2)+')').removeClass('wb-highlight');
                    self.find('.wb-day:nth-child(-n +'+(self.endSelectionEl.index())+')').removeClass('wb-highlight');
                }else{
                    // If We Select ASC
                    self.startSelectionEl.addClass('wb-highlight');
                    self.startSelectionEl.nextUntil(self.endSelectionEl).addClass('wb-highlight');
                    self.endSelectionEl.addClass('wb-highlight');

                    self.find('.wb-day:nth-child(n+'+(self.endSelectionEl.index()+2)+')').removeClass('wb-highlight');
                    self.find('.wb-day:nth-child(-n+'+(self.startSelectionEl.index()-1)+')').removeClass('wb-highlight');


                }
            }

            self.calendarDiv.addClass('on-selected');

            settings.onSelectionRange(self.startSelectionEl,self.endSelectionEl);

            // Draw Icon Selection
            self.drawIconSelection();
        };
        this.drawIconSelection=function(){
            if(self.startSelectionEl && self.endSelectionEl){
                var prev_left,prev_top,next_left,next_top;

                if(self.startSelectionEl.index()==self.endSelectionEl.index()){
                    prev_left=self.startSelectionEl.position().left-12;
                    prev_top=self.startSelectionEl.position().top+(self.startSelectionEl.height()/2)+self.find('.wb-heading').height()+self.find('.wb-weekdays').height()-20;

                    next_left=prev_left+self.startSelectionEl.width()+4;
                    next_top=prev_top;
                }else{
                    if(self.startSelectionEl.index()>self.endSelectionEl.index()){
                        prev_left=self.endSelectionEl.position().left-12;
                        prev_top=self.endSelectionEl.position().top+(self.endSelectionEl.height()/2)+self.find('.wb-heading').height()+self.find('.wb-weekdays').height()-20;

                        next_left=self.startSelectionEl.position().left-12+self.startSelectionEl.width()+4;
                        next_top=self.startSelectionEl.position().top+(self.startSelectionEl.height()/2)+self.find('.wb-heading').height()+self.find('.wb-weekdays').height()-20;
                    }else{
                        prev_left=self.startSelectionEl.position().left-12;
                        prev_top=self.startSelectionEl.position().top+(self.startSelectionEl.height()/2)+self.find('.wb-heading').height()+self.find('.wb-weekdays').height()-20;

                        next_left=self.endSelectionEl.position().left-12+self.endSelectionEl.width()+4;
                        next_top=self.endSelectionEl.position().top+(self.endSelectionEl.height()/2)+self.find('.wb-heading').height()+self.find('.wb-weekdays').height()-20;
                    }
                }

                self.iconSelectionPrev.css({
                    'left':prev_left,
                    'top':prev_top
                });
                self.iconSelectionNext.css({
                    'left':next_left,
                    'top':next_top
                });

            }
        }

        this.getDaySource=function(day_element){
            var key='wb-source';
            if(day_element.length && key in day_element[0]){
                return day_element[0]['wb-source']
            }

            return false;
        }

        this.bindEvent=function(){
            self.find('.wb-prev-month').click(function(){
                self.goPrevMonth();
            });
            self.find('.wb-next-month').click(function(){
                self.goNextMonth();
            });

            // Day Click
            self.on('click','.wb-days .wb-day',function(){
                var source=self.getDaySource($(this));
                self.log('Source:');
                self.log(source);
                settings.dayClick($(this),source);
            });

            // Start Selection
            self.on('mousedown','.wb-days .wb-day.wb-enable',function(){
                self.log('down');
                self.onMouseDown=true;
                self.startSelectionEl=$(this);
                self.endSelectionEl=self.startSelectionEl;
                self.find('.wb-day').removeClass('wb-highlight');
                self.drawSelection();
            });

            // On Drag Selection
            self.on('mouseenter','.wb-days .wb-day.wb-enable',function(){
                self.log('drag');
                self.endSelectionEl=$(this);
                if(self.onMouseDown && self.startSelectionEl.length && self.endSelectionEl.length &&self.startSelectionEl.index()!=self.endSelectionEl.index()){
                    self.drawSelection();
                }
                if(self.onMouseDown && self.startSelectionEl.length && self.startSelectionEl.index()==$(this).index()){
                    self.endSelectionEl.siblings().removeClass('wb-highlight');
                    self.drawIconSelection();
                }

            });

            // End Selection
            self.on('mouseup','.wb-days .wb-day.wb-enable',function(){

                self.endSelectionEl=$(this);
                if(self.onMouseDown && self.startSelectionEl.index()!=self.endSelectionEl.index()){
                    self.drawSelection();
                }
                self.onMouseDown=false;
            });

            // Drag Icon Selection

            self.on('mousedown',self.iconSelectionPrev,function(){
                self.log('Icon Selection Mouse Down');
                self.onMouseDown=true;
            });self.on('mousedown',self.iconSelectionNext,function(){
                self.log('Icon Selection Mouse Down');
                self.onMouseDown=true;
            });
            self.on('mouseup',self.iconSelectionPrev,function(){
                self.log('Icon Selection Mouse Up');
                self.onMouseDown=false;
            });
            self.on('mouseup',self.iconSelectionNext,function(){
                self.log('Icon Selection Mouse Up');
                self.onMouseDown=false;
            });
        };

        this.sourceHandler=function(source){
            if(typeof source !=undefined && source.length){
                for(var i=0;i<source.length;i++){
                    var element=self.find('.wb-day[data-date='+source[i].start+']');
                    if(element.length){
                        element.addClass(source[i].status);
                        settings.sourceRender(source[i],element);
                        element[0]['wb-source']=source[i];
                    }

                }
            }
            self.sourceData=source;
        }


        this.clearSelection=function(){
            self.calendarDiv.removeClass('on-selected');
            self.find('.wb-day').removeClass('wb-highlight');
            //self.iconSelectionNext.css('')
        }

        this.goNextMonth=function(){
            self.currentMonth++;
            if(self.currentMonth==13){
                self.currentMonth=1;
                self.currentYear++;
            }
            self.drawCalendarDays();
            self.clearSelection();
        };

        this.goPrevMonth=function(){
            self.currentMonth--;
            if(self.currentMonth==0){
                self.currentMonth=12;
                self.currentYear--;
            }
            self.drawCalendarDays();
            self.clearSelection();
        };

        /**
         * Refresh Calendar and Source
         *
         * @since 1.0
         * @author dungdt
         */
        this.refreshCalendar=function(){
            self.drawCalendarDays();
            self.clearSelection();
        }

        /**
         * If Debug Options is on, then console.log the string
         *
         * @since 1.0
         * @author dungdt
         *
         * @param str
         */
        this.log=function(str){
            if(settings.debug){
                console.log(str);
            }
        }


        /**
         * Get Number of Days in a month and year
         *
         * @since 1.0
         * @author dungdt
         *
         * @param month
         * @param year
         * @returns {string}
         */
        this.getDaysAmount=function(month,year){
            month=parseInt(month);
            year=parseInt(year);
            var totalFeb = "";
            if (month == 2) {
                if ((year % 100 !== 0) && (year % 4 === 0) || (year % 400 === 0)) {
                    totalFeb = 29;
                } else {
                    totalFeb = 28;
                }
            }
            var totalDays = ["31", "" + totalFeb + "", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31"];

            return totalDays[month-1];

        }

        this.init();

        return this;
    }
})(jQuery);