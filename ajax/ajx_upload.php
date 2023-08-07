<?php
if (member::$current->id <1) {
    // only clients allowed
    die('Not allowed _1');
}
if (!$_SERVER['HTTP_REFERER']) {
    $msg = 'Upload I/O System <br>';
    if (member::$current->id > 0) {
        $msg .= 'You are logged in with id : '.member::$current->id;
    } else {
        $msg .= 'You are not logged in';
    }
    die($msg);
}
$script = 'uf_'.data::$get->script;
if ($_FILES['up_file']['error']) {
    create_error('File Upload Error '.$_FILES['up_file']['error']);
}
if (function_exists($script)) {
    $script();
} else {
    print '<span style="color: red; ">Upload Handler Error !</span>';
}
die();
function uf_price_upload() {

    $uid = explode('_',data::$get->tid)[1];
    $user = member::load_member($uid);
    if ($user->is_empty() || $user->deleted) {
        print style::error_gritter('لا يمكن العثور على هذا المستخدم');
        die();
    }

    $xls = new SimpleXLSX($_FILES['up_file']['tmp_name']);
    if (!$xls->success()) {
        create_error('الملف مرفوض ، يجب أن يكون الملف من نوع XLSX حصراً ');
    }
    $sheets = $xls->sheetNames();
    foreach ($sheets as $key => $sheet) {
        $sheet_index[$sheet] = $key+1;
    }
    if (!$sheet_index) create_error('الملف غير صحيح');
    $price_tags = phrases::$price_tags;
    //$prices = get_prices($user->id);
    //dd($price_tags);
    $user_id = $user->id;
    $final_ans = '';
    foreach ($price_tags as $key => $value) {
        if (!isset($sheet_index[$key])) continue;
        unset($info);
        $info = $xls->rows($sheet_index[$key]-1);
        if (! count($info) ) continue;
        $file = './services/'.$value.'/import.php';
        //print 'Doin '.$value.'<br>';
        //if ($key == 'جملة كازيات') dd($info);
        if (file_exists($file)) require $file;
    }
    if ($final_ans)
    {
        print 'تم تعديل <br>'.$final_ans;
    } else {
        print 'لم يتم اجراء أي تغيير';
    }
    die();
}