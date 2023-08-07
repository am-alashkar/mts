<?php
die('Step DONE');
$tmp = db::$db->select('*','maintenance_old','id','>' ,'8000');
foreach ($tmp as $item) {
    $old_id = $item['id'];
    unset($item['id']);
    $id = db::$db->insert('maintenance_2',$item);
    if ($id && $id != $old_id)
    {
        db::$db->update('maintenance_2',['id'=>$old_id],'id','=',$id);
    }
    if (!$id) die(db::$db->get_last_error());
}
die('Done already');
set_time_limit(0);
$statNumbers['تمت الصيانة'] = '2';
$statNumbers['صيانة دبي'] = '5';
$statNumbers['صيانة معلقة'] = '6';
$statNumbers['قيد الصيانة'] = '1';
$tmp = db::$db->select('*','cases');//, 'caseID' , '>' , '5000');
foreach ($tmp as $item) {
    $stat = trim($item['Status'].'');
    if ($stat == 'ملغى') continue;
    //$id = $item['ID'];
    $case_id = max($item['ID'],$item['caseID']);
    $device = trim($item['Title'].'');
    $type = trim($item['Category'].'');
    $sn = trim($item['SN'].'');
    $msn = trim($item['FNumber'].'');
    $enter_date = trim($item['Opened Date'].'');
    $customer_id = trim($item['Customer'].'');
    $customer = db::$db->select('*','customers','old_id','=',$customer_id)->first();
    if (!$customer)
    {
        $tc = db::$db->select('*','customers2','ID','=',$customer_id)->first();
        $cin = new result();
        if (!$tc)
        {
            $test = db::$db->select('*','customers','name','=','غير معروف')->first();
            if (!$test)
            {
                $cin->name = 'غير معروف';
                $cin->phone = '';
                $id = db::$db->insert('customers',$cin);
            } else
            {
                $id = $test['id'];
            }
        } else
        {
            $cin->name = trim($tc['Company'].'');
            if (!$cin->name)
            {
                $cin->name = trim($tc['First Name'].'').' '.trim($tc['Last Name'].'');
            }
            if (!trim($cin->name.''))
            {
                $test = db::$db->select('*','customers','name','=','غير معروف')->first();
                if (!$test)
                {
                    $cin->name = 'غير معروف';
                    $cin->phone = '';
                    $id = db::$db->insert('customers',$cin);
                } else
                {
                    $id = $test['id'];
                }
            }
            unset($phones);
            $phones[] = trim($tc['Business Phone'].'');
            $phones[] = trim($tc['Home Phone'].'');
            $phones[] = trim($tc['Mobile Phone2'].'');
            $phones[] = trim($tc['Mobile Phone3'].'');
            $phones[] = trim($tc['Mobile Phone'].'');
            $phones[] = trim($tc['Fax Number'].'');
            $cnt = '';
            foreach ($phones as $phone) {
                if ($phone)
                {
                    $k = 'phone'.$cnt;
                    $cin->$k = $phone;
                    if (!$cnt) $cnt++;
                    $cnt++;
                }
            }
            if (!$cin->phone)
            {
                $cin->phone = 'لا يوجد';
            }
            $cin->address = trim($tc['Address'].'');
            $cin->city = trim($tc['City'].'');
            $cin->email = trim($tc['E-mail Address'].'');
            $cin->old_id = $customer_id;
            foreach ($cin as $key => $itt) {
                $cin->$key = cleaner($itt);
            }
            $id = db::$db->insert('customers',$cin);
        }

    } else
    {
        $id = $customer['id'];
    }
    $customer_id = $id;

    $added_by = trim($item['Assigned To'].'');
    if (!$added_by) $added_by = '1';
    $ut = db::$db->select('*','employees','ID','=',$added_by)->first();
    if (!$ut)
    {
        $added_by = '1';
    }
    $user = db::$db->select('*','members','credit','=',$added_by)->first();
    if (!$user)
    {
        $uin = new result();
        $ut = db::$db->select('*','employees','ID','=',$added_by)->first();
        $uin->login = $ut['user_name'];
        //if (!$uin->login) dd($added_by);
        $uin->name = $ut['First Name'] .' '. $ut['Last Name'];
        if (!$ut['password']) $ut['password'] = '123';
        $uin->password = (new password($ut['password']))->get_hash();
        $uin->credit = $added_by;
        $id = db::$db->insert('members',$uin);

    } else
    {
        $id = $user['id'];
    }
    $added_by = $id;

    $adds = trim($item['Accessories'].'');
    $notes = trim($item['Comments'].'');
    $description = trim($item['Description'].'');
    $issue = trim($item['Issue'].'');
    $stat = trim($item['Status'].'');
    $delivered = trim($item['dilevr_ok'].'');
    $out_date = trim($item['dilevr_date'].'');
    if (strpos($out_date,'000-00-') == '1')
    {
        $delivered = '';
        $out_date = '';
    }
//    if ($case_id == '6632')
//    {
//        var_dump( strpos($out_date,'000-'));;
//        var_dump($out_date);
//        die();
//    }
    $out_by = $added_by;
    if ($delivered == '1' || mb_strlen($out_date) > 4)
    {
        $stat = '3';
        $out_by = $added_by;
        try {
            $out_date = (new DateTime($out_date.''))->format(config::$get->storedatetime);
        } catch (Exception $exe)
        {
            $out_date = (new DateTime($enter_date.''))->format(config::$get->storedatetime);
        }
        $out_notes = 'من البرنامج القديم '.'('.trim($item['Status'].'').')';
    }
    else
    {
        $stat = $statNumbers[$stat];
        $out_by = $out_date = $out_notes = null;
    }
    $in = new result();
    $in->device = $device;
    $in->type = $type;
    $in->sn = $sn;
    $in->msn = $msn;
    $in->enter_date = $enter_date;
    $in->customer = $customer_id;
    $in->added_by = $added_by;
    $in->adds = $adds;
    $in->notes = $notes;
    $in->description = $issue;
    $in->stat = $stat;
    if ($stat == '3')
    {
        $in->out_date = $out_date;
        $in->out_by = $out_by;
        $in->out_notes = $out_notes;
    }
    foreach ($in as $key => $itt) {
        $in->$key = cleaner($itt);
    }
    $id = db::$db->insert('maintenance_2',$in);
    db::$db->update('maintenance_2',['id'=>$case_id],'id','=',$id);
    $log = new result();
    $log->m_id = $case_id;
    $log->user_id = $added_by;
    $log->report = $description;
    $log->log_enter_date = $enter_date;
    $log->prev_stat = '0';
    $log->new_stat = $statNumbers[trim($item['Status'].'')];;
    foreach ($log as $key => $itt) {
        $log->$key = cleaner($itt);
    }
    //db::$db->insert('m_log_2',$log);
}
//dd($tmp);
die('Done');
function cleaner($value)
{
    $value = str_replace("&", ' ', $value.''); // &amp;
    $value = str_replace("\\\\", '&#92;', $value);
    $value = str_replace("\\", '', $value);
    //$value = str_replace('/', '&frasl;', $value);
    $value = str_replace('<', '&lt;', $value);
    $value = str_replace('>', '&gt;', $value);
    $value = str_replace('"', ' ', $value); // &quot;
    $value = str_replace('‘', '&lsquo;', $value);
    $value = str_replace('’', '&rsquo;', $value);
    $value = str_replace("'", " ", $value); //&#39;
    $value = str_replace("%", "&#37;", $value);
    $value = str_replace("¬", '&not', $value);
    $value = str_replace("`", '', $value);
    return $value.'';
}