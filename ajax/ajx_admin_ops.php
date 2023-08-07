<?php
if (!allowed('administration')) redirect_to_index();
$fu = 'admin_op_btn_'.data::$get->btn;
if (function_exists($fu)) $fu();
else create_error('Function Not Found '.data::$get->btn);
die();




function admin_op_btn_(){
    dd();
}
function admin_op_btn_del_dsl_company() {
    $info = data::decode(file_get_contents('./data/dsl.prs'));
    foreach ($info['company_list'] as $k => $value) {
        if ($value == data::$get->list) unset($info['company_list'][$k]);
    }
    unset($info['services'][data::$get->list]);
    file_put_contents('./data/dsl.prs',data::encode($info));
    $sc = '<script>
$("[dsl_name=\''.data::$get->list.'\']").remove();
</script>';
    die($sc);


}
function admin_op_btn_add_dsl_company()
{
    $info = data::decode(file_get_contents('./data/dsl.prs'));
    $companies = $info['company_list'];
    $html = new style('admin_price_dsl');
    if (!data::$get->save)
    {
        $c_option ='';
        foreach ($companies as $company) {
            $c_option .= '<option value="'.$company.'">'.$company.'</option>';
        }
        $html->part(2)->str_replace('<!-- COMPANY_LIST -->',$c_option)->create_modal('انشاء شركة DSL جديدة');
        print $html;
        die();
    }
    clean(data::$get->list);
    $msg = "";
    $newnames = explode("\n",data::$get->list);
    if ($companies)
    foreach ($newnames as $newname) {
        if (in_array($newname,$companies))
        {
            $msg .= '<br>'.$newname.' مستخدم سابقاً ';
        }
    }
    if ($msg)
    {
        create_error($msg);
    }
    if (data::$get->cp) $cinfo = $info['services'][data::$get->cp];
    else $cinfo = ['enabled'=>false];
    foreach ($newnames as $newname) {
        $info['company_list'][] = $newname;
        $info['services'][$newname] = $cinfo;
    }
    file_put_contents('./data/dsl.prs',data::encode($info));
    refresh(true,1);

}