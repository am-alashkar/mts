<?php
if (!allowed('administration')) redirect_to_index();
if (!is_numeric(data::$get->from) ||!is_numeric(data::$get->who)) die('البيانات غير صحيحة');
$agent = member::load_member(data::$get->from) ;
$client = member::load_member(data::$get->who);
if ($agent->is_empty() || $client->is_empty()) redirect(true,'','admincp/agent/');
$sql = "WHERE user_id = '".$client->id."' AND agent_id = '".$agent->id."'";
db::$db->delete('agent_clients',null,null,null,$sql);
$clients = db::$db->select('*','agent_clients','agent_id','=',$agent->id);
$html = new style('agent_client');
foreach ($clients as $item) {
    $tmp =  member::load_member($item['user_id']);
    $tmp['client_count'] = db::$db->select('count(user_id) as c','agent_clients','agent_id','=',$item['user_id'])->first()['c'];
    $users[] = $tmp;
}
$table = (new style())->add_html($html->get_table('agent_table'));
$table->fill_table('agent_table',$users,true);
$scr = '<script>
$("#table_area").html(`'.$table.'`);
</script>';
print $scr;
die();