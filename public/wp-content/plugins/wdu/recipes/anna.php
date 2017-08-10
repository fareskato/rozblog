<?php

// sample recipe
require_once dirname(__FILE__) . '/../lib/scraper.php';
require_once dirname(__FILE__) . '/../../../wp-load.php';

$scraper->browser->useCache = true;

function scrape($div){
  $item = array();
  if(!preg_match('/\d+\.\d{2}/', $div->at('.price-current')->text, $m)) die('no price!');
  $item['price'] = $m[0];
  $item['sku'] = $div->at('b[text()*="Item #:"]')->next->text;

  return $item;
}

$scraper = new scraper();

$page = $scraper->get('http://83.14.127.114/pulpitkontrahenta/ExportCSV/pr_stany.csv');
$fn = wp_tempnam();
echo $fn;
exit;
//$page->body

/*
1: http://83.14.127.114/pulpitkontrahenta/ExportCSV/pr_stany.csv  In this file: Kod = SAP, Liczba = stock, Cena = price
2: http://www.xpartner.net.pl/index.php?a=b2b_api_offerFile&m=getFile&hash=b61d84bb745c8f8541e0dcf66ac63cfb   In this file: Kod produktu = SAP, Ilosc = stock, Cena zakupu netto[pln] = price
3: attached file. In this file: indekskatalogowy = SAP, Liczba = stock
*/

foreach($page->search('.itemCell') as $div){
  $products[] = scrape($div);
}

$scraper->save($products);

?>