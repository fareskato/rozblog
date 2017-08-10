<?php
/**
 * Created by PhpStorm.
 * User: Florian
 * Date: 5/13/2015
 * Time: 3:38 PM
 */
// This includes gives us all the WordPress functionality
$root_url = (strpos($_SERVER['DOCUMENT_ROOT'], 'devaltimarussia') !== false) ? '/feyarose' : '';
include_once($_SERVER['DOCUMENT_ROOT'].$root_url.'/wp-load.php' );
include('feyarose_orders_functions.php');

// Make sure to use some namespacing for your functions:
// Mine for this example: "fz_csv_"
function fz_csv_export() {
    // This line gets the WordPress Database Object
    global $wpdb;

    // Make a DateTime object and get a time stamp for the filename
    $date = new DateTime();
    $ts = $date->format("Y-m-d-G-i-s");

    // A name with a time stamp, to avoid duplicate filenames
    $filename = "report-for-".date('Y-m-d', $_GET['date']);
    $orders = feyaroseorders_get_orders_for_deliverydate($_GET['date'], $_GET['current_city_id'] );

    $itemsAlreadyInExport = array();
    $item_meta_not_displayed = array('_wc_checkout_add_on_id','_wc_checkout_add_on_label','_wc_checkout_add_on_value','_tax_class', '_product_id', '_variation_id', '_line_subtotal', '_line_total', '_line_subtotal_tax', '_line_tax', '_line_tax_data');
    $order_meta_displayed = array(
        'additional_field_804'=>'Street',
        'additional_field_154'=>'Phone',
        'additional_field_30'=>'Recipient',
        'additional_field_593'=>'Metro',
        'additional_field_596'=>'Floor',
        'additional_field_394'=>'City',
        'additional_field_533'=>'Apartment',
        'additional_field_926'=>'House',
        'additional_field_655'=>'Street',
        'additional_field_270'=>'Period of delivery'
    );

    $lines = array();
    $headers = array();

    foreach($orders as $order) {


        foreach($order['items'] as $item) {


            if(!in_array($item['item']->order_item_id, $itemsAlreadyInExport) && $item['item_meta']['_qty'] > 0) {
                $itemsAlreadyInExport[]=$item['item']->order_item_id;
                $line = array();
                $line['ID'] = $order['post']->ID;
                /*
                var_dump($order);
                die();
                */
                $headers['ID'] = 'ID';
                $line['ORDER DATE'] = $order['post']->post_date;
                $headers['ORDER DATE'] = 'ORDER DATE';





                $headers['ITEM TITLE'] = 'ITEM TITLE';
                $line['ITEM TITLE'] = $item['item']->order_item_name;



                foreach($order['post_meta'] as $ind_meta => $value) {
                    foreach($order_meta_displayed as $ind_disp => $value_disp) {
                        if($ind_meta == $ind_disp) {
                            $headers[$value_disp] = $value_disp;
                            $line[$value_disp] = $value;
                        }
                    }

                }


                foreach($item['item_meta'] as $ind_meta => $value) {
                    if(!in_array($ind_meta, $item_meta_not_displayed)) {
                        $label_header = feyaroseorders_util_getSearchLabel($ind_meta, true);
                        $label_header = ($ind_meta == '_qty') ? 'QTY' : $label_header;
                        $headers[$label_header] = $label_header;
                        $line[$label_header] =$value;
                    }
                }




                $lines[] = $line;

            }

        }


    }

    /*
     * Reorder columns and fill empty cells in data lines
     *
     * */
    $temp_headers = array();
    foreach($headers as $header) {
        $temp_headers[]=$header;
    }
    $headers = $temp_headers;

    $nb_columns = count($headers);
    $temp_lines = array();
    foreach($lines as $line) {
        $temp_line = array();
        for($i = 0; $i < $nb_columns;$i++) {
            if(array_key_exists($headers[$i], $line)) {
                $temp_line[] = $line[$headers[$i]];
            } else {
                $temp_line[] = '';
            }
        }
        $temp_lines[] = $temp_line;
    }
    $lines = $temp_lines;
    /*
     * Generate file
     * */
    /*
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment;filename='.$filename);
    $fp = fopen('php://output', 'w');


    fputcsv($fp, $headers);
    foreach ($lines as $data) {
        fputcsv($fp, $data);
    }
    fclose($fp);

    // Send the size of the output buffer to the browser
    $contLength = ob_get_length();
    header( 'Content-Length: '.$contLength);
    */
    $content = '';
    $content .= '<html charset="UTF-8" xmlns:x="urn:schemas-microsoft-com:office:excel">';
    $content .= '<head>';
    $content .= '<!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Sheet 1</x:Name>
                    <x:WorksheetOptions>
                        <x:Print>
                            <x:ValidPrinterInfo/>
                        </x:Print>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->';
    $content .= ' <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"> ';
    $content .= ' <meta charset="UTF-8"> ';
    $content .= '</head>';
    $content .= '<body>';
    $content .= '<table>';
    $content .= '<thead><tr>';
    foreach($headers as $header) {
        $content .= '<th>'.$header.'</th>';
    }
    $content .= '</tr></thead>';
    $content .= '<tbody>';
    foreach ($lines as $data) {
        $content .= '<tr>';
        foreach($data as $field) {
            $content .= '<td>'.$field.'</td>';
        }
        $content .= '</tr>';
    }
    $content .= '</tbody>';
    $content .= '</table>';
    $content .= '</body>';
    $content .= '</html>';

    $excel_content = $content;
    $filename = $filename.'.xls';
    $filepath = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/feyarose-orders/'.$filename;
    if(is_file($filepath)) {
        unlink($filepath);
    }
    $ourFileHandle = fopen($filepath, 'w') or die("can't open file");
    fwrite($ourFileHandle, $excel_content);
    fclose($ourFileHandle);
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel, charset=UTF-8; encoding=UTF-8");
    $fileuri = $filepath;
    $myfile = fopen($fileuri, "r") or die("Unable to open file!");
    echo fread($myfile,filesize($fileuri));
    fclose($myfile);
}
// This function removes all content from the output buffer
//ob_end_clean();
// Execute the function
fz_csv_export();
