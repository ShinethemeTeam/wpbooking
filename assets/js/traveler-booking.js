/**
 * Created by Dungdt on 3/30/2016.
 */
jQuery(document).ready(function($){
    $('.traveler-rating-review a').hover(function(){
        var index=$(this).index();
        index=parseInt(index);

        $(this).addClass('active');
        $(this).prevAll().addClass('active');
        $(this).nextAll().removeClass('active');

        $(this).closest('.traveler-rating-review').find('.traveler_review_detail_rate').val(index+1);

        var totalRate=0;
        var rateStats=$('.traveler_review_detail_rate');
        if(rateStats.length){
            rateStats.each(function(){
                totalRate+=parseInt($(this).val());
            });
            $('[name=traveler_review]').val(parseFloat(totalRate/rateStats.length));
        }else{
            $('[name=traveler_review]').val(index+1);
        }


    });

});