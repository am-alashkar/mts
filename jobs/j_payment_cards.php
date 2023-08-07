<?php
redirect_to_index();
if (!allowed('is_agent')) redirect_to_index();
mark_active('payment_cards');
$html = new style('payment_cards_mp');
data::$get->page_title = 'أكواد التعبئة';

/**
 * load unused cards from unused cards table
 * load last 20 used cards from used cards table
 * put every data in its table
 *
 */
$unused = db::$db->select('*','unused_cards','maker','=',member::get_id(),'id','DESC');
$used = db::$db->select('*','used_cards','maker','=',member::get_id(),'id','DESC',20);
foreach ($used as $key=>$item) {
    $used->$key['client'] = member::load_member($item['client_id'])->name;
}
$html->fill_table('unused_cards',$unused,true)
->fill_table('used_cards',$used,true);
job::$body = $html;