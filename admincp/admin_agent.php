<?php
if (!allowed('administration')) redirect_to_index();
data::$get->page_title = 'حسابات الوكلاء';
if (is_numeric(data::$get->all[2]))
{
    $user = member::load_member(data::$get->all[2]);
    if ($user->is_empty()) redirect(true,'','admincp/agent/');
    $clients = db::$db->select('*','agent_clients','agent_id','=',data::$get->all[2]);
    $html = new style('agent_client');
    foreach ($clients as $item) {
        $tmp =  member::load_member($item['user_id']);
        //$tmp['client_count'] = db::$db->select('count(user_id) as c','agent_clients','agent_id','=',$item['user_id'])->first()['c'];
        $users[] = $tmp;
    }
    $html->fill_table('agent_table',$users,true);
    $html->fill($user,null,true);

} else {
    $tmp = db::$db->select('*','permissions','script','=','is_agent');
    foreach ($tmp as $item) {
        $user =  member::load_member($item['user_id']);
        $user['client_count'] = db::$db->select('count(user_id) as c','agent_clients','agent_id','=',$item['user_id'])->first()['c'];
        $users[] = $user;

    }

    $html = new style('agent_mp');

    $html->fill_table('agent_table',$users,true);
}
job::$body = $html.'';
