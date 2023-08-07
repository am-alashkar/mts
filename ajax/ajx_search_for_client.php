<?php
if (!allowed('administration')) redirect_to_index();
if (!data::$get->s || !data::$get->oid) die();
$s = data::$get->s;
$sql = "WHERE name REGEXP '".$s."' OR id = '".$s."' AND id <> ".data::$get->oid." AND deleted IS NULL AND disabled IS NULL LIMIT 10;";
$result = db::$db->adv_select('id,name','members',$sql);
if ($result->is_empty())
{
out();
}
out($result);
/**
 * @param string|result $result
 */
function out($result = '')
{
    if (is_object($result))
    {
        $ht = '<table class="table-hover table">';
        foreach ($result as $item)
        {
            $ht .= '<tr id="tr_'.$item['id'].'"><td>'.$item['id'].'</td>  <td>'.$item['name'].'</td><td> <button class="btn btn-default btn-sm" onclick="add_this_one(\''.$item['id'].'\')"><i class="fa fa-plus"></i> </button>
</td></tr>';
        }
        $ht .='</table>';
        print '<script>
$("#result_area").html(`'.$ht.'`);
</script>';
    } else
    {
        print '<script>
$("#result_area").html("لا يوجد نتائج");
</script>';
    }
    die();
}