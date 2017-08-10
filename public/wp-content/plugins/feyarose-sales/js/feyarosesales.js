/**
 * Created by fares-altima on 15.06.2016.
 */
(function($) {
    $(document).ready(function () {
// **********************************
        //  Description of ALL pager options
        // **********************************
        var pagerOptions = {
            // target the pager markup - see the HTML block below
            container: $(".pager"),
            output: '{startRow:input} to {endRow} ({totalRows})',
            // apply disabled classname (cssDisabled option) to the pager arrows when the rows
            // are at either extreme is visible; default is true
            updateArrows: true,
            // starting page of the pager (zero based index)
            page: 0,
            // Number of visible rows - default is 10
            size: 10,
            // Saves tablesorter paging to custom key if defined.
            // Key parameter name used by the $.tablesorter.storage function.
            // Useful if you have multiple tables defined
            storageKey:'tablesorter-pager',
            // Reset pager to this page after filtering; set to desired page number (zero-based index),
            // or false to not change page at filter start
            pageReset: 0,
            // css class names of pager arrows
            cssNext: '.next', // next page arrow
            cssPrev: '.prev', // previous page arrow
            cssFirst: '.first', // go to first page arrow
            cssLast: '.last', // go to last page arrow
            cssGoto: '.gotoPage', // select dropdown to allow choosing a page
            cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
            cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option
        };
        // Initialize tablesorter
        $("#sales_table").tablesorter({
                theme: 'default',
            })
            // bind to pager events
            .bind('pagerChange pagerComplete pagerInitialized pageMoved', function(e, c){
                var msg = '"</span> event triggered, ' + (e.type === 'pagerChange' ? 'going to' : 'now on') +
                    ' page <span class="typ">' + (c.page + 1) + '/' + c.totalPages + '</span>';
                $('#display')
                    .append('<li><span class="str">"' + e.type + msg + '</li>')
                    .find('li:first').remove();
            })
            // initialize the pager plugin
            .tablesorterPager(pagerOptions);
        // go to page 1 showing 10 rows
        $('.goto').click(function(){
            $('table').trigger('pageAndSize', [1, 10]);
        });
        
        $('.filter_date').datepicker({
            dateFormat : 'yy-mm-dd'
        });
    });
})(jQuery);