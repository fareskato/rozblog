/*
* @author Fares
* this file will handle the ajax request for the stock calendar
* */
 jQuery("document").ready(function ($) {

// handle the ajax request
     function loadAjaxCalendar (evt, eltClicked, extraData) {
         var product_ID =  $(eltClicked).attr('id');            /* get product id */
         var parameters = {
             // define the action (in feyarose_orders.php ).
             action : 'get_stock_modal',
             // send the variable to the action(the function feyarose_orders.php).
             'ID': product_ID
         };
         if(extraData !== undefined) {
             for(var d in extraData) {
                 parameters[d] = extraData[d];
             }
         }
         //console.log(parameters);
         $.ajax({
             url: ajaxurl,
             type: 'POST',
             dataType: 'json',
             data: parameters,
             success: function(response){
                 $('#rozblog_stock_modal').find('.modal-body').html(response);
                 $('#rozblog_stock_modal').modal({backdrop: false , keyboard: false});
                 // handle the next and prev links
                 $('.cal-next, .cal-prev').on('click',function(e){
                     var $this = jQuery(this);
                     loadAjaxCalendar(e,this, {'ID': $this.data('productid'), 'year': $this.data('year'), 'month':$this.data('month')});
                     e.preventDefault();
                 });  // End on click
             },
             error: function(error){
                 console.log(error);
             }
         });
     }
     // handle the main popup
    $('.view-modal').on('click',function(event){
        event.preventDefault();
        loadAjaxCalendar(event,this);
    });


});  // End document ready

