<?php
if (member::$current->id < 1 ) {
    redirect_to_index();
}
mark_active('customers');
if (!data::$get->all[1]) {
    data::$get->page_title = 'قائمة الزبائن';
    $html = new style('client_report_mp');
    $sql = "select * from customers  c left join (SELECT count(id) as total_jobs ,customer FROM `maintenance` GROUP BY customer) s on s.customer = c.id;";
    //if (allowed('super_admin')) $sql = "WHERE deleted IS NULL AND parent_id IS NULL;";
    $clients = db::$db->adv_select('','',$sql);

    $html->fill_table('clients',$clients,true);
} else {
    $e = null;
    if (!is_numeric(data::$get->all[1])) {
        create_error('Illegal Operation');
    }
    $client = db::$db->select('*','customers','id','=',data::$get->all[1])->first();
    if (!$client) {
        $html = style::msgbox('لا يمكن العثور على الحساب المطلوب','danger');
        $e = 1;
    }
    if (!$e) {
        data::$get->user_id = data::$get->all[1];
        data::$get->client_name = $client['name'];
        $html = new style('client_report_single');
        $sql = "WHERE customer = '".data::$get->user_id."' AND out_by IS NULL ORDER BY id DESC;";
        $results = db::$db->adv_select('*','search_table',$sql);// or die(db::$db->get_last_error());
        $sql = "WHERE customer = '".data::$get->user_id."' AND out_by IS NOT NULL ORDER BY id DESC;";
        $results2 = db::$db->adv_select('*','search_table',$sql);// or die(db::$db->get_last_error());
        //$workingOn = db::$db->select('*','stm','stat','IN','(0,1,4,5,6,2)','id','DESC');
        foreach ($results as $key => $value) {
            $results->{$key}['stat_html'] = phrases::$stat_name[$value['stat']];
        }
        foreach ($results2 as $key => $value) {
            $results2->{$key}['stat_html'] = phrases::$stat_name[$value['stat']];
        }
        if(!$results->is_empty())
        {
            foreach ($results as $key => $result) {
                foreach ($result as $k => $item) {
                    if (is_string($item))
                    {
                        $item = str_replace("\n",'<br>',$item);
                        $results->{$key}[$k] = $item;
                    }
                }
            }
        }
        if(!$results2->is_empty())
        {
            foreach ($results2 as $key => $result) {
                foreach ($result as $k => $item) {
                    if (is_string($item))
                    {
                        $item = str_replace("\n",'<br>',$item);
                        $results2->{$key}[$k] = $item;
                    }
                }
            }
        }
        $html->fill_table('working_on_table',$results,true)->fill_table('done_table',$results2,true);
        data::$get->page_title = 'صيانات الزبون '.$client['name'];
    }

}
job::$body = $html;