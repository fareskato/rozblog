
jQuery(document).ready(function() {
  jQuery(".tab_content").hide();
  jQuery("ul.tabs li:first").addClass("active").show();
  jQuery(".tab_content:first").show();
  jQuery("ul.tabs li").click(function() {
    jQuery("ul.tabs li").removeClass("active");
    jQuery(this).addClass("active");
    jQuery(".tab_content").hide();
    var activeTab = jQuery(this).find("a").attr("href");
    jQuery(activeTab).show();
    return false;
  });
});

var result;

jQuery(function () {
    'use strict';

    var url = ajaxurl + '?action=upload_csv';
    jQuery('#fileupload').fileupload({
        url: ajaxurl,
        action: 'upload_csv',
        dataType: 'json',
        send: function (e, data) {
          jQuery('#step3,#step4,#step5').addClass('hidden');
        },
        done: function (e, data) {
          result = data.result
          jQuery("#step3").removeClass('hidden');
          jQuery("#step4, #step5").addClass('hidden');

          if(result.warning){
             jQuery.magnificPopup.open({items: { src: '<div class="red-popup popup"><b>Warning: ' + result.warning + '</b></div>'}, type: 'inline'}, 0);
             if(result.warning.match(/check delimiter/i)) jQuery("#step3").addClass('hidden');

          }else{
            jQuery.magnificPopup.open({items: { src: '<div class="white-popup popup"><b>Upload Successful!</b><br>Next, map the product attributes to the CSV columns and click <b>Continue</b></div>'}, type: 'inline'}, 0);
            jQuery('#file_name').text(result['file']['name']);

          }
          jQuery("#choose_columns table").html(result.html);
      },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !jQuery.support.fileInput).parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');

  jQuery( "#step3_continue").click(function(){
    jQuery("#step4").removeClass('hidden');
  });

  jQuery( "#run_test").click(function(){
    var body = {
      action: 'run_test',
      file: result.file,
      delimiter: jQuery('[name="delimiter"]').val(),
      chunk_size: jQuery('[name="chunk_size"]').val(),
      sku_column: jQuery('[name="sku_column"]').val(),
      column_to_update: jQuery('[name="column_to_update"]').val(),
      ctu_column: jQuery('[name="ctu_column"]').val()

    }
    jQuery.post(ajaxurl, body, function(response) {

      if(response['status'] == 'success'){

        jQuery.magnificPopup.open({items: { src: '<div class="white-popup popup"><b>Test Successful!</b><br>' + response['affected_rows'] + ' products will be affected.<br>Tests ran successfully in ' + response['elapsed_time'] + ' seconds.<br><i>Now you\'re ready to update.</i> Click <b>Update</b> to continue.</div>'}, type: 'inline'}, 0);
        jQuery("#step5").removeClass('hidden');

      }else{
        jQuery.magnificPopup.open({items: { src: '<div class="red-popup popup"><b>Warning: something went wrong with the test.</b><br>Try increasing the chunk size and if that doesn\'t work you can post a message on the plugin board.</div>'}, type: 'inline'}, 0);
        jQuery("#step5").addClass('hidden');

      }
    });
  });

  jQuery( "#update").click(function(){
    var body = {
      action: 'run_update',
      file: result.file,
      delimiter: jQuery('[name="delimiter"]').val(),
      chunk_size: jQuery('[name="chunk_size"]').val(),
      sku_column: jQuery('[name="sku_column"]').val(),
      column_to_update: jQuery('[name="column_to_update"]').val(),
      ctu_column: jQuery('[name="ctu_column"]').val()
    }

    jQuery.post(ajaxurl, body, function(response) {
      if(response['status'] == 'success'){
        jQuery.magnificPopup.open({items: { src: '<div class="white-popup popup"><b>Update Successful!</b><br>' + response['affected_rows'] + ' products were affected.<br>Update ran successfully in ' + response['elapsed_time'] + ' seconds.<br><br><a id="update_schedule" class="button primary button-primary">Click Here to Schedule this Import as a Daily Task</a></div>'}, type: 'inline'}, 0);
        jQuery("#step3, #step4, #step5").addClass('hidden');

      }else{
        jQuery.magnificPopup.open({items: { src: '<div class="red-popup popup"><b>Warning: something went wrong with the update.</b><br>Try increasing the chunk size and if that doesn\'t work you can post a message on the plugin board.</div>'}, type: 'inline'}, 0);
        jQuery("#step5").addClass('hidden');

      }
    });
  });

  jQuery('#clear_schedule').click(function(){
    var body = {
      action: 'clear_schedule',
    }
    jQuery.post(ajaxurl, body, function(response) {
      if(response['status'] == 'success'){
        jQuery.magnificPopup.open({items: { src: '<div class="white-popup popup"><b>Schedule Cleared Successfully!</b></div>'}, type: 'inline'}, 0);
        jQuery('#schedule_table td').text('');
      }else{
        jQuery.magnificPopup.open({items: { src: '<div class="red-popup popup"><b>Warning: something went wrong.</div>'}, type: 'inline'}, 0);
      }
    });
  });

  jQuery('#save_schedule').click(function(){
    var body = {
      action: 'update_schedule',
      source: jQuery('#schedule_table td:nth-child(1)').text(),
      delimiter: jQuery('#schedule_table td:nth-child(2)').text(),
      chunk_size: jQuery('#schedule_table td:nth-child(3)').text(),
      sku_column: jQuery('#schedule_table td:nth-child(4)').text(),
      column_to_update: jQuery('#schedule_table td:nth-child(5)').text(),
      ctu_column: jQuery('#schedule_table td:nth-child(6)').text(),
      next_event: jQuery('#schedule_table td:nth-child(7)').text()
    }

    jQuery.post(ajaxurl, body, function(response) {
      if(response['source']){
        jQuery.magnificPopup.open({items: { src: '<div class="white-popup popup"><b>Schedule Saved Successfully!</b></div>'}, type: 'inline'}, 0);
      }else{
        jQuery.magnificPopup.open({items: { src: '<div class="red-popup popup"><b>Warning: something went wrong.</div>'}, type: 'inline'}, 0);
      }
    });
  });

  jQuery('#schedule_table').editableTableWidget();

  jQuery('body').on('click', '#update_schedule', function () {
    var body = {
      action: 'update_schedule',
      file: result.file,
      delimiter: jQuery('[name="delimiter"]').val(),
      chunk_size: jQuery('[name="chunk_size"]').val(),
      sku_column: jQuery('[name="sku_column"]').val(),
      column_to_update: jQuery('[name="column_to_update"]').val(),
      ctu_column: jQuery('[name="ctu_column"]').val()
    }
    jQuery.post(ajaxurl, body, function(response) {
      if(response['source']){
        update_schedule(response);
        jQuery('button.mfp-close').trigger('click')
        jQuery("ul.tabs li:eq(1)").trigger('click')
      }else{
        jQuery.magnificPopup.open({items: { src: '<div class="red-popup popup"><b>Warning: something went wrong with the schedule update.</b></div>'}, type: 'inline'}, 0);
        jQuery("#step5").addClass('hidden');
      }
    });
  });

  // schedule
  function update_schedule(vars){
    if(!vars) return;
    jQuery('#schedule_table td:nth-child(1)').text(vars['source'])
    jQuery('#schedule_table td:nth-child(2)').text(vars['delimiter'].replace('\\\\','\\'))
    jQuery('#schedule_table td:nth-child(3)').text(vars['chunk_size'])
    jQuery('#schedule_table td:nth-child(4)').text(vars['sku_column'])
    jQuery('#schedule_table td:nth-child(5)').text(vars['column_to_update'])
    jQuery('#schedule_table td:nth-child(6)').text(vars['ctu_column'])
    jQuery('#schedule_table td:nth-child(7)').text(next_import)
  }

  update_schedule(wdu_options['daily_task']);

  // chunk size slider
  jQuery( "#slider" ).slider({
    value: wdu_options['chunk_size'] ,
    change: function(event, ui) { 
      jQuery('[name="chunk_size"]').val(ui.value);
    } 
  });


});