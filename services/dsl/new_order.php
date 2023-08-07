<?php
/**
 * Client ONLY KAZIA
 * New Order page
 * generate new order html
 * store it in $html
*/
if (!allowed('dsl_client')) {
    redirect_to_index();
}
data::$get->page_title = 'فواتير DSL';
$user_id = member::$current->on_behalf ? member::$current->on_behalf : member::$current->id; // on behalf send ..
$main = new style('client_dsl');
$prices = null;
require './services/dsl/price.php';
$html = $main->get_part(1);
