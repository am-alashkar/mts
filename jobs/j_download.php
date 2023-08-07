<?php
$oldtime = time()-(2600000);
$old_files = db::$db->select('*','files','m_date' ,'<' ,$oldtime); // almost 30 days
foreach ($old_files as $old_file) {
    //die('OK');
    unlink('./downloads/'.$old_file['location']);
}
db::$db->delete('files','m_date','<',$oldtime);
if (!data::$get->all[1]) {
    $msg = 'You are trying to download nothing. <br>
 If you really want to download nothing this page is for you. You have successfully downloaded nothing.';
    create_error($msg,404);
}
$file = db::$db->select('*','files','id','=',data::$get->all[1])->first();
if (!$file) create_error('File Not Found' , 404);
if ($file['user_id'] && $file['user_id'] != member::$current->id) create_error('File Not Found.',404);
if ($file['n_p'] && !allowed($file['n_p'])) create_error('Not Allowed',403);
header('Content-disposition: attachment; filename="'.$file['file_name'].'"');
if ($file['file_format'] ) header("Content-Type: ".$file['file_format']);//application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$file = './downloads/'.$file['location'];
if (file_exists($file)) {
    readfile($file);
} else {
    create_error('File Not Found',404);
}

die();