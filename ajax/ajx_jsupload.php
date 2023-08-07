<?php
if (data::$get->data_file)
{
    $data = data::get_file();
    $script = 'uf_'.data::$get->script;
    if (function_exists($script)) {
        $script($data);
    } else {
        print '<span style="color: red; ">Upload Handler Error !</span>';
    }
}
else
{
    die('File Uploader');
}

function uf_price_upload($file) {

    if (!$file) create_error('Upload Error');
    $uid = data::$get->user_id;
    if (!$uid || !is_numeric($uid)) create_error('لا يمكن العثور على هذا المستخدم.');
    $user = member::load_member($uid);
    if ($user->is_empty() || $user->deleted) {
        print style::error_gritter('لا يمكن العثور على هذا المستخدم');
        die();
    }

    $xls = new SimpleXLSX($file,true);
    if (!$xls->success()) {
        create_error('الملف مرفوض ، يجب أن يكون الملف من نوع XLSX حصراً '.$xls->error());
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