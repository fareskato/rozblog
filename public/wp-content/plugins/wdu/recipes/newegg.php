<?php

// sample recipe
require_once dirname(__FILE__) . '/../lib/scraper.php';

function scrape($div){
  $item = array();
  if(!preg_match('/\d+\.\d{2}/', $div->at('.price-current')->text, $m)) die('no price!');
  $item['price'] = $m[0];
  $item['sku'] = $div->at('b[text()*="Item #:"]')->next->text;

  return $item;
}

$scraper = new scraper();

$page = $scraper->get('http://www.newegg.com/PS3-Games/SubCategory/ID-545');

foreach($page->search('.itemCell') as $div){
  $products[] = scrape($div);
}

$scraper->save($products);

?>