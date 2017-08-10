<?php

/**
 * @author  Xu Ding
 * @email   thedilab@gmail.com
 * @website http://www.StarTutorial.com
 **/

class Calendar
{

    /*
    * @author Fares
    *   get the id od each product
    **/
//
//    public function getTheId(){
//        $allProducts = unserialize(get_option('feyaroseorders_products_types'));
//        foreach ($allProducts as $key => $value) {
//            $productID = $value[1];
//        }
//        return $productID;
//    }
    /********************* PROPERTY ********************/
    private $dayLabels = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;
    private $naviHref = null;
    private $stockData = null;
    private $baseUrl = '';
    private $productID;
    private $currentCity = null;
    private $stockType = null;
    private $stockLabel = null;
    private $currentCityId = null;
    /**
     * Constructor
     */
    public function __construct($id)
    {
        $this->productID = $id ;
    }
    /********************* PUBLIC **********************/
    public function setStockData($st)
    {
        $this->stockData = $st;
    }
    public function setStockType($stocktype) {
        $this->stockType = $stocktype;
    }
    public function setStockLabel($stocktype) {
        $this->stockLabel = $stocktype;
    }
    public function setCity($city) {
        $this->currentCity = $city;
        $this->currentCityId = ( $this->currentCity == 'Москва') ? 'm' : 'sp';
    }
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        $this->naviHref = $this->naviHref . $this->baseUrl;
    }
    /**
     * print out the calendar
     */
    public function show($year=null,$month=null)
    {
        if (null == $year && isset($_GET['year'])) {
            $year = $_GET['year'];
        } else if (null == $year) {
            $year = date("Y", time());
        }
        if (null == $month && isset($_GET['month'])) {
            $month = $_GET['month'];
        } else if (null == $month) {
            $month = date("m", time());
        }
        $this->currentYear = $year;
        $this->currentMonth = $month;
        $this->daysInMonth = $this->_daysInMonth($month, $year);
        $content = '
            <form name="feyaroseorders_stockform" method="post"
                              action="/wp-admin/admin.php'.$this->naviHref.'">
            <div id="calendar">' .
            '<div class="box">' .
            $this->_createNavi() .
            '</div>' .
            '<div class="box-content">' .
            '<ul class="label">' . $this->_createLabels() . '</ul>';
        $content .= '<div class="clear"></div>';
        $content .= '<ul class="dates">';
        $weeksInMonth = $this->_weeksInMonth($month, $year);
        // Create weeks in a month
        for ($i = 0; $i < $weeksInMonth; $i++) {
            //Create days in a week
            for ($j = 1; $j <= 7; $j++) {
                $content .= $this->_showDay($i * 7 + $j);
            }
        }
        $content .= '</ul>';
        $content .= '<div class="clear"></div>';
        $content .= '</div>';
        $content .= '</div>';
        $content .= '<input type="hidden" name="rozstore" value="'.$this->currentCity.'">
                        <input type="hidden" name="product_id" value="'.$this->productID.'"/>
                            <input type="hidden" name="feyaroseorders_hidden" value="Stocks">
                            <input type="hidden" name="stocktype" value="'.$this->stockType.'">
                            <input type="submit" name="updatestocks" value="'.__("Update stocks").'"/>
                            </form>';
        return $content;
    }
    /********************* PRIVATE **********************/
    /**
     * create the li element for ul
     */
    private function _showDay($cellNumber)
    {
        if ($this->currentDay == 0) {
            $firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));
            if (intval($cellNumber) == intval($firstDayOfTheWeek)) {
                $this->currentDay = 1;
            }
        }
        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {
            $this->currentDate = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
            //$cellContent = $this->currentDay;
            $cellContent = $this->_showStockInput($this->currentDay, $this->currentMonth, $this->currentYear);
            $this->currentDay++;
        } else {
            $this->currentDate = null;
            $cellContent = null;
        }
        return '<li id="li-' . $this->currentDate . '" class="' . ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
        ($cellContent == null ? 'mask' : '') . '">' . $cellContent . '</li>';
    }
    private function _showStockInput($day, $month, $year)
    {
        $retour = '';
        $start_date = mktime(0, 0, 0, $month, $day, $year);
        $stockAtDate = array('date' => $start_date, 'stock' => 0, 'orders' => array());

        if (isset($this->stockData[$start_date])) {
            $stockAtDate = $this->stockData[$start_date];
        }
        //$retour .= 'Calc Date : '.print_r($this->stockData[$start_date], true);
        $soldeStock = $stockAtDate['stock'] - $stockAtDate['to_deliver'];
        $stockClass = '';
        if ($soldeStock <= 20) {
            $stockClass = 'alert-stock';
        }
        $retour .= "<div class='day-stock " . $stockClass . "'>";
        $retour .= $day . '<br />';
        $retour .= "<input type='number' name='list_stock[" . $start_date . "]' value='" . $stockAtDate['stock'] . "' min='0' />";
        $retour .= "<div class='stock-detail'><b>" . $stockAtDate['to_deliver']. "</b> roses to deliver</div>"; // $this->productID
        $retour .= "<div class='stock-detail'>" . $soldeStock . " roses in stock</div>";


        if ($stockAtDate['to_deliver']> 0) {
            $root_url = (strpos($_SERVER['DOCUMENT_ROOT'], 'devaltimarussia') !== false) ? '/feyarose' : '';
            $retour .= "<div class='stock-detail'><a href='" . $root_url . "/wp-content/plugins/feyarose-orders/feyaroseorders_export.php?date=" . $start_date . "&current_city_id=".$this->currentCityId."'>View orders</a></div>";
        }
        $retour .= "</div>";


        return $retour;
    }
    /**
     * create navigation
     */
    private function _createNavi()
    {
        $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth) + 1;
        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear) + 1 : $this->currentYear;
        $preMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth) - 1;
        $preYear = $this->currentMonth == 1 ? intval($this->currentYear) - 1 : $this->currentYear;
        return
            '<div class="header">' .
            '<a class="prev cal-prev" data-month="'.sprintf("%02d", $preMonth).'" data-year="'.sprintf("%02d", $preYear).'" data-productid="'.$this->productID.'" href="' . $this->naviHref . '&month=' . sprintf('%02d', $preMonth) . '&year=' . $preYear . '">Prev</a>' .
            '<span class="title">' . date('Y M', strtotime($this->currentYear . '-' . $this->currentMonth . '-1')) . ' '.$this->currentCity.' - '. $this->stockLabel.'</span>' .
            '<a class="next cal-next" data-month="'.sprintf("%02d", $nextMonth).'" data-year="'.sprintf("%02d", $nextYear).'" data-productid="'.$this->productID.'" href="' . $this->naviHref . '&month=' . sprintf("%02d", $nextMonth) . '&year=' . $nextYear . '">Next</a>' .
            '</div>';
    }
    /**
     * create calendar week labels
     */
    private function _createLabels()
    {
        $content = '';
        foreach ($this->dayLabels as $index => $label) {
            $content .= '<li class="' . ($label == 6 ? 'end title' : 'start title') . ' title">' . $label . '</li>';
        }
        return $content;
    }
    /**
     * calculate number of weeks in a particular month
     */
    private function _weeksInMonth($month = null, $year = null)
    {
        if (null == ($year)) {
            $year = date("Y", time());
        }
        if (null == ($month)) {
            $month = date("m", time());
        }
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month, $year);
        $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);
        $monthEndingDay = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));
        $monthStartDay = date('N', strtotime($year . '-' . $month . '-01'));
        if ($monthEndingDay < $monthStartDay) {
            $numOfweeks++;
        }
        return $numOfweeks;
    }
    /**
     * calculate number of days in a particular month
     */
    private function _daysInMonth($month = null, $year = null)
    {
        if (null == ($year))
            $year = date("Y", time());
        if (null == ($month))
            $month = date("m", time());
        return date('t', strtotime($year . '-' . $month . '-01'));
    }

}

