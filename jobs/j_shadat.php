<?php
redirect_to_index();
if (!allowed('shadat_client')) {
    redirect_to_index();
}
data::$get->page_title = 'شدات';
$mem_id = data::$get->mem_id ? data::$get->mem_id : member::$current->id; // in case send on behalf
$html = new style('client_shadat');
$info = file_get_contents('./data/shadat.data');
$info = data::decode($info);
if (!$info['token']) {
    $html->part(1);
} else {
    $html->part(2);

}
$prices = file_get_contents('./data/shadat.prs');
$info = data::decode($info);
$prices = data::decode($info);
$sql = "WHERE user_id = '".$mem_id."' AND script = 'shadat' ";
$custom_prices = db::$db->adv_select('*','custom_prices',$sql);

job::$body = $html;