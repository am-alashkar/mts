<?php
function obj_nav_links(){
    $links = new style('nav_links');
    $i = 1;
    $html = '';
    while ($link = $links->get_part($i++).''){
        $ht = explode("\n",$link,2);
        $per = strtolower(trim((string) $ht['0']));
        if (allowed($per) || "all" == $per) $html .= $ht['1'];
        elseif ('member' == $per && member::$current->id) $html .= $ht['1'];
        elseif ('guest' == $per && !member::$current->id) $html .= $ht['1'];
    }
    return $html;
}
function obj_user_info() {
    //if (member::$current->id)
    $html = member::$current->name .'<input type="button" id="user_id" style="display:none" value="'.+member::$current->id.'">';
    /** if (member::$current->id > 0) {
        $html .= ' <input type="button" class="btn btn-outline-dark" id="balance" value="'.member::$current->balance.'" title="الرصيد الحالي">
        <input type="button" id="credit" style="display:none" value="'.+member::$current->credit.'">
        ';
    } */
        return $html;
    //return '';
}
function obj_nav_links_services() {
    $html = new style('nav_link_services');
    $tmp = db::$db->select('*','services');
    foreach ($tmp->next() as $service)
        if (allowed($service['n_p'])) {
            if (data::$get->all[0] == $service['folder_link']) {
                $service['active'] = style::variable('active');
                $info['is_open'] = style::variable('menu_open');
                $info['active'] = style::variable('active');
            }
            $services[] = $service;
        }
    $html->fill_template($services)->fill($info);
    return $html;
}
function obj_nav_links_prices() {
    $html = new style('nav_link_prices');
    $tmp = db::$db->select('*','prices');
    foreach ($tmp->next() as $price)
        if (allowed($price['n_p'])) {
            if (data::$get->all[1] == $price['folder_link']) {
                $price['active'] = style::variable('active');
                $info['is_open'] = style::variable('menu_open');
                $info['active'] = style::variable('active');
            }
            $prices[] = $price;
        }
    $html->fill_template($prices)->fill($info);
    return $html;
}
function obj_nav_links_bills() {
    if (!member::$current->id) return '';
    $main = new style('nav_link_bills');
//    $tmp = db::$db->select('*','prices');
//    foreach ($tmp->next() as $price)
//        if (allowed($price['n_p'])) {
//            if (data::$get->all[1] == $price['folder_link']) {
//                $price['active'] = style::variable('active');
//                $info['is_open'] = style::variable('menu_open');
//                $info['active'] = style::variable('active');
//            }
//            $prices[] = $price;
//        }
//    $html->fill_template($prices)->fill($info);
    $html  = new style();
    if (allowed('can_send_bills')){
        $html->add_html($main->get_part(1));
    }
    if (allowed('can_do_bills'))
    {
        $html->add_html($main->get_part(2));
    }
    return $html;
}
function obj_nav_user_links() {
    if (!member::$current->id) return '';
    $main = new style('nav_link_users');
    $parts = explode('<!-- SEP -->',$main.'');
    $html  = new style();
    foreach ($parts as $part) {
        $tmp = explode("\n",trim($part),2);
        if (allowed(trim((string) $tmp[0]))) {
            $html->add_html(trim((string) $tmp[1]));
        }
    }
    return $html;
}
function obj_customer_list_options() {
    $tmp = db::$db->select('*','customers');
    $result = '';
    foreach ($tmp as $item) {
        $result .= '<option value="'.$item['id'].'">'.$item['name'].' / '.$item['phone'].'</option><br>';
    }
    return $result;
}
function obj_wakeel_btn() {
    if (allowed('is_agent'))
    {
        return '<li class="nav-item">
    <a href="'.config::$get->home_link.'payment_cards/" class="nav-link">
        <i class="nav-icon fa fa-money-bill-wave"> </i>
        <p> أكواد التعبئة </p>
    </a>
</li>';
    }
    return '';
}