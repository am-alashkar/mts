<?php
if (data::$get->confirm == 1) {
    member::logout();
    redirect_to_index();
}
if (member::$current->id <= 0) {
    redirect_to_index();
}
$main = new style('logout');

$html = $main->get_part(1);
$html->add_html($main->get_part(2).'')->add('header',true)->add('footer');
job::clear_html();
job::$body = $html;
data::$get->lmscript = $html->get_part(2);