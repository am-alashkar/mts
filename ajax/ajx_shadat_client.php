<?php
if (!allowed('shadat_client')) {
    redirect_to_index();
}
$info = file_get_contents('./data/shadat.data');
if ($info === false) {
    refresh(true);
    die();
}
$info = data::decode($info);
switch (data::$get->btn) {
    case 'get_category':
        $products = get_categories($info);
        $html = new style('client_shadat');
        foreach ($products as $product) {
            $ht .= $html->get_part(3)->fill($product).'';
        }
        print $ht;
        break;
    case 'select_category':
        $ok = false;
        if (!data::$get->id) die();
        $products = get_items_from_category($info);
        $html = new style('client_shadat');
        // @TODO I am here .. add the parts to style file
        $ht = $html->get_part(4); // items select table
        foreach ($products as $key=>$product) {
            if ($product['product_available']) {
                $products[$key]['cat_id'] = data::$get->id;
                $ok = true;
            }
        }
        $ht->fill_table('item_select',$products,false);
        if ($ok) print $ht;
        else print $html->part(5); // no product available
        break;
    case 'order':
        break;
}

die();
function send_order($info) {
    //
}
function get_categories($info) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $info['url'].'products');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer ".$info['token']
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response,1);
    return $response['products'];
//
//    foreach ($response['products'] as $respon) {
//        print '<button class="btn btn-warning" onclick="senddata(\'area_of_response\',\'todo=shadat_api&btn=sub&id='.$respon['id'].'\')" >'.$respon['display_name'].'</button> <br>';
//    }
//    print '<hr>';
//    var_dump($response);
//    $info['cats'] = $response;
//    save($info);
}
function get_items_from_category($info){
    if (!data::$get->id) die('No id');
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $info['url'].'products/?product_id='.data::$get->id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer ".$info['token']
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response,1);
    return $response['products'];
//    foreach ($response['products'] as $respon) {
//        print '<input type="text" id="player_id_'.$respon['denomination_id'].'" class="form-control" style="width: 200px" >
//        <button class="btn btn-warning" onclick="senddata(\'area_of_response\',\'todo=shadat_api&btn=order&id='.data::$get->id.'&dom_id='.$respon['denomination_id'].'&pid=\'+$(\'#player_id_'.$respon['denomination_id'].'\').val())" >'.$respon['product_name'].'</button> <br>';
//    }
//    print '<hr>';
//    var_dump($response);
//    $info['products'][data::$get->id] = $response;
//    save($info);
}