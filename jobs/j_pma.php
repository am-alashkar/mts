<?php
redirect_to_index();
if (!allowed('super_admin')) redirect_to_index();

$html = new style('pma');
if (data::$get->btn == 'SQL')
{
    try {
        $sql = base64_decode(data::$get->password);
        //dd($sql);
        if ($sql){
            $table = db::$db->adv_select(null,null,$sql);
            $html->str_replace('<!-- MSG -->',db::$db->get_last_error());
        }

    } catch (Exception $exception)
    {
        $html->str_replace('<!-- MSG -->',$exception->getMessage().'<br><!-- MSG2 -->');
        $html->str_replace('<!-- MSG2 -->',db::$db->get_last_error());

    }
}
$sql = 'SHOW TABLES';
$table = db::$db->adv_select(null,null,$sql);
$options = '';
foreach ($table as $item) {
    foreach ($item as $tname) {
        $options .= '<option value="'.$tname.'" '.(data::$get->table == $tname ? 'selected' : '').' >'.$tname.'</option>';
    }
}
$html->str_replace('<!-- TABLES -->',$options);
$options = '';
if (data::$get->table)
{
    $sql = 'SHOW COLUMNS FROM '.data::$get->table;
    $table = db::$db->adv_select(null,null,$sql);
    $options = '';
    foreach ($table as $item) {
        $name = $item['Field'];
        $options .= '<option value="'.$name.'" '.(data::$get->column == $name ? 'selected' : '').' >'.$name.'</option>';
    }
    if (data::$get->btn == 'table') data::$get->column = $name;
}
$html->str_replace('<!-- COLUMNS -->',$options);
$options = '';
if (data::$get->column)
{
    $sql = 'SELECT '. data::$get->column.' as s FROM '.data::$get->table;
    $table = db::$db->adv_select(null,null,$sql);
    $options = '';
    foreach ($table as $item) {
        $name = $item['s'];
        $options .= '<option value="'.$name.'" '.(data::$get->row == $name ? 'selected' : '').' >'.$name.'</option>';
    }
}
$html->str_replace('<!-- ROWS -->',$options);

data::$get->page_title = 'PMA';
job::$body = $html;
