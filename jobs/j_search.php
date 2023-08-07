<?php
if (!member::check()) redirect_to_index();
mark_active('search');
data::$get->page_title = 'بحث';
$html = new style('search');
$search_html = $html->get_part(2);
$html->part(1);
if (data::$get->from_enter_date) data::$get->from_enter_date = trim(str_replace('من','',data::$get->from_enter_date));
else
{
    data::$get->open_search_box = '<script>
$("#card_header").click();
document.getElementById("Card_result").scrollIntoView(); 
</script>';
    data::$get->from_enter_date = '1-1-2000';
}
if (data::$get->to_enter_date) data::$get->to_enter_date = trim(str_replace('إلى','',data::$get->to_enter_date));
else
{
    data::$get->to_enter_date = (new DateTime())->format(config::$get->storedatetime);
}
$results = new result();
if (data::$get->btn == 'search')
{
    $sql = 'WHERE';
    if (data::$get->customers)
    {
        $sql .= ' customer IN ('.data::$get->customers.')';
    }
    if (data::$get->device)
    {
        if ($sql != 'WHERE')
        {
            $sql .= ' AND';
        }
        $sql .= " device REGEXP '".data::$get->device."'";
    }
    if (data::$get->device_type)
    {
        if ($sql != 'WHERE')
        {
            $sql .= ' AND';
        }
        $sql .= " type REGEXP '".data::$get->device_type."'";
    }
    if (data::$get->from_enter_date && data::$get->to_enter_date)
    {
        try {
            $fromDate = new DateTime(data::$get->from_enter_date.'');
            $todate = new DateTime(data::$get->to_enter_date.'');
            $fromDate->setTime(0,0,0);
            $todate->setTime(23,59,59);
            if ($sql != 'WHERE')
            {
                $sql .= ' AND';
            }
            $sql .= " ( enter_date BETWEEN '".$fromDate->format(config::$get->storedatetime)."' AND '".$todate->format(config::$get->storedatetime)."' )";
        } catch (Throwable $ignored) {}
    }
    if (data::$get->fast_search)
    {
        data::$get->open_search_box = '<script>
 
</script>';
        if ($sql != 'WHERE')
        {
            $sql .= ' AND';
        }
        $snarr = explode('/',data::$get->fast_search.'',2);
        if (trim($snarr[1].''))
        {
            $sql .= " ( sn REGEXP '".trim($snarr[0].'')."' OR msn REGEXP '".trim($snarr[0].'')."' OR sn REGEXP '".trim($snarr[1].'')."' OR msn REGEXP '".
                trim($snarr[1].'')."' )";
        } else  $sql .= " ( sn REGEXP '".data::$get->fast_search."' OR msn REGEXP '".data::$get->fast_search."' )";
    }
    if (data::$get->city)
    {
        if ($sql != 'WHERE')
        {
            $sql .= ' AND';
        }
        $sql .= " customer_city REGEXP '".data::$get->city."'";
    }
    if (data::$get->customer_other_phone)
    {
        if ($sql != 'WHERE')
        {
            $sql .= ' AND';
        }
        $sql .= " ( customer_phone REGEXP '".data::$get->customer_other_phone."' OR ".
        " customer_phone2 REGEXP '".data::$get->customer_other_phone."' OR ".
        " customer_phone3 REGEXP '".data::$get->customer_other_phone."' OR ".
        " customer_phone4 REGEXP '".data::$get->customer_other_phone."' OR ".
        " customer_phone5 REGEXP '".data::$get->customer_other_phone."' OR ".
        " customer_phone6 REGEXP '".data::$get->customer_other_phone."' )"
        ;
    }
    if (data::$get->stats != '') // could be 0
    {
        if ($sql != 'WHERE')
        {
            $sql .= ' AND';
        }
        $sql .= " stat IN (".data::$get->stats.")";
    }
    //dd($sql);
    $sql .= ' ORDER BY id DESC LIMIT 200;';
    $results = db::$db->adv_select('*','search_table',$sql);// or die(db::$db->get_last_error());
    foreach ($results as $key => $value) {
        $results->{$key}['stat_html'] = phrases::$stat_name[$value['stat']];
        ($value['sn'] && $value['msn'] && $results->{$key}['snsep'] = '/') || $results->{$key}['snsep'] = '' ;
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
        //create_error(db::$db->get_last_error());
}
$search_html->fill_table('result_table',$results,true);
$html->str_replace('<!-- RESULTS -->',$search_html.'');
job::$body = $html;