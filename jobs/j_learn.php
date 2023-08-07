<?php
die('Done');
set_time_limit(0);
/** STEP 1
$d = file_get_contents('learn.bak');
$e = explode("\n",$d);
foreach ($e as $item) {
    $a = data::decode($item);
    foreach ($a as $b) {
        $in['ot'] = $b['msg'];
        $i = db::$db->insert('ai_data',$in);
    }
}
die('Inserted '.$i);
 END STEP 1  */

/** STEP 2

$d = db::$db->select('*','ai_data');
foreach ($d as $item) {
    print '<div style="width: 90%" dir="rtl">';
    $txt = trim($item['ot']);
    $txt = str_replace(' ','_',$txt);
    //
    $txt = preg_replace('/[0-9]+/','d',$txt);
    //print '<span dir="rtl">'.$txt.'</span> - ';
    $tmp = explode('d',$txt);
    $i = 1;
    $txt = '';
    foreach ($tmp as $t) {
        if ($t) $txt .= $t . '|d'.$i++.'|';
    }
    //print '<span dir="rtl">'.trim($item['ot']).' : '.$txt.'</span><br></div>';
    //die();
    $in['st'] = $txt;
    if (!db::$db->update('ai_data',$in,'id','=',$item['id'])) {
        die('Error : '.db::$db->get_last_error());
    }
}
die('Done');

 END STEP 2  */

/** STEP 3
$d = db::$db->select('*','ai_data_step2');
$i = 0;
foreach ($d as $item) {
    $txt = trim($item['st']);
    $map[$txt] = $i++;
}
foreach ($map as $key => $item) {
    $in['msg'] = $key;
    $i = db::$db->insert('ai_data',$in);
}
die('Done . Inserted '.$i);
 END STEP 3 */
/** STEP 4
 $d= db::$db->select('*','ai_data_step3');
 $i = 1;
foreach ($d as $item) {
    $msg = $item['msg'];
    $msg = preg_replace('/\|d[0-9]+\|/','|D|',$msg);
    $tmp = explode('----------',$msg);
    foreach ($tmp as $t) {
        $map[trim($t)] = $i++;
    }
 }
foreach ($map as $key => $item) {
    $in['msg'] = $key;
    $i = db::$db->insert('ai_data',$in);
}
die('Done . Inserted '.$i);
/**  */
/** STEP 5 */

$d = db::$db->select('*','ai_data');
foreach ($d as $item) {
    $msg = $item['msg'];
    $msg = trim(str_replace('ارسال','',$msg));
    $msg = trim(str_replace('إرسال','',$msg));
    $msg = trim(str_replace('مزيد','',$msg));
    $msg = strtolower($msg);
    $msg = str_replace('|d|','|D|',$msg);
    $smgs = trim(str_replace('_','',$msg));
    $smgs = trim(str_replace('|D|','',$smgs));
    $smgs = trim(str_replace('-','',$smgs));
    $smgs = trim(str_replace('*','',$smgs));
    $smgs = trim(str_replace('.','',$smgs));
    $smgs = trim(str_replace('<','',$smgs));
    $smgs = trim(str_replace('>','',$smgs));
    $smgs = trim(str_replace('"','',$smgs));
    $smgs = trim(str_replace("'",'',$smgs));
    $smgs = trim(str_replace("/",'',$smgs));
    $smgs = trim(str_replace("\\",'',$smgs));
    $smgs = trim(str_replace(',','',$smgs));
    $smgs = trim(str_replace('،','',$smgs));
    $smgs = trim(str_replace(':','',$smgs));
    $smgs = trim(str_replace(';','',$smgs));
    $smgs = trim(str_replace("\n",'',$smgs));
    $smgs = trim(str_replace("\r",'',$smgs));
    $smgs = trim(str_replace('ـ','',$smgs));
    $smgs = trim(str_replace('#','',$smgs));
    $smgs = trim(str_replace('َ','',$smgs));
    $smgs = trim(str_replace('ً','',$smgs));
    $smgs = trim(str_replace('ُ','',$smgs));
    $smgs = trim(str_replace('ٌ','',$smgs));
    $smgs = trim(str_replace('ِ','',$smgs));
    $smgs = trim(str_replace('ٍ','',$smgs));
    $smgs = trim(str_replace('~','',$smgs));
    $smgs = trim(str_replace('ْ','',$smgs));
    $smgs = strtolower($smgs);

    $in['amsg'] = $msg;
    $in['smsg'] = $smgs;
    db::$db->update('ai_data',$in,'id','=',$item['id']);
}
die('END STEP 5');