<?php function wdu_tools_page(){

wp_enqueue_style('wf-bootstrap-style', 'bootstrap.min.css');
wp_enqueue_style('jquery-ui.min.css', WDU_URL . 'css/jquery-ui.min.css');
wp_enqueue_style('jquery.fileupload', WDU_URL . 'css/jquery.fileupload.css');
wp_enqueue_style('magnificent.popup.css', WDU_URL . 'css/popup.css');

// wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
wp_enqueue_script('jquery-2.1.1.min.js', WDU_URL . 'js/jquery-2.1.1.min.js', array(), '2.1.1', true );
wp_enqueue_script('jquery-ui.js', WDU_URL . 'js/jquery-ui.js', array(), '1.0.0', true );
wp_enqueue_script('jquery.ui.widget', WDU_URL . 'js/jquery.ui.widget.js', array(), '1.0.0', true );
wp_enqueue_script('fileupload', WDU_URL . 'js/jquery.fileupload.js', array(), '1.0.0', true );
wp_enqueue_script('iframe-transport', WDU_URL . 'js/jquery.iframe-transport.js', array(), '1.0.0', true );
wp_enqueue_script('magnificent.popup.js', WDU_URL . 'js/popup.js', array(), '1.0.0', true );
wp_enqueue_script('js-wdu', WDU_URL . 'js/js-wdu.js', array(), '1.0.0', true );
wp_enqueue_script('editable_table.js', WDU_URL . 'js/editable_table.js', array(), '1.0.0', true );

?>
<script type="text/javascript">
var wdu_options = <?php echo json_encode(get_option('wdu_options')) ?>;
var ajax_url = '<?php echo plugins_url( "ajax.php", __FILE__ ); ?>';
var next_import = '<?php echo  date("H:i:s", wp_next_scheduled("wdu_daily_import")); ?>'
</script>
<style type="text/css">
.container {width: 100%; margin: 10px 0px; font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;}
ul.tabs {margin: 0;padding: 0;float: left;list-style: none;height: 25px;border-bottom: 1px solid #e3e3e3;border-left: 1px solid #e3e3e3;width: 100%;}
ul.tabs li {float: left;margin: 0;padding: 0;	height: 24px;line-height: 24px;border: 1px solid #e3e3e3;border-left: none;margin-bottom: -1px;background:#EBEBEB;overflow: hidden;position: relative; background-repeat:repeat-x;}
ul.tabs li a {text-decoration: none;color: #21759b;display: block;font-size: 12px;padding: 0 20px;border: 1px solid #fff;outline: none;}
ul.tabs li a:hover {color: #d54e21;}
html ul.tabs li.active, html ul.tabs li.active a:hover  {background: #fff;border-bottom: 1px solid #fff;}
.tab_container {border: 1px solid #e3e3e3;border-top: none;clear: both;float: left; width: 100%;background: #fff;font-size:11px;}
.tab_content {padding: 20px;font-size: 1.2em;}
.tab_content h3 {margin-top:0px;margin-bottom:10px;}
.tab_content .head-description{font-style:italic;}
.tab_content .description{padding-left:15px}
.tab_content ul li{list-style:square outside; margin-left:20px}
li.m3{ display: inline-block; width: 250px }
.sub-item{margin-top:3px}
.alert{background-color: #ffd5cc !important; width:100%; border:solid 1px red; padding:3px 10px 3px 10px}
.notice{background-color: #ccffcc !important; width:100%; border:solid 1px green; padding:3px 10px 3px 10px}
#progress{width:500px}
#choose_columns table td{border:0px;margin:0px;padding:2px 20px 0px 0px}
[name=delimiter]{width:24px; margin-left:0px}
#step3_continue{margin-top:10px}
.popup {
  position: relative;
  background: #FFF;
  padding: 20px;
  width: auto;
  max-width: 500px;
  margin: 20px auto;
}
#file_name{width:200px;position: relative; top:-8px; left:20px; font-size:16px; font-weight: bold}
.white-popup { background: #FFF }
.red-popup { background: red ; color: white}
#stock_template,#prices_template,#clear_schedule{margin-left:10px}
#slider{width:300px; border: solid gray 1px}
#schedule_table td:nth-child(1){width:50%}
#schedule_table td:nth-child(2){width:10%}
#schedule_table td:nth-child(3){width:10%}
#schedule_table td:nth-child(4){width:10%}
#schedule_table td:nth-child(5){width:10%}
#schedule_table td:nth-child(6){width:10%}
#schedule_table{padding:5px}
#schedule_table td{height:24px;font-size:16px}

</style>
<div class="wrap">

<h2><?php _e('WC Daily Update'); ?></h2>
<div class="container">
    <ul class="tabs">
            <li><a href="#tab1">Fast Update</a></li>
            <li><a href="#tab2">Schedule</a></li>
            <li><a href="#tab3">Read Me</a></li>
    </ul>
    <div class="tab_container">
        <div id="tab1" class="tab_content">
            <form method="post" id="wdu_options">
                <?php
                settings_fields('wdu_options');
                $wdu_options = get_option('wdu_options');
                ?>

<a class="button-primary" id="stock_template" href="<?php echo WDU_URL ?>tasks/generate_template.php?column=stock">Download Stock Template</a><a class="button-primary" id="prices_template" href="<?php echo WDU_URL ?>tasks/generate_template.php?column=price">Download Prices Template</a>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>Step 1: Settings</label></th>
                        <td>

                          <table>
                            <tr>
                              <td>Chunk Size<a title="Refers to the size of product records that can be processed at a time. Lower is faster, but higher should be used if the update fails due to memory restrictions.">*</a>:<input type="hidden" name="chunk_size" value="<?php echo $wdu_options['chunk_size'] ?>"></td>
                              <td><div id="slider"></div></td>
                            </tr>
                            <tr>
                              <td>CSV Delimiter:</td>
                              <td><input type="text" name="delimiter" maxlength="2" value="<?php echo preg_replace('/(\\\\)+/', '\\', $wdu_options['delimiter']); ?>"></td>
                            </tr>
                          </table>


                        </td>
                    </tr>
                    <tr valign="top" id="top_tr">
                        <th scope="row"><label>Step 2: Upload CSV</label></th>
                        <td>

                          <span class="button-primary fileinput-button">
                              
                              <span>Select file...</span>
                              <!-- The file input field used as target for the file upload widget -->
                              <input id="fileupload" type="file" name="files[]" data-action="server/php/" multiple foo="bar">
                          </span>
                          <span id="file_name"></span>
                          
                          <br>
                          <br>
                          <!-- The global progress bar -->
                          <div id="progress" class="progress">
                              <div class="progress-bar progress-bar-primary"></div>
                          </div>
                          <!-- The container for the uploaded files -->
                          <div id="files" class="files"></div>

                        </td>
                    </tr>
                    <tr valign="top" class="hidden" id="step3">
                        <th scope="row"><label>Step 3: Map Columns</label></th>
                        <td id="choose_columns">
                          <table>
                            
                          </table>
                           <a class="button button-primary" id="step3_continue">Continue...</a>
                           
                        </td>
                    </tr>
                    <tr valign="top" class="hidden" id="step4">
                        <th scope="row"><label>Step 4: Test</label></th>
                        <td><a class="button button-primary" id="run_test">Run Test</a>
                        <p class="info">
                        When you're ready to test the update click Run Test.<br>
                        This will give you a report on how many records will be updated.
                        </p>
                        </td>
                    </tr>

                    <tr valign="top" class="hidden" id="step5">
                        <th scope="row"><label>Step 5: Run Update</label></th>
                        <td><a class="button button-primary" id="update">Update</a>
                        <p class="info">
                        Click update when you're ready to make changes to your live store.
                        </p>
                        </td>
                    </tr>

                </table>
            </form>
        </div>
        <div id="tab2" class="tab_content">
          <h3>Scheduled Daily Import (click to edit)</h3>
          <div class="postbox">
          <table id="schedule_table" class="table table-striped">
            <thead><tr><th title="The CSV Data Source">Source</th><th title="This is usually a comma (,)">Delimiter</th><th title="Chunk Size">Chunk Size</th><th title="Column to Update">CTU</th><th title="The column is the CSV that maps to _sku">SKU Column</th><th title="The column is the CSV that maps to _stock or _price">CTU Column</th><th title="The next scheduled Import">Next Event</th></tr></thead>
            <tbody>
              <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
          </table>
          </div>

<a class="button-primary" id="save_schedule">Save Changes</a><a class="button-primary" id="clear_schedule">Clear Schedule</a>

        </div>
        <div id="tab3" class="tab_content">
            <?php
              require_once WDU_DIR . '/lib/parsedown.php';
              $p = new Parsedown();
              echo $p->parse(file_get_contents(WDU_DIR . '/readme.txt'));
            ?>
        </div>

    </div>
</div>
<?php } ?>