<?php
if (!member::check()) redirect_to_index();
$html = new style('add');
$btn  = $html->get_btn("add_new_customer_btn");
$html = new style('add_customer');
$html->create_modal('إضافة زبون');
if (data::$get->save == 1)
{
    $in = new result();
    $in->name = data::$get->name;
    $in->phone = data::$get->phone;
    $in->phone2 = data::$get->phone2;
    $in->phone3 = data::$get->phone3;
    $in->phone4 = data::$get->phone4;
    $in->phone5 = data::$get->phone5;
    $in->phone6 = data::$get->phone6;
    $in->city = data::$get->city;
    $in->email = data::$get->email;
    $in->address = data::$get->address;
    $id = db::$db->insert('customers',$in);
    if (!$id) create_error('Error while adding new customer');
    $scr = style::success_gritter('تم',true);
    $scr .= '<script>
var newOption = new Option("'.$in->name.' / '.$in->phone.'","'.$id .'", false, false);
$("#customer").append(newOption).trigger("change").val("'.$id.'").trigger("change");

</script>';
    die($scr);
}
die($html.$btn);