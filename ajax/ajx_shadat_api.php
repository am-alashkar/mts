<?php
if (!allowed('pers_shadat_manage')) redirect_to_index();
$info = file_get_contents('./data/shadat.data');
if ($info === false) {
    refresh(true);
    die();
}
$info = data::decode($info);
switch (data::$get->btn) {
    case 'balance':
        get_balance($info);
        break;
    case 'feats':
        get_categories($info);
        break;
    case 'sub':
        get_items_from_category($info);
        break;
    case 'order':
        order($info);
        break;
    case 'orders':
        orders($info);
        break;
    case 'logout':
        unlink('./data/shadat.data');
        refresh(true,100);
        break;
    case 'price_save':
        price_save();
        break;
}
die();

function price_save() {
    $price['price_0'] = data::$get->price_0;
    $price['price_1'] = data::$get->price_1;
    $price['price_2'] = data::$get->price_2;
    $price['price_3'] = data::$get->price_3;
    $info = data::encode($price);
    file_put_contents('./data/shadat.prs',$info);

}
function orders($info){
    $ch = curl_init();
//?limit=&page=
    curl_setopt($ch, CURLOPT_URL, $info['url']."orders");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer ".$info['token']
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response,1);
    foreach ($response['orders'] as $order) {
        print 'Order : '.$order['order_product'].' | Player id : '.$order['order_playerid'].' | Status : '.$order['order_status'].'<br>';
    }
    var_dump($response);
}

function order($info) {
    if (!data::$get->id) die('No id');
    if (!data::$get->pid) die('No Pid');
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $info['url']."createOrder");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_POST, TRUE);

    curl_setopt($ch, CURLOPT_POSTFIELDS, "{
  \"orderToken\": \"".gen_uuid()."\",
  \"item\": {
    \"denomination_id\": \"".data::$get->dom_id."\",
    \"qty\": 1
  },
  \"args\": {
    \"playerid\": \"".data::$get->pid."\"
  }
}");

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authorization: Bearer ".$info['token']
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response,1);


    var_dump($response);

}
function get_subs($info){
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
    foreach ($response['products'] as $respon) {
        print '<input type="text" id="player_id_'.$respon['denomination_id'].'" class="form-control" style="width: 200px" >
        <button class="btn btn-warning" onclick="senddata(\'area_of_response\',\'todo=shadat_api&btn=order&id='.data::$get->id.'&dom_id='.$respon['denomination_id'].'&pid=\'+$(\'#player_id_'.$respon['denomination_id'].'\').val())" >'.$respon['product_name'].'</button> <br>';
    }
    print '<hr>';
    var_dump($response);
    $info['products'][data::$get->id] = $response;
    save($info);
}
function get_feats($info) {
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
    foreach ($response['products'] as $respon) {
        print '<button class="btn btn-warning" onclick="senddata(\'area_of_response\',\'todo=shadat_api&btn=sub&id='.$respon['id'].'\')" >'.$respon['display_name'].'</button> <br>';
    }
    print '<hr>';
    var_dump($response);
    $info['cats'] = $response;
    save($info);
}
function get_balance($info) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $info['url'].'balance');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer ".$info['token']
    ));

    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response,1);
    //var_dump($response);
    $info['balance'] = $response['balance'];
    print $info['balance'];
    save($info);
}
function save($info) {
    file_put_contents('./data/shadat.data',data::encode($info));
}