<?php
if (!data::$get->all[2]) redirect(true,'','admincp/c_price/1');
$id = data::$get->all['2'];
$prices = db::$db->select('*','custom_prices','user_id','=',$id);
$html = '<pre>';
foreach ($prices as $key=>$price) {

    $prices->{$key}['info'] = data::decode($price['info']);

}
$html .= print_r($prices,1);
$html .= '</pre>';

job::$body = $html;